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

<section class="p10" id="providentFund">
<div class="panel">
    <div class="panel-heading">
        <div class="panel-title">
            <span class="glyphicon glyphicon-tasks"></span>Provident Fund
            <span class="pull-right">
              <a v-on:click="modal_open('#provident_fund_modal'), providentFund = []" onclick="document.getElementById('provident_fund_modal_form').reset()" class="btn btn-sm btn-dark btn-gradient dark"><span class="glyphicons glyphicon-pencil"></span> &nbsp; Add Provident Fund</a>
            </span>
        </div>
    </div>
    <div class="panel-body pn">
        <table class="table table-striped table-hover" id="datatableCall" cellspacing="0" width="100%">
            <thead>
            <tr class="bg-dark">
                <th>SL:</th>
                <th>Employee Name</th>
                <th>Percentage of Amount</th>
                <th>Effective Date</th>
                <th>Interest Calculate</th>
                <th>Status</th>
                <th>Approved By</th>
                <th>Created By</th>
                <th>Updated By</th>
                <th>Action</th>
            </tr>
            </thead>
            <tfoot>
            <tr class="bg-dark" style="background: #f2f2f2!important">
                <th>SL:</th>
                <th>Employee Name</th>
                <th>Percentage of Amount</th>
                <th>Effective Date</th>
                <th>Interest Calculate</th>
                <th>Status</th>
                <th>Approved By</th>
                <th>Created By</th>
                <th>Updated By</th>
                <th>Action</th>
            </tr>
            </tfoot>
            <tbody>
                <tr v-for="(provident,index) in providentFunds">
                   <td v-text="index+1"></td>
                   <td v-html="getFullName(provident.user)"></td>
                   <td v-text="provident.pf_percent_amount"></td>
                   <td v-text="provident.pf_effective_date"></td>
                   <td v-text="provident.pf_interest_calculate"></td>
                   <td>
                      <div class="btn-group pt5">
                         <a class="btn btn-sm" :class="(provident.pf_status == 0)?'text-primary':'text-danger'" v-on:click="changeStatus($event,provident.id)" :status="provident.pf_status" v-text="(provident.pf_status == 0)?'Active':'Inactive'"></a>
                      </div>
                   </td>
                   <td v-if="provident.approved_by">
                     <span v-html="getFullName(provident.approved_by)"></span><br>
                     <span class="text-success">Approved</span>
                   </td>
                   <td v-else>
                     <a class="btn btn-sm text-warning" v-on:click="approvedProvidentFund(provident.id, index)">Approved</a>
                   </td>
                   <td>
                     <span v-html="getFullName(provident.created_by)"></span><br>
                     <span v-text="provident.created_at"></span>
                   </td>
                   <td>
                     <div v-if="provident.updated_by">
                       <span v-html="getFullName(provident.updated_by)"></span><br>
                       <span v-text="provident.updated_at"></span>
                     </div>
                   </td>
                   <td>
                      <div class="btn-group">
                        <a v-on:click="editProvidentFund(provident.id, index, '#provident_fund_modal'),providentFund=[]" class="btn btn-xs btn-primary"><i class="glyphicons glyphicons-pencil"></i>
                        </a>
                      </div>

                      <div class="btn-group"><button class="btn btn-xs btn-success" v-on:click.prevent="showDetails(provident.id, '#provident_fund_details'), user_name = getFullName(provident.user)"><i class="fa fa-eye"></i></button></div>

                      <div v-if="!provident.approved_by" class="btn-group">
                        <a v-on:click="deleteProvidentFund(provident.id,index)" class="btn btn-xs btn-danger">
                          <i class="glyphicons glyphicons-bin"></i>
                        </a>
                      </div>

                   </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div id="provident_fund_modal" class="popup-basic mfp-with-anim mfp-hide">
    <div class="panel">
        <div class="panel-heading">
            <span class="panel-title" v-if="providentFund !=''">
                <i class="fa fa-rocket"></i>Edit Provident Fund
            </span>
            <span class="panel-title" v-else>
                <i class="fa fa-rocket"></i>Add Provident Fund
            </span>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">

                    <form v-if="providentFund ==''" id="provident_fund_modal_form" method="post" v-on:submit.prevent="addProvidentFund">
                      <div class="row">
                        <div class="col-md-12">
                          <div class="form-group" :class="{'has-error': errors.user_id}">
                              <label class="control-label">Employee Name : <span class="text-danger">*</span></label>
                              <select2 class="form-control select-sm input-sm" v-model="user_id" name="user_id">
                                  <option :value="''">...All Employee...</option>
                                  <option v-for="(user,index) in users" :value="user.id" v-text="user.fullname+' - ('+user.designation_name+' ) - ( '+user.level_name+' )'"></option>
                              </select2>
                              <span v-if="errors.user_id" class="help-block" v-text="errors.user_id[0]"></span>
                              <span v-if="errors.has_fund" class="text-danger" v-text="errors.has_fund[0]"></span>
                          </div>
                        </div>
  
                        <div class="col-md-6">
                          <div class="form-group" :class="{'has-error': errors.pf_effective_date}">
                              <label class="control-label">Effective Date: <span class="text-danger">*</span></label>
                              <input type="text" name="pf_effective_date" v-on:mouseover="myDatePicker" class="myDatePicker form-control input-sm" placeholder="Effective Date" readonly="readonly">
                              <span v-if="errors.pf_effective_date" class="help-block" v-text="errors.pf_effective_date[0]"></span>
                          </div>
                        </div>

                        <div class="col-md-6">
                          <div class="form-group" :class="{'has-error': errors.pf_interest_calculate}">
                              <label class="control-label">Interest Calculate: <span class="text-danger">*</span></label>
                              <select class="form-control select-sm input-sm" name="pf_interest_calculate">
                                  <option :value="''">...Select Interest Calculate...</option>
                                  <option value="monthly">Monthly</option>
                                  <option value="yearly">Yearly</option>
                              </select>
                              <span v-if="errors.pf_interest_calculate" class="help-block" v-text="errors.pf_interest_calculate[0]"></span>
                          </div>
                        </div>

                        <div class="col-md-12">
                          <div class="form-group" :class="{'has-error': errors.pf_percent_amount}">
                              <label class="control-label">Percentage of Amount: <span class="text-danger">*</span></label>
                              <input type="text" name="pf_percent_amount" class="form-control input-sm" placeholder="Percentage of Amount">
                              <span v-if="errors.pf_percent_amount" class="help-block" v-text="errors.pf_percent_amount[0]"></span>
                          </div>
                        </div>
                      </div>

                      <hr class="short alt">

                      <div class="section row mbn">
                          <div class="col-sm-6 pull-right">
                              <p class="text-left">
                                  <button type="submit" :disabled="hasFund" name="add_provident_fund" class="btn btn-dark btn-gradient dark btn-block"><span class="glyphicons glyphicons-ok_2"></span> &nbsp; Add Provident Fund
                                  </button>
                              </p>
                          </div>
                      </div>
                    </form>


                    <form v-else id="provident_fund_modal_form" method="post" v-on:submit.prevent="updateProvidentFund">
                      <input v-if="providentFund.id" type="hidden" name="id" :value="providentFund.id">

                      <div class="row">
                        <div class="col-md-12">
                          <div class="form-group" :class="{'has-error': errors.user_id}">
                              <label class="control-label">Employee Name : <span class="text-danger">*</span></label>
                              <select2 class="form-control select-sm input-sm" name="user_id" v-model="user_id">
                                  <option :value="0">...All Employee...</option>
                                  <option v-for="(user,index) in users" :value="user.id" v-text="user.fullname+' - ('+user.designation_name+' ) - ( '+user.level_name+' )'"></option>
                              </select2>
                              <span v-if="errors.user_id" class="help-block" v-text="errors.user_id[0]"></span>
                              <span v-if="errors.has_fund" class="text-danger" v-text="errors.has_fund[0]"></span>
                          </div>
                        </div>
  
                        <div class="col-md-6">
                          <div class="form-group" :class="{'has-error': errors.pf_effective_date}">
                              <label class="control-label">Effective Date: <span class="text-danger">*</span></label>
                              <input type="text" name="pf_effective_date" :value="providentFund.pf_effective_date" v-on:mouseover="myDatePicker" class="myDatePicker form-control input-sm" placeholder="Effective Date" readonly="readonly">
                              <span v-if="errors.pf_effective_date" class="help-block" v-text="errors.pf_effective_date[0]"></span>
                          </div>
                        </div>

                        <div class="col-md-6">
                          <div class="form-group" :class="{'has-error': errors.pf_interest_calculate}">
                              <label class="control-label">Interest Calculate: <span class="text-danger">*</span></label>
                              <select class="form-control select-sm input-sm" name="pf_interest_calculate">
                                  <option :value="''">...Select Interest Calculate...</option>
                                  <option value="monthly" :selected="providentFund.pf_interest_calculate == 'monthly'">Monthly</option>
                                  <option value="yearly" :selected="providentFund.pf_interest_calculate == 'yearly'">Yearly</option>
                              </select>
                              <span v-if="errors.pf_interest_calculate" class="help-block" v-text="errors.pf_interest_calculat[0]"></span>
                          </div>
                        </div>

                        <div class="col-md-12">
                          <div class="form-group" :class="{'has-error': errors.pf_percent_amount}">
                              <label class="control-label">Percentage of Amount: <span class="text-danger">*</span></label>
                              <input type="text" name="pf_percent_amount" :value="providentFund.pf_percent_amount" class="form-control input-sm" placeholder="Percentage of Amount">
                              <span v-if="errors.pf_percent_amount" class="help-block" v-text="errors.pf_percent_amount[0]"></span>
                          </div>
                        </div>
                      </div>

                      <hr class="short alt">

                      <div class="section row mbn">
                          <div class="col-sm-6 pull-right">
                              <p class="text-left">
                                  <button type="submit" :disabled="hasFund" name="update_provident_fund" class="btn btn-dark btn-gradient dark btn-block"><span class="glyphicons glyphicons-ok_2"></span> &nbsp; Update Provident Fund
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

<div id="provident_fund_details" class="popup-basic mfp-with-anim mfp-hide" style="max-width: 80%!important">
    <div class="panel">
        <div class="panel-heading">
            <span class="panel-title">
                <i class="fa fa-rocket"></i>Provident Fund Details
                <span v-html="user_name"></span>
            </span>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                  <table class="table table-striped table-hover" id="datatableCall2" cellspacing="0" width="100%">
                    <thead>
                      <tr class="bg-dark">
                        <th>SL:</th>
                        <th>Date</th>
                        <th>Percentage</th>
                        <th>Amount</th>
                        <th>Interest Percentage</th>
                        <th>Interest Amount</th>
                        <th>Debit</th>
                        <th>Credit</th>
                        <th>Remarks</th>
                        </tr>
                    </thead>

                    <tfoot>
                      <tr class="bg-dark" style="background: #f2f2f2!important">
                        <th>SL:</th>
                        <th>Date</th>
                        <th>Percentage</th>
                        <th>Amount</th>
                        <th>Interest Percentage</th>
                        <th>Interest Amount</th>
                        <th>Debit</th>
                        <th>Credit</th>
                        <th>Remarks</th>
                      </tr>
                    </tfoot>

                    <tbody>
                      <tr v-for="(pf_details, index) in providentfundDetails">
                        <td v-text="index+1"></td>
                        <td v-text="pf_details.pf_date"></td>
                        <td v-text="pf_details.pf_percent"></td>
                        <td v-text="pf_details.pf_amount"></td>
                        <td v-text="pf_details.pf_interest_percent"></td>
                        <td v-text="pf_details.pf_interest_amount"></td>
                        <td v-text="pf_details.pf_debit"></td>
                        <td v-text="pf_details.pf_credit"></td>
                        <td v-text="pf_details.pf_remarks"></td>
                      </tr>
                      <tr>
                        <td v-text="providentfundDetails.length + 1"></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-right">Total</td>
                        <td v-text="total_debit"></td>
                        <td v-text="total_credit"></td>
                        <td></td>
                      </tr>
                    </tbody>
                  </table>
                </div>
            </div>
        </div>
    </div>
</div>


</section>


@section('script')

<script type="text/javascript" src="{{asset('js/providentFund.js')}}"></script>

@endsection

@endsection