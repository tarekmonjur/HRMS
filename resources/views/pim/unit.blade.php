@extends('layouts.hrms')

@section('style')
    
@endsection

@section('content')
<div id="unitDiv">
    <!-- Begin: Content -->
    <section id="content" class="animated fadeIn">
        <div class="row">
            <div class="col-md-12">
                <div class="panel">
                    <div class="panel-heading">
                        <span class="panel-title">All Units</span>
                        <?php 
                          $chkUrl = \Request::segment(1);
                        ?>
                        @if(in_array($chkUrl."/add", session('userMenuShare')))
                            <button type="button" class="btn btn-xs btn-success pull-right" data-toggle="modal" data-target=".unitAdd" style="margin-top: 12px;">Add New Unit</button>
                        @endif
                    </div>
                    <div class="panel-body">
                        <div id="showData">
                            <table class="table table-hover" id="datatable">
                                <thead>
                                    <tr class="success">
                                        <th>sl</th>
                                        <th>Unit Name</th>
                                        <th>Parent Name</th>
                                        <th>Department</th>
                                        <th>Details</th>
                                        <th>Effective Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(info,index) in units">
                                        <td >@{{ index+1 }}</td>
                                        <td>@{{ info.unit_name }}</td>
                                        <td>
                                            <span v-if="info.parent">@{{ info.parent.unit_name }}</span>
                                            <span v-else>...</span>
                                        </td>
                                        <td>@{{ info.department.department_name }}</td>
                                        <td>@{{ info.unit_details }}</td>
                                        <td>@{{ info.unit_effective_date }}</td>
                                        <td>@{{ info.unit_status==1?"Active":"Inactive" }}</td>
                                        <td>
                                        <!-- Checking permission -->
                                        @if(in_array($chkUrl."/edit", session('userMenuShare')))
                                            <button type="button" @click="editUnit(info.id, index)" class="btn btn-sm btn-primary edit-btn" data-toggle="modal" data-target=".unitEdit">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                        @endif
                                        @if(in_array($chkUrl."/delete", session('userMenuShare')))
                                            <button type="button" @click="deleteUnit(info.id, index)" class="btn btn-sm btn-danger">
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

    <!-- unitAdd modal start -->
    <div class="modal fade bs-example-modal-lg unitAdd" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" id="modalUnitAdd">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Add New Unit</h4>
              </div>
              <form class="form-horizontal" @submit.prevent="saveUnit('addUnitFormData')" id="addUnitFormData">
                <div class="modal-body">

                    <div id="create-form-errors">
                    </div>

                    {{ csrf_field() }}

                    <div class="form-group">
                        <label for="name" class="col-md-3 control-label">Unit Name <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <input name="unit_name" class="form-control input-sm" v-model="unit_name" v-validate:unit_name.initial="'required'" :class="{'input': true, 'is-danger': errors.has('unit_name') }" data-vv-as="unit name" type="text" placeholder="Unit name">
                            <div v-show="errors.has('unit_name')" class="help text-danger">
                                <i v-show="errors.has('unit_name')" class="fa fa-warning"></i> 
                                @{{ errors.first('unit_name') }}
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="unit_department_id" class="col-md-3 control-label">Department Name <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <select class="form-control input-sm" name="unit_department_id" v-model="unit_department_id" v-validate:unit_department_id.initial="'required'" :class="{'input': true, 'is-danger': errors.has('unit_department_id') }" data-vv-as="unit department">
                                <option value="">Select Department</option>
                                <option v-for="department in departments" v-bind:value="department.id"> 
                                    @{{ department.department_name }} 
                                </option>
                            </select>
                            <div v-show="errors.has('unit_department_id')" class="help text-danger">
                                <i v-show="errors.has('unit_department_id')" class="fa fa-warning"></i> 
                                @{{ errors.first('unit_department_id') }}
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="amount" class="col-md-3 control-label">
                        </label>
                        <div class="col-md-9">
                            <div class="checkbox-custom mb5">
                                <input type="checkbox" @click="chk_parent==1?chk_parent=0:chk_parent=1" id="checkboxDefault3" name="chk_parent" value="1">
                                <label for="checkboxDefault3"> If this unit have parent</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group" v-show="chk_parent == 1">
                        <label for="amount" class="col-md-3 control-label">Parent Name <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <select class="form-control input-sm" name="unit_parent_id" v-model="unit_parent_id" v-validate:unit_parent_id.initial="'required'" :class="{'input': true, 'is-danger': errors.has('unit_parent_id') }" data-vv-as="unit parent">
                                <option value="">Select Unit's Parent</option>
                                <option v-for="unit in activeUnits" v-bind:value="unit.id"> @{{unit.unit_name}} </option>
                            </select>
                            <div v-show="errors.has('unit_parent_id')" class="help text-danger">
                                <i v-show="errors.has('unit_parent_id')" class="fa fa-warning"></i> 
                                @{{ errors.first('unit_parent_id') }}
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="unit_details" class="col-md-3 control-label">Unit Details</label>
                        <div class="col-md-9">
                            <textarea name="unit_details" class="form-control input-sm" v-model="unit_details" data-vv-as="details" placeholder="Unit details"></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="unit_effective_date" class="col-md-3 control-label">Effective Date </label>
                        <div class="col-md-9">
                            <input type="text" name="unit_effective_date" class="gui-input datepicker form-control input-sm edit_effective_date" placeholder="Select Effective Date">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="amount" class="col-md-3 control-label"></label>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="radio-custom radio-success mb5">
                                        <input type="radio" name="unit_status" id="active" v-model="unit_status" value="1">
                                        <label for="active">Active</label>
                                    </div>    
                                </div>
                                <div class="col-md-4">
                                    <div class="radio-custom radio-danger mb5">
                                        <input type="radio" name="unit_status" id="inactive" v-model="unit_status" value="0">
                                        <label for="inactive">Inactive</label>
                                    </div>    
                                </div>
                            </div>     
                        </div>
                    </div>
                </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default modal-close-btn" id="modal-close-btn" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save Unit</button>
              </div>

              </form>
            </div>
        </div>
    </div>
    <!-- unitAdd modal end --> 

    <!-- salary Info Edit modal start -->
    <div class="modal fade bs-example-modal-lg unitEdit" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" id="modalUnitEdit">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Edit Unit</h4>
              </div>
              <form class="form-horizontal department-create" @submit.prevent="updateUnit('updateUnitFormData')" id="updateUnitFormData">
                
                <div class="modal-body">

                    <div id="edit-form-errors">
                    </div>

                    {{ csrf_field() }}

                    <input type="hidden" name="hdn_id" data-vv-as="id" v-model="hdn_id">

                    <div class="form-group">
                        <label for="edit_unit_name" class="col-md-3 control-label">Unit Name</label>
                        <div class="col-md-9">
                            <input name="edit_unit_name" class="form-control input-sm" v-model="edit_unit_name" v-validate:edit_unit_name.initial="'required'" :class="{'input': true, 'is-danger': errors.has('edit_unit_name') }" data-vv-as="unit name" type="text" placeholder="Unit name">
                            <div v-show="errors.has('edit_unit_name')" class="help text-danger">
                                <i v-show="errors.has('edit_unit_name')" class="fa fa-warning"></i> 
                                @{{ errors.first('edit_unit_name') }}
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="edit_unit_department_id" class="col-md-3 control-label">Department Name</label>
                        <div class="col-md-9">
                            <select class="form-control input-sm" name="edit_unit_department_id" v-model="edit_unit_department_id" v-validate:edit_unit_department_id.initial="'required'" :class="{'input': true, 'is-danger': errors.has('edit_unit_department_id') }" data-vv-as="unit department">
                                <option value="">Select Department</option>
                                <option v-for="department in departments" v-bind:value="department.id"> 
                                    @{{ department.department_name }} 
                                </option>
                            </select>
                            <div v-show="errors.has('edit_unit_department_id')" class="help text-danger">
                                <i v-show="errors.has('edit_unit_department_id')" class="fa fa-warning"></i> 
                                @{{ errors.first('edit_unit_department_id') }}
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="amount" class="col-md-3 control-label">
                        </label>
                        <div class="col-md-9">
                            <div class="checkbox-custom mb5">
                                <input type="checkbox" @click="chk_parent==1?chk_parent=0:chk_parent=1" id="checkboxDefault3" :checked="0" name="chk_parent" v-model="chk_parent" value="1">
                                <label for="checkboxDefault3"> If this unit have parent</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group" v-show="chk_parent == 1">
                        <label for="edit_unit_parent_id" class="col-md-3 control-label">Parent Name</label>
                        <div class="col-md-9">
                            <select class="form-control input-sm" name="edit_unit_parent_id" v-model="edit_unit_parent_id" v-validate:edit_unit_parent_id.initial="'required'" :class="{'input': true, 'is-danger': errors.has('edit_unit_parent_id') }" data-vv-as="unit parent">
                                <option value="">Select Unit's Parent</option>
                                <option v-for="unit in activeUnits" v-if="unit.id != hdn_id" v-bind:value="unit.id"> @{{unit.unit_name}} </option>
                            </select>
                            <div v-show="errors.has('edit_unit_parent_id')" class="help text-danger">
                                <i v-show="errors.has('edit_unit_parent_id')" class="fa fa-warning"></i> 
                                @{{ errors.first('edit_unit_parent_id') }}
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="edit_unit_details" class="col-md-3 control-label">Unit Details</label>
                        <div class="col-md-9">
                            <textarea name="edit_unit_details" class="form-control input-sm" v-model="edit_unit_details" data-vv-as="details" placeholder="Unit details"></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="edit_unit_effective_date" class="col-md-3 control-label">Effective Date </label>
                        <div class="col-md-9">
                            <input type="text" name="edit_unit_effective_date" class="gui-input datepicker form-control input-sm" v-model="edit_unit_effective_date" placeholder="Select Effective Date">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="amount" class="col-md-3 control-label"></label>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="radio-custom radio-success mb5">
                                        <input type="radio" name="edit_unit_status" id="edit_active" v-model="edit_unit_status" value="1">
                                        <label for="edit_active">Active</label>
                                    </div>    
                                </div>
                                <div class="col-md-4">
                                    <div class="radio-custom radio-danger mb5">
                                        <input type="radio" name="edit_unit_status" id="edit_inactive" v-model="edit_unit_status" value="0">
                                        <label for="edit_inactive">Inactive</label>
                                    </div>    
                                </div>
                            </div>     
                        </div>
                    </div>
                </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default modal-edit-close-btn" id="modal-edit-close-btn" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">
                    Update Unit
                </button>
              </div>

              </form>
            </div>
        </div>
    </div>
    <!-- salary Info Edit modal end --> 
</div>
@endsection

@section('script')

<script src="{{asset('js/unit.js')}}"></script>

@endsection