<?php

namespace Gdevilbat\SpardaCMS\Modules\Role\Entities;

use Illuminate\Database\Eloquent\Model;

use Str;

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
}
