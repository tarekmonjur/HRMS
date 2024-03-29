@extends('layouts.hrms')
@section('content')

<section class="p10" id="loan_type">
<div class="panel">
    <div class="panel-heading">
        <div class="panel-title">
            <span class="glyphicon glyphicon-tasks"></span>Loan Type

            <?php 
              $chkUrl = \Request::segment(1);
            ?>
            @if(in_array($chkUrl."/add", session('userMenuShare')))
              <span class="pull-right">
                <a v-on:click="modal_open('#loan_type_modal'),loanType = []" onclick="document.getElementById('loan_type_modal_form').reset()" class="btn btn-sm btn-dark btn-gradient dark"><span class="glyphicons glyphicon-pencil"></span> &nbsp; Add Loan Type</a>
              </span>
            @endif
        </div>
    </div>
    <div class="panel-body pn">
        <table class="table table-striped table-hover" id="datatableCall" cellspacing="0" width="100%">
            <thead>
            <tr class="bg-dark">
                <th>SL:</th>
                <th>Type Name</th>
                <th>Remarks</th>
                <th>Status</th>
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
                <th>Type Name</th>
                <th>Remarks</th>
                <th>Status</th>
                <th>Created By</th>
                <th>Updated By</th>
                <th>Created Date</th>
                <th>Updated Date</th>
                <th>Action</th>
            </tr>
            </tfoot>
            <tbody>
                <tr v-for="(loanType,index) in loanTypes">
                   <td v-text="index+1"></td>
                   <td v-text="loanType.loan_type_name"></td>
                   <td v-text="loanType.loan_type_remarks"></td>
                   <td>
                      @if(in_array($chkUrl."/edit", session('userMenuShare')))
                      <div class="btn-group pt5">
                         <a class="btn btn-sm" :class="(loanType.loan_type_status == 0)?'text-primary':'text-danger'" v-on:click="changeStatus(loanType.id,index,loanType.loan_type_status)" v-text="(loanType.loan_type_status == 0)?'Active':'Inactive'"></a>
                      </div>
                      @endif
                   </td>
                   <td v-html="getFullName(loanType.created_by)"></td>
                   <td v-html="getFullName(loanType.updated_by)"></td>
                   <td v-text="loanType.created_at"></td>
                   <td v-text="loanType.updated_at"></td>
                   <td>
                      @if(in_array($chkUrl."/edit", session('userMenuShare')))
                        <div class="btn-group">
                           <a v-on:click="editLoanType(loanType.id, index, '#loan_type_modal'),loanType=[]" class="btn btn-sm btn-primary"><i class="glyphicons glyphicons-pencil"></i>
                           </a>
                        </div>
                      @endif
                      @if(in_array($chkUrl."/delete", session('userMenuShare')))
                        <div class="btn-group">
                           <a v-on:click="deleteLoanType(loanType.id,index)" class="btn btn-sm btn-danger">
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

<div id="loan_type_modal" class="popup-basic mfp-with-anim mfp-hide">
    <div class="panel">
        <div class="panel-heading">
            <span class="panel-title" v-if="loanType !=''">
                <i class="fa fa-rocket"></i>Edit Loan Type
            </span>
            <span class="panel-title" v-else>
                <i class="fa fa-rocket"></i>Add Loan Type
            </span>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    <form v-if="loanType ==''" id="loan_type_modal_form" method="post" v-on:submit.prevent="addLoanType">
                        <div class="row">
                          <div class="col-md-12">
                              <div class="form-group" :class="{'has-error':errors.loan_type_name}">
                                  <label class="control-label">Loan Type Name : <span class="text-danger">*</span></label>
                                  <input type="text" name="loan_type_name" class="form-control input-sm">
                                  <span v-if="errors.loan_type_name" class="text-danger" v-text="errors.loan_type_name[0]"></span>
                              </div>
                          </div>
                          <div class="col-md-12">
                              <div class="form-group" :class="{'has-error':errors.loan_type_remarks}">
                                  <label class="control-label">Loan Type Remarks: </label>
                                  <textarea name="loan_type_remarks" class="form-control input-sm"></textarea>
                                  <span v-if="errors.loan_type_remarks" class="text-danger" v-text="errors.loan_type_remarks[0]"></span>
                              </div>
                          </div>
                        </div>

                        <hr class="short alt">

                        <div class="section row mbn">
                            <div class="col-sm-6 pull-right">
                                <p class="text-left">
                                    <button type="submit" name="add_loan_type" class="btn btn-dark btn-gradient dark btn-block"><span class="glyphicons glyphicons-ok_2"></span> &nbsp; Add Loan Type
                                    </button>
                                </p>
                            </div>
                        </div>
                    </form>


                    <form v-else id="loan_type_modal_form" method="post" v-on:submit.prevent="updateLoanType">
                        <input v-if="loanType.id" type="hidden" name="id" :value="loanType.id">

                        <div class="row">
                          <div class="col-md-12">
                              <div class="form-group" :class="{'has-error':errors.loan_type_name}">
                                  <label class="control-label">Loan Type Name : <span class="text-danger">*</span></label>
                                  <input type="text" name="loan_type_name" :value="loanType.loan_type_name" class="form-control input-sm">
                                  <span v-if="errors.loan_type_name" class="text-danger" v-text="errors.loan_type_name[0]"></span>
                              </div>
                          </div>
                          <div class="col-md-12">
                              <div class="form-group" :class="{'has-error':errors.loan_type_remarks}">
                                  <label class="control-label">Loan Type Remarks: </label>
                                  <textarea name="loan_type_remarks" class="form-control input-sm" v-text="loanType.loan_type_remarks"></textarea>
                                  <span v-if="errors.loan_type_remarks" class="text-danger" v-text="errors.loan_type_remarks[0]"></span>
                              </div>
                          </div>
                        </div>

                        <hr class="short alt">

                        <div class="section row mbn">
                            <div class="col-sm-6 pull-right">
                                <p class="text-left">
                                    <button type="submit" name="add_loan_type" class="btn btn-dark btn-gradient dark btn-block"><span class="glyphicons glyphicons-ok_2"></span> &nbsp; Update Loan Type
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

<script type="text/javascript" src="{{asset('js/loanType.js')}}"></script>

@endsection

@endsection