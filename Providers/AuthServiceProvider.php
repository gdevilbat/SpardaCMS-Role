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
                        Gate::define($authentication, function ($user, $model = null) use ($value, $module, $modules){

                            $scope = $user->hasAccess($value, $user, $module->slug, $modules);

                            if(!empty($model))
                            {
                                if(is_a($model, 'Gdevilbat\SpardaCMS\Modules\Core\Entities\User'))
                                {
                                  $id_user = $model->id;
                                  return $scope || ($user->id == $model->user_id);
                                }
                                else
                                {
                                  return $scope || ($user->id == $model->user_id);
                                }
                            }

                            return $scope;
                        });
                   }
            }
       }
       
       Gate::define('super-access', function ($user){
            if(empty($user->role))
                abort(403, "User Doesn't Have Role");

            return $user->role->first()->slug == 'super-admin';
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
