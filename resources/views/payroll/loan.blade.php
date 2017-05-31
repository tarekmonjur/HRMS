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

<section class="p10" id="loans">
<div class="panel">
    <div class="panel-heading">
        <div class="panel-title">
            <span class="glyphicon glyphicon-tasks"></span>Loans
            <span class="pull-right">
              <a v-on:click="modal_open('#loan_modal'), loan = []" onclick="document.getElementById('loan_modal_form').reset()" class="btn btn-sm btn-dark btn-gradient dark"><span class="glyphicons glyphicon-pencil"></span> &nbsp; Add Loan</a>
            </span>
        </div>
    </div>
    <div class="panel-body pn">
        <table class="table table-striped table-hover" id="datatableCall" cellspacing="0" width="100%">
            <thead>
            <tr class="bg-dark">
                <th>SL:</th>
                <th>Employee Name</th>
                <th>Loan Type</th>
                <th>Loan Aganist</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Duration</th>
                <th>Amount</th>
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
                <th>Loan Type</th>
                <th>Loan Aganist</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Duration</th>
                <th>Amount</th>
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
                <tr v-for="(loan,index) in loans">
                   <td v-text="index+1"></td>
                   <td v-html="getFullName(loan.user)"></td>
                   <td v-text="loan.loan_type.loan_type_name"></td>
                   <td v-text="loan.loan_aganist"></td>
                   <td v-text="loan.loan_start_date"></td>
                   <td v-text="loan.loan_end_date"></td>
                   <td v-text="loan.loan_duration"></td>
                   <td v-text="loan.loan_amount"></td>
                   <td v-text="loan.loan_remarks"></td>
                   <td v-if="loan.approved_by" v-html="getFullName(loan.approved_by)"></td>
                   <td v-else>
                     <a class="btn btn-sm text-warning" v-on:click="changeStatus(loan.id, index)">Approved</a>
                   </td>
                   <td v-html="getFullName(loan.created_by)"></td>
                   <td v-html="getFullName(loan.updated_by)"></td>
                   <td v-text="loan.created_at"></td>
                   <td v-text="loan.updated_at"></td>
                   <td v-if="loan.approved_by">
                       <span class="text-success">Approved</span>
                   </td>
                   <td v-else>
                     <div class="btn-group">
                           <a v-on:click="editloan(loan.id, index, '#loan_modal'),loan=[]" class="btn btn-sm btn-primary"><i class="glyphicons glyphicons-pencil"></i>
                           </a>
                       </div>
                       <div class="btn-group">
                           <a v-on:click="deleteloan(loan.id,index)" class="btn btn-sm btn-danger">
                               <i class="glyphicons glyphicons-bin"></i>
                           </a>
                       </div>
                   </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div id="loan_modal" class="popup-basic mfp-with-anim mfp-hide">
    <div class="panel">
        <div class="panel-heading">
            <span class="panel-title" v-if="loan !=''">
                <i class="fa fa-rocket"></i>Edit Loan
            </span>
            <span class="panel-title" v-else>
                <i class="fa fa-rocket"></i>Add Loan
            </span>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    <form v-if="loan ==''" id="loan_modal_form" method="post" v-on:submit.prevent="addLoan">

                      <div class="row">
                        <div class="col-md-12">
                          <div class="form-group" :class="{'has-error': errors.user_id}">
                              <label class="control-label">Employee Name : <span class="text-danger">*</span></label>
                              <select2 class="form-control select-sm input-sm" name="user_id">
                                  <option :value="0">...All Employee...</option>
                                  <option v-for="(user,index) in users" :value="user.id" v-text="user.fullname+' - ('+user.designation_name+' ) - ( '+user.level_name+' )'"></option>
                              </select2>
                              <span v-if="errors.user_id" class="help-block" v-text="errors.user_id[0]"></span>
                          </div>
                        </div>
  
                        <div class="col-md-6">
                          <div class="form-group" :class="{'has-error': errors.loan_type_id}">
                              <label class="control-label">Loan Type: <span class="text-danger">*</span></label>
                              <select class="form-control select-sm input-sm" name="loan_type_id">
                                  <option :value="''">...Select Loan Type...</option>
                                  <option v-for="(loanType,index) in loanTypes" :value="loanType.id" v-text="loanType.loan_type_name"></option>
                              </select>
                              <span v-if="errors.loan_type_id" class="help-block" v-text="errors.loan_type_id[0]"></span>
                          </div>
                        </div>

                        <div class="col-md-6">
                          <div class="form-group" :class="{'has-error': errors.loan_aganist}">
                              <label class="control-label">Loan Aganist: <span class="text-danger">*</span></label>
                              <select class="form-control select-sm input-sm" name="loan_aganist">
                                  <option :value="''">...Select Loan Aganist...</option>
                                  <option value="PF">provident fund</option>
                                  <option value="salary">Salary</option>
                              </select>
                              <span v-if="errors.loan_aganist" class="help-block" v-text="errors.loan_aganist[0]"></span>
                          </div>
                        </div>


                        <div class="col-md-6">
                          <div class="form-group" :class="{'has-error': errors.loan_start_date}">
                              <label class="control-label">Loan Start Date: <span class="text-danger">*</span></label>
                              <input type="text" name="loan_start_date" v-on:mouseover="myDatePicker" class="myDatePicker form-control input-sm" placeholder="Start Date" readonly="readonly">
                              <span v-if="errors.loan_start_date" class="help-block" v-text="errors.loan_start_date[0]"></span>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group" :class="{'has-error': errors.loan_end_date}">
                              <label class="control-label">Loan End Date: <span class="text-danger">*</span></label>
                              <input type="text" name="loan_end_date" v-on:mouseover="myDatePicker" class="myDatePicker form-control input-sm" placeholder="End Date" readonly="readonly">
                              <span v-if="errors.loan_end_date" class="help-block" v-text="errors.loan_end_date[0]"></span>
                          </div>
                        </div>


                        <div class="col-md-6">
                          <div class="form-group" :class="{'has-error': errors.loan_amount}">
                              <label class="control-label">Loan Amount : <span class="text-danger">*</span></label>
                              <input type="text" name="loan_amount" class="form-control input-sm" placeholder="Enter Loan Amount">
                              <span v-if="errors.loan_amount" class="help-block" v-text="errors.loan_amount[0]"></span>
                          </div>
                        </div>

                        <div class="col-md-6">
                          <div class="form-group" :class="{'has-error': errors.loan_deduct_amount}">
                              <label class="control-label"> Deduct Amount: <span class="text-danger">*</span></label>
                              <input type="text" name="loan_deduct_amount" class="form-control input-sm" placeholder="Deduct Amount">
                              <span v-if="errors.loan_deduct_amount" class="help-block" v-text="errors.loan_deduct_amount[0]"></span>
                          </div>
                        </div>
            

                        <div class="col-md-12">
                          <div class="form-group" :class="{'has-error': errors.loan_remarks}">
                              <label class="control-label">Loan Remarks:</label>
                              <textarea type="text" name="loan_remarks" class="form-control input-sm" placeholder="Remarks"></textarea>
                              <span v-if="errors.loan_remarks" class="help-block" v-text="errors.loan_remarks[0]"></span>
                          </div>
                        </div>
                      </div>

                      <hr class="short alt">

                      <div class="section row mbn">
                          <div class="col-sm-6 pull-right">
                              <p class="text-left">
                                  <button type="submit" name="add_loan" class="btn btn-dark btn-gradient dark btn-block"><span class="glyphicons glyphicons-ok_2"></span> &nbsp; Add Loan
                                  </button>
                              </p>
                          </div>
                      </div>
                    </form>


                    <form v-else id="loan_modal_form" method="post" v-on:submit.prevent="updateLoan">
                      <input v-if="loan.id" type="hidden" name="id" :value="loan.id">

                      <div class="row">
                        <div class="col-md-12">
                          <div class="form-group" :class="{'has-error': errors.user_id}">
                              <label class="control-label">Employee Name : <span class="text-danger">*</span></label>
                              <select2 class="form-control select-sm input-sm" name="user_id">
                                  <option :value="0">...All Employee...</option>
                                  <option v-for="(user,index) in users" :value="user.id" v-text="user.fullname+' - ('+user.designation_name+' ) - ( '+user.level_name+' )'"></option>
                              </select2>
                              <span v-if="errors.user_id" class="help-block" v-text="errors.user_id[0]"></span>
                          </div>
                        </div>
  
                        <div class="col-md-6">
                          <div class="form-group" :class="{'has-error': errors.loan_type_id}">
                              <label class="control-label">Loan Type: <span class="text-danger">*</span></label>
                              <select class="form-control select-sm input-sm" name="loan_type_id">
                                  <option :value="''">...Select Loan Type...</option>
                                  <option v-for="(loanType,index) in loanTypes" :value="loanType.id" v-text="loanType.loan_type_name"></option>
                              </select>
                              <span v-if="errors.loan_type_id" class="help-block" v-text="errors.loan_type_id[0]"></span>
                          </div>
                        </div>

                        <div class="col-md-6">
                          <div class="form-group" :class="{'has-error': errors.loan_aganist}">
                              <label class="control-label">Loan Aganist: <span class="text-danger">*</span></label>
                              <select class="form-control select-sm input-sm" name="loan_aganist">
                                  <option :value="''">...Select Loan Aganist...</option>
                                  <option value="PF">provident fund</option>
                                  <option value="salary">Salary</option>
                              </select>
                              <span v-if="errors.loan_aganist" class="help-block" v-text="errors.loan_aganist[0]"></span>
                          </div>
                        </div>


                        <div class="col-md-6">
                          <div class="form-group" :class="{'has-error': errors.loan_start_date}">
                              <label class="control-label">Loan Start Date: <span class="text-danger">*</span></label>
                              <input type="text" name="loan_start_date" v-on:mouseover="myDatePicker" class="myDatePicker form-control input-sm" placeholder="Start Date" readonly="readonly">
                              <span v-if="errors.loan_start_date" class="help-block" v-text="errors.loan_start_date[0]"></span>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group" :class="{'has-error': errors.loan_end_date}">
                              <label class="control-label">Loan End Date: <span class="text-danger">*</span></label>
                              <input type="text" name="loan_end_date" v-on:mouseover="myDatePicker" class="myDatePicker form-control input-sm" placeholder="End Date" readonly="readonly">
                              <span v-if="errors.loan_end_date" class="help-block" v-text="errors.loan_end_date[0]"></span>
                          </div>
                        </div>


                        <div class="col-md-6">
                          <div class="form-group" :class="{'has-error': errors.loan_amount}">
                              <label class="control-label">Loan Amount : <span class="text-danger">*</span></label>
                              <input type="text" name="loan_amount" class="form-control input-sm" placeholder="Enter Loan Amount">
                              <span v-if="errors.loan_amount" class="help-block" v-text="errors.loan_amount[0]"></span>
                          </div>
                        </div>

                        <div class="col-md-6">
                          <div class="form-group" :class="{'has-error': errors.loan_deduct_amount}">
                              <label class="control-label"> Deduct Amount: <span class="text-danger">*</span></label>
                              <input type="text" name="loan_deduct_amount" class="form-control input-sm" placeholder="Deduct Amount">
                              <span v-if="errors.loan_deduct_amount" class="help-block" v-text="errors.loan_deduct_amount[0]"></span>
                          </div>
                        </div>
            

                        <div class="col-md-12">
                          <div class="form-group" :class="{'has-error': errors.loan_remarks}">
                              <label class="control-label">Loan Remarks:</label>
                              <textarea type="text" name="loan_remarks" class="form-control input-sm" placeholder="Remarks"></textarea>
                              <span v-if="errors.loan_remarks" class="help-block" v-text="errors.loan_remarks[0]"></span>
                          </div>
                        </div>
                      </div>

                      <hr class="short alt">

                      <div class="section row mbn">
                          <div class="col-sm-6 pull-right">
                              <p class="text-left">
                                  <button type="submit" name="update_loan" class="btn btn-dark btn-gradient dark btn-block"><span class="glyphicons glyphicons-ok_2"></span> &nbsp; Update Loan
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

<script type="text/javascript" src="{{asset('js/loan.js')}}"></script>

@endsection

@endsection