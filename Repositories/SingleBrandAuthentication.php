<?php

namespace Gdevilbat\SpardaCMS\Modules\Role\Repositories;

use Illuminate\Support\Facades\Schema;;

use Gdevilbat\SpardaCMS\Modules\Role\Entities\RoleUser as RoleUser_m;
use Gdevilbat\SpardaCMS\Modules\Core\Entities\Module as Module_m;

use Auth;

/**
 * Class EloquentCoreRepository
 *
 * @package Gdevilbat\SpardaCMS\Modules\Core\Repositories\Eloquent
 */
class SingleBrandAuthentication implements \Gdevilbat\SpardaCMS\Modules\Role\Repositories\Contract\AuthenticationRepository
{
	public function getAuthenticationRule(string $value, \Gdevilbat\SpardaCMS\Modules\Core\Entities\Module $module, \Illuminate\Database\Eloquent\Collection $modules, \Illuminate\Database\Eloquent\Model $model = null, \App\User $user): bool
	{
		$role_user = RoleUser_m::with(['role.modules' => function($query) use ($module){
                                                        $query->where(Module_m::getTableName().'.'.Module_m::getPrimaryKey(), $module->getKey());
                                                    }])
                                                    ->where('user_id', $user->id)
                                                    ->first();

        if(empty($role_user))
            abort(403, "User Doesn't Have Role");

		$scope = $role_user->role->hasAccess($value, $module->slug, $modules);

	    if(!empty($model))
	    {
	    	if(!in_array($value, config('role.exclude_permission_id')))
	            return $scope || ($user->id == $model->created_by);
	    }

	    return $scope;
	}

	public function getDataByCreatedUser(\Illuminate\Database\Eloquent\Builder $builder, \Illuminate\Database\Eloquent\Model $model): \Illuminate\Database\Eloquent\Builder
    {
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