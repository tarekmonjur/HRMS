<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LevelPermission extends Model
{
    protected $table = 'level_permissions';
    protected $fillable = ['level_id', 'menu_id'];

    // each menu info
	public function eachMenu(){
		return $this->belongsTo('App\Models\Menu', 'menu_id');
	}
}

