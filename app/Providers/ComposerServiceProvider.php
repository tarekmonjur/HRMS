<?php

namespace App\Providers;

use Auth;
use Illuminate\Support\ServiceProvider;

// use App\Models\Setting;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // View()->composer('*', function($view) {
            
        //     if(Auth::guard('hrms')->check()){
        //         $view->with('auth', Auth()->guard('hrms')->user());
        //     }

        //     if(Auth::guard('setup')->check()){
        //         $view->with('auth', Auth()->guard('setup')->user());
        //     }
        // 

        // View()->composer('layouts.hrms_navbar',function($view){
        //     $settings = Setting::all();
        //     foreach ($settings as $setting) {
        //         $settingData[$setting->field_name] = $setting->field_value;
        //         \Config::set('hrms.'.$setting->field_name,$setting->field_value);
        //     }
        //     $view->with('settings',$settingData);
        // });

        View()->share('chkUrl', \Request::segment(1));

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
