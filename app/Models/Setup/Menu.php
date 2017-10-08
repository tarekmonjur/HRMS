<?php

namespace App\Models\Setup;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = [
    	'id', 'menu_parent_id', 'module_id', 'menu_name', 'menu_url', 'menu_section_name', 'menu_icon_class', 'menu_status'
    ];

    public function module(){
		return $this->hasOne('App\Models\Setup\Module', 'id', 'module_id');
	}
	
	// each category might have one parent
	public function parent(){
		return $this->belongsTo('App\Models\Setup\Menu', 'menu_parent_id');
	}

	// one parent section menu hv many menus
	public function child_menu(){
		return $this->hasMany('App\Models\Setup\Menu', 'menu_parent_id', 'id');
	}
}
