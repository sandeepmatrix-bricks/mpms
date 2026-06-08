<?php

namespace App\Http\Controllers\Tenant;

use App\Concerns\HandlesJsonFields;
use App\Http\Controllers\Controller;
use App\Models\Collection;
use App\Models\Record;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RecordController extends Controller
{
    use HandlesJsonFields;

    public function index(Tenant $tenant): View
    {
        $records = Record::with('collection')->latest()->get();

        return view('tenant.records.index', compact('tenant', 'records'));
    }

    public function create(Tenant $tenant): View
    {
        return view('tenant.records.create', [
            'tenant' => $tenant,
            'record' => new Record(),
            'collections' => Collection::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request, Tenant $tenant): RedirectResponse
    {
        Record::create($this->validated($request));

        return redirect()->route('tenant.records.index', $tenant)
            ->with('sweetalert', ['icon' => 'success', 'title' => 'Record created', 'text' => 'Row saved.']);
    }

    public function edit(Tenant $tenant, Record $record): View
    {
        return view('tenant.records.edit', [
            'tenant' => $tenant,
            'record' => $record,
            'collections' => Collection::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Tenant $tenant, Record $record): RedirectResponse
    {
        $record->update($this->validated($request));

        return redirect()->route('tenant.records.index', $tenant)
            ->with('sweetalert', ['icon' => 'success', 'title' => 'Record updated', 'text' => 'Row saved.']);
    }

    public function destroy(Tenant $tenant, Record $record): RedirectResponse
    {
        $record->delete();

        return back()->with('sweetalert', ['icon' => 'success', 'title' => 'Record deleted', 'text' => 'Row removed.']);
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'collection_id' => ['required', 'exists:collections,id'],
            'data' => ['nullable', 'string', $this->jsonRule()],
        ]);

        // The chosen collection must belong to this tenant (Collection::find is scope-filtered).
        if (Collection::find($data['collection_id']) === null) {
            throw ValidationException::withMessages(['collection_id' => 'Invalid collection.']);
        }

        // tenant_id is auto-filled by BelongsToTenant from the scoped context.
        return [
            'collection_id' => $data['collection_id'],
            'data' => $this->decodeJson($request->input('data')),
        ];
    }
}
