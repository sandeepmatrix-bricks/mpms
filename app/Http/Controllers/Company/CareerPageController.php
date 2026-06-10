<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\CareerPage;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Job Management → Main Page (the reference's "Main Page Details"). The careers
 * landing-page content — a single row per company, so this is an edit/update
 * screen rather than a full CRUD. Tenant-scoped and gated by the jobs module.
 */
class CareerPageController extends Controller
{
    public function edit(Request $request): View
    {
        $company = $this->company($request);

        return view('company.career_page.edit', [
            'company' => $company,
            'page' => CareerPage::firstOrNew(['tenant_id' => $company->id]),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $company = $this->company($request);

        $data = $request->validate([
            'page_title' => ['nullable', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
            'banner_content' => ['nullable', 'string', 'max:1000'],
            'introduction' => ['nullable', 'string'],
            'section_heading' => ['nullable', 'string'],
            'icon_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'banner_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $page = CareerPage::firstOrNew(['tenant_id' => $company->id]);

        $page->fill([
            'tenant_id' => $company->id,
            'page_title' => $data['page_title'] ?? null,
            'title' => $data['title'] ?? null,
            'banner_content' => $data['banner_content'] ?? null,
            'introduction' => $data['introduction'] ?? null,
            'section_heading' => $data['section_heading'] ?? null,
            'icon_image' => $this->upload($request, 'icon_image') ?? $page->icon_image,
            'banner_image' => $this->upload($request, 'banner_image') ?? $page->banner_image,
            'status' => 'active',
        ])->save();

        return redirect()->route('company.career-page.edit')->with('sweetalert', [
            'icon' => 'success', 'title' => 'Main page saved', 'text' => $page->page_title ?? '',
        ]);
    }

    private function company(Request $request): Tenant
    {
        return $request->user()->company();
    }

    private function upload(Request $request, string $field): ?string
    {
        if (! $request->hasFile($field)) {
            return null;
        }

        $file = $request->file($field);
        $name = time().random_int(10, 999).'.'.$file->getClientOriginalExtension();
        $file->move(public_path('uploads/careers'), $name);

        return $name;
    }
}
