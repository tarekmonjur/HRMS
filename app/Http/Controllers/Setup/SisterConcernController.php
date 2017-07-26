<?php

namespace App\Http\Controllers\Setup;

// form validation class
use App\Http\Requests\AddSisterConcern;

// model class
use App\Models\User;
use App\Models\Setting;
use App\Models\Menu;
use App\Models\Module;
use App\Models\LevelPermission;
use App\Models\UserPermission;
use App\Models\Setup\UserEmails;
use App\Models\Setup\Config;
use App\Models\Setup\Package;
use App\Models\Setup\ModulePackageMap;
use App\Models\Setup\Payment;

// laravel service class
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Session;

class SisterConcernController extends Controller
{
    public function __construct(){
        $this->middleware('auth:setup');
    }

    public function add(){

    	$data['title'] = "Add Concern-HRMS";

    	return view('setup.sisterConcern.addSisterConcern', $data);
    }

    public function create(AddSisterConcern $request){

        $ConfigController = new ConfigController();
	    $database_name = $ConfigController->makeDatabaseName($request->company_name);

        $packageId = Payment::where('config_id', session('parent_id'))->first();

        $company_name          = $request->company_name;
        $company_code          = $request->company_code;
        $first_name            = $request->first_name;
        $last_name             = $request->last_name;
        $email                 = $request->email;
        $mobile_number         = $request->mobile_number;
        $password              = $request->password;
        $password_confirmation = $request->password_confirmation;
        $company_address       = $request->company_address;
        $package_id            = $packageId->package_id;

        $package_info     = Package::find($package_id);
        $package_amount   = $package_info->package_price;
        $package_duration = $package_info->package_duration;

        $formet = "+$package_duration month";
        $package_end_date = date("Y-m-d", strtotime($formet));

        $all_data = ModulePackageMap::with('singleModule','singleModule.menus')
                    ->where('package_id', $package_id)->get();

        foreach($all_data as $info){
            $modules[] = [
                            'id' => $info->singleModule->id,
                            'module_name' => $info->singleModule->module_name,
                            'module_icon_class' => $info->singleModule->module_icon_class,
                            'module_details' => $info->singleModule->module_details,
                            'module_status' => $info->singleModule->module_status,
                        ];
            foreach($info->singleModule->menus as $menuInfo){
                $menus[] = [
                            'id' => $menuInfo->id,
                            'menu_parent_id' => $menuInfo->menu_parent_id,
                            'module_id' => $menuInfo->module_id,
                            'menu_name' => $menuInfo->menu_name,
                            'menu_url'  => $menuInfo->menu_url,
                            'menu_section_name' => $menuInfo->menu_section_name,
                            'menu_icon_class' => $menuInfo->menu_icon_class,
                            'menu_status' => $menuInfo->menu_status,
                        ];

                $level_permission[] = [
                            'level_id' => 1,
                            'menu_id'  => $menuInfo->id,
                        ];

                $user_permission[] = [
                            'user_id' => 1,
                            'menu_id'  => $menuInfo->id,
                        ];
            }
        }

    	try{
	     	DB::beginTransaction();

	    	$config = Config::create([
	    			'user_id'          => session('user_id'),
                    'company_name'     => $company_name,
	    			'company_code'     => $company_code,
	    			'company_address'  => $company_address,
	    			'database_name'    => $database_name,
                    'package_end_date' => session('end_date'),
                    'parent_id'        => session('parent_id'),
	    		]);

	    	UserEmails::create([
	    			'config_id' => $config->id,
	    			'email' => $email,
	    		]);

			if(!DB::statement('CREATE DATABASE IF NOT EXISTS '.$database_name)){
                Artisan::call('db:connect');
                DB::rollback();
                $request->session()->flash('danger','Application setup not success!');
                return back();
            }
			
	    	Artisan::call("db:connect", ['database'=> $database_name]);
	    	Artisan::call("migrate:hrms");
            Artisan::call("db:seed");

	    	User::create([
                    'employee_no'    => $config->company_code.'-0000',
                    'employee_type_id' => 1,
                    'branch_id' => 1,
                    'designation_id' => 1,
                    'unit_id' => 1,
                    'first_name'     => $first_name,
                    'last_name'      => $last_name,    
                    'email'          => $email, 
                    'password'       => bcrypt($password),  
                    'mobile_number'  => $mobile_number,
                ]);

            Setting::insert([
                    ['field_name' => "company_name",'field_value' => $company_name],
                    ['field_name' => "company_code",'field_value' => empty($company_code)?"00":$company_code]
                ]);

            if(!empty($modules)){
                Module::insert($modules);
                Menu::insert($menus);
                LevelPermission::insert($level_permission);
                UserPermission::insert($user_permission);
            }

	    	DB::commit();

	    	$request->session()->flash('success','Application successfully setup!');

	    }catch(\Exception $e){
	    	Artisan::call('db:connect');
	    	DB::rollback();

	    	Artisan::call('db:connect', ['database'=> $database_name]);
	    	Artisan::call("migrate:hrms:rollback");
	    	DB::statement('DROP DATABASE IF EXISTS '.$database_name);

	    	$request->session()->flash('danger','Application setup not success!');
	    }

    	return back();
    }

}
