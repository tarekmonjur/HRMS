@extends('layouts.setup')

@section('style')
    
@endsection

@section('content')

<div id="mainDiv">
    
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel">
                    <div class="panel-heading">
                        <span class="panel-title">All Menus</span>
                        <button type="button" class="btn btn-xs btn-success pull-right" data-toggle="modal" data-target=".dataAdd" style="margin-top: 12px;">Add New Menu</button>
                    </div>
                    <div class="panel-body">
                        <div id="showData">
                            <table class="table table-hover" id="datatable">
                                <thead>
                                    <tr class="success">
                                        <th>sl</th>
                                        <th>Menu Name</th>
                                        <th>Parent Name</th>
                                        <th>Module Name</th>
                                        <th>Menu Url</th>
                                        <th>Menu Section Name</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(info,index) in menus">
                                        <td v-text="index+1"></td>
                                        <td>
                                            <span class="text-success">
                                                <i :class="info.menu_icon_class" aria-hidden="true"></i>     
                                            </span>
                                            @{{info.menu_name}}
                                        </td>
                                        <td>
                                            <span v-if="info.parent" v-text="info.parent.menu_name"></span>
                                            <span v-else>...</span>
                                        </td>
                                        <td>
                                            <span v-if="info.module" v-text="info.module.module_name"></span>
                                            <span v-else>...</span>
                                        </td>
                                        <td v-text="info.menu_url"></td>
                                        <td v-text="info.menu_section_name"></td>
                                        <td v-text="info.menu_status==1?'Active':'Inactive'"></td>
                                        <td>
                                            <button type="button" @click="editData(info.id, index)" class="btn btn-sm btn-primary edit-btn" data-toggle="modal" data-target=".dataEdit">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                            <button type="button" @click="deleteData(info.id, index)" class="btn btn-sm btn-danger">
                                                <i class="fa fa-trash-o"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End: Content -->   

    <!-- Add modal start -->
    <div class="modal fade bs-example-modal-lg dataAdd" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" id="modalDataAdd">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Add New Data</h4>
              </div>
              <form class="form-horizontal" @submit.prevent="saveData('addFormData')" id="addFormData">
                <div class="modal-body">

                    <div id="create-form-errors">
                    </div>

                    {{ csrf_field() }}

                    <div class="form-group">
                        <label for="menu_name" class="col-md-3 control-label">Name <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <input name="menu_name" class="form-control input-sm" v-model="menu_name" type="text" placeholder="Menu name">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="module_id" class="col-md-3 control-label">Module Name <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <select class="form-control input-sm" name="module_id" v-model="module_id">
                                <option value="">Select Module</option>
                                <option v-for="info in activeModules" v-bind:value="info.id"> 
                                    @{{ info.module_name }} 
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="chk_parent" class="col-md-3 control-label">
                        </label>
                        <div class="col-md-9">
                            <div class="checkbox-custom mb5">
                                <input type="checkbox" @click="chk_parent==1?chk_parent=0:chk_parent=1" id="chk_parent" name="chk_parent" value="1">
                                <label for="chk_parent"> If this menu have parent</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group" v-show="chk_parent == 1">
                        <label for="amount" class="col-md-3 control-label">Parent's Name <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <select class="form-control input-sm" name="menu_parent_id" v-model="menu_parent_id">
                                <option value="">Select Menu's Parent</option>
                                <option v-for="info in activeMenus" v-bind:value="info.id" v-show="info.menu_parent_id == 0"> @{{info.menu_name+'-'+info.menu_section_name}} </option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="menu_url" class="col-md-3 control-label">Menu Url <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <input name="menu_url" class="form-control input-sm" v-model="menu_url" type="text" placeholder="Menu url">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="menu_section_name" class="col-md-3 control-label">Section Name <span class="text-danger" v-show="chk_parent == 0">*</span></label>
                        <div class="col-md-9">
                            <input name="menu_section_name" class="form-control input-sm" v-model="menu_section_name" type="text" placeholder="Menu section name">
                        </div>
                    </div>

                    <div class="form-group" v-show="chk_parent == 0">
                        <label for="menu_icon_class" class="col-md-3 control-label">Font Awesome</label>
                        <div class="col-md-9">
                            <input name="menu_icon_class" class="form-control input-sm" v-model="menu_icon_class" type="text" placeholder="Only class name. Ex: fa fa-user-o">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="amount" class="col-md-3 control-label"></label>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="radio-custom radio-success mb5">
                                        <input type="radio" name="menu_status" id="active" v-model="menu_status" value="1">
                                        <label for="active">Active</label>
                                    </div>    
                                </div>
                                <div class="col-md-4">
                                    <div class="radio-custom radio-danger mb5">
                                        <input type="radio" name="menu_status" id="inactive" v-model="menu_status" value="0">
                                        <label for="inactive">Inactive</label>
                                    </div>    
                                </div>
                            </div>     
                        </div>
                    </div>
                </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default modal-close-btn" id="modal-close-btn" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save Data</button>
              </div>

              </form>
            </div>
        </div>
    </div>
    <!-- Add modal end --> 

    <!-- salary Info Edit modal start -->
    <div class="modal fade bs-example-modal-lg dataEdit" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" id="modalDataEdit">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Edit Data</h4>
              </div>
              <form class="form-horizontal department-create" @submit.prevent="updateData('updateFormData')" id="updateFormData">
                
                <div class="modal-body">

                    <div id="edit-form-errors">
                    </div>

                    {{ csrf_field() }}

                    <input type="hidden" name="hdn_id" v-model="hdn_id">

                    <div class="form-group">
                        <label for="edit_menu_name" class="col-md-3 control-label">Name <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <input name="edit_menu_name" class="form-control input-sm" v-model="edit_menu_name" type="text" placeholder="Menu name">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="edit_module_id" class="col-md-3 control-label">Module Name <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <select class="form-control input-sm" name="edit_module_id" v-model="edit_module_id">
                                <option value="">Select Module</option>
                                <option v-for="info in activeModules" v-bind:value="info.id"> 
                                    @{{ info.module_name }} 
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="amount" class="col-md-3 control-label">
                        </label>
                        <div class="col-md-9">
                            <div class="checkbox-custom mb5">
                                <input type="checkbox" @click="chk_parent==1?chk_parent=0:chk_parent=1" id="chk_parent" :checked="0" name="chk_parent" v-model="chk_parent" value="1">
                                <label for="chk_parent"> If this menu have parent</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group" v-show="chk_parent == 1">
                        <label for="amount" class="col-md-3 control-label">Parent's Name <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <select class="form-control input-sm" name="edit_menu_parent_id" v-model="edit_menu_parent_id">
                                <option value="">Select Menu's Parent</option>
                                <option v-for="info in activeMenus" v-bind:value="info.id"> @{{info.menu_name}} </option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="edit_menu_url" class="col-md-3 control-label">Menu Url <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <input name="edit_menu_url" class="form-control input-sm" v-model="edit_menu_url" type="text" placeholder="Menu url">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="edit_menu_section_name" class="col-md-3 control-label">Section Name <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <input name="edit_menu_section_name" class="form-control input-sm" v-model="edit_menu_section_name" type="text" placeholder="Menu section name">
                        </div>
                    </div>

                    <div class="form-group" v-show="chk_parent == 0">
                        <label for="edit_menu_icon_class" class="col-md-3 control-label">Font Awesome</label>
                        <div class="col-md-9">
                            <input name="edit_menu_icon_class" class="form-control input-sm" v-model="edit_menu_icon_class" type="text" placeholder="Only class name. Ex: fa fa-user-o">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="amount" class="col-md-3 control-label"></label>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="radio-custom radio-success mb5">
                                        <input type="radio" name="edit_menu_status" id="edit_active" v-model="edit_menu_status" value="1">
                                        <label for="edit_active">Active</label>
                                    </div>    
                                </div>
                                <div class="col-md-4">
                                    <div class="radio-custom radio-danger mb5">
                                        <input type="radio" name="edit_menu_status" id="edit_inactive" v-model="edit_menu_status" value="0">
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
                    Update Data
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

<script src="{{asset('js/setupMenu.js')}}"></script>

@endsection