<?php

namespace App\Http\Controllers\Tenant;

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

/**
 * Pages for the current tenant. TenantContext is scoped, so reads auto-filter
 * and creates auto-fill tenant_id via the BelongsToTenant trait.
 */
class PageController extends Controller
{
    use HandlesJsonFields;

    public function index(Tenant $tenant): View
    {
        $pages = Page::with('parent')
            ->withCount('pageBlocks')
            ->orderBy('sort_order')
            ->get();

        return view('tenant.pages.index', compact('tenant', 'pages'));
    }

    public function create(Tenant $tenant): View
    {
        return view('tenant.pages.create', [
            'tenant' => $tenant,
            'page' => new Page(['layout' => 'single', 'is_active' => true, 'sort_order' => 0]),
            'parents' => Page::orderBy('title')->get(),
        ]);
    }

    public function store(Request $request, Tenant $tenant): RedirectResponse
    {
        $page = Page::create($this->validated($request, $tenant));

        return redirect()->route('tenant.pages.index', $tenant)
            ->with('sweetalert', ['icon' => 'success', 'title' => 'Page created', 'text' => $page->title]);
    }

    public function edit(Tenant $tenant, Page $page): View
    {
        return view('tenant.pages.edit', [
            'tenant' => $tenant,
            'page' => $page,
            'parents' => Page::where('id', '!=', $page->id)->orderBy('title')->get(),
        ]);
    }

    public function update(Request $request, Tenant $tenant, Page $page): RedirectResponse
    {
        $page->update($this->validated($request, $tenant, $page));

        return redirect()->route('tenant.pages.index', $tenant)
            ->with('sweetalert', ['icon' => 'success', 'title' => 'Page updated', 'text' => $page->title]);
    }

    public function destroy(Tenant $tenant, Page $page): RedirectResponse
    {
        $title = $page->title;
        $page->delete();

        return back()->with('sweetalert', ['icon' => 'success', 'title' => 'Page deleted', 'text' => $title]);
    }

    private function validated(Request $request, Tenant $tenant, ?Page $page = null): array
    {
        $request->merge(['slug' => Str::slug($request->input('slug') ?: $request->input('title'))]);

        $data = $request->validate([
            'parent_id' => ['nullable', 'exists:pages,id', Rule::notIn([$page?->id])],
            'slug' => [
                'required', 'string', 'max:255',
                Rule::unique('pages', 'slug')
                    ->where(fn ($q) => $q->where('tenant_id', $tenant->id))
                    ->ignore($page),
            ],
            'title' => ['required', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:100'],
            'layout' => ['required', 'string', 'max:50'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'config' => ['nullable', 'string', $this->jsonRule()],
        ]);

        // Parent must be one of this tenant's pages (Page::find is scope-filtered).
        if (($data['parent_id'] ?? null) && Page::find($data['parent_id']) === null) {
            throw ValidationException::withMessages(['parent_id' => 'Invalid parent page.']);
        }

        $data['is_active'] = $request->boolean('is_active');
        $data['config'] = $this->decodeJson($request->input('config'));
        $data['parent_id'] = ($data['parent_id'] ?? null) ?: null;

        return $data;
    }
}
