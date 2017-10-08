<?php

namespace App\Models\Setup;

use Illuminate\Database\Eloquent\Model;

class ModulePackageMap extends Model
{
    protected $table = "module_package_maps";
    protected $fillable = [
    	'id', 'module_id', 'package_id'
    ];

    public function singleModule()
    {
        return $this->belongsTo('App\Models\Setup\Module', 'module_id', 'id');
    }
}
