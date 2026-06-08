<?php

namespace Database\Seeders;

use App\Models\Collection;
use App\Models\Membership;
use App\Models\Page;
use App\Models\PageBlock;
use App\Models\Permission;
use App\Models\Record;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MpmsSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $permissions = collect([
            'manage_tenants',
            'manage_users',
            'manage_pages',
            'manage_collections',
            'view_dashboard',
        ])->map(fn (string $key) => Permission::create(['key' => $key]));

        $platformRole = Role::create([
            'name' => 'super_admin',
            'scope' => 'platform',
        ]);

        $tenantRole = Role::create([
            'name' => 'tenant_admin',
            'scope' => 'tenant',
        ]);

        $platformRole->permissions()->sync($permissions->pluck('id')->all());
        $tenantRole->permissions()->sync($permissions->whereNotIn('key', ['manage_tenants'])->pluck('id')->all());

        $tenants = collect([
            ['name' => 'Southseas Distilleries', 'slug' => 'southseas'],
            ['name' => 'MediCare', 'slug' => 'medicare'],
            ['name' => 'FleetGo', 'slug' => 'fleetgo'],
        ])->map(fn (array $attributes) => Tenant::create([
            'name' => $attributes['name'],
            'slug' => $attributes['slug'],
            'status' => 'active',
            'settings' => [
                'branding' => [
                    'theme' => 'default',
                ],
            ],
        ]));

        $platformAdmin = User::factory()->create([
            'name' => 'Priya',
            'email' => 'priya@mpms.local',
            'is_admin' => true,
        ]);

        Membership::create([
            'user_id' => $platformAdmin->id,
            'tenant_id' => null,
            'role_id' => $platformRole->id,
        ]);

        foreach ($tenants as $tenant) {
            $tenantAdmin = User::factory()->create([
                'name' => "{$tenant->name} Admin",
                'email' => Str::slug($tenant->slug).'@mpms.local',
            ]);

            Membership::create([
                'user_id' => $tenantAdmin->id,
                'tenant_id' => $tenant->id,
                'role_id' => $tenantRole->id,
            ]);

            $page = $tenant->pages()->create([
                'slug' => 'dashboard',
                'title' => 'Dashboard',
                'icon' => 'mdi-view-dashboard',
                'layout' => 'single',
                'sort_order' => 1,
                'is_active' => true,
                'config' => ['hero' => true],
            ]);

            if ($tenant->slug === 'shopmart') {
                $page->pageBlocks()->createMany([
                    [
                        'type' => 'kpi',
                        'region' => 'hero',
                        'position' => 1,
                        'config' => ['label' => 'Monthly Revenue', 'value' => '$64.2K'],
                        'data_source' => ['metric' => 'revenue'],
                    ],
                    [
                        'type' => 'chart',
                        'region' => 'body',
                        'position' => 2,
                        'config' => ['title' => 'Sales by Category'],
                        'data_source' => ['source' => 'orders'],
                    ],
                    [
                        'type' => 'table',
                        'region' => 'footer',
                        'position' => 3,
                        'config' => ['columns' => ['SKU', 'Name', 'Stock']],
                        'data_source' => ['collection' => 'products'],
                    ],
                ]);

                $collection = $tenant->collections()->create([
                    'key' => 'products',
                    'name' => 'Products',
                    'schema' => [
                        'fields' => [
                            ['name' => 'name', 'type' => 'string'],
                            ['name' => 'price', 'type' => 'decimal'],
                            ['name' => 'inventory', 'type' => 'integer'],
                        ],
                    ],
                ]);

                $collection->records()->createMany([
                    ['tenant_id' => $tenant->id, 'data' => ['name' => 'Blue Widget', 'price' => 29.99, 'inventory' => 132]],
                    ['tenant_id' => $tenant->id, 'data' => ['name' => 'Sale Widget', 'price' => 18.50, 'inventory' => 48]],
                ]);
            }

            if ($tenant->slug === 'medicare') {
                $page->pageBlocks()->createMany([
                    [
                        'type' => 'kpi',
                        'region' => 'hero',
                        'position' => 1,
                        'config' => ['label' => 'Active Patients', 'value' => 214],
                        'data_source' => ['metric' => 'patients'],
                    ],
                    [
                        'type' => 'list',
                        'region' => 'body',
                        'position' => 2,
                        'config' => ['title' => 'Today’s Appointments'],
                        'data_source' => ['source' => 'appointments'],
                    ],
                ]);

                $collection = $tenant->collections()->create([
                    'key' => 'patients',
                    'name' => 'Patient Records',
                    'schema' => [
                        'fields' => [
                            ['name' => 'first_name', 'type' => 'string'],
                            ['name' => 'last_name', 'type' => 'string'],
                            ['name' => 'condition', 'type' => 'string'],
                        ],
                    ],
                ]);

                $collection->records()->createMany([
                    ['tenant_id' => $tenant->id, 'data' => ['first_name' => 'Asha', 'last_name' => 'Khan', 'condition' => 'Diabetes']],
                    ['tenant_id' => $tenant->id, 'data' => ['first_name' => 'Martin', 'last_name' => 'Lopez', 'condition' => 'Hypertension']],
                ]);
            }

            if ($tenant->slug === 'fleetgo') {
                $tenant->pages()->create([
                    'slug' => 'shipments',
                    'title' => 'Shipments',
                    'icon' => 'mdi-truck',
                    'layout' => 'single',
                    'sort_order' => 1,
                    'is_active' => true,
                    'config' => ['subtitle' => 'Live shipment status'],
                ])->pageBlocks()->create([
                    'type' => 'table',
                    'region' => 'body',
                    'position' => 1,
                    'config' => ['columns' => ['Tracking', 'Destination', 'Status']],
                    'data_source' => ['collection' => 'shipments'],
                ]);

                $collection = $tenant->collections()->create([
                    'key' => 'shipments',
                    'name' => 'Shipments',
                    'schema' => [
                        'fields' => [
                            ['name' => 'tracking_number', 'type' => 'string'],
                            ['name' => 'destination', 'type' => 'string'],
                            ['name' => 'status', 'type' => 'string'],
                        ],
                    ],
                ]);

                $collection->records()->create([
                    'tenant_id' => $tenant->id,
                    'data' => ['tracking_number' => 'FLEET-0031', 'destination' => 'Dallas', 'status' => 'In Transit'],
                ]);
            }
        }
    }
}
