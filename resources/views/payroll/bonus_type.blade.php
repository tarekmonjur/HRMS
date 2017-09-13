@extends('layouts.hrms')
@section('content')

<section class="p10" id="bonus_type">
<div class="panel">
    <div class="panel-heading">
        <div class="panel-title">
            <span class="glyphicon glyphicon-tasks"></span>Bonus Type

            <?php 
              $chkUrl = \Request::segment(1);
            ?>
            @if(in_array($chkUrl."/add", session('userMenuShare')))
              <span class="pull-right">
                <a v-on:click="modal_open('#bonus_type_modal'),bonusType = []" onclick="document.getElementById('bonus_type_modal_form').reset()" class="btn btn-sm btn-dark btn-gradient dark"><span class="glyphicons glyphicon-pencil"></span> &nbsp; Add Bonus Type</a>
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
            <tbody v-if="bonusTypes !=''">
                <tr v-for="(bonusType,index) in bonusTypes">
                   <td v-text="index+1"></td>
                   <td v-text="bonusType.bonus_type_name"></td>
                   <td v-text="bonusType.bonus_type_remarks"></td>
                   <td>
                      @if(in_array($chkUrl."/edit", session('userMenuShare')))
                      <div class="btn-group pt5">
                         <a class="btn btn-sm" :class="(bonusType.bonus_type_status == 0)?'text-primary':'text-danger'" v-on:click.prevent="changeStatus(bonusType.id,index,bonusType.bonus_type_status)" v-text="(bonusType.bonus_type_status == 0)?'Active':'Inactive'"></a>
                      </div>
                      @endif
                   </td>
                   <td v-html="getFullName(bonusType.created_by)"></td>
                   <td v-html="getFullName(bonusType.updated_by)"></td>
                   <td v-text="bonusType.created_at"></td>
                   <td v-text="bonusType.updated_at"></td>
                   <td>
                      @if(in_array($chkUrl."/edit", session('userMenuShare')))
                        <div class="btn-group">
                           <a v-on:click="editBonusType(bonusType.id, index, '#bonus_type_modal'),bonusType=[]" class="btn btn-sm btn-primary"><i class="glyphicons glyphicons-pencil"></i>
                           </a>
                        </div>
                      @endif
                      @if(in_array($chkUrl."/delete", session('userMenuShare')))
                        <div class="btn-group">
                           <a v-on:click="deleteBonusType(bonusType.id,index)" class="btn btn-sm btn-danger">
                               <i class="glyphicons glyphicons-bin"></i>
                           </a>
                        </div>
                      @endif
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

<div id="bonus_type_modal" class="popup-basic mfp-with-anim mfp-hide">
    <div class="panel">
        <div class="panel-heading">
            <span class="panel-title" v-if="bonusType !=''">
                <i class="fa fa-rocket"></i>Edit Bonus Type
            </span>
            <span class="panel-title" v-else>
                <i class="fa fa-rocket"></i>Add Bonus Type
            </span>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    <form v-if="bonusType ==''" id="bonus_type_modal_form" method="post" v-on:submit.prevent="addBonusType">
                        <div class="row">
                          <div class="col-md-12">
                              <div class="form-group" :class="{'has-error':errors.bonus_type_name}">
                                  <label class="control-label">Bonus Type Name : <span class="text-danger">*</span></label>
                                  <input type="text" name="bonus_type_name" class="form-control input-sm">
                                  <span v-if="errors.bonus_type_name" class="text-danger" v-text="errors.bonus_type_name[0]"></span>
                              </div>
                          </div>
                          <div class="col-md-12">
                              <div class="form-group" :class="{'has-error':errors.bonus_type_remarks}">
                                  <label class="control-label">Bonus Type Remarks: </label>
                                  <textarea name="bonus_type_remarks" class="form-control input-sm"></textarea>
                                  <span v-if="errors.bonus_type_remarks" class="text-danger" v-text="errors.bonus_type_remarks[0]"></span>
                              </div>
                          </div>
                        </div>

                        <hr class="short alt">

                        <div class="section row mbn">
                            <div class="col-sm-6 pull-right">
                                <p class="text-left">
                                    <button type="submit" name="add_bonus_type" class="btn btn-dark btn-gradient dark btn-block"><span class="glyphicons glyphicons-ok_2"></span> &nbsp; Add Bonus Type
                                    </button>
                                </p>
                            </div>
                        </div>
                    </form>


                    <form v-else id="bonus_type_modal_form" method="post" v-on:submit.prevent="updateBonusType">
                        <input v-if="bonusType.id" type="hidden" name="id" :value="bonusType.id">

                        <div class="row">
                          <div class="col-md-12">
                              <div class="form-group" :class="{'has-error':errors.bonus_type_name}">
                                  <label class="control-label">Bonus Type Name : <span class="text-danger">*</span></label>
                                  <input type="text" name="bonus_type_name" :value="bonusType.bonus_type_name" class="form-control input-sm">
                                  <span v-if="errors.bonus_type_name" class="text-danger" v-text="errors.bonus_type_name[0]"></span>
                              </div>
                          </div>
                          <div class="col-md-12">
                              <div class="form-group" :class="{'has-error':errors.bonus_type_remarks}">
                                  <label class="control-label">Bonus Type Remarks: </label>
                                  <textarea name="bonus_type_remarks" class="form-control input-sm" v-text="bonusType.bonus_type_remarks"></textarea>
                                  <span v-if="errors.bonus_type_remarks" class="text-danger" v-text="errors.bonus_type_remarks[0]"></span>
                              </div>
                          </div>
                        </div>

                        <hr class="short alt">

                        <div class="section row mbn">
                            <div class="col-sm-6 pull-right">
                                <p class="text-left">
                                    <button type="submit" name="add_bonus_type" class="btn btn-dark btn-gradient dark btn-block"><span class="glyphicons glyphicons-ok_2"></span> &nbsp; Update Bonus Type
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

<script type="text/javascript" src="{{asset('js/bonusType.js')}}"></script>

@endsection

@endsection