<?php

namespace Gdevilbat\SpardaCMS\Modules\Role\Entities;

use Illuminate\Database\Eloquent\Model;

class AccessRole extends Model
{
    protected $fillable = [];
    protected $table= 'access_roles';
    protected $casts = [
        'access_scope' => 'array',
    ]; 
}
