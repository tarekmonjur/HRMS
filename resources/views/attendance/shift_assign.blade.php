@extends('layouts.hrms')
@section('content')

<section id="shiftassign" class="p10">
  <div class="row">

    <div class="col-md-9">
      <div class="panel">
          <div class="panel-heading">
              <a href="#" class="panel-title pull-right" v-on:click="getEmployeeShifts(),isActive=0"><i class="fa fa-refresh"></i></a>
              <span class="panel-title"><i class="glyphicons glyphicons-history"></i></span>
              <strong>Employee Work Shift Assign</strong>
          </div>

        <div class="panel-body pn">
          <table class="table table-bordered table-striped">
            <thead class="bg-dark">
              <tr>
                <th rowspan="2" class="text-center" width="20px" style="vertical-align: middle;">SL</th>
                <th rowspan="2" class="text-center" width="150px" style="vertical-align: middle;">Employee Name</th>
                <th colspan="4" class="text-center">Employee Work Shift & Days</th>
                <th rowspan="2" class="text-center" width="50px" style="vertical-align: middle;">Assign</th>
              </tr>
              <tr>
                <th class="text-center" style="min-width: 100px; width: 200px;" width="200px">Work Shift</th>
                <th class="text-center" style="min-width: 170px; width: 350px;" width="350px">Work Days</th>
                <th class="text-center" style="min-width: 80px; width: 120px;" width="120px">Start Date</th>
                <th class="text-center" style="min-width: 80px; width: 120px;" width="120px">End Date</th>
              </tr>
            </thead>

            <tbody>
              <tr v-for="(empShift,indexs) in employeeShifts">
                <td class="text-center" v-text="indexs+1"></td>
                <td class="text-center" v-text="empShift.full_name+' ('+empShift.employee_no+')'"></td>
                  
                <td colspan="4" style="padding: 0px">
                  <table class="table table-striped" :class="{'table-bordered':empShift.work_shift.length>0}">
                    <tbody>
                      <tr v-for="(shift,index) in empShift.work_shift">
                       <td class="text-center" style="min-width: 105px; width: 200px" v-text="shift.shift_name"></td>
                       <td class="text-center" style="min-width: 180px; width: 350px" v-text="shift.days_word"></td>
                       <td class="text-center" style="min-width: 80px; width: 120px" v-text="shift.start_date"></td>
                       <td class="text-center" style="min-width: 80px; width: 120px" v-text="shift.end_date"></td>
                     </tr>
                     <tr v-if="empShift.work_shift.length <= 0">
                       <td>No work shift assign</td>
                     </tr>
                   </tbody>
                  </table>
                </td>
                <td class="text-right">
                  <button type="button" class="btn btn-dark btn-sm light" v-on:click.prevent="getWorkAssign('#workshiftassign_modal',indexs),deleted=[]">
                    <i class="imoon imoon-loop2"></i>
                  </button>
                </td>
                
              </tr>
            </tbody>
          </table>
        </div>

      </div>
    </div>


    <div class="col-md-3">
      <div class="panel">
        <div class="panel-body p10">
          <!-- Work Shift -->
          <div class="list-group list-group-links">
            <div class="list-group-header"> Work Shift </div>
            <a href="#" class="list-group-item" v-for="(workshift,index) in workshifts">
              <span v-text="workshift.shift_name" v-on:click="getEmployeeShifts(workshift.id),isActive=workshift.id" :class="{'text-dark': isActive==workshift.id}"></span>
            </a>
          </div>
        </div>
      </div>
    </div>

  </div>

  <!-- modal -->
  <div id="workshiftassign_modal" class="popup-basic mfp-with-anim mfp-hide" style="max-width:800px">
    <div class="panel">
        <div class="panel-heading">
            <span class="panel-title">
                <i class="fa fa-rocket"></i>Assign Work Shift for <strong class="text-dark" v-text="employeeShift.full_name+' ('+employeeShift.employee_no+')'"></strong>
            </span>
        </div>
        <div class="panel-body">
          <div class="row">

            <form id="work_shift_asign_form" method="post" v-on:submit.prevent="workShiftAssign">
              <div class="col-md-12">
                  <div class="form-group">
                      <input type="hidden" name="user_id" :value="employeeShift.user_id">
                      <input type="hidden" name="deleted[]" v-for="de in deleted" :value="de">
                  </div>
              </div>

              <div class="col-md-12">
                <table class="table table-bordered">
                  <thead class="bg-dark">
                    <tr>
                      <th>Work Shift</th>
                      <th>Work Days</th>
                      <th>start Date</th>
                      <th>End Date</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(work_shift,indexs) in employeeShift.work_shift">
                     <input type="hidden" :name="'work_shift['+indexs+'][id]'" :value="work_shift.id">
                      <td>
                          <select :name="'work_shift['+indexs+'][work_shift_id]'" class="form-control input-sm" v-model="work_shift.work_shift_id" required="">
                            <option v-for="(ws,index) in workshifts" :value="ws.id" v-text="ws.shift_name"></option>
                          </select>
                      </td>
                      <td>
                          <label>
                            <input type="checkbox" :name="'work_shift['+indexs+'][work_days][]'" :value="1" v-model="work_shift.days">Sat
                          </label>
                          <label>
                            <input type="checkbox" :name="'work_shift['+indexs+'][work_days][]'" :value="2" v-model="work_shift.days">Sun
                          </label>
                          <label>
                            <input type="checkbox" :name="'work_shift['+indexs+'][work_days][]'" :value="3" v-model="work_shift.days">Mon
                          </label>
                          <label>
                            <input type="checkbox" :name="'work_shift['+indexs+'][work_days][]'" :value="4" v-model="work_shift.days">Tus
                          </label>
                          <label>
                            <input type="checkbox" :name="'work_shift['+indexs+'][work_days][]'" :value="5" v-model="work_shift.days">Wed
                          </label>
                          <label>
                            <input type="checkbox" :name="'work_shift['+indexs+'][work_days][]'" :value="6" v-model="work_shift.days">Thu
                          </label>
                          <label>
                            <input type="checkbox" :name="'work_shift['+indexs+'][work_days][]'" :value="7" v-model="work_shift.days">Fri
                            <!-- <input type="checkbox" name="work_days[]" :value="7" :checked="inArray(7,work_shift.days)">Fri -->
                          </label>
                      </td>
                      <td>
                        <input type="text" :name="'work_shift['+indexs+'][start_date]'" v-on:mouseover="myDatePicker" class="myDatePicker form-control input-sm" v-model="work_shift.start_date" v-on:focusout="work_shift.start_date = $event.target.value" required="">
                      </td>
                      <td>
                        <input type="text" :name="'work_shift['+indexs+'][end_date]'" v-on:mouseover="myDatePicker" class="myDatePicker form-control input-sm" v-model="work_shift.end_date" v-on:focusout="work_shift.end_date = $event.target.value" required="">
                      </td>
                      <td>
                        <button v-on:click.prevent="deleteWorkShift(indexs, work_shift.id)"><span class="text-danger glyphicons glyphicons-bin"></span></button>
                      </td>
                    </tr>
                  </tbody>
                </table>

                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group mt25">
                            <button v-on:click.prevent="addMoreShift" class="btn btn-sm btn-dark btn-gradient dark btn-block">Add More
                            </button>
                        </div>
                    </div>
                </div>

                <hr class="short alt">

                <div class="section row mbn">
                    <div class="col-sm-4 pull-right">
                        <p class="text-left">
                            <button type="submit" :disabled="employeeShift.work_shift == ''" name="assign_shift" class="btn btn-dark btn-gradient dark btn-block"><span class="glyphicons glyphicons-ok_2"></span> &nbsp; Assign Shift
                            </button>
                        </p>
                    </div>
                </div>

              </div>
            </form>
          </div>
      </div>
    </div>
  </div>

</section>




@section('script')

<script type="text/javascript" src="{{asset('js/shiftassign.js')}}"></script>

@endsection

@endsection