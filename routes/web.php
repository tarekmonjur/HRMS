<?php

/***************** ...Setup Login Routes... ******************/
Route::group(['prefix'=>'setup','namespace'=>'Setup\Auth'], function(){
	Route::get('login', 'LoginController@showLoginForm');
	Route::post('login', 'LoginController@login');
	Route::post('logout', 'LoginController@logout');

	//Password Reset Routes...
	Route::get('password/reset', 'ForgotPasswordController@showLinkRequestForm');
	Route::post('password/reset', 'ResetPasswordController@reset');
	Route::get('password/reset/{token?}', 'ResetPasswordController@showResetForm');
	Route::post('password/email', 'ForgotPasswordController@sendResetLinkEmail');
});

/***************** ...Setup Dashboard Routes... ******************/
Route::group(['prefix'=>'setup','namespace'=>'Setup'], function(){
    Route::get('/','DashboardController@index');
    Route::get('/home','DashboardController@index');
    Route::get('/details/{id}','UserDetailsController@index');
    Route::post('/addHrmsModule','UserDetailsController@addHrmsModule');
});

/*************** ... Setup Admin Sister Concern Routes... **************/
Route::group(['prefix'=>'setup/concern','namespace'=>'Setup'], function(){
	Route::get('/add','SisterConcernController@add');
	Route::post('/create','SisterConcernController@create');
});

/************* ...Setup Admin Config Route... ******************/
Route::group(['prefix'=>'config','namespace'=>'Setup'], function(){
	Route::get('/', 'ConfigController@index');
	Route::post('/', 'ConfigController@config');
	Route::post('/get_package_info', 'ConfigController@get_package_info');
});

/******************** ...Setup Modules Routes... **************/
Route::group(['prefix' => 'modules', 'namespace' => 'Setup'],function (){
    Route::get('/index','ModuleController@index');
    Route::get('/getModule','ModuleController@getModule');
    Route::post('/add','ModuleController@create');
    Route::post('/edit','ModuleController@update');
    Route::get('/delete/{id}/{indexId}','ModuleController@delete');
});

/******************** ...Setup menus Routes... **************/
Route::group(['prefix' => 'menus', 'namespace' => 'Setup'],function (){
    Route::get('/index','MenuController@index');
    Route::get('/getMenus','MenuController@getMenus');
    Route::get('/getActiveMenus','MenuController@getActiveMenus');
    Route::get('/get-Module','MenuController@getModule');
    Route::post('/add','MenuController@create');
    Route::post('/edit','MenuController@update');
    Route::get('/delete/{id}/{indexId}','MenuController@delete');
});

/******************** ...Setup Packages Routes... **************/
Route::group(['prefix' => '/packages', 'namespace' => 'Setup'],function (){
    Route::get('/index','PackageController@index');
    Route::get('/add','PackageController@add');
    Route::post('/add','PackageController@create');
    Route::get('/edit/{id}','PackageController@edit');
    Route::post('/edit','PackageController@update');
    Route::get('/delete/{id}','PackageController@delete');
});


/******************************* ..End Setup System Route.. ****************************/


/******************************* ..Start HRMS Routes.. *********************************/

/************ ...HRMS Login Route... ****************/
Route::group(['prefix' => '/','namespace'=>'Auth'], function(){
    Route::get('login','LoginController@showLoginForm');
    Route::post('login', 'LoginController@login');
    Route::post('logout', 'LoginController@logout');

    Route::get('password/reset', 'ForgotPasswordController@showLinkRequestForm');
    Route::post('password/email', 'ForgotPasswordController@sendResetLinkEmail');
    Route::get('password/reset/{token}', 'ResetPasswordController@showResetForm');
    Route::post('password/reset', 'ResetPasswordController@reset')->name('password.reset');
});


/******************* ...HRMS Switch Account... ************************/
Route::group(['prefix' => '/switch','namespace' => 'Auth'],function(){
    Route::post('/account/{database_name}/{config_id}','SwitchAccountController@switchAccount');
});


/***************** ...HRMS Dashboard Routes... ******************/
Route::group(['prefix' => '/'], function(){
    Route::get('/','DashboardController@index');
});


/******************** ...HRMS Employee Routes... **************/
Route::group(['prefix' => '/employee', 'namespace' => 'Pim'],function (){
    Route::get('/index','EmployeeController@index');
    Route::get('/view/{employee_no?}','EmployeeController@viewEmployeeProfile');

    Route::get('/add/{id?}/{tab?}','EmployeeController@showEmployeeAddForm');
    Route::post('/add','EmployeeController@addEmployee');

    Route::get('/edit/{id}/{tab?}','EmployeeController@showEmployeeEditForm');
    Route::get('/edit/tab/{data_tab}/{data_id}','EmployeeController@getDataByTabAndId');
    Route::post('/edit/{userId}','EmployeeController@editEmployee');

    Route::get('/delete/{id}','EmployeeController@deleteEmployee');
    Route::post('/status-change','EmployeeController@statusChange');

    Route::post('/add/{userId}/personal/','EmployeeController@addPersonalInfo');
    Route::post('/edit/{userId}/personal','EmployeeController@editPersonalInfo');

    Route::post('/add/{userId}/education/','EmployeeController@addEditEducation');
    Route::post('/edit/{userId}/education','EmployeeController@addEditEducation');
    Route::delete('/delete/{id}/education','EmployeeController@deleteEmployeeData');

    Route::post('/add/{userId}/experience/','EmployeeController@addEditExperience');
    Route::post('/edit/{userId}/experience','EmployeeController@addEditExperience');
    Route::delete('/delete/{id}/experience','EmployeeController@deleteEmployeeData');

    Route::post('/add/{userId}/salary/','EmployeeController@addSalary');
    Route::post('/edit/{userId}/salary','EmployeeController@editSalary');

    Route::post('/add/{userId}/nominee/','EmployeeController@addEditNominee');
    Route::post('/edit/{userId}/nominee/','EmployeeController@addEditNominee');
    Route::delete('/delete/{id}/nominee/','EmployeeController@deleteEmployeeData');

    Route::post('/add/{userId}/training/','EmployeeController@addEditTraining');
    Route::post('/edit/{userId}/training/','EmployeeController@addEditTraining');
    Route::delete('/delete/{id}/training','EmployeeController@deleteEmployeeData');

    Route::post('/add/{userId}/reference/','EmployeeController@addEditReference');
    Route::post('/edit/{userId}/reference/','EmployeeController@addEditReference');
    Route::delete('/delete/{id}/reference/','EmployeeController@deleteEmployeeData');

    Route::post('/add/{userId}/children/','EmployeeController@addEditChildren');
    Route::post('/edit/{userId}/children/','EmployeeController@addEditChildren');
    Route::delete('/delete/{id}/children/','EmployeeController@deleteEmployeeData');

    Route::post('/add/{userId}/language/','EmployeeController@addEditLanguage');
    Route::post('/edit/{userId}/language/','EmployeeController@addEditLanguage');
    Route::delete('/delete/{id}/language/','EmployeeController@deleteEmployeeData');

    Route::get('/permission/{id}','EmployeeController@permission');
    Route::post('/updatePermission','EmployeeController@updatePermission');

    Route::get('/leave/{id}','EmployeeController@leave');
    Route::post('/updateLeave','EmployeeController@updateLeave');
});


/******************** ...HRMS Employee Level Routes... **************/
Route::group(['prefix' => '/levels', 'namespace' => 'Pim'],function (){
    Route::get('/index','LevelController@index');
    Route::get('/add','LevelController@add');
    Route::post('/add','LevelController@create');
    Route::get('/edit/{id}','LevelController@edit');
    Route::post('/edit','LevelController@update');
    Route::post('/edit/info','LevelController@update_info');
    Route::get('/delete/{id}','LevelController@delete');
    Route::get('/permission/{id}','LevelController@permission');
    Route::post('/updatePermission','LevelController@updatePermission');
});


/******************** ...HRMS Employee Department Routes... **************/
Route::group(['prefix' => '/department', 'namespace' => 'Pim'],function (){
    Route::get('/index','DepartmentController@index');
    Route::post('/add','DepartmentController@create');
    Route::get('/edit/{id}','DepartmentController@edit');
    Route::post('/edit','DepartmentController@update');
    Route::get('/delete/{id}','DepartmentController@delete');
});


/******************** ...HRMS Employee Designations Routes... **************/
Route::group(['prefix' => '/designation', 'namespace' => 'Pim'],function (){
    Route::get('/index','DesignationController@index');
    Route::post('/add','DesignationController@create');
    Route::get('/edit/{id}','DesignationController@edit');
    Route::post('/edit','DesignationController@update');
    Route::get('/delete/{id}','DesignationController@delete');
});


/******************** ...HRMS Employee SalaryInfo Routes... **************/
Route::group(['prefix' => 'salaryInfo', 'namespace' => 'Pim'],function (){
    Route::get('/index','SalaryInfoController@index');
    Route::get('/getAllInfo','SalaryInfoController@getAllInfo');
    Route::post('/add','SalaryInfoController@create');
    Route::get('/edit/{id}','SalaryInfoController@edit');
    Route::post('/edit','SalaryInfoController@update');
    Route::get('/delete/{id}/{indexId}','SalaryInfoController@delete');
});


/******************** ...HRMS Employee Units Routes... **************/
Route::group(['prefix' => 'unit', 'namespace' => 'Pim'],function (){
    Route::get('/index','UnitController@index');
    Route::get('/getUnits','UnitController@getUnits');
    Route::post('/add','UnitController@create');
    Route::post('/edit','UnitController@update');
    Route::get('/delete/{id}/{indexId}','UnitController@delete');
});


/******************** ...HRMS Employee Settings Routes... **************/
Route::group(['prefix' => 'settings', 'namespace' => 'Setting'],function (){
    Route::get('/index','SettingsController@index');
    Route::get('/getSettings','SettingsController@getSettings');
    Route::post('/add','SettingsController@create');
    Route::post('/edit','SettingsController@update');
});


/******************** ...HRMS Employee Branch Routes... **************/
Route::group(['prefix' => 'branch', 'namespace' => 'Pim'],function (){
    Route::get('/index','BranchController@index');
    Route::get('/getBranch','BranchController@getBranch');
    Route::post('/add','BranchController@create');
    Route::post('/edit','BranchController@update');
    Route::get('/delete/{id}/{indexId}','BranchController@delete');
});


/******************** ...HRMS Employee Bank Routes... **************/
Route::group(['prefix' => 'bank', 'namespace' => 'Pim'],function (){
    Route::get('/index','BankController@index');
    Route::post('/add','BankController@create');
    Route::get('/edit/{id}','BankController@edit');
    Route::post('/edit','BankController@update');
    Route::delete('/delete/{id}','BankController@delete');
});


/******************** ...HRMS Employee promotion/transfer Routes... **************/
Route::group(['prefix' => '/promotion', 'namespace' => 'Pim'],function (){
    Route::get('/index','PromotionController@index');
    Route::get('/getPromotionsData','PromotionController@getPromotionsData');
    Route::get('/getSingelUser/{id}','PromotionController@getSingelUser');
    Route::post('/add','PromotionController@create');
    // Route::get('/edit/{id}','PromotionController@edit');
    Route::post('/edit','PromotionController@update');
    // Route::post('/edit/info','PromotionController@update_info');
    // Route::get('/delete/{id}','PromotionController@delete');
});


/******************** .......HRMS Time & Attendance Routes........ **************/

/******* .......Work Shift Routes........ ******/
Route::group(['prefix' => 'workshift', 'namespace' => 'Attendance'],function (){
    Route::get('/index','WorkShiftController@index');
    Route::post('/add','WorkShiftController@create');
    Route::get('/edit/{id}','WorkShiftController@edit');
    Route::post('/edit','WorkShiftController@update');
    Route::delete('/delete/{id}','WorkShiftController@delete');
});

/******* .......Shift Assign Routes........ ******/
Route::group(['prefix' => 'shiftassign', 'namespace' => 'Attendance'],function (){
    Route::get('/index/{work_shift_id?}','WorkShiftAssignController@index');
    Route::post('/assign','WorkShiftAssignController@assignWorkShift');
    Route::get('/delete/{id}','WorkShiftAssignController@delete');
});

/******* .......Attendance Timesheet Routes........ ******/
Route::group(['prefix' => 'attendance', 'namespace' => 'Attendance'],function (){
    Route::get('/index','AttendanceController@index');
    Route::post('/index','AttendanceController@attendanceTimesheet');

    //MY Attendance URL
    Route::get('/view/{employee_no?}','AttendanceController@viewAttendance');
    
    Route::post('/add','AttendanceController@addAttendance');
    Route::post('/manual','AttendanceController@manualAttendance');
});

/******************** .......Start HRMS PayRoll Module Routes........ **************/

/******* .......PayRoll Bonus Type Routes........ ******/
Route::group(['prefix' => 'bonustype', 'namespace' => 'Payroll'],function (){
    Route::get('/index','BonusTypeController@index');
    Route::post('/add','BonusTypeController@create');
    Route::get('/edit/{id}','BonusTypeController@edit');
    Route::post('/edit','BonusTypeController@update');
    Route::delete('/delete/{id}','BonusTypeController@delete');
});

/******* .......PayRoll Bonus Routes........ ******/
Route::group(['prefix' => 'bonus', 'namespace' => 'Payroll'],function (){
    Route::get('/index','BonusController@index');
    Route::post('/add','BonusController@create');
    Route::get('/edit/{id}','BonusController@edit');
    Route::post('/edit','BonusController@update');
    Route::delete('/delete/{id}','BonusController@delete');
});

/******* .......PayRoll Increment Type Routes........ ******/
Route::group(['prefix' => 'incrementtype', 'namespace' => 'Payroll'],function (){
    Route::get('/index','IncrementTypeController@index');
    Route::post('/add','IncrementTypeController@create');
    Route::get('/edit/{id}','IncrementTypeController@edit');
    Route::post('/edit','IncrementTypeController@update');
    Route::delete('/delete/{id}','IncrementTypeController@delete');
});

/******* .......PayRoll Increment Routes........ ******/
Route::group(['prefix' => 'increment', 'namespace' => 'Payroll'],function (){
    Route::get('/index','IncrementController@index');
    Route::post('/add','IncrementController@create');
    Route::get('/edit/{id}','IncrementController@edit');
    Route::post('/edit','IncrementController@update');
    Route::delete('/delete/{id}','IncrementController@delete');
});

/******* .......PayRoll Provident Fund Routes........ ******/
Route::group(['prefix' => 'providentfund', 'namespace' => 'Payroll'],function (){
    Route::get('/index/{id?}','ProvidentFundController@index');
    Route::post('/add','ProvidentFundController@create');
    Route::get('/edit/{id}','ProvidentFundController@edit');
    Route::post('/edit','ProvidentFundController@update');
    Route::delete('/delete/{id}','ProvidentFundController@delete');
});

/******* .......PayRoll Loan Type Routes........ ******/
Route::group(['prefix' => 'loantype', 'namespace' => 'Payroll'],function (){
    Route::get('/index','LoanTypeController@index');
    Route::post('/add','LoanTypeController@create');
    Route::get('/edit/{id}','LoanTypeController@edit');
    Route::post('/edit','LoanTypeController@update');
    Route::delete('/delete/{id}','LoanTypeController@delete');
});

/******* .......PayRoll Loan Routes........ ******/
Route::group(['prefix' => 'loan', 'namespace' => 'Payroll'],function (){
    Route::get('/index','LoanController@index');
    Route::post('/add','LoanController@create');
    Route::get('/edit/{id}','LoanController@edit');
    Route::post('/edit','LoanController@update');
    Route::delete('/delete/{id}','LoanController@delete');
});

/******* .......PayRoll Routes........ ******/
Route::group(['prefix' => 'payroll', 'namespace' => 'Payroll'],function (){
    Route::get('/index/{id1?}/{id2?}/{id3?}','PayrollController@index');
    Route::post('/index','PayrollController@index');
    Route::post('/add','PayrollController@addSalary');
    Route::get('/salaries','ReportController@index');
    Route::post('/salaries','ReportController@salaries');
});

/******************** .......End HRMS PayRoll Module Routes........ **************/


/******************* ........HRMS Leave Management System........... ************/
Route::group(['prefix' => '/weekend', 'namespace' => 'Leave'],function (){
    Route::get('/index','WeekendController@index');
    Route::get('/getAllData','WeekendController@getAllData');
    Route::post('/add','WeekendController@create');
    // Route::get('/edit/{id}','WeekendController@edit');
    // Route::post('/edit','WeekendController@update');
    Route::get('/delete/{id}','WeekendController@delete');
});

/*********...HRMS Leave Management - Holiday...***********/
Route::group(['prefix' => '/holiday', 'namespace' => 'Leave'],function (){
    Route::get('/index','HolidayController@index');
    Route::get('/getAllData','HolidayController@getAllData');
    Route::post('/add','HolidayController@create');
    Route::get('/edit/{id}','HolidayController@edit');
    Route::post('/edit','HolidayController@update');
});

/*********...HRMS Leave Management - Leave Type...***********/
Route::group(['prefix' => '/leaveType', 'namespace' => 'Leave'],function (){
    Route::get('/index','LeaveTypeController@index');
    // Route::get('/get-types','LeaveTypeController@getTypes');
    Route::get('/getAllData','LeaveTypeController@getAllData');
    Route::post('/add','LeaveTypeController@create');
    Route::get('/edit/{id}','LeaveTypeController@edit');
    Route::post('/edit','LeaveTypeController@update');
    Route::get('/delete/{id}','LeaveTypeController@delete');

    // Route::get('/calculateEarnLeave','LeaveTypeController@calculateEarnLeave');
});

/*********...HRMS Leave Management - Leave...***********/
Route::group(['prefix' => '/leave', 'namespace' => 'Leave'],function (){
    Route::get('/index','LeaveController@index');
    Route::get('/get-types','LeaveController@getTypes');
    // Route::get('/user-taken-leave/{id}','LeaveController@userTakenLeave');
    Route::get('/getWeekendHolidays/{fromDate}/{toDate}/{id}','LeaveController@getWeekendHolidays');
    Route::post('/add','LeaveController@create');
    Route::get('/details/{id?}','LeaveController@details');
    Route::get('/edit/{id}','LeaveController@edit');
    Route::post('/edit','LeaveController@update');
    Route::get('/changeStatus/{id}/{stat}','LeaveController@changeStatus');
    Route::get('/chResponsibleStatus/{id}/{stat}/{loginEmp}','LeaveController@chResponsibleStatus');
    // Route::get('/view/{id}', 'LeaveController@showIndiReport');
});

Route::group(['prefix' => '/myLeave', 'namespace' => 'Leave'],function (){
    Route::get('/details/{id?}','LeaveController@details');
    Route::get('/edit/{id}','LeaveController@edit');
    Route::post('/add','LeaveController@create');
});


/******************** ...HRMS Common Function Routes... **************/
Route::group(['prefix' => '/'], function(){
    Route::get('get-employee-type','CommonController@getEmployeeType');
    Route::get('get-employee','CommonController@getEmployee');
    Route::get('get-departments','CommonController@getDepartments');
    Route::get('get-levels','CommonController@getLevels');
    Route::get('get-branches','CommonController@getBranches');
    Route::get('get-units','CommonController@getUnits');
    Route::get('get-unit-by-department-id/{id}','CommonController@getUnitByDepartmentId');
    Route::get('get-unit-by-designation-id/{id}','CommonController@getUnitByDesignationId');
    Route::get('get-designations','CommonController@getDesignations');
    Route::get('get-supervisor-by-designation-id/{id}/{user_id?}','CommonController@getSupervisorByDesignationId');
    Route::get('get-divisions','CommonController@getDivisions');
    Route::get('get-district-by-division/{id}','CommonController@getDistrictByDivision');
    Route::get('get-police-station-by-district/{id}','CommonController@getPolicStationByDistrict');
    Route::get('get-blood-groups','CommonController@getBloodGroups');
    Route::get('get-education-levels','CommonController@getEducationLevels');
    Route::get('get-institute-by-education-level/{id}','CommonController@getInstituteByEducationLevel');
    Route::get('get-degree-by-education-level/{id}','CommonController@getDegreeByEducationLevel');
    Route::get('get-banks','CommonController@getBanks');
    Route::get('get-level-salary-info/{id}','CommonController@getLevelSalaryInfoByUser');
    Route::get('get-allowances','CommonController@getAllowances');
    // Route::get('get-allowance-by-ids/{ids}','CommonController@getAllowanceByIds');
    // Route::get('get-allowance-notin-level/{ids?}','CommonController@getAllowanceNotinLevel');
    Route::get('get-language','CommonController@getLanguage');
    Route::get('get-religions','CommonController@getReligions');

    Route::post('add-designation','CommonController@addDesignation');
    Route::post('add-language','CommonController@addLanguage');

    Route::get('get-workshifts/{id?}/{status?}','CommonController@getWorkShift');
    Route::get('get-bonus-type','CommonController@getBonusType');
    Route::get('get-employee-by-deisgnation-id/{id}','CommonController@getEmployeeByDesignationId');
    Route::get('get-increment-type','CommonController@getIncrementType');
    Route::get('get-loan-type','CommonController@getLoanType');
    Route::get('get-employees','CommonController@getEmployees');
    // Route::get('get-providentfund-by-user-id/{id}','CommonController@getProvidentFundByUserId');

    Route::get('/user-taken-leave/{id}','CommonController@userTakenLeave');
    Route::get('/leaveView/{id}', 'CommonController@showIndiReport');

    Route::get('page-not-available', function(){
        return view('errors.503');
    });
});


/********** ..End HRMS Routes.. *****************/
