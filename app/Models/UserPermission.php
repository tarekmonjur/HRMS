<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPermission extends Model
{
	protected $table = 'user_permissions';
    protected $fillable = ['user_id', 'menu_id'];

    // each menu info
	public function eachMenu(){
		return $this->belongsTo('App\Models\Menu', 'menu_id');
	}
}

