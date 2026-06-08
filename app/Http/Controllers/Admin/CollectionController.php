<?php

namespace App\Http\Controllers\Admin;

use App\Concerns\HandlesJsonFields;
use App\Http\Controllers\Controller;
use App\Models\Collection;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CollectionController extends Controller
{
    use HandlesJsonFields;

    public function index(): View
    {
        $collections = Collection::with('tenant')
            ->withCount('records')
            ->orderBy('tenant_id')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.collections.index', compact('collections'));
    }

    public function create(): View
    {
        return view('admin.collections.create', $this->formData() + ['collection' => new Collection()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $collection = Collection::create($this->validated($request));

        return redirect()->route('admin.collections.index')
            ->with('sweetalert', ['icon' => 'success', 'title' => 'Collection created', 'text' => $collection->name]);
    }

    public function edit(Collection $collection): View
    {
        return view('admin.collections.edit', $this->formData() + compact('collection'));
    }

    public function update(Request $request, Collection $collection): RedirectResponse
    {
        $collection->update($this->validated($request, $collection));

        return redirect()->route('admin.collections.index')
            ->with('sweetalert', ['icon' => 'success', 'title' => 'Collection updated', 'text' => $collection->name]);
    }

    public function destroy(Collection $collection): RedirectResponse
    {
        $name = $collection->name;
        $collection->delete();

        return back()->with('sweetalert', ['icon' => 'success', 'title' => 'Collection deleted', 'text' => $name]);
    }

    private function formData(): array
    {
        return ['tenants' => Tenant::orderBy('name')->get()];
    }

    private function validated(Request $request, ?Collection $collection = null): array
    {
        $request->merge([
            'key' => Str::of($request->input('key') ?: $request->input('name'))->lower()->snake()->toString(),
        ]);

        $data = $request->validate([
            'tenant_id' => ['required', 'exists:tenants,id'],
            'key' => [
                'required', 'string', 'max:255',
                Rule::unique('collections', 'key')
                    ->where(fn ($q) => $q->where('tenant_id', $request->input('tenant_id')))
                    ->ignore($collection),
            ],
            'name' => ['required', 'string', 'max:255'],
            'schema' => ['nullable', 'string', $this->jsonRule()],
        ]);

        $data['schema'] = $this->decodeJson($request->input('schema'));

        return $data;
    }
}
