<?php

use App\Models\CommonWorkShift;
use Illuminate\Database\Seeder;

class HrmsCommonWorkShiftTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CommonWorkShift::create([
            'common_shift_name' => 'Common Work Shift',
            'common_shift_start_time' => '09:00:00',
            'common_shift_end_time' => '18:00:00',
            'common_late_count_time' => '09:05:00',
            'common_work_shift_status' => 1,
        ]);
    }
}
