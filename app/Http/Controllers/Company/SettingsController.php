<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Company profile settings — the company edits its own basic details.
 */
class SettingsController extends Controller
{
    public function edit(Request $request): View
    {
        $company = $request->user()->company();

        // The owner (assigned by the Super Admin) first, then users the company
        // created itself.
        $members = $company->memberships()
            ->with(['user', 'role'])
            ->oldest()
            ->get();

        return view('company.settings.edit', compact('company', 'members'));
    }

    public function update(Request $request): RedirectResponse
    {
        $company = $request->user()->company();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        $company->update($data);

        return redirect()->route('company.settings.edit')->with('sweetalert', [
            'icon' => 'success', 'title' => 'Settings saved', 'text' => $company->name,
        ]);
    }
}
