<?php

namespace Gdevilbat\SpardaCMS\Modules\Role\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

use DB;

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

        DB::table('role')->insert([
            [
                'name' => 'Super Admin',
                'slug' => 'super-admin',
                'created_by' => 1,
                'modified_by' => 1,
            ],
            [
                'name' => 'Public',
                'slug' => 'public',
                'created_by' => 1,
                'modified_by' => 1,
            ],
        ]);
    }
}
