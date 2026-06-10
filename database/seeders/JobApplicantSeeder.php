<?php

namespace Database\Seeders;

use App\Models\JobApplicant;
use App\Models\JobListing;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

/**
 * Demo applicants for every company. Mirrors the reference: complete applications
 * (final_submit = true) and unfinished drafts (final_submit = false).
 */
class JobApplicantSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Tenant::all() as $tenant) {
            $listings = JobListing::where('tenant_id', $tenant->id)->get();
            $listingId = fn () => $listings->isNotEmpty() ? $listings->random()->id : null;

            foreach ($this->complete() as $row) {
                $this->make($tenant->id, $listingId(), $row, finalSubmit: true);
            }

            foreach ($this->incomplete() as $row) {
                $this->make($tenant->id, $listingId(), $row, finalSubmit: false);
            }
        }
    }

    private function make(string $tenantId, ?string $listingId, array $row, bool $finalSubmit): void
    {
        JobApplicant::updateOrCreate(
            ['tenant_id' => $tenantId, 'email' => $row['email']],
            array_merge($row, [
                'tenant_id' => $tenantId,
                'job_listing_id' => $listingId,
                'final_submit' => $finalSubmit,
                'status' => $finalSubmit ? ($row['status'] ?? 'new') : 'new',
            ])
        );
    }

    private function complete(): array
    {
        return [
            [
                'name' => 'Anita Vikram Ram', 'email' => 'anita.ditya@gmail.com', 'phone' => '+919987299358',
                'gender' => 'Female', 'address' => 'Greenfield Apartment, Building 12, Flat 15, Sector 8, Nerul, Navi Mumbai, 400706, India',
                'source' => 'LinkedIn', 'position' => 'Business Operations Lead',
                'profile_image' => 'sample1.png', 'resume' => 'sample-resume.pdf', 'status' => 'shortlisted',
                'education' => [
                    ['school' => 'University of Mumbai', 'program' => 'MBA - Operations', 'startDate' => '2012', 'endDate' => '2014'],
                    ['school' => 'Mumbai University', 'program' => 'B.Com', 'startDate' => '2009', 'endDate' => '2012'],
                ],
                'work_experience' => [
                    ['company' => 'Acme Corp', 'position' => 'Operations Manager', 'startDate' => '2018', 'endDate' => 'Present'],
                ],
                'answers' => [
                    ['question' => 'Do you have 15+ years of experience in international sales or export management?', 'answer' => 'Yes'],
                    ['question' => 'Do you have direct experience in the alcoholic beverages industry?', 'answer' => 'No'],
                ],
            ],
            [
                'name' => 'Aroop Rout', 'email' => 'aroop.rout@gmail.com', 'phone' => '+919971018936',
                'gender' => 'Male', 'address' => 'Andheri East, Mumbai, 400069, India', 'source' => 'Naukri',
                'position' => 'Business Operations Lead',
                'profile_image' => 'sample2.png', 'resume' => 'sample-resume-2.pdf', 'status' => 'in_pipeline',
                'education' => [
                    ['school' => 'Delhi University', 'program' => 'B.Tech - Mechanical', 'startDate' => '2010', 'endDate' => '2014'],
                ],
                'work_experience' => [
                    ['company' => 'Globex', 'position' => 'Sales Lead', 'startDate' => '2016', 'endDate' => 'Present'],
                ],
                'answers' => [
                    ['question' => 'Do you have 15+ years of experience in international sales or export management?', 'answer' => 'Yes'],
                ],
            ],
            [
                'name' => 'Priya Sharma', 'email' => 'priya.sharma@gmail.com', 'phone' => '+919812345678',
                'gender' => 'Female', 'address' => 'Bandra West, Mumbai, 400050, India', 'source' => 'Referral',
                'position' => 'Marketing Manager',
                'profile_image' => 'sample3.png', 'resume' => 'sample-resume.pdf', 'status' => 'contacted',
                'education' => [
                    ['school' => 'Symbiosis', 'program' => 'MBA - Marketing', 'startDate' => '2013', 'endDate' => '2015'],
                ],
                'work_experience' => [],
                'answers' => [],
            ],
        ];
    }

    private function incomplete(): array
    {
        return [
            [
                'name' => 'Rahul Mehta', 'email' => 'rahul.mehta@gmail.com', 'phone' => '+919900112233',
                'gender' => 'Male', 'address' => 'Powai, Mumbai, India', 'source' => 'Company Website',
                'position' => 'Sales Executive', 'resume' => null,
                'education' => [], 'work_experience' => [], 'answers' => [],
            ],
            [
                'name' => 'Sneha Patil', 'email' => 'sneha.patil@gmail.com', 'phone' => '+919876501234',
                'gender' => 'Female', 'address' => 'Thane West, Mumbai, India', 'source' => 'Indeed',
                'position' => 'HR Associate', 'resume' => null,
                'education' => [
                    ['school' => 'Pune University', 'program' => 'BBA', 'startDate' => '2017', 'endDate' => '2020'],
                ],
                'work_experience' => [], 'answers' => [],
            ],
        ];
    }
}
