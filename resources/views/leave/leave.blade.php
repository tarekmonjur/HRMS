@extends('layouts.hrms')

@section('style')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
    <style type="text/css" media="screen">
        .sm-height {
            padding: 4px;
        }

        .btn-custom{
            color: #ffffff;
            text-decoration: none;
        }

        .btn-custom:hover{
            color: #ffffff;
            text-decoration: none;
        }
    </style>
@endsection

@section('content')
<div id="mainDiv">
    <!-- Begin: Content -->
    <section id="content" class="">
        <div class="row">
            <div class="col-md-12">
                <div class="panel">
                    <div class="panel-heading">
                        <span class="panel-title">Leave Application</span>

                        <?php 
                          $chkUrl = \Request::segment(1);
                        ?>
                        @if(in_array($chkUrl."/add", session('userMenuShare')))
                            <button type="button" class="btn btn-xs btn-success pull-right" data-toggle="modal" data-target=".dataAdd" style="margin-top: 12px;">Leave Application</button>
                        @endif
                    
                    </div>
                    <div class="panel-body">
                        <div id="showData">
                            <table class="table table-hover" id="datatable">
                                <thead>
                                    <tr class="success">
                                        <th>sl</th>
                                        <th>User Name</th>
                                        <th>Leave Type</th>
                                        <th>Leave Duration/Total</th>
                                        <th>Balance</th>
                                        <th>Attachment</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($leaves) > 0)
                                        <?php $sl=1; ?>
                                        @foreach($leaves as $info)
                                            <tr>
                                                <td>{{$sl++}}</td>
                                                <td>
                                                    <?php 
                                                        $emp_no = $info->userName->employee_no;
                                                    ?>
                                                    <a target="__blank" href="{{url("leave/details/$emp_no")}}">
                                                    {{$info->userName->first_name." ".$info->userName->last_name." (".$info->userName->employee_no.") - ".$info->userName->designation->designation_name}}
                                                    </a>
                                                    @if($info->employee_leave_noc_required == 1)
                                                        <span class="text-warning"> (NOC required)</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{$info->leaveType->leave_type_name}}
                                                    @if($info->employee_leave_half_or_full == 2)
                                                        <span class="text-primary"> /Half Day</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{$info->employee_leave_from." - ".$info->employee_leave_to." / ".$info->employee_leave_total_days}}
                                                </td>
                                                {{-- <td> {{$info->employee_leave_user_remarks}} </td>
                                                <td>
                                                    {{"Address: ". $info->employee_leave_contact_address}}<br/>
                                                    {{"Contract No.: ". $info->employee_leave_contact_number}}<br/>
                                                    {{"Passport No.: ". $info->employee_leave_passport_no}}<br/>
                                                </td>
                                                <td>{{$info->responsibleUser?$info->responsibleUser->first_name." ".$info->responsibleUser->last_name:'-'}}
                                                </td> --}}
                                                <td>
                                                    <button type="button" class="btn btn-system btn-xs" data-toggle="modal" data-target=".leaveBalance" @click="showLeaveBalance({{$info->user_id}})">Leave Balance</button>
                                                </td>
                                                <td>
                                                    <?php 
                                                        $folderName = $info->userName->id;
                                                        $fileName = $info->employee_leave_attachment;
                                                    ?>
                                                    @if(!empty($fileName))
                                                        <a target="_blank" href="{{asset("storage/leave_doc/$folderName/$fileName")}}" style="cursor: pointer;"><i class="fa fa-file fa-2x" aria-hidden="true"></i></a>
                                                    @endif
                                                </td>
                                                {{-- <td>{{$info->approvedByUser?$info->approvedByUser->first_name." ".$info->approvedByUser->last_name:'-'}}</td>
                                                <td> {{$info->employee_leave_approval_remarks}} </td> --}}
                                                <td>
                                                    <div class="btn-group remov-cls-toggle">
                                                        <button type="button" class="btn btn-xs" :class="leaveStatusBtn({{$info->employee_leave_status}})" v-text="showLeaveStatus({{$info->employee_leave_status}})">
                                                        </button>
                                                        <button type="button" class="btn btn-primary btn-xs dark dropdown-toggle" :class="leaveStatusBtn({{$info->employee_leave_status}})" data-toggle="dropdown" aria-expanded="false" v-show="{{$info->employee_leave_status}} != 4">
                                                        <span class="caret"></span>
                                                        <span class="sr-only">Toggle Dropdown</span>
                                                        </button>
                                                        @if(in_array($chkUrl."/edit", session('userMenuShare')))
                                                        <ul class="dropdown-menu toggle-cls" role="menu">
                                                            <li>
                                                              <a @click="changeStatus({{$info->id}}, 1)" v-show="{{$info->employee_leave_status}} != 1 && {{$info->employee_leave_status}} != 2 && {{$info->employee_leave_status}} != 3">Pending</a>
                                                            </li>
                                                            <li>
                                                              <a @click="changeStatus({{$info->id}}, 3)" v-show="{{$info->employee_leave_status}} != 3">Accepted</a>
                                                            </li>
                                                            <li>
                                                              <a @click="changeStatus({{$info->id}}, 4)" v-show="{{$info->employee_leave_status}} != 4">Cancel</a>
                                                            </li>
                                                        </ul>
                                                        @endif
                                                    </div>
                                                    <br/>{{ $info->employee_leaves_approval_date }}
                                                </td>
                                                <td>
                                                    {{-- <button type="button" class="btn btn-info btn-xs"><a target="_blank" href="{{url("leave/view/$info->id")}}" class="btn-custom">View</a></button> --}}
                                                    <button type="button" class="btn btn-info btn-xs"><a target="_blank" href="{{url("leaveView/$info->id")}}" class="btn-custom">View</a></button>
                                                    @if(in_array($chkUrl."/edit", session('userMenuShare')))
                                                        @if($info->employee_leave_status != 3 && $info->employee_leave_status != 4)
                                                            <button type="button" class="btn btn-system btn-xs edit-btn-Cls" data-toggle="modal" data-target=".dataEdit" @click="editData({{$info->id}})">Edit</button>
                                                        @else
                                                            <button type="button" disabled="disabled" class="btn btn-system btn-xs">Edit</button>
                                                        @endif
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End: Content -->   

    <!-- dataAdd modal start -->
    <div class="modal fade bs-example-modal-lg dataAdd" role="dialog" aria-labelledby="myLargeModalLabel" id="modalDataAdd">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Leave Application</h4>
                </div>
                <form class="form-horizontal" @submit.prevent="saveData" id="addFormData" method="post" enctype="multipart/form-data">
                    <div class="modal-body">

                        <div id="create-form-errors">
                        </div>

                        {{ csrf_field() }}

                        <div class="form-group">
                            <label for="emp_name" class="col-md-3 control-label">Employee Name <span class="text-danger">*</span></label>
                            <div class="col-md-9">
                                <select2 name="emp_name" v-model="emp_name" style="
                                width: 100%;color: #555555;
                                border: 1px solid #dddddd;
                                transition: border-color ease-in-out .15s;
                                height: 30px;
                                padding: 5px 10px;
                                font-size: 12px;
                                line-height: 1.5;
                                border-radius: 2px;"
                                >
                                    <option value="">Select Employee Name For Leave</option>
                                    <option v-for="(info,index) in users" 
                                        :value="info.id" 
                                        v-text="info.first_name+' '+info.last_name+' ('+info.employee_no+') - '+info.designation.designation_name"
                                    ></option>
                                </select2>
                            </div>
                        </div>

                        <div class="form-group" style="padding-top: 0px;" v-show="emp_name>0">
                            <div class="row">
                                <div class="col-md-12 control-label">
                                    <div align="center"><h5 style="padding-top: 0px;margin-top: 0px;">Leave History</h5></div>
                                </div>
                            </div>
                            <div class="col-md-10 col-md-offset-1">
                                <table class="table">
                                    <tr class="success">
                                        <th>Sl</th>
                                        <th>Name</th>
                                        <th>Amount(Days)</th>
                                        <th>Taken(Days)</th>
                                        <th>Balance</th>
                                    </tr>
                                    <tr v-for="(info, index) in show_history">
                                        <td v-text="index+1"></td>
                                        <td v-text="info.name"></td>
                                        <td v-text="info.amount_days == null ? 'Undefined':info.amount_days"></td>
                                        <td v-text="info.taken_days"></td>
                                        <td v-text="info.balance"></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                
                        <div class="form-group">
                            <label for="emp_leave_type" class="col-md-3 control-label">Leave Type <span class="text-danger">*</span></label>
                            <div class="col-md-9">
                                <select class="form-control input-sm" name="emp_leave_type" id="emp_leave_type" v-model="emp_leave_type">
                                    <option value="">Select Type</option>
                                    <option v-for="(info,index) in userLeaveType" 
                                        :value="info.id" 
                                        v-text="info.name"
                                        :disabled="info.days == 0"
                                    ></option>
                                </select>
                                <input type="hidden" name="userLeaveType" v-model="userLeaveType">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="from_date" class="col-md-3 control-label">Select Date <span class="text-danger">*</span></label>
                            <div class="col-md-3">
                                <input type="text" id="from_date" name="from_date" v-model="from_date" class="gui-input datepicker form-control input-sm" placeholder="From">
                            </div>
                            <div class="col-md-3">
                                <input type="text" id="to_date" name="to_date" v-model="to_date" class="gui-input datepicker form-control input-sm" placeholder="To">
                            </div>
                            <label for="" class="col-md-2 control-label"><div @click="date_diff_cal()" style="margin-top: -10px;" class="btn btn-xs btn-success">Cal. date diff.</div></label>
                            <label for="" class="col-md-1 control-label result" id="show_date_diff"></label>
                            <input type="hidden" v-model="date_diff" name="date_diff">
                            <label for="" style="color:#e95947;" class="col-md-9 control-label" id="show_date_diff_msg">
                                
                            </label>
                        </div>

                        <div class="form-group">
                            <label for="" class="col-md-3 control-label">Reason <span class="text-danger">*</span></label>
                            <div class="col-md-9">
                                <textarea name="leave_reason" v-model="leave_reason" class="form-control input-sm" placeholder="Application reason"></textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="" class="col-md-3 control-label">Contact Address </label>
                            <div class="col-md-9">
                                <textarea name="leave_contact_address" v-model="leave_contact_address" class="form-control input-sm" placeholder="Leave time contact address."></textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="" class="col-md-3 control-label">Contact Number</label>
                            <div class="col-md-9">
                                <input name="leave_contact_number" v-model="leave_contact_number" class="form-control input-sm" type="text" placeholder="Leave contract number.">
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-9 col-md-offset-3">
                                <input type="checkbox" name="employee_leave_noc_required" v-model="employee_leave_noc_required" value="1">  NOC required.
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="passport_no" class="col-md-3 control-label">Passport Number</label>
                            <div class="col-md-9">
                                <input name="passport_no" v-model="passport_no" class="form-control input-sm" type="text" placeholder="Passport number">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="responsible_emp" class="col-md-3 control-label">Responsible for official duty</label>
                            <div class="col-md-9">
                                <select2 name="responsible_emp" v-model="responsible_emp" style="
                                width: 100%;color: #555555;
                                border: 1px solid #dddddd;
                                transition: border-color ease-in-out .15s;
                                height: 30px;
                                padding: 5px 10px;
                                font-size: 12px;
                                line-height: 1.5;
                                border-radius: 2px;"
                                >
                                    <option value="">Select Responsible Employee</option>
                                    <option v-for="(info,index) in options" 
                                        :value="info.id" 
                                        v-text="info.first_name+' '+info.last_name+' ('+info.employee_no+') - '+info.designation.designation_name"
                                    ></option>
                                </select2>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="" class="col-md-3 control-label"></label>
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="radio-custom radio-success mb5">
                                            <input type="radio" name="leave_half_or_full" id="active" v-model="leave_half_or_full" value="1">
                                            <label for="active">Full day</label>
                                        </div>    
                                    </div>
                                    <div class="col-md-4">
                                        <div class="radio-custom radio-primary mb5">
                                            <input type="radio" name="leave_half_or_full" id="inactive" v-model="leave_half_or_full" value="2">
                                            <label for="inactive">Half day</label>
                                        </div>    
                                    </div>
                                </div>     
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="passport_no" class="col-md-3 control-label">Files</label>
                            <div class="col-md-9">
                                <input type="file" name="file[]" id="file" multiple="multiple"></input>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                    <button type="button" class="btn btn-default modal-close-btn" id="modal-close-btn" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Process</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- dataAdd modal end --> 

    <!-- Leave Balance Start -->
    <div class="modal fade bs-example-modal-lg leaveBalance" role="dialog" aria-labelledby="myLargeModalLabel" id="modalDataAdd">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Leave Balance</h4>
                </div>
                <form class="form-horizontal">
                    <div class="modal-body">
                        <div class="form-group" style="padding-top: 0px;">
                            <div class="row">
                                <div class="col-md-12 control-label">
                                    <div align="center"><h5 style="padding-top: 0px;margin-top: 0px;">Leave History</h5></div>
                                </div>
                            </div>
                            <div class="col-md-10 col-md-offset-1">
                                <table class="table">
                                    <tr class="success">
                                        <th>Sl</th>
                                        <th>Name</th>
                                        <th>Amount(Days)</th>
                                        <th>Taken+Pending(Days)</th>
                                        <th>Balance</th>
                                    </tr>
                                    <tr v-for="(info, index) in show_history">
                                        <td v-text="index+1"></td>
                                        <td v-text="info.name"></td>
                                        <td v-text="info.amount_days == null ? 'Undefined':info.amount_days"></td>
                                        <td v-text="info.taken_days"></td>
                                        <td v-text="info.balance"></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Leave Balance End -->

    <!-- salary Info Edit modal start ..... dataEdit-->
    <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" id="modalDataEdit">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Edit Leave Application</h4>
                </div>
                
                <form class="form-horizontal" @submit.prevent="updateData" id="updateFormData" enctype="multipart/form-data">
                    <div class="modal-body">

                        <div id="edit-form-errors">
                        </div>

                        {{ csrf_field() }}
                        <input type="hidden" name="hdn_id" v-model="hdn_id">

                        <div class="form-group">
                            <label for="edit_emp_name" class="col-md-3 control-label">Employee Name </label>
                            <div class="col-md-9">
                                <select name="edit_emp_name" disabled="" v-model="edit_emp_name" class="form-control input-sm">
                                    <option v-for="(info,index) in users" 
                                        :value="info.id" 
                                        v-text="info.first_name+' '+info.last_name+' - '+info.designation.designation_name"
                                    ></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="emp_supervisor" class="col-md-3 control-label">Supervisor Name </label>
                            <div class="col-md-9">
                                <select name="emp_supervisor" disabled="" v-model="emp_supervisor" class="form-control input-sm">
                                    <option v-for="(info,index) in users" 
                                        :value="info.id" 
                                        v-text="info.first_name+' '+info.last_name+' - '+info.designation.designation_name"
                                    ></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group" style="padding-top: 0px;">
                            <div class="row">
                                <div class="col-md-12 control-label">
                                    <div align="center"><h5 style="padding-top: 0px;margin-top: 0px;">Leave History</h5></div>
                                </div>
                            </div>
                            <div class="col-md-10 col-md-offset-1">
                                <table class="table">
                                    <tr class="success">
                                        <th>Sl</th>
                                        <th>Name</th>
                                        <th>Amount(Days)</th>
                                        <th>Taken+Pending(Days)</th>
                                        <th>Balance</th>
                                    </tr>
                                    <tr v-for="(info, index) in show_history">
                                        <td v-text="index+1"></td>
                                        <td v-text="info.name"></td>
                                        <td v-text="info.amount_days == null ? 'Undefined':info.amount_days"></td>
                                        <td v-text="info.taken_days"></td>
                                        <td v-text="info.balance"></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                
                        <div class="form-group">
                            <label for="emp_leave_type" class="col-md-3 control-label">Leave Type <span class="text-danger">*</span></label>
                            <div class="col-md-9">
                                <select class="form-control input-sm" name="edit_leave_type_id" v-model="edit_leave_type_id">
                                    <option v-for="(info,index) in edit_leave_type" 
                                        :value="info.id"
                                        v-text="info.name"
                                    ></option>
                                </select>
                                {{-- <input type="hidden" name="userLeaveType" v-model="userLeaveType"> --}}
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="edit_from_date" class="col-md-3 control-label">Select Date <span class="text-danger">*</span></label>
                            <div class="col-md-3">
                                <input type="text" id="edit_from_date" name="edit_from_date" v-model="edit_from_date" class="gui-input datepicker form-control input-sm" placeholder="From">
                            </div>
                            <div class="col-md-3">
                                <input type="text" id="edit_to_date" name="edit_to_date" v-model="edit_to_date" class="gui-input datepicker form-control input-sm" placeholder="To">
                            </div>
                            <label for="" class="col-md-2 control-label"><div @click="edit_date_diff_cal()" style="margin-top: -10px;" class="btn btn-xs btn-success">Cal. date diff.</div></label>
                            <label for="" class="col-md-1 control-label result" v-text="edit_date_diff"></label>
                        
                            <label for="" style="color:#e95947;" class="col-md-9 control-label" id="edit_show_date_diff_msg">
                                
                            </label>
                        </div>

                        <div class="form-group">
                            <label for="" class="col-md-3 control-label">Reason <span class="text-danger">*</span></label>
                            <div class="col-md-9">
                                <textarea name="edit_leave_reason" v-model="edit_leave_reason" class="form-control input-sm" placeholder="Application reason"></textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="" class="col-md-3 control-label">Contact Address </label>
                            <div class="col-md-9">
                                <textarea name="edit_leave_contact_address" v-model="edit_leave_contact_address" class="form-control input-sm" placeholder="Leave time contact address."></textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="" class="col-md-3 control-label">Contact Number</label>
                            <div class="col-md-9">
                                <input name="edit_leave_contact_number" v-model="edit_leave_contact_number" class="form-control input-sm" type="text" placeholder="Leave contract number.">
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-9 col-md-offset-3">
                                <input type="checkbox" name="edit_employee_leave_noc_required" v-model="edit_employee_leave_noc_required" value="1">  NOC required.
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="edit_passport_no" class="col-md-3 control-label">Passport Number</label>
                            <div class="col-md-9">
                                <input name="edit_passport_no" v-model="edit_passport_no" class="form-control input-sm" type="text" placeholder="Passport number">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="edit_responsible_emp" class="col-md-3 control-label">Responsible for official duty</label>
                            <div class="col-md-9">
                                <select name="edit_responsible_emp" v-model="edit_responsible_emp" class="form-control input-sm">
                                    <option value="">Select Responsible Employee</option>
                                    <option v-for="(info,index) in options" v-if="edit_emp_name != info.id" 
                                        :value="info.id"
                                        v-text="info.first_name+' '+info.last_name+' ('+info.employee_no+') - '+info.designation.designation_name"
                                    ></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="edit_responsible_emp" class="col-md-3 control-label"></label>
                            <div class="col-md-9">
                                <input type="checkbox" name="want_to_forward" v-model="want_to_forward" value="1"> Want to forward.
                                <span class="text-danger"><br/>* Only Supervisor or Hr Can forward leave application.</span>
                            </div>
                        </div>

                        <div class="form-group"  v-if="want_to_forward">
                            <label for="edit_forward_to" class="col-md-3 control-label">Forward to <span class="text-danger">*</span></label>
                            <div class="col-md-9">
                                <select name="edit_forward_to" v-model="edit_forward_to" class="form-control input-sm">
                                    <option value="">Select Forward to Employee</option>
                                    <option v-for="(info,index) in options" v-if="edit_emp_name != info.id" 
                                        :value="info.id"
                                        v-text="info.first_name+' '+info.last_name+' ('+info.employee_no+') - '+info.designation.designation_name"
                                    ></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="" class="col-md-3 control-label"></label>
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="radio-custom radio-success mb5">
                                            <input type="radio" name="edit_leave_half_or_full" id="aactive" v-model="edit_leave_half_or_full" value="1">
                                            <label for="aactive">Full day</label>
                                        </div>    
                                    </div>
                                    <div class="col-md-4">
                                        <div class="radio-custom radio-primary mb5">
                                            <input type="radio" name="edit_leave_half_or_full" id="ainactive" v-model="edit_leave_half_or_full" value="2">
                                            <label for="ainactive">Half day</label>
                                        </div>    
                                    </div>
                                </div>     
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="passport_no" class="col-md-3 control-label">Files</label>
                            <div class="col-md-9">
                                <input type="file" name="file" id="file"></input>
                                <span style="color: green;" v-text="edit_file_info"></span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default modal-close-btn" id="modal-edit-close-btn" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
              
            </div>
        </div>
    </div>
    <!-- salary Info Edit modal end --> 
</div>

@endsection

@section('script')

{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script> --}}
<script src="https://unpkg.com/vue-select@2.2.0"></script>
<script src="{{asset('/js/leave.js')}}"></script>

<script>
    $('.toggle-cls').click(function(event) {
        
        document.getElementsByClassName("remov-cls-toggle").remove("open");
    });

    $('.edit-btn-Cls').click(function(event) {
        setTimeout(function() {
            $('#modalDataEdit').modal();
        }, 200);
    });

</script>
@endsection