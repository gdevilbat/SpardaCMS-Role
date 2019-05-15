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
                'user_id' => 1
            ],
        ]);
    }
}
