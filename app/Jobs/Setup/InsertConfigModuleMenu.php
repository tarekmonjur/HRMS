<?php

namespace App\Jobs\Setup;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
//Model
use App\Models\Setup\ModulePackageMap;
use App\Models\Setup\Module;


class InsertConfigModuleMenu implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data['data'] = Module::create([
                'module_name'    => "tsss",
                'module_details' => "sdf",
                'module_status'  => 1,
            ]);
        
        // dd($all);
        
    }
}
