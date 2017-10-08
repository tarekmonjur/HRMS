<?php

use Illuminate\Database\Seeder;

class SetupModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('modules')->insert([
            ['module_name'=>'Employee Mangement', 'module_icon_class'=>'glyphicons glyphicons-group', 'module_details'=>'', 'module_status'=>'1'],

            ['module_name'=>'Leave Management', 'module_icon_class'=>'fa fa-calendar', 'module_details'=>'', 'module_status'=>'1'],

            ['module_name'=>'Time & Attendance', 'module_icon_class'=>'icon-stopwatch', 'module_details'=>'', 'module_status'=>'1'],

            ['module_name'=>'PayRoll Management', 'module_icon_class'=>'fa fa-money', 'module_details'=>'', 'module_status'=>'1'],

            ['module_name'=>'Setup', 'module_icon_class'=>'fa fa-cogs', 'module_details'=>'', 'module_status'=>'1'],
            
            ['module_name'=>'Application Settings', 'module_icon_class'=>'glyphicons glyphicons-adjust_alt', 'module_details'=>'', 'module_status'=>'1'],

            ['module_name'=>'Report', 'module_icon_class'=>'fa fa-file-pdf-o', 'module_details'=>'', 'module_status'=>'1'],
        ]);
    }
}
