@extends('layouts.hrms')

@section('style')
    
@endsection

@section('content')
<div id="mainDiv">
    <!-- Begin: Content -->
    <section id="content" class="">
        <div class="row">
            <div class="col-md-12">
                <div class="panel">
                    <div class="panel-heading">
                        <span class="panel-title"> Leave Type</span>
                        <?php 
                          $chkUrl = \Request::segment(1);
                        ?>
                        @if(in_array($chkUrl."/add", session('userMenuShare')))
                            <button type="button" class="btn btn-xs btn-success pull-right" data-toggle="modal" data-target=".dataAdd" @click="uncheckAll()" style="margin-top: 12px;">Add New Leave Type</button>
                        @endif
                    </div>
                    <div class="panel-body">
                    <span class="text-danger">
                            <b>** If any one apply for leave using this type then u can not delete existing leave type.</b>
                        </span>
                        <div id="showData">
                            <table class="table table-hover" id="datatable">
                                <thead>
                                    <tr class="success">
                                        <th>sl</th>
                                        <th>Name</th>
                                        <th>EL</th>
                                        <th>Without Pay</th>
                                        <th>Encashable</th>
                                        <th>Encashable Limit</th>
                                        <th>Number of Days</th>
                                        <th>Effective For</th>
                                        <th>Carry Forward</th>
                                        <th>Max Carry Limit</th>
                                        <th>Including Holiday</th>
                                        <th>Effective Year</th>
                                        <th>Details</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(info,index) in leaveTypes">
                                        <td v-text="index+1"></td>
                                        <td v-text="info.leave_type_name"></td>
                                        <td v-text="info.leave_type_is_earn_leave==1?'EL':''"></td>
                                        <td v-text="info.leave_type_with_out_pay == 1?'Yes':'No'"></td>
                                        <td v-text="info.leave_type_is_sellable==1?'Yes':''"></td>
                                        <td v-text="info.leave_type_max_sell_limit"></td>
                                        <td v-text="info.leave_type_number_of_days == null?'Undefined':info.leave_type_number_of_days"></td>
                                        <td v-text="returnEffectedEmpType(info.leave_type_effective_for)"></td>
                                        <td v-text="info.leave_type_is_remain == 1?'CF':'No'"></td>
                                        <td v-text="info.leave_type_max_remain_limit"></td>
                                        <td v-text="info.leave_type_include_holiday == 1?'Yes':'No'"></td>
                                        <td v-text="info.leave_type_active_from_year+' - '+info.leave_type_active_to_year"></td>
                                        <td v-text="info.leave_type_details"></td>
                                        <td v-text="info.leave_type_status==1?'Active':'Inactive'"></td>
                                        <td>
                                        @if(in_array($chkUrl."/edit", session('userMenuShare')))
                                            <button type="button" @click="editData(info.id, index)" class="btn btn-sm btn-primary edit-btn" data-toggle="modal" data-target=".dataEdit">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                        @endif
                                        @if(in_array($chkUrl."/delete", session('userMenuShare')))
                                            <button type="button" @click="wantToDelete(info.id)" class="btn btn-sm btn-danger">
                                            <i class="fa fa-trash-o"></i>
                                            </button>
                                        @endif
                                        </td>
                                    </tr>
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
                    <h4 class="modal-title">Add Leave Type</h4>
                    <span class="text-danger">
                        <b>** Be careful ! After add data can't be edited.</b>
                    </span>
                </div>
                <form class="form-horizontal" @submit.prevent="saveData('addFormData')" id="addFormData">
                    <div class="modal-body">

                        <div id="create-form-errors">
                        </div>

                        {{ csrf_field() }}

                        <div class="form-group">
                            <label for="type_name" class="col-md-3 control-label">Type Name <span class="text-danger">*</span></label>
                            <div class="col-md-9">
                                <input name="type_name" class="form-control input-sm" type="text" placeholder="Type name">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="duration" class="col-md-3 control-label">Number of Days</label>
                            <div class="col-md-9">
                                <input name="duration" class="form-control input-sm" type="number" placeholder="Number of days.">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="valid_after" class="col-md-3 control-label">Valid after(months)</label>
                            <div class="col-md-9">
                                <input name="valid_after" class="form-control input-sm" type="number" placeholder="Leave valid after how many months.">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Effective For <span class="text-danger">*</span></label>
                            <div class="col-md-9">
                                @if(count($emp_types) > 0)
                                    @foreach($emp_types as $type)
                                        <div class="col-md-4">
                                            <input type="checkbox" name="emp_type[]" value="{{$type->id}}"> {{$type->type_name}}
                                        </div>
                                    @endforeach
                                @endif
                            </div>      
                        </div>

                        <div class="form-group">
                            <label for="" class="col-md-3 control-label">Type Details</label>
                            <div class="col-md-9">
                                <textarea name="type_details" class="form-control input-sm" placeholder="Type Description"></textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-9 col-md-offset-3">
                                <input type="checkbox" name="leave_type_with_out_pay" v-model="leave_type_with_out_pay" value="1"> Leave with out pay.
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-9 col-md-offset-3">
                                <input type="checkbox" name="is_earn" value="1"> Is earn leave.
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-9 col-md-offset-3">
                                <input type="checkbox" name="sellable" value="1"> Leave type is encashable.
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="max_sell_limit" class="col-md-3 control-label">Max encashable limit</label>
                            <div class="col-md-9">
                                <input name="max_sell_limit" class="form-control input-sm" type="number" placeholder="Max sell limit.">
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-9 col-md-offset-3">
                                <input type="checkbox" name="carry_to_next_year" value="1"> Leave type is carry forward.
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="max_remain_limit" class="col-md-3 control-label">Max carry forward limit</label>
                            <div class="col-md-9">
                                <input name="max_remain_limit" class="form-control input-sm" type="number" placeholder="Max remain limit.">
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-9 col-md-offset-3">
                                <input type="checkbox" name="include_holiday" value="1"> Leave calculate including holiday.
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="" class="col-md-3 control-label">Select Activation<span class="text-danger">*</span></label>
                            <div class="col-md-4">
                                <select name="from_year" class="form-control input-sm" id="">
                                    <option value="">From Year</option>
                                    @for($i=2017; $i<=2030; $i++)
                                        <option value="{{$i}}">{{$i}}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select name="to_year" class="form-control input-sm" id="">
                                    <option value="">To Year</option>
                                    @for($i=2017; $i<=2030; $i++)
                                        <option value="{{$i}}">{{$i}}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                    <button type="button" class="btn btn-default modal-close-btn" id="modal-close-btn" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- unitAdd modal end --> 

    <!-- salary Info Edit modal start -->
    <div class="modal fade bs-example-modal-lg dataEdit" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" id="modalDataEdit">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Edit Leave Type</h4>
                    <span class="text-danger">
                        <b>** Only Activation 'To Year' and 'Status' is editable.</b>
                    </span>
                </div>
                
                <form class="form-horizontal" @submit.prevent="updateData('updateFormData')" id="updateFormData">
                    <div class="modal-body">

                        <div id="edit-form-errors">
                        </div>

                        {{ csrf_field() }}

                        <input type="hidden" name="hdn_id" v-model="hdn_id">

                        <div class="form-group">
                            <label for="type_name" class="col-md-3 control-label">Type Name <span class="text-danger">*</span></label>
                            <div class="col-md-9">
                                <input name="type_name" disabled="" v-model="type_name" class="form-control input-sm" type="text" placeholder="Type name">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="duration" class="col-md-3 control-label">Number of Days</label>
                            <div class="col-md-9">
                                <input name="duration" disabled="" v-model="duration" class="form-control input-sm" type="number" placeholder="Number of days.">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="valid_after" class="col-md-3 control-label">Valid after(months)</label>
                            <div class="col-md-9">
                                <input name="valid_after" disabled="" class="form-control input-sm" v-model="valid_after" type="number" placeholder="Leave valid after how many months.">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Effective For <span class="text-danger">*</span></label>
                            <div class="col-md-9">
                                @if(count($emp_types) > 0)
                                    @foreach($emp_types as $type)
                                        <div class="col-md-4">
                                            <input type="checkbox" disabled="" name="emp_type[]"  value="{{$type->id}}"> {{$type->type_name}}
                                        </div>
                                    @endforeach
                                @endif
                            </div>      
                        </div>

                        <div class="form-group">
                            <label for="" class="col-md-3 control-label">Type Details</label>
                            <div class="col-md-9">
                                <textarea name="type_details" disabled="" v-model="type_details" class="form-control input-sm" placeholder="Type Description"></textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-9 col-md-offset-3">
                                <input type="checkbox" disabled="" name="leave_type_with_out_pay" v-model="leave_type_with_out_pay" value="1"> Leave with out pay.
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-9 col-md-offset-3">
                                <input type="checkbox" disabled="" name="is_earn" v-model="is_earn" value="1"> Is earn leave.
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-9 col-md-offset-3">
                                <input type="checkbox" disabled="" name="sellable" v-model="sellable" value="1"> Leave type is encashable.
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="max_sell_limit" class="col-md-3 control-label">Max encashable limit</label>
                            <div class="col-md-9">
                                <input name="max_sell_limit" disabled="" v-model="max_sell_limit" class="form-control input-sm" type="number" placeholder="Max sell limit.">
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-9 col-md-offset-3">
                                <input type="checkbox" disabled="" name="carry_to_next_year" v-model="carry_to_next_year" value="1999"> Leave type is carry forward.
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="max_remain_limit" class="col-md-3 control-label">Max carry forward limit</label>
                            <div class="col-md-9">
                                <input name="max_remain_limit" disabled="" v-model="max_remain_limit" class="form-control input-sm" type="number" placeholder="Max remain limit.">
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-9 col-md-offset-3">
                                <input type="checkbox" disabled="" name="include_holiday" v-model="include_holiday" value="1"> Leave calculate including holiday.
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="" class="col-md-3 control-label">Select Activation<span class="text-danger">*</span></label>
                            <div class="col-md-4">
                                <select name="from_year" disabled="" v-model="from_year" class="form-control input-sm" id="">
                                    <option value="">From Year</option>
                                    @for($i=2017; $i<=2030; $i++)
                                        <option value="{{$i}}">{{$i}}</option>
                                    @endfor
                                </select>
                            </div>

                            <div class="col-md-4">
                                <select name="to_year" v-model="to_year" class="form-control input-sm" id="">
                                    <option value="">To Year</option>
                                    @for($i=2017; $i<=2030; $i++)
                                        <option value="{{$i}}">{{$i}}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="" class="col-md-3 control-label"></label>
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="radio-custom radio-success mb5">
                                            <input type="radio" name="leave_type_status" id="edit_active" v-model="leave_type_status" value="1">
                                            <label for="edit_active">Active</label>
                                        </div>    
                                    </div>
                                    <div class="col-md-4">
                                        <div class="radio-custom radio-danger mb5">
                                            <input type="radio" name="leave_type_status" id="edit_inactive" v-model="leave_type_status" value="0">
                                            <label for="edit_inactive">Inactive</label>
                                        </div>    
                                    </div>
                                </div>     
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

<script src="{{asset('/js/leaveType.js')}}"></script>

@endsection
