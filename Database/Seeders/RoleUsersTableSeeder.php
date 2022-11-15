<?php

namespace Gdevilbat\SpardaCMS\Modules\Role\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

use DB;

use Gdevilbat\SpardaCMS\Modules\Role\Entities\RoleUser;

class RoleUsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        RoleUser::firstOrCreate(
            ['user_id' => '1'],
            [
                'role_id' => '1',
            ],
        );
    }
}
