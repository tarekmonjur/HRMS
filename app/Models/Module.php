<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
	protected $table = "modules";
    protected $fillable = [
    	'id', 'module_name', 'module_icon_class', 'module_details', 'module_status'
    ];

    public function menus(){
		return $this->hasMany('App\Models\Menu');
	}
}

