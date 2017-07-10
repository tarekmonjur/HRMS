<?php

namespace App\Http\Controllers;

use App\Services\CommonService;
use App\Services\PermissionService;
use App\Services\OrganogramService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;

class DashboardController extends Controller
{
    use CommonService, PermissionService;

	protected $auth;

    public function __construct(Auth $auth){
        $this->middleware('auth:hrms');

        $this->middleware(function($request, $next){
            $this->auth = Auth::guard('hrms')->user();
            view()->share('auth',$this->auth);
            return $next($request);
        });
    }


    public function index()
    {
        if(Session('config_id')){
            $data['sisterConcern'] = $this->getSisterConcern(Session('config_id'));
            $data['motherConcern'] = $this->getMotherConcern(Session('config_id'));
            Artisan::call("db:connect", ['database' => Session('database')]);

            Session([
                'sisterConcern' => $data['sisterConcern']->toArray(),
                'motherConcern' => $data['motherConcern']->toArray()
                ]);

        }else{
            $data['sisterConcern'] = [];
            $data['motherConcern'] = [];
        }

        $data['organogram'] = $this->getOrganogram();
        return view('dashboard')->with($data);
    }


    public function getOrganogram(){
        $organogram = new OrganogramService();
        return  $organogram->organogram();
    }


    public function notFound(){
        return view('errors.503');
    }










}
