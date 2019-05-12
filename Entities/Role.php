<?php

namespace Gdevilbat\SpardaCMS\Modules\Role\Entities;

use Illuminate\Database\Eloquent\Model;

use Str;
use App;

class Role extends Model
{
    protected $fillable = [];
    protected $table = 'role';

    /**
     * Set the user's Slug.
     *
     * @param  string  $value
     * @return void
     */
    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = Str::slug($value, '-');
    }

    public function modules()
    {
        return $this->belongsToMany('\Gdevilbat\SpardaCMS\Modules\Core\Entities\Module', 'access_roles', 'role_id', 'module_id')->withPivot(['access_scope']);
    }

    public function hasAccess( $permission, $module_slug, $modules)
    {
        if(empty($module_slug))
            return false;

        $module = $modules->where('slug', $module_slug)->first();
        if(empty($module))
          abort(404, 'Module Not Exist');

        try {
            if($this->slug == 'super-admin')
                return true;
        } catch (\Exception $e) {
            if(!App::environment('production'))
            {
                throw new \Gdevilbat\SpardaCMS\Modules\Core\Exceptions\ManualHandler($e);
            }
            else
            {
                Log::info($e->getMessage());
            }
        }

        try {
            $scope = json_decode($this->modules->first()->pivot->access_scope);
            if(!(property_exists($scope, $permission)))
            {
                return false;
            }

            return json_decode($scope->$permission) ? true : false;
        } catch (\Exception $e) {
            if(!App::environment('production'))
            {
                throw new \Gdevilbat\SpardaCMS\Modules\Core\Exceptions\ManualHandler($e);
            }
            else
            {
                Log::info($e->getMessage());
            }
        }

        return false;
    }
}
