<?php

namespace Gdevilbat\SpardaCMS\Modules\Role\Providers;

use Illuminate\Support\ServiceProvider;

use Gdevilbat\SpardaCMS\Modules\Core\Entities\Module as Module_m;
use Gdevilbat\SpardaCMS\Modules\Role\Entities\RoleUser as RoleUser_m;

use Schema;
use Gate;
use Config;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerAllModule();

        //
    }

    public function registerAllModule()
    {
        $acl = resolve(\Gdevilbat\SpardaCMS\Modules\Role\Repositories\Contract\AuthenticationRepository::class);

        if(!Schema::hasTable('module'))
            return true;

       $modules = Module_m::get();

       foreach ($modules as $module) 
       {
            if(!empty($module->scope))
            {
                   foreach ($module->scope as $key => $value) 
                   {
                        $authentication = $value.'-'.$module->slug;
                        Gate::define($authentication, function ($user, $model = null) use ($value, $module, $modules, $acl){
                            return $acl->getAuthenticationRule($value, $module, $modules, $model, $user);
                        });
                   }
            }
       }
       
       Gate::define('super-access', function ($user){

        if(empty(Config::get('role_user.'.$user->id)))
        {
            $role_user =  RoleUser_m::with('role')->where('user_id', $user->id)->first();
            Config::set('role_user.'.$user->id, $role_user);
        }
        else
        {
            $role_user = Config::get('role_user.'.$user->id);
        }
        
            if(empty($role_user))
                abort(403, "User Doesn't Have Role");

            return $role_user->role->slug == 'super-admin';
       });
    }

    public function register()
    {
        $this->app->bind(\Gdevilbat\SpardaCMS\Modules\Role\Repositories\Contract\AuthenticationRepository::class, function($app){
            $acl =  config('cms-role.aclRepository');
            return new $acl; 
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
