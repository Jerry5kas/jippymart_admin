<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class SeoPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Add SEO permission to admin role (role_id = 1)
        $adminRole = Role::find(1);
        if ($adminRole) {
            $existingPermissions = Permission::where('role_id', 1)->pluck('permission')->toArray();
            
            if (!in_array('seo', $existingPermissions)) {
                Permission::create([
                    'permission' => 'seo',
                    'role_id' => 1,
                    'routes' => 'seo,seo.create,seo.store,seo.edit,seo.update,seo.destroy,seo.generate-sitemap,seo.preview-sitemap,seo.sitemap-stats,seo.settings.update'
                ]);
                
                $this->command->info('✅ SEO permission added to admin role');
            } else {
                $this->command->info('ℹ️  SEO permission already exists for admin role');
            }
        }

        $this->command->info('🎯 SEO permissions seeded successfully!');
    }
}
