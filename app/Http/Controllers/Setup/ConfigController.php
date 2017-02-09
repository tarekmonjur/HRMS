<?php

namespace App\Http\Controllers\Setup;

// model class
use App\Models\User;
use App\Models\Setup\UserEmails;
use App\Models\Setup\Config;
use App\Models\Setup\Package;
use App\Models\Setup\User as SetupUser;
use App\Models\Setup\Payment;

// form validation class
use App\Http\Requests\ConfigRequest;

// laravel service class
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class ConfigController extends Controller
{
    /**
     * ConfigController constructor.
     */
    public function __construct(){
    	$this->middleware('auth:setup');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(){
    	
        Artisan::call('db:connect');

        $data['packages'] = Package::where('package_status',1)->get();

        return view('setup.config', $data);
    }

    /**
     * @param ConfigRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function config(ConfigRequest $request){

    	$database_name = $this->makeDatabaseName($request->company_name);

        $company_name          = $request->company_name;
        $package_id            = $request->package_name;
        $first_name            = $request->first_name;
        $last_name             = $request->last_name;
        $email                 = $request->email;
        $mobile_number         = $request->mobile_number;
        $password              = $request->password;
        $password_confirmation = $request->password_confirmation;
        $company_address       = $request->company_address;
        $package_amount        = $request->package_amount;
        $package_duration      = $request->package_days;

    	//try{
	     	DB::beginTransaction();

            $setup_user = SetupUser::create([
<<<<<<< HEAD
                'first_name'    => $request->first_name,
                'last_name'     => $request->last_name,
                'email'         => $request->email,
                'password'      => bcrypt($request->password),
                'mobile_number' => $request->mobile_number,
||||||| merged common ancestors
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'mobile_number' => $request->mobile_number,
=======
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $email,
                'password' => bcrypt($request->password),
                'mobile_number' => $mobile_number,
                'user_type' => 2,
>>>>>>> 3acab317d2698712ac38bb52e8b58b1d7273157f
            ]);

	    	$config = Config::create([
<<<<<<< HEAD
	    			'user_id'           => $setup_user->id,
	    			'company_name'      => $request->company_name,
	    			'company_address'   => $request->company_address,
	    			'database_name'     => $database_name,
                    'package_end_date'  => Carbon::now()->addDays(30),
||||||| merged common ancestors
	    			'user_id' => $setup_user->id,
	    			'company_name' => $request->company_name,
	    			'company_address' => $request->company_address,
	    			'database_name' => $database_name,
                    'package_end_date' => Carbon::now()->addDays(30),
=======
	    			'user_id' => $setup_user->id,
	    			'company_name' => $company_name,
	    			'company_address' => $company_address,
	    			'database_name' => $database_name,
                    'package_end_date' => Carbon::now()->addDays($package_duration),
>>>>>>> 3acab317d2698712ac38bb52e8b58b1d7273157f
	    		]);

	    	UserEmails::create([
	    			'config_id' => $config->id,
	    			'email' => $email,
	    		]);

            Payment::create([
                    'user_id' => $setup_user->id,
                    'config_id' => $config->id,
                    'package_id' => $package_id,
                    'payment_amount' => $package_amount,
                    'payment_duration' => $package_duration,
                ]);

			if(!DB::statement('CREATE DATABASE IF NOT EXISTS '.$database_name)){
                Artisan::call('db:connect');
                DB::rollback();
                $request->session()->flash('danger','Application setup not success!');
                return back();
            }
			
	    	Artisan::call("db:connect", ['database'=> $database_name]);
	    	Artisan::call("migrate:hrms");

	    	User::create([
<<<<<<< HEAD
	    			'employee_id'    => '0-00',
	    			'designation_id' => 1,
	    			'first_name'     => $request->first_name,
	    			'last_name'      => $request->last_name,
	    			'email'          => $request->email,
	    			'password'       => bcrypt($request->password),
	    			'mobile_number'  => $request->mobile_number,
||||||| merged common ancestors
	    			'first_name' => $request->first_name,
	    			'last_name' => $request->last_name,
	    			'email' => $request->email,
	    			'password' => bcrypt($request->password),
=======
	    			'employee_id'    => '0-00', 
                    'designation_id' => 1,   
                    'first_name'     => $first_name, 
                    'last_name'      => $last_name,    
                    'email'          => $email, 
                    'password'       => bcrypt($password),  
                    'mobile_number'  => $mobile_number,
>>>>>>> 3acab317d2698712ac38bb52e8b58b1d7273157f
	    		]);

	    	DB::commit();

	    	$request->session()->flash('success','Application successfully setup!');

	    // }catch(\Exception $e){
	    // 	Artisan::call('db:connect');
	    // 	DB::rollback();

	    // 	Artisan::call('db:connect', ['database'=> $database_name]);
	    // 	Artisan::call("migrate:hrms:rollback");
	    // 	DB::statement('DROP DATABASE IF EXISTS '.$database_name);

	    // 	$request->session()->flash('danger','Application setup not success!');
	    // }

    	return back();
    }


    /**
     * @param $database
     * @return mixed
     */
    private function makeDatabaseName($database){
    	if(stristr($database,' ')){
    		$database = str_replace(' ', '_', $database);
    		if(stristr($database,'-')){
    			$database = str_replace(' ', '_', $database);
    		}
    	}
    	return $database;
    }

    public function get_package_info(Request $request){

        $package_id = $request->package_name;

        $info = Package::find($package_id);

        if($info){
            $data['price'] = $info->package_price;
            $data['duration'] = $info->package_duration;

            return $data;
        }
        else{
            return "error";
        }
    }


}