@extends('layouts.hrms')
@section('content')

@section('style')

    <style type="text/css">
        .odd{background: #f5f5f5;padding: 20px 15px 0px;margin: -20px -15px -0px;}
        .even{padding: 10px 15px 0px;margin: 0px -15px;}

        .select2-container .select2-selection--single{height:32px!important}
        .select2-container--default .select2-selection--single .select2-selection__rendered{line-height:30px!important}
        .select2-container--default .select2-selection--single .select2-selection__arrow{height:30px!important}

        .select2-container{width:100%!important;height:32px!important}
        /*.fileupload-preview img{max-width: 200px!important;}*/
    </style>
@endsection

<section class="p10" id="increment">
<div class="panel">
    <div class="panel-heading">
        <div class="panel-title">
            <span class="glyphicon glyphicon-tasks"></span> Increments

            <?php 
              $chkUrl = \Request::segment(1);
            ?>
            @if(in_array($chkUrl."/add", session('userMenuShare')))
              <span class="pull-right">
                <a v-on:click="modal_open('#increment_modal'), increment = []" onclick="document.getElementById('increment_modal_form').reset()" class="btn btn-sm btn-dark btn-gradient dark"><span class="glyphicons glyphicon-pencil"></span> &nbsp; Add Increment</a>
              </span>
            @endif

        </div>
    </div>
    <div class="panel-body pn">
        <table class="table table-striped table-hover" id="datatableCall" cellspacing="0" width="100%">
            <thead>
            <tr class="bg-dark">
                <th>SL:</th>
                <th>Employee Name</th>
                <th>Increment Type</th>
                <th>Amount</th>
                <th>Amount Type</th>
                <th>Effective Date</th>
                <th>Remarks</th>
                <th>Approved By</th>
                <th>Created By</th>
                <th>Updated By</th>
                <th>Created Date</th>
                <th>Updated Date</th>
                <th>Action</th>
            </tr>
            </thead>
            <tfoot>
            <tr class="bg-dark" style="background: #f2f2f2!important">
                <th>SL:</th>
                <th>Employee Name</th>
                <th>Increment Type</th>
                <th>Amount</th>
                <th>Amount Type</th>
                <th>Effective Date</th>
                <th>Remarks</th>
                <th>Approved By</th>
                <th>Created By</th>
                <th>Updated By</th>
                <th>Created Date</th>
                <th>Updated Date</th>
                <th>Action</th>
            </tr>
            </tfoot>
            <tbody>
                <tr v-for="(increment,index) in increments">
                   <td v-text="index+1"></td>
                   <td v-html="getFullName(increment.user)"></td>
                   <td v-text="increment.increment_type.increment_type_name"></td>
                   <td v-text="increment.increment_amount"></td>
                   <td v-text="(increment.increment_amount_type == 'fixed')?'fixed':increment.increment_type_amount+'%'"></td>
                   <td v-text="increment.increment_effective_date"></td>
                   <td v-text="increment.increment_remarks"></td>
                   <td v-if="increment.approved_by" v-html="getFullName(increment.approved_by)"></td>
                   <td v-else>
                     <a class="btn btn-sm text-warning" v-on:click="changeStatus(increment.id, index)">Approved</a>
                   </td>
                   <td v-html="getFullName(increment.created_by)"></td>
                   <td v-html="getFullName(increment.updated_by)"></td>
                   <td v-text="increment.created_at"></td>
                   <td v-text="increment.updated_at"></td>
                   <td v-if="increment.approved_by">
                       <span class="text-success">Approved</span>
                   </td>
                   <td v-else>
                      @if(in_array($chkUrl."/edit", session('userMenuShare')))
                        <div class="btn-group">
                           <a v-on:click="editincrement(increment.id, index, '#increment_modal'),increment=[]" class="btn btn-sm btn-primary"><i class="glyphicons glyphicons-pencil"></i>
                           </a>
                        </div>
                      @endif
                      
                      @if(in_array($chkUrl."/delete", session('userMenuShare')))
                        <div class="btn-group">
                           <a v-on:click="deleteincrement(increment.id,index)" class="btn btn-sm btn-danger">
                               <i class="glyphicons glyphicons-bin"></i>
                           </a>
                        </div>
                      @endif

                   </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div id="increment_modal" class="popup-basic mfp-with-anim mfp-hide">
    <div class="panel">
        <div class="panel-heading">
            <span class="panel-title" v-if="increment !=''">
                <i class="fa fa-rocket"></i>Edit increment
            </span>
            <span class="panel-title" v-else>
                <i class="fa fa-rocket"></i>Add increment
            </span>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    <form v-if="increment ==''" id="increment_modal_form" method="post" v-on:submit.prevent="addincrement">

                      <div class="row">
                        <div class="col-md-12">
                          <div class="form-group" :class="{'has-error': errors.designation_id}">
                              <label class="control-label">Employee Designation : </label>
                              <select2 class="form-control input-sm" name="designation_id" v-model="designation_id">
                                  <option :value="0">...All Designation...</option>
                                  <option v-for="(designation,index) in designations" :value="designation.id" v-text="designation.designation_name+' - ('+designation.level.level_name+' ) - ( '+designation.department.department_name+' )'"></option>
                              </select2>
                              <span v-if="errors.designation_id" class="help-block" v-text="errors.designation_id[0]"></span>
                          </div>
                        </div>

                        <div class="col-md-12">
                          <div class="form-group" :class="{'has-error': errors.user_id}">
                              <label class="control-label">Employee Name : <span class="text-danger">*</span></label>
                              <select2 class="form-control select-sm input-sm" name="user_id[]" multiple>
                                  <option :value="0">...All Employee...</option>
                                  <option v-for="(user,index) in users" :value="user.id" v-text="user.fullname+' - ('+user.designation_name+' ) - ( '+user.level_name+' )'"></option>
                              </select2>
                              <span v-if="errors.user_id" class="help-block" v-text="errors.user_id[0]"></span>
                          </div>
                        </div>
  
                      
                        <div class="col-md-6">
                          <div class="form-group" :class="{'has-error': errors.increment_type_id}">
                              <label class="control-label">increment Type: <span class="text-danger">*</span></label>
                              <select class="form-control select-sm input-sm" name="increment_type_id">
                                  <option :value="''">...Select increment Type...</option>
                                  <option v-for="(incrementType,index) in incrementTypes" :value="incrementType.id" v-text="incrementType.increment_type_name"></option>
                              </select>
                              <span v-if="errors.increment_type_id" class="help-block" v-text="errors.increment_type_id[0]"></span>
                          </div>
                        </div>

                        <div class="col-md-6">
                          <div class="form-group" :class="{'has-error': errors.increment_amount_type}">
                              <label class="control-label">Amount Type: <span class="text-danger">*</span></label>
                              <select class="form-control select-sm input-sm" name="increment_amount_type" v-model="increment_amount_type">
                                  <option :value="''">...Select Amount Type...</option>
                                  <option value="percent">Percentage</option>
                                  <option value="fixed">Fixed</option>
                              </select>
                              <span v-if="errors.increment_amount_type" class="help-block" v-text="errors.increment_amount_type[0]"></span>
                          </div>
                        </div>

                        <div class="col-md-12" v-if="increment_amount_type == 'percent'">
                          <div class="form-group" :class="{'has-error': errors.increment_type_amount}">
                              <label class="control-label">Amount Calculate Percentage of Basic Salary : <span class="text-danger">*</span></label>
                              <input type="text" name="increment_type_amount" class="form-control input-sm" placeholder="Enter Percentage">
                              <span v-if="errors.increment_type_amount" class="help-block" v-text="errors.increment_type_amount[0]"></span>
                          </div>
                        </div>

                        <div class="col-md-12" v-if="increment_amount_type == 'fixed'">
                          <div class="form-group" :class="{'has-error': errors.increment_amount}">
                              <label class="control-label">Fixed increment Amount: <span class="text-danger">*</span></label>
                              <input type="text" name="increment_amount" class="form-control input-sm" placeholder="Fixed Amount">
                              <span v-if="errors.increment_amount" class="help-block" v-text="errors.increment_amount[0]"></span>
                          </div>
                        </div>

                        <div class="col-md-12">
                          <div class="form-group" :class="{'has-error': errors.increment_effective_date}">
                              <label class="control-label">increment Effective Date: <span class="text-danger">*</span></label>
                              <input type="text" name="increment_effective_date" v-on:mouseover="myDatePicker" class="myDatePicker form-control input-sm" placeholder="Effective Date" readonly="readonly">
                              <span v-if="errors.increment_effective_date" class="help-block" v-text="errors.increment_effective_date[0]"></span>
                          </div>
                        </div>

                        <div class="col-md-12">
                          <div class="form-group" :class="{'has-error': errors.increment_remarks}">
                              <label class="control-label">increment Remarks:</label>
                              <textarea type="text" name="increment_remarks" class="form-control input-sm" placeholder="Remarks"></textarea>
                              <span v-if="errors.increment_remarks" class="help-block" v-text="errors.increment_remarks[0]"></span>
                          </div>
                        </div>
                      </div>

                      <hr class="short alt">

                      <div class="section row mbn">
                          <div class="col-sm-6 pull-right">
                              <p class="text-left">
                                  <button type="submit" name="add_increment" class="btn btn-dark btn-gradient dark btn-block"><span class="glyphicons glyphicons-ok_2"></span> &nbsp; Add increment
                                  </button>
                              </p>
                          </div>
                      </div>
                    </form>


                    <form v-else id="increment_modal_form" method="post" v-on:submit.prevent="updateincrement">
                      <input v-if="increment.id" type="hidden" name="id" :value="increment.id">

                      <div class="row">
                        <div class="col-md-12">
                          <div class="form-group" :class="{'has-error': errors.designation_id}">
                              <label class="control-label">Employee Designation : </label>
                              <select2 class="form-control input-sm" name="designation_id" v-model="designation_id = increment.user.designation_id">
                                  <option :value="''">...Select Designation...</option>
                                  <option v-for="(designation,index) in designations" :value="designation.id" v-text="designation.designation_name+' - ('+designation.level.level_name+' ) - ( '+designation.department.department_name+' )'"></option>
                              </select2>
                              <span v-if="errors.designation_id" class="help-block" v-text="errors.designation_id[0]"></span>
                          </div>
                        </div>

                        <div class="col-md-12">
                          <div class="form-group" :class="{'has-error': errors.user_id}">
                              <label class="control-label">Employee Name : <span class="text-danger">*</span></label>
                              <select class="form-control select-sm input-sm" name="user_id">
                                  <option :value="''">...Select Employee...</option>
                                  <option v-for="(user,index) in users" :value="user.id" v-text="user.fullname+' - ('+user.designation_name+' ) - ( '+user.level_name+' )'" :selected="increment.user_id == user.id"></option>
                              </select>
                              <span v-if="errors.user_id" class="help-block" v-text="errors.user_id[0]"></span>
                          </div>
                        </div>
  
                      
                        <div class="col-md-6">
                          <div class="form-group" :class="{'has-error': errors.increment_type_id}">
                              <label class="control-label">increment Type: <span class="text-danger">*</span></label>
                              <select class="form-control select-sm input-sm" name="increment_type_id" v-model="increment.increment_type_id">
                                  <option :value="''">...Select increment Type...</option>
                                  <option v-for="(incrementType,index) in incrementTypes" :value="incrementType.id" v-text="incrementType.increment_type_name"></option>
                              </select>
                              <span v-if="errors.increment_type_id" class="help-block" v-text="errors.increment_type_id[0]"></span>
                          </div>
                        </div>

                        <div class="col-md-6">
                          <div class="form-group" :class="{'has-error': errors.increment_amount_type}">
                              <label class="control-label">Amount Type: <span class="text-danger">*</span></label>
                              <select class="form-control select-sm input-sm" name="increment_amount_type" v-model="increment_amount_type = increment.increment_amount_type">
                                  <option :value="''">...Select Amount Type...</option>
                                  <option value="percent">Percentage</option>
                                  <option value="fixed">Fixed</option>
                              </select>
                              <span v-if="errors.increment_amount_type" class="help-block" v-text="errors.increment_amount_type[0]"></span>
                          </div>
                        </div>

                        <div class="col-md-12" v-if="increment_amount_type == 'percent'">
                          <div class="form-group" :class="{'has-error': errors.increment_type_amount}">
                              <label class="control-label">Amount Calculate Percentage of Basic Salary : <span class="text-danger">*</span></label>
                              <input type="text" name="increment_type_amount" class="form-control input-sm" v-model="increment_type_amount = increment.increment_type_amount" placeholder="Enter Percentage">
                              <span v-if="errors.increment_type_amount" class="help-block" v-text="errors.increment_type_amount[0]"></span>
                          </div>
                        </div>

                        <div class="col-md-12" v-if="increment_amount_type == 'fixed'">
                          <div class="form-group" :class="{'has-error': errors.increment_amount}">
                              <label class="control-label">Fixed increment Amount: <span class="text-danger">*</span></label>
                              <input type="text" name="increment_amount" class="form-control input-sm" :value="increment.increment_amount" placeholder="Fixed Amount">
                              <span v-if="errors.increment_amount" class="help-block" v-text="errors.increment_amount[0]"></span>
                          </div>
                        </div>

                        <div class="col-md-12">
                          <div class="form-group" :class="{'has-error': errors.increment_effective_date}">
                              <label class="control-label">increment Effective Date: <span class="text-danger">*</span></label>
                              <input type="text" name="increment_effective_date" v-on:mouseover="myDatePicker" :value="increment.increment_effective_date" class="myDatePicker form-control input-sm" placeholder="Effective Date" readonly="readonly">
                              <span v-if="errors.increment_effective_date" class="help-block" v-text="errors.increment_effective_date[0]"></span>
                          </div>
                        </div>

                        <div class="col-md-12">
                          <div class="form-group" :class="{'has-error': errors.increment_remarks}">
                              <label class="control-label">increment Remarks:</label>
                              <textarea type="text" name="increment_remarks" class="form-control input-sm" placeholder="Remarks" v-text="increment.increment_remarks"></textarea>
                              <span v-if="errors.increment_remarks" class="help-block" v-text="errors.increment_remarks[0]"></span>
                          </div>
                        </div>
                      </div>

                      <hr class="short alt">

                      <div class="section row mbn">
                          <div class="col-sm-6 pull-right">
                              <p class="text-left">
                                  <button type="submit" name="update_increment" class="btn btn-dark btn-gradient dark btn-block"><span class="glyphicons glyphicons-ok_2"></span> &nbsp; Update increment
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

<script type="text/javascript" src="{{asset('js/increment.js')}}"></script>

@endsection

@endsection