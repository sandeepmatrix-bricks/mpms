<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Mail\MentionMail;
use App\Models\ActivityLog;
use App\Models\ApplicantComment;
use App\Models\CommentReaction;
use App\Models\JobApplicant;
use App\Models\Mention;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

/**
 * Activity Chat on an applicant — WhatsApp-style: text + image/video/file
 * attachments, @mentions (email + header bell), emoji reactions, edit (1-minute
 * window), and delete (for me / for everyone).
 */
class ApplicantCommentController extends Controller
{
    /** How long after posting a message can still be edited. */
    private const EDIT_WINDOW_SECONDS = 60;

    public function index(Request $request, JobApplicant $jobApplicant): JsonResponse
    {
        $company = $this->company($request);
        $this->ensureOwned($company, $jobApplicant);

        $me = $request->user()->id;

        $comments = $jobApplicant->comments()->with(['author', 'reactions'])->oldest()->get()
            ->reject(fn (ApplicantComment $c) => in_array($me, $c->deleted_for ?? [], true))
            ->map(fn (ApplicantComment $c) => $this->present($c, $me))
            ->values();

        return response()->json(['comments' => $comments]);
    }

    public function store(Request $request, JobApplicant $jobApplicant): JsonResponse
    {
        $company = $this->company($request);
        $this->ensureOwned($company, $jobApplicant);

        $data = $request->validate([
            'body' => ['nullable', 'string', 'max:2000', 'required_without:attachment'],
            'attachment' => ['nullable', 'file', 'max:10240', 'mimes:jpg,jpeg,png,webp,gif,mp4,webm,mov,pdf,doc,docx,xls,xlsx'],
            'mentions' => ['nullable', 'array'],
            'mentions.*' => ['uuid'],
        ]);

        $author = $request->user();
        $members = $this->companyUsers($company);

        [$attachment, $type] = $this->storeAttachment($request);

        $body = $data['body'] ?? '';
        $ids = collect($request->input('mentions', []))
            ->merge($this->parseMentionNames($body, $members))
            ->unique()
            ->filter(fn ($id) => $members->has($id) && $id !== $author->id)
            ->values();

        $comment = ApplicantComment::create([
            'tenant_id' => $company->id,
            'job_applicant_id' => $jobApplicant->id,
            'user_id' => $author->id,
            'body' => $body,
            'attachment' => $attachment,
            'attachment_type' => $type,
            'mentions' => $ids->all() ?: null,
        ]);

        foreach ($ids as $userId) {
            Mention::create([
                'tenant_id' => $company->id,
                'comment_id' => $comment->id,
                'job_applicant_id' => $jobApplicant->id,
                'mentioned_user_id' => $userId,
                'mentioned_by_user_id' => $author->id,
            ]);
            $this->notify($members->get($userId), $author, $comment, $jobApplicant);
        }

        ActivityLog::record('comment', "Commented on {$jobApplicant->name}", [
            'subject_type' => JobApplicant::class, 'subject_id' => $jobApplicant->id,
        ]);

        return response()->json(['comment' => $this->present($comment->load(['author', 'reactions']), $author->id)], 201);
    }

    public function update(Request $request, JobApplicant $jobApplicant, ApplicantComment $comment): JsonResponse
    {
        $this->ensureComment($request, $jobApplicant, $comment);
        $me = $request->user();

        abort_unless($comment->user_id === $me->id, 403, 'You can only edit your own messages.');
        abort_if($comment->is_deleted, 403, 'Message was deleted.');
        abort_if($comment->created_at->lt(now()->subSeconds(self::EDIT_WINDOW_SECONDS)),
            403, 'The 1-minute edit window has passed.');

        $data = $request->validate(['body' => ['required', 'string', 'max:2000']]);

        $comment->update(['body' => $data['body'], 'edited_at' => now()]);

        return response()->json(['comment' => $this->present($comment->fresh(['author', 'reactions']), $me->id)]);
    }

    /** Delete for everyone — author only. */
    public function destroy(Request $request, JobApplicant $jobApplicant, ApplicantComment $comment): JsonResponse
    {
        $this->ensureComment($request, $jobApplicant, $comment);
        abort_unless($comment->user_id === $request->user()->id, 403, 'You can only delete your own messages for everyone.');

        $comment->update(['is_deleted' => true, 'body' => '', 'attachment' => null, 'attachment_type' => null]);
        $comment->reactions()->delete();

        return response()->json(['ok' => true]);
    }

    /** Delete for me — hide this message for the current user only. */
    public function deleteForMe(Request $request, JobApplicant $jobApplicant, ApplicantComment $comment): JsonResponse
    {
        $this->ensureComment($request, $jobApplicant, $comment);

        $deletedFor = $comment->deleted_for ?? [];
        $deletedFor[] = $request->user()->id;
        $comment->update(['deleted_for' => array_values(array_unique($deletedFor))]);

        return response()->json(['ok' => true]);
    }

    /** Toggle an emoji reaction by the current user. */
    public function react(Request $request, JobApplicant $jobApplicant, ApplicantComment $comment): JsonResponse
    {
        $this->ensureComment($request, $jobApplicant, $comment);
        $data = $request->validate(['emoji' => ['required', 'string', 'max:16']]);
        $me = $request->user();

        $existing = $comment->reactions()->where('user_id', $me->id)->where('emoji', $data['emoji'])->first();

        if ($existing) {
            $existing->delete();
        } else {
            CommentReaction::create(['comment_id' => $comment->id, 'user_id' => $me->id, 'emoji' => $data['emoji']]);
        }

        return response()->json(['reactions' => $this->reactions($comment->fresh('reactions'), $me->id)]);
    }

    /* ---------------------------------------------------------------- */

    private function storeAttachment(Request $request): array
    {
        if (! $request->hasFile('attachment')) {
            return [null, null];
        }

        $file = $request->file('attachment');
        $ext = strtolower($file->getClientOriginalExtension());
        $name = time().random_int(10, 999).'.'.$ext;
        $file->move(public_path('chat_attachments'), $name);

        $type = in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true) ? 'image'
            : (in_array($ext, ['mp4', 'webm', 'mov'], true) ? 'video' : 'file');

        return [$name, $type];
    }

    private function notify(?User $recipient, User $author, ApplicantComment $comment, JobApplicant $applicant): void
    {
        if (! $recipient?->email) {
            return;
        }

        try {
            Mail::to($recipient->email)->send(new MentionMail($recipient, $author, $comment, $applicant));
        } catch (\Throwable $e) {
            report($e);
        }
    }

    private function parseMentionNames(string $body, $members)
    {
        $byName = $members->mapWithKeys(fn (User $u) => [mb_strtolower($u->name) => $u->id]);
        preg_match_all('/@([\w .\-]+)/u', $body, $matches);

        return collect($matches[1] ?? [])
            ->map(fn ($name) => $byName->get(mb_strtolower(trim($name))))
            ->filter()->values();
    }

    private function present(ApplicantComment $comment, string $meId): array
    {
        return [
            'id' => $comment->id,
            'body' => $comment->is_deleted ? null : $comment->body,
            'deleted' => $comment->is_deleted,
            'attachment' => $comment->is_deleted || ! $comment->attachment ? null : asset('chat_attachments/'.$comment->attachment),
            'attachment_type' => $comment->is_deleted ? null : $comment->attachment_type,
            'author' => $comment->author?->name ?? 'Unknown',
            'author_id' => $comment->user_id,
            'mine' => $comment->user_id === $meId,
            'initials' => mb_strtoupper(mb_substr($comment->author?->name ?? '?', 0, 1)),
            'time' => $comment->created_at?->format('h:i A'),
            'edited' => (bool) $comment->edited_at,
            'editable' => $comment->user_id === $meId && ! $comment->is_deleted
                && $comment->created_at->gte(now()->subSeconds(self::EDIT_WINDOW_SECONDS)),
            'reactions' => $comment->is_deleted ? [] : $this->reactions($comment, $meId),
        ];
    }

    private function reactions(ApplicantComment $comment, string $meId): array
    {
        return $comment->reactions->groupBy('emoji')->map(fn ($group, $emoji) => [
            'emoji' => $emoji,
            'count' => $group->count(),
            'mine' => $group->contains('user_id', $meId),
        ])->values()->all();
    }

    private function companyUsers(Tenant $company)
    {
        return $company->memberships()->with('user')->get()
            ->map->user->filter()->keyBy('id');
    }

    private function company(Request $request): Tenant
    {
        return $request->user()->company();
    }

    private function ensureOwned(Tenant $company, JobApplicant $applicant): void
    {
        abort_unless($applicant->tenant_id === $company->id, 404);
    }

    private function ensureComment(Request $request, JobApplicant $applicant, ApplicantComment $comment): void
    {
        $company = $this->company($request);
        $this->ensureOwned($company, $applicant);
        abort_unless($comment->job_applicant_id === $applicant->id && $comment->tenant_id === $company->id, 404);
    }
}
