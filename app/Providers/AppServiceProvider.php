<?php

namespace App\Providers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('alpha_spaces', function($attribute, $value)
        {
            return preg_match('/^[\pL\s]+$/u', $value);
        });

        Validator::extend('alpha_spaces_dot', function($attribute, $value)
        {
            return preg_match('/^[\pL\s\.]+$/u', $value);
        });

        Validator::extend('percentage', function($attribute, $value)
        {
            return preg_match('/^\d{1,2}(\.\d{1,2})?$/', $value);
        });

        Validator::extend('less_then', function($attribute, $value, $parameters, $validator)
        {
            $validator->addReplacer('less_then',function($message, $attribute, $rule, $parameters)
            {
                return str_replace(':other',$parameters[0], $message);
            });

            return ($parameters[0] > $value)?true:false;
        });

        Validator::extend('greater_then', function($attribute, $value, $parameters, $validator)
        {
            $validator->addReplacer('greater_then',function($message, $attribute, $rule, $parameters)
            {
                return str_replace([':other'],$parameters, $message);
            });

            return ($parameters[0] < $value)?true:false;
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
