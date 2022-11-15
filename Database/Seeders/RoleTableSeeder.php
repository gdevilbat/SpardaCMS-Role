<?php

namespace Gdevilbat\SpardaCMS\Modules\Role\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

use DB;

use Gdevilbat\SpardaCMS\Modules\Role\Entities\Role;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        Role::firstOrCreate(
            ['slug' => 'super-admin'],
            [
                'name' => 'Super Admin',
                'created_by' => 1,
                'modified_by' => 1,
            ],
        );

        Role::firstOrCreate(
            ['slug' => 'public'],
            [
                'name' => 'Public',
                'created_by' => 1,
                'modified_by' => 1,
            ],
        );
    }
}
