<?php

use Illuminate\Database\Seeder;

class HrmsDegreeCreateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		DB::table('degrees')->insert([
			['education_level_id'=>'2','degree_name'=>'A.A. (Associate of Arts)', 'status'=>'1'],
			['education_level_id'=>'2','degree_name'=>'A.S. (Associate of Science)', 'status'=>'1'],
			['education_level_id'=>'2','degree_name'=>'A.A.S. (Associate of Applied Science)', 'status'=>'1'],
			['education_level_id'=>'2','degree_name'=>'A.E. (Associate of Engineering)', 'status'=>'1'],
			['education_level_id'=>'2','degree_name'=>'A.A.A. (Associate of Applied Arts)', 'status'=>'1'],
			['education_level_id'=>'2','degree_name'=>'A.P.S. (Associate of Political Science)', 'status'=>'1'],
			['education_level_id'=>'2','degree_name'=>'B.A. (Bachelor of Arts)', 'status'=>'1'],
			['education_level_id'=>'2','degree_name'=>'B.Sc. (Bachelor of Science)', 'status'=>'1'],
			['education_level_id'=>'2','degree_name'=>'B.F.A. (Bachelor of Fine Arts)', 'status'=>'1'],
			['education_level_id'=>'2','degree_name'=>'B.B.A. (Bachelor of Business Administration)', 'status'=>'1'],
			['education_level_id'=>'2','degree_name'=>'B.Arch. (Bachelor of Architecture)', 'status'=>'1'],
			['education_level_id'=>'2','degree_name'=>'M.A. (Master of Arts) ', 'status'=>'1'],
			['education_level_id'=>'2','degree_name'=>'M.S. (Master of Science)', 'status'=>'1'],
			['education_level_id'=>'2','degree_name'=>'M.Res. (Master of Research)', 'status'=>'1'],
			['education_level_id'=>'2','degree_name'=>'M.Phil. (Master of Philosophy)', 'status'=>'1'],
			['education_level_id'=>'2','degree_name'=>'LL.M. (Master of Laws)', 'status'=>'1'],
			['education_level_id'=>'2','degree_name'=>'M.B.A. (Master of Business Administration)', 'status'=>'1'],
			['education_level_id'=>'2','degree_name'=>'PhD (Doctor of Philosophy)', 'status'=>'1'],
			['education_level_id'=>'2','degree_name'=>'M.D. (Doctor of Medicine)', 'status'=>'1'],
			['education_level_id'=>'2','degree_name'=>'Ed.D. (Doctor of Education)', 'status'=>'1'],
			['education_level_id'=>'2','degree_name'=>'J.D. (Juris Doctor)', 'status'=>'1'],
			['education_level_id'=>'1','degree_name'=>'S.S.C (Secondary School Certificate)', 'status'=>'1'],
			['education_level_id'=>'1','degree_name'=>'H.S.C (Higher Secondary Certificate)', 'status'=>'1'],
			['education_level_id'=>'2','degree_name'=>'B.Sc. Engr.(Bachelor of Science in Engineering)', 'status'=>'1'],
			['education_level_id'=>'2','degree_name'=>'M.B.S (Master of Business and Science)', 'status'=>'1'],
			['education_level_id'=>'2','degree_name'=>'B.B.S (Bachelor of Business Studies)', 'status'=>'1'],
		]);
    }
}
