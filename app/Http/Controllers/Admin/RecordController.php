<?php

namespace App\Http\Controllers\Admin;

use App\Concerns\HandlesJsonFields;
use App\Http\Controllers\Controller;
use App\Models\Collection;
use App\Models\Record;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RecordController extends Controller
{
    use HandlesJsonFields;

    public function index(): View
    {
        $records = Record::with('collection.tenant')
            ->latest()
            ->paginate(15);

        return view('admin.records.index', compact('records'));
    }

    public function create(): View
    {
        return view('admin.records.create', $this->formData() + ['record' => new Record()]);
    }

    public function store(Request $request): RedirectResponse
    {
        Record::create($this->validated($request));

        return redirect()->route('admin.records.index')
            ->with('sweetalert', ['icon' => 'success', 'title' => 'Record created', 'text' => 'Row saved.']);
    }

    public function edit(Record $record): View
    {
        return view('admin.records.edit', $this->formData() + compact('record'));
    }

    public function update(Request $request, Record $record): RedirectResponse
    {
        $record->update($this->validated($request));

        return redirect()->route('admin.records.index')
            ->with('sweetalert', ['icon' => 'success', 'title' => 'Record updated', 'text' => 'Row saved.']);
    }

    public function destroy(Record $record): RedirectResponse
    {
        $record->delete();

        return back()->with('sweetalert', ['icon' => 'success', 'title' => 'Record deleted', 'text' => 'Row removed.']);
    }

    private function formData(): array
    {
        return [
            'collections' => Collection::with('tenant')->orderBy('name')->get(),
        ];
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'collection_id' => ['required', 'exists:collections,id'],
            'data' => ['nullable', 'string', $this->jsonRule()],
        ]);

        // tenant_id is derived from the chosen collection (kept consistent automatically).
        $collection = Collection::findOrFail($data['collection_id']);

        return [
            'collection_id' => $collection->id,
            'tenant_id' => $collection->tenant_id,
            'data' => $this->decodeJson($request->input('data')),
        ];
    }
}
