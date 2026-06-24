<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RbacObject extends Model
{
    protected $table = 'objects';

    protected $fillable = ['name', 'slug', 'description'];

    public function permissions()
    {
        return $this->hasMany(Permission::class, 'object_id');
    }
}