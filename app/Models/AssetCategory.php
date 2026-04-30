<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetCategory extends Model
{
    protected $fillable = ['name', 'type', 'description'];

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }
}
