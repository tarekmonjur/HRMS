<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(SetupUserCreateSeeder::class);
        
        $this->call(HrmsEducationLevelCreateSeeder::class);
        $this->call(HrmsDegreeCreateSeeder::class);
        $this->call(HrmsInstituteCreateSeeder::class);
        $this->call(HrmsBloodGroupCreateSeeder::class);

        $this->call(HrmsLevelCreateSeeder::class);
        $this->call(HrmsDepartmentCreateSeeder::class);
        $this->call(HrmsDesignationCreateSeeder::class);
        $this->call(HrmsBasicSalaryInfoTableSeeder::class);
        
        $this->call(HrmsDivisionCreateSeeder::class);
        $this->call(HrmsDistrictCreateSeeder::class);
        $this->call(HrmsPoliceStationCreateSeeder::class);

        $this->call(HrmsEmployeeTypeCreateSeeder::class);
        $this->call(HrmsEmployeeStatusSeeder::class);

        $this->call(HrmsBranchCreateSeeder::class);
        $this->call(HrmsUnitCreateSeeder::class);

        $this->call(HrmsReligionsCreateSeeder::class);
        $this->call(HrmsCommonWorkShiftTableSeeder::class);
    }
}
