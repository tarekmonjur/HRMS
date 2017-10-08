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
          <table class="table table-bordered" id="datatableCall">
            <thead class="bg-dark" style="color: #fff!important">
              <tr>
                <th>SL</th>
                <th>Employee</th>
                <th>Salary Month</th>
                <th>Pay Type</th>
                <th>Pay Days</th>
                <th>Allowance</th>
                <th>Deduction</th>
                <th>Over Time</th>
                <th>Salary</th>
                <th>Gross Salary</th>
                <th>Net Salary</th>
                <th>Total Salary</th>
                <th>Remarks</th>
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
                <td v-text="payroll.salary_month"></td>
                <td v-text="payroll.salary_pay_type"></td>
                <td>
                  <span class="text-success" v-text="payroll.salary_days"></span> days<br>
                </td>
                <td v-text="payroll.total_allowance"></td>
                <td v-text="payroll.total_deduction"></td>
                <td>
                  Over Time Hour : <span v-text="payroll.overtime_hour"></span><br>
                  Over Time Amount : <span v-text="payroll.overtime_amount"></span><br>
                </td>
                <td v-text="payroll.salary"></td>
                <td v-text="payroll.gross_salary"></td>
                <td v-text="payroll.net_salary"></td>
                <td v-text="payroll.total_salary"></td>
                <td v-text="payroll.remarks"></td>
                <td>
                  <div class="btn-group mt5">
                    <a class="btn btn-xs btn-primary" v-on:click.prevent="paySlip(index, payroll.salary_month_format)">
                    Payslip
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

  <div id="payslip_modal" class="popup-basic mfp-with-anim mfp-hide" style="min-width: 980px!important">
    <div class="panel">
      <div class="panel-body" id="payslip">
          <div class="row">
            <div class="col-md-12">
              <h1 class="text-center">{{Session('company_name')}}</h3>
              <h3 class="text-center">Location</h4>
              <h2 class="text-center">Pay Slip for the month of <span v-text="payRoll.salary_month_format"></span></h2>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <table class="table table-bordered">
                <tbody>
                  <tr>
                    <td><strong>Employee ID: </strong></td>
                    <td v-text="payRoll.employee_no"></td>
                    <td><strong>Date of Joining: </strong></td>
                    <td v-text="payRoll.joining_date"></td>
                  </tr>
                  <tr>
                    <td><strong>Name: </strong></td>
                    <td colspan="2" v-text="payRoll.full_name"></td>
                  </tr>
                  <tr>
                    <td><strong>Department: </strong></td>
                    <td v-text="payRoll.department"></td>
                    <td><strong>Designation: </strong></td>
                    <td v-text="payRoll.designation"></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <div class="row mt20">
            <div class="col-md-12">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th class="text-center">Earnings</th>
                    <th class="text-center">Deductions</th>
                  </tr>
                </thead>

                <tbody>
                  <tr>
                    <td style="padding: 0px!important; vertical-align:top!important">
                      <table class="table table-bordered">
                        <thead>
                          <tr>
                            <th class="text-center">Descriptions</th>
                            <th class="text-center">Amount</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td>Basic</td>
                            <td class="text-center" v-text="payRoll.salary"></td>
                          </tr>
                          <tr v-for="(allowance, index) in payRoll.allowances">
                            <td v-text="allowance.name"></td>
                            <td class="text-center" v-text="allowance.amount"></td>
                          </tr>
                          <tr>
                            <td>Over Time</td>
                            <td class="text-center" v-text="payRoll.overtime_amount"></td>
                          </tr>
                        </tbody>
                        <tfoot>
                          <tr>
                            <th class="text-center">Gross Earning</th>
                            <th class="text-center" v-text="payRoll.gross_salary"></th>
                          </tr>
                        </tfoot>
                      </table>
                    </td>

                    <td style="padding: 0px!important; vertical-align:top!important">
                      <table class="table table-bordered">
                        <thead>
                          <tr>
                            <th class="text-center">Descriptions</th>
                            <th class="text-center">Amount</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr v-for="(deduction, index) in payRoll.deductions">
                            <td v-text="deduction.name"></td>
                            <td class="text-center" v-text="deduction.amount"></td>
                          </tr>
                          <tr v-for="row in allowance_row_diff">
                            <td style="visibility: hidden;">-</td>
                            <td style="visibility: hidden;">-</td>
                          </tr>
                        </tbody>
                        <tfoot>
                          <tr>
                            <th class="text-center">Gross Deduction</th>
                            <th class="text-center" v-text="payRoll.total_deduction"></th>
                          </tr>
                        </tfoot>
                      </table>
                    </td>
                  </tr>
                </tbody>

                <tfoot>
                  <tr>
                    <th colspan="2" class="text-center">
                      Net Pay : <span v-text="payRoll.total_salary"></span>
                      ( <span v-text="convertNumberToWords(payRoll.total_salary)"></span> )
                    </th>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
          
          <hr class="short alt">
          <div class="section row mbn" id="payslip_button">
            <div class="col-sm-2 pull-right">
                <p class="text-left">
                    <button type="submit" v-on:click.prevent="PrintElem('payslip')" class="btn btn-dark btn-gradient dark btn-block">
                            <i class="fa fa-print pr5"></i> &nbsp; Print PaySlip
                    </button>
                </p>
            </div>
        </div>
      </div>
    </div>
  </div>

</section>


@section('script')

<script type="text/javascript" src="{{asset('js/payroll_report.js')}}"></script>

@endsection

@endsection