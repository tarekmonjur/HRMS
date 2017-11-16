@extends('layouts.hrms')

@section('style')
    
@endsection

@section('content')
<div id="mainDiv">
    <!-- Begin: Content -->
    <section id="content" class="animated fadeIn">
        <div class="row">
            <div class="col-md-12">
                <div class="panel">
                    <div class="panel-heading">
                        <span class="panel-title">Weekends</span>
                        <?php 
                          $chkUrl = \Request::segment(1);
                        ?>
                        @if(in_array($chkUrl."/add", session('userMenuShare')))
                            <button type="button" class="btn btn-xs btn-success pull-right" data-toggle="modal" data-target=".dataAdd" style="margin-top: 12px;">Add New Weekend</button>
                        @endif
                    </div>
                    <div class="panel-body">
                        <span class="text-danger">
                            <b>** Only active one is cosider as weekend.</b>
                        </span>
                        <div id="showData">
                            <table class="table table-hover" id="datatable">
                                <thead>
                                    <tr class="success">
                                        <th>sl</th>
                                        <th>Weekend</th>
                                        <th>From Date</th>
                                        <th>To Date</th>
                                        <th>Status</th>
                                        <th>Created Time</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(info,index) in weekends">
                                        <td v-text="index+1"></td>
                                        <td v-text="info.weekend"></td>
                                        <td v-text="info.weekend_from"></td>
                                        <td v-text="info.weekend_to"></td>
                                        <td v-text="info.status==1?'Active':'Inactive'"></td>
                                        <td v-text="info.created_at"></td>
                                        <td>
                                            @if(in_array($chkUrl."/delete", session('userMenuShare')))
                                                <button type="button" v-if="info.status==0" @click="deleteData(info.id, index)" class="btn btn-sm btn-danger">
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
    <div class="modal fade bs-example-modal-lg dataAdd" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" id="modalDataAdd">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Add Weekend</h4>
                </div>
                <form class="form-horizontal" @submit.prevent="saveData('addFormData')" id="addFormData">
                    <div class="modal-body">
                        <span class="text-danger">
                            <b>
                                ** Inserted data can not be changed.
                            </b>
                        </span>

                        <div id="create-form-errors">
                        </div>

                        {{ csrf_field() }}

                        <div class="form-group" style="margin-top: 10px;">
                            <label class="col-md-3">Select Weekend</label>
                            <div class="col-md-9">
                                <div class="col-md-3">
                                    <input type="checkbox" name="weekend_name[]" value="Friday"> Friday
                                </div>
                                <div class="col-md-3">
                                    <input type="checkbox" name="weekend_name[]" value="Saturday"> Saturday
                                </div>
                                <div class="col-md-3">
                                    <input type="checkbox" name="weekend_name[]" value="Sunday"> Sunday
                                </div>
                                <div class="col-md-3">
                                    <input type="checkbox" name="weekend_name[]" value="Monday"> Monday
                                </div>
                                <div class="col-md-3">
                                    <input type="checkbox" name="weekend_name[]" value="Tuesday"> Tuesday
                                </div>
                                <div class="col-md-3">
                                    <input type="checkbox" name="weekend_name[]" value="Wednesday"> Wednesday
                                </div>
                                <div class="col-md-3">
                                    <input type="checkbox" name="weekend_name[]" value="Thursday"> Thursday
                                </div>
                            </div>      
                        </div>

                        <div class="form-group">
                            <label for="weekend_from_date" class="col-md-3 control-label">Effective Date</label>
                            <div class="col-md-9">
                                <input type="text" id="weekend_from_date" name="weekend_from_date" v-model="weekend_from_date"  class="gui-input datepicker form-control input-sm jqueryDate" placeholder="Effective date">
                            </div>
                            {{-- <label for="weekend_to_date" class="col-md-1 control-label">To</label>
                            <div class="col-md-4">
                                <input type="text" id="weekend_to_date" name="weekend_to_date" v-model="weekend_to_date" class="gui-input datepicker form-control input-sm jqueryDate" placeholder="To">
                            </div> --}}
                        </div>

                        {{-- <div class="form-group">
                            <label for="" class="col-md-3 control-label"></label>
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="radio-custom radio-success mb5">
                                            <input type="radio" name="weekend_status" id="active" v-model="weekend_status" value="1">
                                            <label for="active">Active</label>
                                        </div>    
                                    </div>
                                    <div class="col-md-4">
                                        <div class="radio-custom radio-danger mb5">
                                            <input type="radio" name="weekend_status" id="inactive" v-model="weekend_status" value="0">
                                            <label for="inactive">Inactive</label>
                                        </div>    
                                    </div>
                                </div>     
                            </div>
                        </div> --}}
                    </div>
                    <div class="modal-footer">
                    <button type="button" class="btn btn-default modal-close-btn" id="modal-close-btn" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Add modal end --> 

    <!-- Edit modal start -->
    {{-- <div class="modal fade bs-example-modal-lg dataEdit" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" id="modalDataEdit">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Edit Weekend</h4>
                </div>
                
                <form class="form-horizontal" @submit.prevent="updateData('updateFormData')" id="updateFormData">
                    <div class="modal-body">

                        <div id="edit-form-errors">
                        </div>

                        {{ csrf_field() }}
                        <input type="hidden" name="hdn_id" v-model="hdn_id">

                        <div class="form-group">
                            <label class="col-md-3">Select Weekend</label>
                            <div class="col-md-9">
                                <div class="col-md-3">
                                    <input disabled="" type="checkbox" name="edit_weekend_name[]" value="Friday"> Friday
                                </div>
                                <div class="col-md-3">
                                    <input disabled="" type="checkbox" name="edit_weekend_name[]" value="Saturday"> Saturday
                                </div>
                                <div class="col-md-3">
                                    <input disabled="" type="checkbox" name="edit_weekend_name[]" value="Sunday"> Sunday
                                </div>
                                <div class="col-md-3">
                                    <input disabled="" type="checkbox" name="edit_weekend_name[]" value="Monday"> Monday
                                </div>
                                <div class="col-md-3">
                                    <input disabled="" type="checkbox" name="edit_weekend_name[]" value="Tuesday"> Tuesday
                                </div>
                                <div class="col-md-3">
                                    <input disabled="" type="checkbox" name="edit_weekend_name[]" value="Wednesday"> Wednesday
                                </div>
                                <div class="col-md-3">
                                    <input disabled="" type="checkbox" name="edit_weekend_name[]" value="Thursday"> Thursday
                                </div>
                            </div>      
                        </div>

                        <div class="form-group">
                            <label for="edit_weekend_from_date" class="col-md-3 control-label">Date From</label>
                            <div class="col-md-4">
                                <input type="text" id="edit_weekend_from_date" name="edit_weekend_from_date" v-model="edit_weekend_from_date"  class="gui-input datepicker form-control input-sm jqueryDate" placeholder="From">

                                <input type="hidden" v-model="old_edit_weekend_from_date" name="old_edit_weekend_from_date">
                            </div>
                            <label for="edit_weekend_to_date" class="col-md-1 control-label">To</label>
                            <div class="col-md-4">
                                <input type="text" id="edit_weekend_to_date" name="edit_weekend_to_date" v-model="edit_weekend_to_date" class="gui-input datepicker form-control input-sm jqueryDate" placeholder="To">
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
    </div> --}}
    <!-- Edit modal end --> 
</div>
@endsection

@section('script')

<script src="{{asset('/js/weekend.js')}}"></script>

@endsection
