<?php

use Illuminate\Database\Seeder;

class HrmsEmployeeStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('employee_status')->insert([
            ['status_name' => 'Active', 'status' => 1],
            ['status_name' => 'Retired', 'status' => 1],
            ['status_name' => 'Released', 'status' => 1],
            ['status_name' => 'Resigned', 'status' => 1],
            ['status_name' => 'Terminated', 'status' => 1],
            ['status_name' => 'Dismissed', 'status' => 1],
            ['status_name' => 'Contract Terminated', 'status' => 1],
            ['status_name' => 'Abscond', 'status' => 1],
            ['status_name' => 'Transfer', 'status' => 0],
            ['status_name' => 'Deactive', 'status' => 1],
        ]);
    }
}
