@extends('layouts.setup')

@section('content')
<div id="mainDiv">
    
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        All Modules
                        <button type="button" class="btn btn-xs btn-success pull-right" data-toggle="modal" data-target=".dataAdd" style="margin-top: 12px;">Add New Module</button>
                    </div>
                    <div class="panel-body">
                        <table class="table table-hover">
                            <thead>
                                <tr class="success">
                                    <th>sl</th>
                                    <th>Name</th>
                                    <th>Details</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(info,index) in modules">
                                    <td v-text="index+1"></td>
                                    <td>
                                        <span class="text-success">
                                            <i :class="info.module_icon_class" aria-hidden="true"></i>   
                                        </span>
                                        @{{info.module_name}}
                                    </td>
                                    <td v-text="info.module_details"></td>
                                    <td v-text="info.module_status==1?'Active':'Inactive'"></td>
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

    <!-- Add modal start -->
    <div class="modal fade bs-example-modal-lg dataAdd" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" id="modalDataAdd">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Add New Module</h4>
                </div>
                <form class="form-horizontal" @submit.prevent="saveData('addFormData')" id="addFormData">
                    <div class="modal-body">

                        <div id="create-form-errors">
                        </div>

                        {{ csrf_field() }}

                        <div class="form-group">
                            <label for="module_name" class="col-md-3 control-label">Module Name <span class="text-danger">*</span></label>
                            <div class="col-md-9">
                                <input name="module_name" class="form-control input-sm" v-model="module_name"  type="text" placeholder="Module Name">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="module_icon_class" class="col-md-3 control-label">Font Awesome <span class="text-danger">*</span></label>
                            <div class="col-md-9">
                                <input name="module_icon_class" class="form-control input-sm" v-model="module_icon_class" type="text" placeholder="Only class name. Ex: fa fa-user-o">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="module_details" class="col-md-3 control-label">Details</label>
                            <div class="col-md-9">
                                <textarea name="module_details" class="form-control input-sm" v-model="module_details" placeholder="Write details"></textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="" class="col-md-3 control-label"></label>
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="radio-custom radio-success mb5">
                                            <input type="radio" name="module_status" id="active" v-model="module_status" value="1">
                                            <label for="active">Active</label>
                                        </div>    
                                    </div>
                                    <div class="col-md-4">
                                        <div class="radio-custom radio-danger mb5">
                                            <input type="radio" name="module_status" id="inactive" v-model="module_status" value="0">
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
    <!-- Edit modal start -->
    <div class="modal fade bs-example-modal-lg dataEdit" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" id="modalDataEdit">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Edit Module</h4>
                </div>
                
                <form class="form-horizontal" @submit.prevent="updateData('updateFormData')" id="updateFormData">
                    <div class="modal-body">

                        <div id="edit-form-errors">
                        </div>

                        {{ csrf_field() }}
                        <input type="hidden" name="hdn_id" v-model="hdn_id">

                        <div class="form-group">
                            <label for="edit_module_name" class="col-md-3 control-label">Module Name <span class="text-danger">*</span></label>
                            <div class="col-md-9">
                                <input name="edit_module_name" class="form-control input-sm" v-model="edit_module_name"  type="text" placeholder="Module Name">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="edit_module_icon_class" class="col-md-3 control-label">Font Awesome <span class="text-danger">*</span></label>
                            <div class="col-md-9">
                                <input name="edit_module_icon_class" class="form-control input-sm" v-model="edit_module_icon_class" type="text" placeholder="Only class name. Ex: fa fa-user-o">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="edit_module_details" class="col-md-3 control-label">Details</label>
                            <div class="col-md-9">
                                <textarea name="edit_module_details" class="form-control input-sm" v-model="edit_module_details" placeholder="Write details"></textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="" class="col-md-3 control-label"></label>
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="radio-custom radio-success mb5">
                                            <input type="radio" name="edit_module_status" id="edit_active" v-model="edit_module_status" value="1">
                                            <label for="edit_active">Active</label>
                                        </div>    
                                    </div>
                                    <div class="col-md-4">
                                        <div class="radio-custom radio-danger mb5">
                                            <input type="radio" name="edit_module_status" id="edit_inactive" v-model="edit_module_status" value="0">
                                            <label for="edit_inactive">Inactive</label>
                                        </div>    
                                    </div>
                                </div>     
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                    <button type="button" class="btn btn-default modal-close-btn" id="modal-edit-close-btn" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Info</button>
                    </div>
                </form>
              
            </div>
        </div>
    </div>
    <!-- Edit modal end --> 
</div>
@endsection

@section('script')
    <script src="{{asset('js/module.js')}}"></script>
@endsection