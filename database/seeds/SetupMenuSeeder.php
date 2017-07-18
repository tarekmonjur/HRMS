<?php

use Illuminate\Database\Seeder;

class SetupMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
            //===== menus.sql ===============
        //======= ******** start ****** =====
//       INSERT INTO `menus` (`id`, `menu_parent_id`, `module_id`, `menu_name`, `menu_url`, `menu_section_name`, `menu_icon_class`, `menu_status`, `created_at`, `updated_at`) VALUES
// (1, 0, 1, 'View', 'promotion/index', 'Transfer / Promotion', 'fa fa-level-up', 1, NULL, NULL),
// (2, 0, 1, 'View', 'employee/index', 'Employee', 'fa fa-level-up', 1, NULL, NULL),
// (3, 1, 1, 'Add', 'promotion/add', 'Transfer / Promotion', 'fa fa-level-up', 1, NULL, NULL),
// (4, 1, 1, 'Edit', 'promotion/edit', 'Transfer / Promotion', 'fa fa-level-up', 1, NULL, NULL),
// (5, 1, 1, 'Delete', 'promotion/delete', 'Transfer / Promotion', 'fa fa-level-up', 1, NULL, NULL),
// (6, 2, 1, 'Add', 'employee/add', 'Employee', 'fa fa-level-up', 1, NULL, NULL),
// (7, 2, 1, 'Edit', 'employee/edit', 'Employee', 'fa fa-level-up', 1, NULL, NULL),
// (8, 2, 1, 'Delete', 'employee/delete', 'Employee', 'fa fa-level-up', 1, NULL, NULL),

// (9, 0, 2, 'View', 'holiday/index', 'Holiday', 'fa fa-level-up', 1, NULL, NULL),
// (10, 9, 2, 'Add', 'holiday/add', 'Holiday', 'fa fa-level-up', 1, NULL, NULL),
// (11, 9, 2, 'Edit', 'holiday/edit', 'Holiday', 'fa fa-level-up', 1, NULL, NULL),
// (12, 0, 2, 'View', 'weekend/index', 'Weekend', 'fa fa-level-up', 1, NULL, NULL),
// (13, 12, 2, 'Add', 'weekend/add', 'Weekend', 'fa fa-level-up', 1, NULL, NULL),
// (14, 12, 2, 'Delete', 'weekend/delete', 'Weekend', 'fa fa-level-up', 1, NULL, NULL),
// (15, 0, 2, 'View', 'leaveType/index', 'Leave Type', 'fa fa-level-up', 1, NULL, NULL),
// (16, 15, 2, 'Add', 'leaveType/add', 'Leave Type', 'fa fa-level-up', 1, NULL, NULL),
// (17, 15, 2, 'Edit', 'leaveType/edit', 'Leave Type', 'fa fa-level-up', 1, NULL, NULL),
// (18, 0, 2, 'View', 'leave/index', 'Leave', 'fa fa-level-up', 1, NULL, NULL),
// (19, 18, 2, 'Add', 'leave/add', 'Leave', 'fa fa-level-up', 1, NULL, NULL),
// (20, 18, 2, 'Edit', 'leave/edit', 'Leave', 'fa fa-level-up', 1, NULL, NULL),
// (21, 0, 2, 'View', 'myLeave/details', 'Personal Leave', 'fa fa-level-up', 1, NULL, NULL),
// (22, 21, 2, 'Add', 'myLeave/add', 'Personal Leave', 'fa fa-level-up', 1, NULL, NULL),
// (23, 21, 2, 'Edit', 'myLeave/edit', 'Personal Leave', 'fa fa-level-up', 1, NULL, NULL),

// (24, 0, 3, 'Index', 'workshift/index', 'Work Shift', 'fa fa-level-up', 1, NULL, NULL),
// (25, 24, 3, 'Add', 'workshift/add', 'Work Shift', 'fa fa-level-up', 1, NULL, NULL),
// (26, 24, 3, 'Edit', 'workshift/edit', 'Work Shift', 'fa fa-level-up', 1, NULL, NULL),
// (27, 24, 3, 'Delete', 'workshift/delete', 'Work Shift', 'fa fa-level-up', 1, NULL, NULL),
// (28, 0, 3, 'Index', 'shiftassign/index', 'Work Shift Assign', 'fa fa-level-up', 1, NULL, NULL),
// (29, 28, 3, 'Assign', 'shiftassign/assign', 'Work Shift Assign', 'fa fa-level-up', 1, NULL, NULL),
// (30, 0, 3, 'Index', 'attendance/index', 'Attendance', 'fa fa-level-up', 1, NULL, NULL),
// (31, 0, 3, 'View', 'attendance/view', 'My Attendance', 'fa fa-level-up', 1, NULL, NULL),

// (32, 0, 4, 'View', 'bonus/index', 'Bonus', 'fa fa-level-up', 1, NULL, NULL),
// (33, 32, 4, 'Add', 'bonus/add', 'Bonus', 'fa fa-level-up', 1, NULL, NULL),
// (34, 32, 4, 'Edit', 'bonus/edit', 'Bonus', 'fa fa-level-up', 1, NULL, NULL),
// (35, 32, 4, 'Delete', 'bonus/delete', 'Bonus', 'fa fa-level-up', 1, NULL, NULL),
// (36, 0, 4, 'View', 'increment/index', 'Increment', 'fa fa-level-up', 1, NULL, NULL),
// (37, 36, 4, 'Add', 'increment/add', 'Increment', 'fa fa-level-up', 1, NULL, NULL),
// (38, 36, 4, 'Edit', 'increment/edit', 'Increment', 'fa fa-level-up', 1, NULL, NULL),
// (39, 36, 4, 'Delete', 'increment/delete', 'Increment', 'fa fa-level-up', 1, NULL, NULL),
// (40, 0, 4, 'View', 'loan/index', 'Loan', 'fa fa-level-up', 1, NULL, NULL),
// (41, 40, 4, 'Add', 'loan/add', 'Loan', 'fa fa-level-up', 1, NULL, NULL),
// (42, 40, 4, 'Edit', 'loan/edit', 'Loan', 'fa fa-level-up', 1, NULL, NULL),
// (43, 40, 4, 'Delete', 'loan/delete', 'Loan', 'fa fa-level-up', 1, NULL, NULL),
// (44, 0, 4, 'View', 'taxcalculation/index', 'Tax Calculation', 'fa fa-level-up', 1, NULL, NULL),
// (45, 44, 4, 'Add', 'taxcalculation/add', 'Tax Calculation', 'fa fa-level-up', 1, NULL, NULL),
// (46, 44, 4, 'Edit', 'taxcalculation/edit', 'Tax Calculation', 'fa fa-level-up', 1, NULL, NULL),
// (47, 44, 4, 'Delete', 'taxcalculation/delete', 'Tax Calculation', 'fa fa-level-up', 1, NULL, NULL),
// (48, 0, 4, 'View', 'providentfund/index', 'PF Calculation', 'fa fa-level-up', 1, NULL, NULL),
// (49, 48, 4, 'Add', 'providentfund/add', 'PF Calculation', 'fa fa-level-up', 1, NULL, NULL),
// (50, 48, 4, 'Edit', 'providentfund/edit', 'PF Calculation', 'fa fa-level-up', 1, NULL, NULL),
// (51, 48, 4, 'Delete', 'providentfund/delete', 'PF Calculation', 'fa fa-level-up', 1, NULL, NULL),
// (52, 0, 4, 'View', 'claim/index', 'Claim', 'fa fa-level-up', 1, NULL, NULL),
// (53, 52, 4, 'Add', 'claim/add', 'Claim', 'fa fa-level-up', 1, NULL, NULL),
// (54, 52, 4, 'Edit', 'claim/edit', 'Claim', 'fa fa-level-up', 1, NULL, NULL),
// (55, 52, 4, 'Delete', 'claim/delete', 'Claim', 'fa fa-level-up', 1, NULL, NULL),
// (56, 0, 4, 'View', 'payroll/index', 'Process Payroll', 'fa fa-level-up', 1, NULL, NULL),
// (57, 56, 4, 'Add', 'payroll/add', 'Process Payroll', 'fa fa-level-up', 1, NULL, NULL),
// (58, 56, 4, 'Edit', 'payroll/edit', 'Process Payroll', 'fa fa-level-up', 1, NULL, NULL),
// (59, 56, 4, 'Delete', 'payroll/delete', 'Process Payroll', 'fa fa-level-up', 1, NULL, NULL),
// (60, 0, 4, 'View', 'salaryInfo/index', 'Salary Info', 'glyphicon glyphicon-usd', 1, NULL, NULL),
// (61, 60, 4, 'Add', 'salaryInfo/add', 'Salary Info', 'glyphicon glyphicon-usd', 1, NULL, NULL),
// (62, 60, 4, 'Edit', 'salaryInfo/edit', 'Salary Info', 'glyphicon glyphicon-usd', 1, NULL, NULL),
// (63, 60, 4, 'Delete', 'salaryInfo/delete', 'Salary Info', 'glyphicon glyphicon-usd', 1, NULL, NULL),

// (64, 0, 5, 'View', 'department/index', 'Department', 'fa fa-level-up', 1, NULL, NULL),
// (65, 0, 5, 'View', 'unit/index', 'Unit', 'fa fa-level-up', 1, NULL, NULL),
// (66, 0, 5, 'View', 'levels/index', 'Levels', 'fa fa-level-up', 1, NULL, NULL),
// (67, 0, 5, 'View', 'designation/index', 'Designation', 'fa fa-level-up', 1, NULL, NULL),
// (68, 0, 5, 'View', 'branch/index', 'Branch', 'fa fa-level-up', 1, NULL, NULL),
// (69, 0, 5, 'View', 'bank/index', 'Bank', 'fa fa-money', 1, NULL, NULL),
// (70, 64, 5, 'Add', 'department/add', 'Department', 'fa fa-level-up', 1, NULL, NULL),
// (71, 64, 5, 'Edit', 'department/edit', 'Department', 'fa fa-level-up', 1, NULL, NULL),
// (72, 64, 5, 'Delete', 'department/delete', 'Department', 'fa fa-level-up', 1, NULL, NULL),
// (73, 65, 5, 'Add', 'unit/add', 'Unit', 'fa fa-level-up', 1, NULL, NULL),
// (74, 65, 5, 'Edit', 'unit/edit', 'Unit', 'fa fa-level-up', 1, NULL, NULL),
// (75, 65, 5, 'Delete', 'unit/delete', 'Unit', 'fa fa-level-up', 1, NULL, NULL),
// (76, 66, 5, 'Add', 'levels/add', 'Levels', 'fa fa-level-up', 1, NULL, NULL),
// (77, 66, 5, 'Edit', 'levels/edit', 'Levels', 'fa fa-level-up', 1, NULL, NULL),
// (78, 66, 5, 'Delete', 'levels/delete', 'Levels', 'fa fa-level-up', 1, NULL, NULL),
// (79, 67, 5, 'Add', 'designation/add', 'Designation', 'fa fa-level-up', 1, NULL, NULL),
// (80, 67, 5, 'Edit', 'designation/edit', 'Designation', 'fa fa-level-up', 1, NULL, NULL),
// (81, 67, 5, 'Delete', 'designation/delete', 'Designation', 'fa fa-level-up', 1, NULL, NULL),
// (82, 68, 5, 'Add', 'branch/add', 'Branch', 'fa fa-level-up', 1, NULL, NULL),
// (83, 68, 5, 'Edit', 'branch/edit', 'Branch', 'fa fa-level-up', 1, NULL, NULL),
// (84, 68, 5, 'Delete', 'branch/delete', 'Branch', 'fa fa-level-up', 1, NULL, NULL),
// (85, 69, 5, 'Add', 'bank/add', 'Bank', 'fa fa-level-up', 1, NULL, NULL),
// (86, 69, 5, 'Edit', 'bank/edit', 'Bank', 'fa fa-level-up', 1, NULL, NULL),
// (87, 69, 5, 'Delete', 'bank/delete', 'Bank', 'fa fa-level-up', 1, NULL, NULL),
// (88, 0, 5, 'View', 'bonustype/index', 'Bonus Type', 'fa fa-level-up', 1, NULL, NULL),
// (89, 88, 5, 'Add', 'bonustype/add', 'Bonus Type', 'fa fa-level-up', 1, NULL, NULL),
// (90, 88, 5, 'Edit', 'bonustype/edit', 'Bonus Type', 'fa fa-level-up', 1, NULL, NULL),
// (91, 88, 5, 'Delete', 'bonustype/delete', 'Bonus Type', 'fa fa-level-up', 1, NULL, NULL),
// (92, 0, 5, 'View', 'incrementtype/index', 'Increment Type', 'fa fa-level-up', 1, NULL, NULL),
// (93, 92, 5, 'Add', 'incrementtype/add', 'Increment Type', 'fa fa-level-up', 1, NULL, NULL),
// (94, 92, 5, 'Edit', 'incrementtype/edit', 'Increment Type', 'fa fa-level-up', 1, NULL, NULL),
// (95, 92, 5, 'Delete', 'incrementtype/delete', 'Increment Type', 'fa fa-level-up', 1, NULL, NULL),
// (96, 0, 5, 'View', 'loantype/index', 'Lone Type', 'fa fa-level-up', 1, NULL, NULL),
// (97, 96, 5, 'Add', 'loantype/add', 'Lone Type', 'fa fa-level-up', 1, NULL, NULL),
// (98, 96, 5, 'Edit', 'loantype/edit', 'Lone Type', 'fa fa-level-up', 1, NULL, NULL),
// (99, 96, 5, 'Delete', 'loantype/delete', 'Lone Type', 'fa fa-level-up', 1, NULL, NULL),
// (100, 0, 5, 'View', 'claimtype/index', 'Claim Type', 'fa fa-level-up', 1, NULL, NULL),
// (101, 100, 5, 'Add', 'claimtype/add', 'Claim Type', 'fa fa-level-up', 1, NULL, NULL),
// (102, 100, 5, 'Edit', 'claimtype/edit', 'Claim Type', 'fa fa-level-up', 1, NULL, NULL),
// (103, 100, 5, 'Delete', 'claimtype/delete', 'Claim Type', 'fa fa-level-up', 1, NULL, NULL),

// (104, 0, 6, 'View', 'settings/index', 'Basic Settings', 'glyphicons glyphicons-settings', 1, NULL, NULL),
// (105, 104, 6, 'Add', 'settings/add', 'Basic Settings', 'glyphicons glyphicons-settings', 1, NULL, NULL),
// (106, 104, 6, 'Edit', 'settings/edit', 'Basic Settings', 'glyphicons glyphicons-settings', 1, NULL, NULL),
// (107, 104, 6, 'Delete', 'settings/delete', 'Basic Settings', 'glyphicons glyphicons-settings', 1, NULL, NULL);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

        //======end========
    }
}
