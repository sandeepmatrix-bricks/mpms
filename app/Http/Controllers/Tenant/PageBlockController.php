<?php

namespace App\Http\Controllers\Tenant;

use App\Concerns\HandlesJsonFields;
use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\PageBlock;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PageBlockController extends Controller
{
    use HandlesJsonFields;

    public const TYPES = ['kpi', 'chart', 'table', 'list', 'form'];

    public function index(Tenant $tenant): View
    {
        $blocks = PageBlock::with('page')
            ->orderBy('page_id')
            ->orderBy('position')
            ->get();

        return view('tenant.page-blocks.index', compact('tenant', 'blocks'));
    }

    public function create(Tenant $tenant): View
    {
        return view('tenant.page-blocks.create', [
            'tenant' => $tenant,
            'block' => new PageBlock(['region' => 'body', 'position' => 0]),
            'pages' => Page::orderBy('title')->get(),
            'types' => self::TYPES,
        ]);
    }

    public function store(Request $request, Tenant $tenant): RedirectResponse
    {
        $block = PageBlock::create($this->validated($request));

        return redirect()->route('tenant.page-blocks.index', $tenant)
            ->with('sweetalert', ['icon' => 'success', 'title' => 'Block created', 'text' => ucfirst($block->type).' block']);
    }

    public function edit(Tenant $tenant, PageBlock $pageBlock): View
    {
        return view('tenant.page-blocks.edit', [
            'tenant' => $tenant,
            'block' => $pageBlock,
            'pages' => Page::orderBy('title')->get(),
            'types' => self::TYPES,
        ]);
    }

    public function update(Request $request, Tenant $tenant, PageBlock $pageBlock): RedirectResponse
    {
        $pageBlock->update($this->validated($request));

        return redirect()->route('tenant.page-blocks.index', $tenant)
            ->with('sweetalert', ['icon' => 'success', 'title' => 'Block updated', 'text' => ucfirst($pageBlock->type).' block']);
    }

    public function destroy(Tenant $tenant, PageBlock $pageBlock): RedirectResponse
    {
        $pageBlock->delete();

        return back()->with('sweetalert', ['icon' => 'success', 'title' => 'Block deleted', 'text' => 'Block removed.']);
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'page_id' => ['required', 'exists:pages,id'],
            'type' => ['required', Rule::in(self::TYPES)],
            'region' => ['required', 'string', 'max:50'],
            'position' => ['required', 'integer', 'min:0'],
            'config' => ['nullable', 'string', $this->jsonRule()],
            'data_source' => ['nullable', 'string', $this->jsonRule()],
            'content' => ['nullable', 'string'],
        ]);

        // The chosen page must belong to this tenant (Page::find is scope-filtered).
        if (Page::find($data['page_id']) === null) {
            throw ValidationException::withMessages(['page_id' => 'Invalid page.']);
        }

        $data['config'] = $this->decodeJson($request->input('config'));
        $data['data_source'] = $this->decodeJson($request->input('data_source'));

        return $data;
    }
}
