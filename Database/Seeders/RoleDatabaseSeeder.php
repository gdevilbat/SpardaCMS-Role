<?php

namespace Gdevilbat\SpardaCMS\Modules\Role\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class RoleDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // $this->call("OthersTableSeeder");
         $this->call(RoleTableSeeder::class);
         $this->call(RoleUsersTableSeeder::class);
         $this->call(RoleModuleTableSeeder::class);
    }
}
