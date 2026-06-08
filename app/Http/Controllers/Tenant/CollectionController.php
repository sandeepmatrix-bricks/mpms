<?php

namespace App\Http\Controllers\Tenant;

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

    public function index(Tenant $tenant): View
    {
        $collections = Collection::withCount('records')->orderBy('name')->get();

        return view('tenant.collections.index', compact('tenant', 'collections'));
    }

    public function create(Tenant $tenant): View
    {
        return view('tenant.collections.create', ['tenant' => $tenant, 'collection' => new Collection()]);
    }

    public function store(Request $request, Tenant $tenant): RedirectResponse
    {
        $collection = Collection::create($this->validated($request, $tenant));

        return redirect()->route('tenant.collections.index', $tenant)
            ->with('sweetalert', ['icon' => 'success', 'title' => 'Collection created', 'text' => $collection->name]);
    }

    public function edit(Tenant $tenant, Collection $collection): View
    {
        return view('tenant.collections.edit', compact('tenant', 'collection'));
    }

    public function update(Request $request, Tenant $tenant, Collection $collection): RedirectResponse
    {
        $collection->update($this->validated($request, $tenant, $collection));

        return redirect()->route('tenant.collections.index', $tenant)
            ->with('sweetalert', ['icon' => 'success', 'title' => 'Collection updated', 'text' => $collection->name]);
    }

    public function destroy(Tenant $tenant, Collection $collection): RedirectResponse
    {
        $name = $collection->name;
        $collection->delete();

        return back()->with('sweetalert', ['icon' => 'success', 'title' => 'Collection deleted', 'text' => $name]);
    }

    private function validated(Request $request, Tenant $tenant, ?Collection $collection = null): array
    {
        $request->merge([
            'key' => Str::of($request->input('key') ?: $request->input('name'))->lower()->snake()->toString(),
        ]);

        $data = $request->validate([
            'key' => [
                'required', 'string', 'max:255',
                Rule::unique('collections', 'key')
                    ->where(fn ($q) => $q->where('tenant_id', $tenant->id))
                    ->ignore($collection),
            ],
            'name' => ['required', 'string', 'max:255'],
            'schema' => ['nullable', 'string', $this->jsonRule()],
        ]);

        $data['schema'] = $this->decodeJson($request->input('schema'));

        return $data;
    }
}
