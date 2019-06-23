<?php

namespace Gdevilbat\SpardaCMS\Modules\Role\Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class RoleTest extends DuskTestCase
{
    use DatabaseMigrations, \Gdevilbat\SpardaCMS\Modules\Core\Tests\ManualRegisterProvider;
    
    /**
     * A basic browser test example.
     *
     * @return void
     */
    public function testCreateRole()
    {
        $user = \App\User::find(1);
        $faker = \Faker\Factory::create();

        $this->browse(function (Browser $browser) use ($user, $faker) {
            $browser->loginAs($user)
                    ->visit(action('\Gdevilbat\SpardaCMS\Modules\Role\Http\Controllers\RoleController@index'))
                    ->assertSee('Master Data of Role')
                    ->clickLink('Add New Role')
                    ->waitForText('Role Form')
                    ->AssertSee('Role Form')
                    ->type('name', $faker->word)
                    ->type('slug', $faker->word)
                    ->type('description', $faker->text)
                    ->press('Submit')
                    ->waitForText('Master Data of Role')
                    ->assertSee('Successfully Add Role!');
        });
    }

    /**
     * A basic browser test example.
     *
     * @return void
     */
    public function testEditRole()
    {
        $user = \App\User::find(1);

        $this->browse(function (Browser $browser) use ($user) {

            $browser->loginAs($user)
                    ->visit(action('\Gdevilbat\SpardaCMS\Modules\Role\Http\Controllers\RoleController@index'))
                    ->assertSee('Master Data of Role')
                    ->clickLink('Actions')
                    ->clickLink('Edit')
                    ->waitForText('Role Form')
                    ->AssertSee('Role Form')
                    ->press('Submit')
                    ->waitForText('Master Data of Role')
                    ->assertSee('Successfully Update Role!');
        });
    }

    /**
     * A basic browser test example.
     *
     * @return void
     */
    public function testDeleteRole()
    {
        $user = \App\User::find(1);

        $faker = \Faker\Factory::create();

        $this->browse(function (Browser $browser) use ($user, $faker) {
            $browser->loginAs($user)
                    ->visit(action('\Gdevilbat\SpardaCMS\Modules\Role\Http\Controllers\RoleController@index'))
                    ->assertSee('Master Data of Role')
                    ->clickLink('Add New Role')
                    ->waitForText('Role Form')
                    ->AssertSee('Role Form')
                    ->type('name', $faker->word)
                    ->type('slug', $faker->word)
                    ->type('description', $faker->text)
                    ->press('Submit')
                    ->waitForText('Master Data of Role')
                    ->assertSee('Successfully Add Role!');
        });

        $this->browse(function (Browser $browser) use ($user) {

            $browser->loginAs($user)
                    ->visit(action('\Gdevilbat\SpardaCMS\Modules\Role\Http\Controllers\RoleController@index'))
                    ->assertSee('Master Data of Role')
                    ->clickLink('Action')
                    ->clickLink('Delete')
                    ->waitForText('Delete Confirmation')
                    ->press('Delete')
                    ->waitForText('Successfully Delete Role!')
                    ->assertSee('Successfully Delete Role!');
        });
    }

    /**
     * A basic browser test example.
     *
     * @return void
     */
    public function testSetRole()
    {
        $user = \App\User::find(1);

        $this->browse(function (Browser $browser) use ($user) {

            $browser->loginAs($user)
                    ->visit(action('\Gdevilbat\SpardaCMS\Modules\Role\Http\Controllers\RoleController@index'))
                    ->assertSee('Master Data of Role')
                    ->waitForText('Actions');

            $browser->script('document.querySelectorAll("input[type=checkbox]")[0].click();');
            $browser->script('document.querySelectorAll("input[type=checkbox]")[1].click();');
            $browser->script('document.querySelectorAll("input[type=checkbox]")[2].click();');
            $browser->script('document.querySelectorAll("input[type=checkbox]")[3].click();');
            $browser->script('document.querySelectorAll("input[type=checkbox]")[4].click();');

            $browser->press('Submit')
                    ->waitForText('Successfully To Update Role Provider!')
                    ->assertSee('Successfully To Update Role Provider!');
        });

        $module = \Gdevilbat\SpardaCMS\Modules\Core\Entities\Module::where('slug', 'role')->first();

        $this->assertDatabaseHas(\Gdevilbat\SpardaCMS\Modules\Role\Entities\AccessRole::getTableName(), [
            'module_id' => $module->getKey(),
            'access_scope' => json_encode([
                'menu' => 'true',
                'create' => 'true',
                'read' => 'true',
                'update' => 'true',
                'delete' => 'true',
            ]),
        ]);
    }
}
