<?php

namespace App\Http\Controllers\Admin;

use App\Concerns\HandlesJsonFields;
use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\PageBlock;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PageBlockController extends Controller
{
    use HandlesJsonFields;

    public const TYPES = ['kpi', 'chart', 'table', 'list', 'form'];

    public function index(): View
    {
        $blocks = PageBlock::with('page.tenant')
            ->orderBy('page_id')
            ->orderBy('position')
            ->paginate(15);

        return view('admin.page-blocks.index', compact('blocks'));
    }

    public function create(): View
    {
        return view('admin.page-blocks.create', $this->formData() + [
            'block' => new PageBlock(['region' => 'body', 'position' => 0]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $block = PageBlock::create($this->validated($request));

        return redirect()->route('admin.page-blocks.index')
            ->with('sweetalert', ['icon' => 'success', 'title' => 'Block created', 'text' => ucfirst($block->type).' block']);
    }

    public function edit(PageBlock $pageBlock): View
    {
        return view('admin.page-blocks.edit', $this->formData() + ['block' => $pageBlock]);
    }

    public function update(Request $request, PageBlock $pageBlock): RedirectResponse
    {
        $pageBlock->update($this->validated($request));

        return redirect()->route('admin.page-blocks.index')
            ->with('sweetalert', ['icon' => 'success', 'title' => 'Block updated', 'text' => ucfirst($pageBlock->type).' block']);
    }

    public function destroy(PageBlock $pageBlock): RedirectResponse
    {
        $pageBlock->delete();

        return back()->with('sweetalert', ['icon' => 'success', 'title' => 'Block deleted', 'text' => 'Block removed.']);
    }

    private function formData(): array
    {
        return [
            'pages' => Page::with('tenant')->orderBy('title')->get(),
            'types' => self::TYPES,
        ];
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

        $data['config'] = $this->decodeJson($request->input('config'));
        $data['data_source'] = $this->decodeJson($request->input('data_source'));

        return $data;
    }
}
