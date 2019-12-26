<?php

namespace Gdevilbat\SpardaCMS\Modules\Role\Providers;

use Illuminate\Support\ServiceProvider;

use Gdevilbat\SpardaCMS\Modules\Core\Entities\Module as Module_m;
use Gdevilbat\SpardaCMS\Modules\Role\Entities\RoleUser as RoleUser_m;

use Schema;
use Gate;

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
        $acl =  config('role.aclRepository');
        $acl = new $acl;

        if(!($acl instanceof \Gdevilbat\SpardaCMS\Modules\Role\Repositories\Contract\AuthenticationRepository))
            throw new \Exception("Access Control Repository Mus Instance Of \Gdevilbat\SpardaCMS\Modules\Role\Repositories\Contract\AuthenticationRepository");
            

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
            $role_user = RoleUser_m::with('role')->where('user_id', $user->id)->first();
            if(empty($role_user))
                abort(403, "User Doesn't Have Role");

            return $role_user->role->slug == 'super-admin';
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
