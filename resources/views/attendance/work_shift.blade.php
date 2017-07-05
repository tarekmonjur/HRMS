@extends('layouts.hrms')
@section('content')

<section class="p10" id="work_shift">
<div class="panel">
    <div class="panel-heading">
        <div class="panel-title">
            <span class="glyphicon glyphicon-tasks"></span>Common Work Shift
        </div>
    </div>
    <div class="panel-body pn">
        <table class="table table-striped table-hover" cellspacing="0" width="100%">
            <thead>
            <tr class="bg-dark">
                <th>Shift Name</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Late Count Time</th>
                <th>Created Date</th>
                <th>Updated Date</th>
            </tr>
            </thead>
            
            <tbody>
                @if(!empty($common_val->common_shift_name))
                <tr>
                   <td>{{ $common_val->common_shift_name }}</td>
                   <td>{{ $common_val->common_shift_start_time }}</td>
                   <td>{{ $common_val->common_shift_end_time }}</td>
                   <td>{{ $common_val->common_late_count_time }}</td>
                   <td>{{ $common_val->created_at }}</td>
                   <td>{{ $common_val->updated_at }}</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    <br/><br/>

    <div class="panel-heading">
        <div class="panel-title">
            <span class="glyphicon glyphicon-tasks"></span>Work Shift
            <span class="pull-right">
              <a v-on:click="modal_open('#workshift_modal'),workshift = []" onclick="document.getElementById('work_shift_modal_form').reset()" class="btn btn-sm btn-dark btn-gradient dark"><span class="glyphicons glyphicon-pencil"></span> &nbsp; Add Work Shift</a>
            </span>
        </div>
    </div>
    <div class="panel-body pn">
        <table class="table table-striped table-hover" id="datatableCall" cellspacing="0" width="100%">
            <thead>
            <tr class="bg-dark">
                <th>SL:</th>
                <th>Shift Name</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Late Count Time</th>
                <th>Status</th>
                <th>Created Date</th>
                <th>Updated Date</th>
                <th>Action</th>
            </tr>
            </thead>
            <tfoot>
            <tr class="bg-dark" style="background: #f2f2f2!important">
                <th>SL:</th>
                <th>Shift Name</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Late Count Time</th>
                <th>Status</th>
                <th>Created Date</th>
                <th>Updated Date</th>
                <th>Action</th>
            </tr>
            </tfoot>
            <tbody v-if="workshifts !=''">
                <tr v-for="(workshift,index) in workshifts">
                   <td v-text="index+1"></td>
                   <td v-text="workshift.shift_name"></td>
                   <td v-text="workshift.shift_start_time"></td>
                   <td v-text="workshift.shift_end_time"></td>
                   <td v-text="workshift.late_count_time"></td>
                   <td>
                     <div class="btn-group pt5">
                         <a class="btn btn-sm" :class="(workshift.work_shift_status == 0)?'text-primary':'text-danger'" v-on:click="changeStatus($event,workshift.id)" :status="workshift.work_shift_status" v-text="(workshift.work_shift_status == 0)?'Active':'Inactive'"></a>
                       </div>
                   </td>
                   <td v-text="workshift.created_at"></td>
                   <td v-text="workshift.updated_at"></td>
                   <td>
                       <div class="btn-group">
                           <a v-on:click="editWorkShift(workshift.id,'#workshift_modal'),workshift=[]" class="btn btn-sm btn-primary"><i class="glyphicons glyphicons-pencil"></i>
                           </a>
                       </div>
                       <div class="btn-group">
                           <a v-on:click="deleteWorkShift(workshift.id,index)" class="btn btn-sm btn-danger">
                               <i class="glyphicons glyphicons-bin"></i>
                           </a>
                       </div>
                   </td>
                </tr>
            </tbody>

            <tbody v-else>
              <tr>
                <td colspan="9">No data available</td>
              </tr>
            </tbody>
            
        </table>
    </div>
</div>

<div id="workshift_modal" class="popup-basic mfp-with-anim mfp-hide">
    <div class="panel">
        <div class="panel-heading">
            <span class="panel-title" v-if="workshift !=''">
                <i class="fa fa-rocket"></i>Edit Work Shift
            </span>
            <span class="panel-title" v-else>
                <i class="fa fa-rocket"></i>Add Work Shift
            </span>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    <form v-if="workshift ==''" id="work_shift_modal_form" method="post" v-on:submit.prevent="addWorkShift">
                        <div class="row">
                          <div class="col-md-12">
                              <div class="form-group" :class="{'has-error':errors.shift_name}">
                                  <label class="control-label">Shift Name : <span class="text-danger">*</span></label>
                                  <input type="text" name="shift_name" class="form-control input-sm">
                                  <span v-if="errors.shift_name" class="text-danger" v-text="errors.shift_name[0]"></span>
                              </div>
                          </div>
                          <div class="col-md-12">
                              <div class="form-group" :class="{'has-error':errors.shift_start_time}">
                                  <label class="control-label">Shift Start Time: <span class="text-danger">*</span></label>
                                  <input type="text" name="shift_start_time" v-on:mouseover="myTimePicker" class="myTimePicker form-control input-sm">
                                  <span v-if="errors.shift_start_time" class="text-danger" v-text="errors.shift_start_time[0]"></span>
                              </div>
                          </div>
                          <div class="col-md-12">
                              <div class="form-group" :class="{'has-error':errors.shift_end_time}">
                                  <label class="control-label">Shift End Time: <span class="text-danger">*</span></label>
                                  <input type="text" name="shift_end_time" v-on:mouseover="myTimePicker" class="myTimePicker form-control input-sm">
                                  <span v-if="errors.shift_end_time" class="text-danger" v-text="errors.shift_end_time[0]"></span>
                              </div>
                          </div>
                          <div class="col-md-12">
                              <div class="form-group" :class="{'has-error':errors.late_count_time}">
                                  <label class="control-label">Late Count Time: </label>
                                  <input type="text" name="late_count_time" v-on:mouseover="myTimePicker" class="myTimePicker form-control input-sm">
                                  <span v-if="errors.late_count_time" class="text-danger" v-text="errors.late_count_time[0]"></span>
                              </div>
                          </div>
                        </div>

                        <hr class="short alt">

                        <div class="section row mbn">
                            <div class="col-sm-6 pull-right">
                                <p class="text-left">
                                    <button type="submit" name="add_work_shift" class="btn btn-dark btn-gradient dark btn-block"><span class="glyphicons glyphicons-ok_2"></span> &nbsp; Add Work Shift
                                    </button>
                                </p>
                            </div>
                        </div>
                    </form>


                    <form v-else id="work_shift_modal_form" method="post" v-on:submit.prevent="updateWorkShift">
                        <input v-if="workshift.id" type="hidden" name="id" :value="workshift.id">

                        <div class="row">
                          <div class="col-md-12">
                              <div class="form-group" :class="{'has-error':errors.shift_name}">
                                  <label class="control-label">Shift Name : <span class="text-danger">*</span></label>
                                  <input type="text" name="shift_name" v-model="workshift.shift_name" class="form-control input-sm">
                                  <span v-if="errors.shift_name" class="text-danger" v-text="errors.shift_name[0]"></span>
                              </div>
                          </div>
                          <div class="col-md-12">
                              <div class="form-group" :class="{'has-error':errors.shift_start_time}">
                                  <label class="control-label">Shift Start Time: <span class="text-danger">*</span></label>
                                  <input type="text" name="shift_start_time" v-model="workshift.shift_start_time" v-on:mouseover="myTimePicker" class="myTimePicker form-control input-sm">
                                  <span v-if="errors.shift_start_time" class="text-danger" v-text="errors.shift_start_time[0]"></span>
                              </div>
                          </div>
                          <div class="col-md-12">
                              <div class="form-group" :class="{'has-error':errors.shift_end_time}">
                                  <label class="control-label">Shift End Time: <span class="text-danger">*</span></label>
                                  <input type="text" name="shift_end_time" v-model="workshift.shift_end_time" v-on:mouseover="myTimePicker" class="myTimePicker form-control input-sm">
                                  <span v-if="errors.shift_end_time" class="text-danger" v-text="errors.shift_end_time[0]"></span>
                              </div>
                          </div>
                          <div class="col-md-12">
                              <div class="form-group" :class="{'has-error':errors.late_count_time}">
                                  <label class="control-label">Late Count Time: </label>
                                  <input type="text" name="late_count_time" v-model="workshift.late_count_time" v-on:mouseover="myTimePicker" class="myTimePicker form-control input-sm">
                                  <span v-if="errors.late_count_time" class="text-danger" v-text="errors.late_count_time[0]"></span>
                              </div>
                          </div>
                        </div>

                        <hr class="short alt">

                        <div class="section row mbn">
                            <div class="col-sm-6 pull-right">
                                <p class="text-left">
                                    <button type="submit" name="add_bank" class="btn btn-dark btn-gradient dark btn-block">   <span class="glyphicons glyphicons-ok_2"></span> &nbsp; Update Work Shift
                                    </button>
                                </p>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>


</section>


@section('script')

<script type="text/javascript" src="{{asset('js/workshift.js')}}"></script>

@endsection

@endsection