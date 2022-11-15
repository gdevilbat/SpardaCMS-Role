<?php

namespace Gdevilbat\SpardaCMS\Modules\Role\Repositories;

use Illuminate\Support\Facades\Schema;;

use Gdevilbat\SpardaCMS\Modules\Role\Entities\RoleUser as RoleUser_m;
use Gdevilbat\SpardaCMS\Modules\Core\Entities\Module as Module_m;

use Auth;
use Config;

/**
 * Class EloquentCoreRepository
 *
 * @package Gdevilbat\SpardaCMS\Modules\Core\Repositories\Eloquent
 */
class SingleBrandAuthentication implements \Gdevilbat\SpardaCMS\Modules\Role\Repositories\Contract\AuthenticationRepository
{
	public function getAuthenticationRule(string $value, \Gdevilbat\SpardaCMS\Modules\Core\Entities\Module $module, \Illuminate\Database\Eloquent\Collection $modules, \Illuminate\Database\Eloquent\Model $model = null, \App\Models\User $user): bool
	{
        if(empty(Config::get('role_user.'.$user->id)))
        {
            $role_user =  RoleUser_m::with('role')
                                    ->where('user_id', $user->id)
                                    ->first();
            Config::set('role_user.'.$user->id, $role_user);
        }
        else
        {
            $role_user = Config::get('role_user.'.$user->id);
        }

        if(empty($role_user))
            abort(403, "User Doesn't Have Role");


        if(empty(Config::get('role_access.'.$user->id.'.'.$module->slug)))
        {
    		$role_access = $role_user->role->load(['modules' => function($query) use ($module){
                                                            $query->where(Module_m::getTableName().'.'.Module_m::getPrimaryKey(), $module->getKey());
                                                        }]);


            //Config::set('role_access.'.$user->id.'.'.$module->slug, $role_access);
        }
        else
        {
            //$role_access = Config::get('role_access.'.$user->id.'.'.$module->slug);
            $role_access = $role_user->role->load(['modules' => function($query) use ($module){
                                                            $query->where(Module_m::getTableName().'.'.Module_m::getPrimaryKey(), $module->getKey());
                                                        }]);
        }

		$scope = $role_access->hasAccess($value, $module->slug, $modules);

	    if(!empty($model))
	    {
	    	if(empty(config('cms-role.exclude_permission_id.'.$module->slug)) || (!empty(config('cms-role.exclude_permission_id.'.$module->slug)) && !in_array($value, config('cms-role.exclude_permission_id.'.$module->slug))))
	            return $scope || ($user->id == $model->created_by);
	    }

	    return $scope;
	}

	public function getDataByCreatedUser(\Illuminate\Database\Eloquent\Builder $builder, \Illuminate\Database\Eloquent\Model $model, string $authentication): \Illuminate\Database\Eloquent\Builder
    {
        if(Auth::user()->can($authentication))
            return $builder;


    	if (Schema::hasColumn($model->getTableName(), 'created_by'))
        {
	    	$query = $builder->where($model->getTableName().'.created_by', Auth::id());
        }
        else
        {
	    	$query = $builder->whereRaw(0);
        }

    	return $query;
    }
}
