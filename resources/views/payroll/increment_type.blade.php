@extends('layouts.hrms')
@section('content')

<section class="p10" id="increment_type">
<div class="panel">
    <div class="panel-heading">
        <div class="panel-title">
            <span class="glyphicon glyphicon-tasks"></span>Increment Type

            <?php 
              $chkUrl = \Request::segment(1);
            ?>
            @if(in_array($chkUrl."/add", session('userMenuShare')))
              <span class="pull-right">
                <a v-on:click="modal_open('#increment_type_modal'),incrementType = []" onclick="document.getElementById('increment_type_modal_form').reset()" class="btn btn-sm btn-dark btn-gradient dark"><span class="glyphicons glyphicon-pencil"></span> &nbsp; Add Increment Type</a>
              </span>
            @endif
        </div>
    </div>
    <div class="panel-body pn">
        <table class="table table-striped table-hover" id="datatableCall" cellspacing="0" width="100%">
            <thead>
            <tr class="bg-dark">
                <th>SL:</th>
                <th>Increment Type Name</th>
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
                <th>Increment Type Name</th>
                <th>Remarks</th>
                <th>Status</th>
                <th>Created By</th>
                <th>Updated By</th>
                <th>Created Date</th>
                <th>Updated Date</th>
                <th>Action</th>
            </tr>
            </tfoot>
            <tbody v-if="incrementTypes !=''">
                <tr v-for="(incrementType,index) in incrementTypes">
                   <td v-text="index+1"></td>
                   <td v-text="incrementType.increment_type_name"></td>
                   <td v-text="incrementType.increment_type_remarks"></td>
                   <td>
                    @if(in_array($chkUrl."/edit", session('userMenuShare')))
                     <div class="btn-group pt5">
                         <a class="btn btn-sm" :class="(incrementType.increment_type_status == 0)?'text-primary':'text-danger'" v-on:click="changeStatus(incrementType.id,index,incrementType.increment_type_status)" v-text="(incrementType.increment_type_status == 0)?'Active':'Inactive'"></a>
                       </div>
                    @endif
                   </td>
                   <td v-html="getFullName(incrementType.created_by)"></td>
                   <td v-html="getFullName(incrementType.updated_by)"></td>
                   <td v-text="incrementType.created_at"></td>
                   <td><span v-if="incrementType.updated_by" v-text="incrementType.updated_at"></span></td>
                   <td>
                      @if(in_array($chkUrl."/edit", session('userMenuShare')))
                      <div class="btn-group">
                           <a v-on:click="editIncrementType(incrementType.id, index, '#increment_type_modal'),incrementType=[]" class="btn btn-sm btn-primary"><i class="glyphicons glyphicons-pencil"></i>
                           </a>
                       </div>
                       @endif
                       @if(in_array($chkUrl."/delete", session('userMenuShare')))
                       <div class="btn-group">
                           <a v-on:click="deleteIncrementType(incrementType.id,index)" class="btn btn-sm btn-danger">
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

<div id="increment_type_modal" class="popup-basic mfp-with-anim mfp-hide">
    <div class="panel">
        <div class="panel-heading">
            <span class="panel-title" v-if="incrementType !=''">
                <i class="fa fa-rocket"></i>Edit Increment Type
            </span>
            <span class="panel-title" v-else>
                <i class="fa fa-rocket"></i>Add Increment Type
            </span>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    <form v-if="incrementType ==''" id="increment_type_modal_form" method="post" v-on:submit.prevent="addIncrementType">
                        <div class="row">
                          <div class="col-md-12">
                              <div class="form-group" :class="{'has-error':errors.increment_type_name}">
                                  <label class="control-label">Increment Type Name : <span class="text-danger">*</span></label>
                                  <input type="text" name="increment_type_name" class="form-control input-sm">
                                  <span v-if="errors.increment_type_name" class="text-danger" v-text="errors.increment_type_name[0]"></span>
                              </div>
                          </div>
                          <div class="col-md-12">
                              <div class="form-group" :class="{'has-error':errors.increment_type_remarks}">
                                  <label class="control-label">Increment Type Remarks: </label>
                                  <textarea name="increment_type_remarks" class="form-control input-sm"></textarea>
                                  <span v-if="errors.increment_type_remarks" class="text-danger" v-text="errors.increment_type_remarks[0]"></span>
                              </div>
                          </div>
                        </div>

                        <hr class="short alt">

                        <div class="section row mbn">
                            <div class="col-sm-6 pull-right">
                                <p class="text-left">
                                    <button type="submit" name="add_increment_type" class="btn btn-dark btn-gradient dark btn-block"><span class="glyphicons glyphicons-ok_2"></span> &nbsp; Add Increment Type
                                    </button>
                                </p>
                            </div>
                        </div>
                    </form>


                    <form v-else id="increment_type_modal_form" method="post" v-on:submit.prevent="updateIncrementType">
                        <input v-if="incrementType.id" type="hidden" name="id" :value="incrementType.id">

                        <div class="row">
                          <div class="col-md-12">
                              <div class="form-group" :class="{'has-error':errors.increment_type_name}">
                                  <label class="control-label">Increment Type Name : <span class="text-danger">*</span></label>
                                  <input type="text" name="increment_type_name" :value="incrementType.increment_type_name" class="form-control input-sm">
                                  <span v-if="errors.increment_type_name" class="text-danger" v-text="errors.increment_type_name[0]"></span>
                              </div>
                          </div>
                          <div class="col-md-12">
                              <div class="form-group" :class="{'has-error':errors.increment_type_remarks}">
                                  <label class="control-label">Increment Type Remarks: </label>
                                  <textarea name="increment_type_remarks" class="form-control input-sm" v-text="incrementType.increment_type_remarks"></textarea>
                                  <span v-if="errors.increment_type_remarks" class="text-danger" v-text="errors.increment_type_remarks[0]"></span>
                              </div>
                          </div>
                        </div>

                        <hr class="short alt">

                        <div class="section row mbn">
                            <div class="col-sm-6 pull-right">
                                <p class="text-left">
                                    <button type="submit" name="add_increment_type" class="btn btn-dark btn-gradient dark btn-block"><span class="glyphicons glyphicons-ok_2"></span> &nbsp; Update Increment Type
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

<script type="text/javascript" src="{{asset('js/incrementType.js')}}"></script>

@endsection

@endsection