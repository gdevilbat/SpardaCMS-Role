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
class MultiBrandSingelRoleAuthentication implements \Gdevilbat\SpardaCMS\Modules\Role\Repositories\Contract\AuthenticationRepository
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


        }
        else
        {
            $role_access = $role_user->role->load(['modules' => function($query) use ($module){
                                                            $query->where(Module_m::getTableName().'.'.Module_m::getPrimaryKey(), $module->getKey());
                                                        }]);
        }

		$scope = $role_access->hasAccess($value, $module->slug, $modules);


	    if(!empty($model))
	    {
            try {
                $group_auth = $model->load(['author.group.users' => function($query) use ($user){
                                            $query->where(\Gdevilbat\SpardaCMS\Modules\Core\Entities\User::getTableName().'.id', $user->id);
                                        }]);

                $group_scope = !empty($group_auth->author->group) && $group_auth->author->group->users->count() > 0 ? true : false;

            } catch (\Illuminate\Database\Eloquent\RelationNotFoundException $e) {
                $group_scope = false;
            }

            if($role_access->slug == \Gdevilbat\SpardaCMS\Modules\Role\Entities\Role::ROLE_SUPER_ADMIN)
                $group_scope = true;

	    	if(empty(config('cms-role.exclude_permission_id.'.$module->slug)) || (!empty(config('cms-role.exclude_permission_id.'.$module->slug)) && !in_array($value, config('cms-role.exclude_permission_id.'.$module->slug))))
	            return ($scope && $group_scope) || ($user->id == $model->created_by);

            return $scope && $group_scope;
	    }

	    return $scope;
	}

	public function getDataByCreatedUser(\Illuminate\Database\Eloquent\Builder $builder, \Illuminate\Database\Eloquent\Model $model, string $authentication): \Illuminate\Database\Eloquent\Builder
    {
        if(Auth::user()->can($authentication))
        {
            if(Auth::user()->can('super-access'))
                return $builder;

            if (Schema::hasColumn($model->getTableName(), 'created_by'))
            {
                $query = $builder->where(function($query) use ($model){
                            $query->where($model->getTableName().'.created_by', Auth::id())
                                ->orWhereHas('author.group.users', function($query){
                                    $query->where(\Gdevilbat\SpardaCMS\Modules\Core\Entities\User::getTableName().'.id', Auth::id());
                                });
                        });
            }
            else
            {
                $query = $builder->whereRaw(0);
            }

            return $query;
        }

    	if (Schema::hasColumn($model->getTableName(), 'created_by'))
        {
	    	$query = $builder->where(function($query) use ($model){
                        $query->where($model->getTableName().'.created_by', Auth::id());
                    });
        }
        else
        {
	    	$query = $builder->whereRaw(0);
        }

    	return $query;
    }
}
