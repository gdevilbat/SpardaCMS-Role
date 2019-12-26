<?php

namespace Gdevilbat\SpardaCMS\Modules\Role\Repositories\Contract;

use Illuminate\Database\Eloquent\Builder;

/**
 * Interface CoreRepository
 * @package Modules\Core\Repositories
 */
interface AuthenticationRepository
{
	public function getAuthenticationRule(string $value, \Gdevilbat\SpardaCMS\Modules\Core\Entities\Module $module, \Illuminate\Database\Eloquent\Collection $modules, \Illuminate\Database\Eloquent\Model $model = null, \App\User $user): bool;

	public function getDataByCreatedUser(\Illuminate\Database\Eloquent\Builder $builder, \Illuminate\Database\Eloquent\Model $model): \Illuminate\Database\Eloquent\Builder;
}
