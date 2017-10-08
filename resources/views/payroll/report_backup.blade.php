@extends('layouts.hrms')
@section('content')

@section('style')
<style type="text/css">
    .select2-container .select2-selection--single{height:32px!important}
    .select2-container--default .select2-selection--single .select2-selection__rendered{line-height:30px!important}
    .select2-container--default .select2-selection--single .select2-selection__arrow{height:30px!important}

    .select2-container{width:100%!important;height:32px!important}
    /*.fileupload-preview img{max-width: 200px!important;}*/
</style>
@endsection

<section id="payroll" class="p5 pt10">
  <div class="row">
    <div class="col-md-10 col-md-offset-1">
      <div class="panel">
        <div class="panel-heading">
            <span class="panel-title"><i class="fa fa-money"></i></span>
            <strong>Employee Salary</strong>
        </div>

        <div class="panel-body">
          <form v-on:submit.prevent="generateSalary">

            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label">Branch :</label>
                  <select class="form-control input-sm" name="branch_id" v-model="branch_id">
                      <option value="0">...All Branch...</option>
                      @foreach($branches as $binfo)
                      <option value="{{$binfo->id}}">{{$binfo->branch_name}}</option>
                      @endforeach
                  </select>
                </div>
              </div>

              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label">Department :</label>
                  <select class="form-control input-sm" name="department_id" v-model="department_id">
                      <option value="0">...All Department...</option>
                      @foreach($departments as $dinfo)
                      <option value="{{$dinfo->id}}">{{$dinfo->department_name}}</option>
                      @endforeach
                  </select>
                </div>
              </div>

              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label">Unit :</label>
                  <select class="form-control input-sm" name="unit_id" v-model="unit_id">
                      <option :value="0">...All Unit...</option>
                      <option v-for="(unit,index) in units" :value="unit.id" v-text="unit.unit_name"></option>
                  </select>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">Employee : </label>
                    <select class="form-control select-sm input-sm" name="user_id">
                        <option :value="0">...All Employee...</option>
                        <option v-for="(user,index) in users" :value="user.id" v-text="user.fullname+' - ('+user.employee_no+' )'"></option>
                    </select>
                </div>
              </div>


              <div class="col-md-2">
                <div class="form-group" :class="{'has-error':errors.salary_month}">
                  <label class="control-label">Salary Month : <span class="text-danger">*</span></label>
                  <input type="text" name="salary_month" v-on:mouseover="myMonthPicker" class="myMonthPicker form-control input-sm" placeholder="Salary Month.." readonly="readonly">
                  <span v-if="errors.salary_month" class="help-block" v-text="errors.salary_month[0]"></span>
                </div>
              </div>
              <div class="col-md-2" style="padding-top:22px!important">
                <div class="form-group">
                  <button type="submit" class="form-control input-sm btn btn-sm btn-gradient btn-dark">Show Salaries</button>
                </div>
              </div>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="panel">
        <div class="panel-heading">
          <span class="panel-title"><i class="fa fa-money"></i></span>
          <strong>Salary Sheet </strong>
        </div>

        <div class="panel-body pn">
          <table class="table table-bordered">
            <thead class="bg-dark" style="color: #fff!important">
              <tr>
                <th>SL</th>
                <th>Employee</th>
                <th style="min-width: 120px">Attendance Info</th>
                <th>Salary Month</th>
                <th>Allowance Details</th>
                <th>Deduction Details</th>
                <th style="min-width: 250px">Salary Details</th>
                <th>Total Salary</th>
                <th>Action</th>
              </tr>
            </thead>

            <tbody>
              <tr v-for="(payroll, index) in payRolls">
                <td v-text="index+1"></td>
                <td>
                  <a :href="'/employee/view/'+payroll.employee_no" target="_blank">
                    <span v-text="payroll.full_name"></span><br>
                    <span v-text="payroll.employee_no"></span>
                  </a>
                </td>

                <td v-if="payroll.attendances !=''" >
                  Absent : <span class="text-danger" v-text="payroll.attendances.attendance_absent"></span> days<br>
                  Present : <span class="text-success" v-text="payroll.attendances.attendance_present"></span> days<br>
                  Leave : <span class="text-info" v-text="payroll.attendances.attendance_leave"></span> days<br>
                  Holiday : <span class="text-danger" v-text="payroll.attendances.attendance_holiday"></span> days<br>
                  Weekend : <span class="text-primary" v-text="payroll.attendances.attendance_weekend"></span> days<br>
                  late : <span class="text-warning" v-text="payroll.attendances.attendance_late"></span> days<br>
                </td>
                <td v-else class="text-center">-----</td>
                <td v-text="payroll.salary_month"></td>

                <td v-if="payroll.allowances !=''">
                  <table class="table table-bordered text-center" style="font-size: 10px!important;">
                    <thead>
                      <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Amount</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(allowance, index) in payroll.allowances">
                        <td v-text="allowance.name"></td>
                        <td>
                          <span v-if="allowance.amount_type =='percent'" v-text="allowance.percent+'%'"></span>
                          <span v-else v-text="allowance.amount_type"></span>
                        </td>
                        <td v-text="allowance.amount"></td>
                      </tr>
                      <tr>
                        <td colspan="2">Total :</td>
                        <td v-text="payroll.total_allowance"></td>
                      </tr>
                    </tbody>
                  </table>
                </td>
                <td v-else class="text-center">-----</td>

                <td v-if="payroll.deductions !=''">
                  <table class="table table-bordered text-center" style="font-size: 10px!important;">
                    <thead>
                      <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Amount</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(deduction, index) in payroll.deductions">
                        <td v-text="deduction.name"></td>
                        <td>
                          <span v-if="deduction.amount_type =='percent'" v-text="deduction.percent+'%'"></span>
                          <span v-else v-text="deduction.amount_type"></span>
                        </td>
                        <td v-text="deduction.amount"></td>
                      </tr>
                      <tr>
                        <td colspan="2">Total :</td>
                        <td v-text="payroll.total_deduction"></td>
                      </tr>
                    </tbody>
                  </table>
                </td>
                <td v-else class="text-center">-----</td>

                <td>
                  Basic : <span v-text="payroll.basic_salary"></span><br>
                  Salary in Cash : <span v-text="payroll.salary_in_cash"></span><br>
                  Per day Salary : <span v-text="payroll.perday_salary"></span><br>
                  Per hour Salary : <span v-text="payroll.perday_salary+' / '+payroll.work_hour"></span> = <span v-text="payroll.perhour_salary"></span><br>
                  Salary : <span v-text="payroll.perday_salary+' x '+payroll.salary_days"></span> = <span v-text="payroll.salary"></span><br>
                  Gross Salary : <span v-text="payroll.gross_salary"></span><br>
                  Net Salary : <span v-text="payroll.net_salary"></span><br>
                  Over Time Hour : <span v-text="payroll.overtime_hour"></span><br>
                  Over Time Amount : <span v-text="payroll.overtime_amount"></span><br>
                  Total : <span v-text="payroll.salary+' + '+payroll.overtime_amount+' + '+payroll.total_allowance+' - '+payroll.total_deduction"></span> = <span v-text="payroll.total_salary"></span><br>

                </td>
                <td v-text="payroll.total_salary"></td>
                <td>
                  <div class="btn-group mt5">
                    <a v-on:click="editSalary(payroll.user_id, index, '#payroll_modal')" class="btn btn-xs btn-primary"><i class="glyphicons glyphicons-pencil"></i>
                    </a>
                  </div>
                  
                  <div class="btn-group mt5">
                    <a v-on:click="comfirmSalary(payroll.user_id, index)" class="btn btn-xs btn-success"><i class="glyphicons glyphicons-ok_2"></i>
                    </a>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

</section>


@section('script')

<script type="text/javascript" src="{{asset('js/payroll_report.js')}}"></script>

@endsection

@endsection