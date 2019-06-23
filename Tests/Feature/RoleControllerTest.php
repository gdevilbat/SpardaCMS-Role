<?php

namespace Gdevilbat\SpardaCMS\Modules\Role\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RoleControllerTest extends TestCase
{
	use RefreshDatabase, \Gdevilbat\SpardaCMS\Modules\Core\Tests\ManualRegisterProvider;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testReadRole()
    {
        $response = $this->get(action('\Gdevilbat\SpardaCMS\Modules\Role\Http\Controllers\RoleController@index'));

        $response->assertStatus(302)
        		 ->assertRedirect(action('\Gdevilbat\SpardaCMS\Modules\Core\Http\Controllers\Auth\LoginController@showLoginForm')); // Return Not Valid, User Not Login

        $user = \App\User::find(1);

        $response = $this->actingAs($user)
        				 ->get(action('\Gdevilbat\SpardaCMS\Modules\Role\Http\Controllers\RoleController@index'))
        				 ->assertSuccessful(); // Return Valid user Login
    }

    public function testFormCreateDataRole()
    {
    	$response = $this->get(action('\Gdevilbat\SpardaCMS\Modules\Role\Http\Controllers\RoleController@create'));

        $response->assertStatus(302)
                 ->assertRedirect(action('\Gdevilbat\SpardaCMS\Modules\Core\Http\Controllers\Auth\LoginController@showLoginForm')); // Return Not Valid, User Not Login

        $user = \App\User::find(1);

        $response = $this->actingAs($user)
        				 ->get(action('\Gdevilbat\SpardaCMS\Modules\Role\Http\Controllers\RoleController@create'))
        				 ->assertSuccessful(); // Return Valid user Login
    }

    public function testCreateDataRole()
    {
    	$response = $this->post(action('\Gdevilbat\SpardaCMS\Modules\Role\Http\Controllers\RoleController@store'));

        $response->assertStatus(302)
                 ->assertRedirect(action('\Gdevilbat\SpardaCMS\Modules\Core\Http\Controllers\Auth\LoginController@showLoginForm')); //Return Not Valid, User Not Login

        $user = \App\User::find(1);

        $response = $this->actingAs($user)
        				 ->from(action('\Gdevilbat\SpardaCMS\Modules\Role\Http\Controllers\RoleController@create'))
        				 ->post(action('\Gdevilbat\SpardaCMS\Modules\Role\Http\Controllers\RoleController@store'))
        				 ->assertStatus(302)
        				 ->assertRedirect(action('\Gdevilbat\SpardaCMS\Modules\Role\Http\Controllers\RoleController@create'))
        				 ->assertSessionHasErrors(); //Return Not Valid, Data Not Complete

	    $faker = \Faker\Factory::create();
	    $slug = $faker->word;

		$response = $this->actingAs($user)
        				 ->from(action('\Gdevilbat\SpardaCMS\Modules\Role\Http\Controllers\RoleController@create'))
        				 ->post(action('\Gdevilbat\SpardaCMS\Modules\Role\Http\Controllers\RoleController@store'), [
								'name' => $faker->word,
								'slug' => $slug,
                                'description' => $faker->text,
        				 	])
        				 ->assertStatus(302)
        				 ->assertRedirect(action('\Gdevilbat\SpardaCMS\Modules\Role\Http\Controllers\RoleController@index'))
        				 ->assertSessionHas('global_message.status', 200)
        				 ->assertSessionHasNoErrors(); //Return Valid, Data Complete

	 	$this->assertDatabaseHas(\Gdevilbat\SpardaCMS\Modules\Role\Entities\Role::getTableName(), ['slug' => $slug]);
    }

    public function testUpdateDataRole()
    {
    	$response = $this->post(action('\Gdevilbat\SpardaCMS\Modules\Role\Http\Controllers\RoleController@store'), [
    					'_method' => 'PUT'
			    	]);

        $response->assertStatus(302)
                 ->assertRedirect(action('\Gdevilbat\SpardaCMS\Modules\Core\Http\Controllers\Auth\LoginController@showLoginForm')); //Return Not Valid, User Not Login


        $user = \App\User::find(1);
        $module = \Gdevilbat\SpardaCMS\Modules\Role\Entities\Role::latest()->first();

        $response = $this->actingAs($user)
				        ->from(action('\Gdevilbat\SpardaCMS\Modules\Role\Http\Controllers\RoleController@create').'?code='.encrypt($module->getKey()))
				        ->post(action('\Gdevilbat\SpardaCMS\Modules\Role\Http\Controllers\RoleController@store'), [
				        	$module->getKeyName() => encrypt($module->getKey()),
				        	'name' => $module->name,
				        	'slug' => $module->slug,
							'_method' => 'PUT'
				    	])
				    	->assertStatus(302)
						->assertRedirect(action('\Gdevilbat\SpardaCMS\Modules\Role\Http\Controllers\RoleController@index'))
						->assertSessionHas('global_message.status', 200)
						->assertSessionHasNoErrors(); //Return Valid, Data Complete
    }

    public function testDeleteDataRole()
    {
    	$response = $this->post(action('\Gdevilbat\SpardaCMS\Modules\Role\Http\Controllers\RoleController@destroy'), [
    					'_method' => 'DELETE'
			    	]);

        $response->assertStatus(302)
                 ->assertRedirect(action('\Gdevilbat\SpardaCMS\Modules\Core\Http\Controllers\Auth\LoginController@showLoginForm')); //Return Not Valid, User Not Login


        $user = \App\User::find(1);

        $module = \Gdevilbat\SpardaCMS\Modules\Role\Entities\Role::latest()->first();

        $response = $this->actingAs($user)
				        ->from(action('\Gdevilbat\SpardaCMS\Modules\Role\Http\Controllers\RoleController@index'))
				        ->post(action('\Gdevilbat\SpardaCMS\Modules\Role\Http\Controllers\RoleController@destroy'), [
				        	$module->getKeyName() => encrypt($module->getKey()),
							'_method' => 'DELETE'
				    	])
				    	->assertStatus(302)
						->assertRedirect(action('\Gdevilbat\SpardaCMS\Modules\Role\Http\Controllers\RoleController@index'))
						->assertSessionHas('global_message.status', 200);
    }
}
