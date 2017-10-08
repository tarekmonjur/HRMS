<?php 
namespace App\Services;

use App\Models\Module;
use App\Models\Menu;
use App\Models\UserPermission;

use Auth;
use Illuminate\Support\Facades\Artisan;

trait PermissionService{

	public function hrmsSideBar()
    {
        $allMenus = Menu::where('menu_status', 1)->get();
		session(['menuShare' => $allMenus]);

		$allModules = Module::with(['menus' => function($query){
            $query->where('menu_status', 1);
        }])->where('module_status', 1)->get();

		session(['moduleShare' => $allModules]);
    }

    public function userPermission($userId){

    	$userModule = UserPermission::with('eachMenu')->where('user_id', $userId)->get();

    	$moduleAry = [];
    	$menuAry = [];

        foreach($userModule as $info){
            if(!in_array($info->eachMenu->module_id, $moduleAry))
            {
                $moduleAry[] = $info->eachMenu->module_id;
            }

            $menuAry[] = $info->eachMenu->menu_url;
        }

        session(['userModuleShare' => $moduleAry]);
        session(['userMenuShare' => $menuAry]);
    }
}