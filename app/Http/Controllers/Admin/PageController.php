<?php

namespace App\Http\Controllers\Admin;

use App\Concerns\HandlesJsonFields;
use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PageController extends Controller
{
    use HandlesJsonFields;

    public function index(): View
    {
        $pages = Page::with(['tenant', 'parent'])
            ->withCount('pageBlocks')
            ->orderBy('tenant_id')
            ->orderBy('sort_order')
            ->paginate(15);

        return view('admin.pages.index', compact('pages'));
    }

    public function create(): View
    {
        return view('admin.pages.create', $this->formData() + ['page' => new Page(['layout' => 'single', 'is_active' => true, 'sort_order' => 0])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $page = Page::create($this->validated($request));

        return redirect()->route('admin.pages.index')
            ->with('sweetalert', ['icon' => 'success', 'title' => 'Page created', 'text' => $page->title]);
    }

    public function edit(Page $page): View
    {
        return view('admin.pages.edit', $this->formData() + compact('page'));
    }

    public function update(Request $request, Page $page): RedirectResponse
    {
        $page->update($this->validated($request, $page));

        return redirect()->route('admin.pages.index')
            ->with('sweetalert', ['icon' => 'success', 'title' => 'Page updated', 'text' => $page->title]);
    }

    public function destroy(Page $page): RedirectResponse
    {
        $title = $page->title;
        $page->delete();

        return back()->with('sweetalert', ['icon' => 'success', 'title' => 'Page deleted', 'text' => $title]);
    }

    private function formData(): array
    {
        return [
            'tenants' => Tenant::orderBy('name')->get(),
            'parents' => Page::with('tenant')->orderBy('title')->get(),
        ];
    }

    private function validated(Request $request, ?Page $page = null): array
    {
        $request->merge(['slug' => Str::slug($request->input('slug') ?: $request->input('title'))]);

        $data = $request->validate([
            'tenant_id' => ['required', 'exists:tenants,id'],
            'parent_id' => [
                'nullable',
                'exists:pages,id',
                Rule::notIn([$page?->id]), // a page cannot be its own parent
            ],
            'slug' => [
                'required', 'string', 'max:255',
                Rule::unique('pages', 'slug')
                    ->where(fn ($q) => $q->where('tenant_id', $request->input('tenant_id')))
                    ->ignore($page),
            ],
            'title' => ['required', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:100'],
            'layout' => ['required', 'string', 'max:50'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'config' => ['nullable', 'string', $this->jsonRule()],
        ]);

        // Parent must belong to the same tenant.
        if ($data['parent_id'] ?? null) {
            $parent = Page::find($data['parent_id']);
            if ($parent && $parent->tenant_id !== $data['tenant_id']) {
                throw ValidationException::withMessages([
                    'parent_id' => 'Parent page must belong to the same tenant.',
                ]);
            }
        }

        $data['is_active'] = $request->boolean('is_active');
        $data['config'] = $this->decodeJson($request->input('config'));
        $data['parent_id'] = ($data['parent_id'] ?? null) ?: null;

        return $data;
    }
}
