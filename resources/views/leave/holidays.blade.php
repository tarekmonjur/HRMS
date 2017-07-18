@extends('layouts.hrms')

@section('style')
    
@endsection

@section('content')
<div id="mainDiv">
    <!-- Begin: Content -->
    <section id="content" class="">
        <div class="row">
            <div class="col-md-12">
                <div class="panel">
                    <div class="panel-heading">
                        <span class="panel-title">Holidays</span>
                        <?php 
                          $chkUrl = \Request::segment(1);
                        ?>
                        @if(in_array($chkUrl."/add", session('userMenuShare')))
                            <button type="button" class="btn btn-xs btn-success pull-right" data-toggle="modal" data-target=".dataAdd" style="margin-top: 12px;">Add New Holidays</button>
                        @endif
                   
                    </div>
                    <div class="panel-body">
                        <div id="showData">
                            <table class="table table-hover" id="datatable">
                                <thead>
                                    <tr class="success">
                                        <th>sl</th>
                                        <th>Holiday Name</th>
                                        <th>From</th>
                                        <th>To</th>
                                        <th>Total Days</th>
                                        <th>Details</th>
                                        <th>Status</th>
                                        @if(in_array($chkUrl."/edit", session('userMenuShare')))
                                        <th>Action</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(info,index) in holidays">
                                        <td v-text="index+1"></td>
                                        <td v-text="info.holiday_name"></td>
                                        <td v-text="info.holiday_from"></td>
                                        <td v-text="info.holiday_to"></td>
                                        <td v-text="calculateTotal(info.holiday_from, info.holiday_to)"></td>
                                        <td v-text="info.holiday_details"></td>
                                        <td v-text="info.holiday_status==1?'Active':'Inactive'"></td>
                                        @if(in_array($chkUrl."/edit", session('userMenuShare')))
                                        <td>
                                            <button type="button" @click="editData(info.id, index)" class="btn btn-sm btn-primary edit-btn" data-toggle="modal" data-target=".dataEdit">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                        </td>
                                        @endif
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
                    <h4 class="modal-title">Add Holiday Info</h4>
                </div>
                <form class="form-horizontal" @submit.prevent="saveData('addFormData')" id="addFormData">
                    <div class="modal-body">

                        <div id="create-form-errors">
                        </div>

                        {{ csrf_field() }}

                        <div class="form-group">
                            <label for="holiday_name" class="col-md-3 control-label">Name <span class="text-danger">*</span></label>
                            <div class="col-md-9">
                                <input name="holiday_name" class="form-control input-sm" v-model="holiday_name" type="text" placeholder="Holiday name">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="from_date" class="col-md-3 control-label">Select Date <span class="text-danger">*</span></label>
                            <div class="col-md-3">
                                <input type="text" id="from_date" name="from_date"  class="gui-input datepicker form-control input-sm jqueryDate" placeholder="From">
                            </div>
                            <div class="col-md-3">
                                <input type="text" id="to_date" name="to_date" class="gui-input datepicker form-control input-sm jqueryDate" placeholder="To">
                            </div>
                            <label for="" class="col-md-2 control-label">Total:</label>
                            <label for="" class="col-md-1 control-label result"></label>
                        </div>

                        <div class="form-group">
                            <label for="" class="col-md-3 control-label">Note</label>
                            <div class="col-md-9">
                                <textarea name="holiday_description" class="form-control input-sm" placeholder="Write note"></textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="" class="col-md-3 control-label"></label>
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="radio-custom radio-success mb5">
                                            <input type="radio" name="holiday_status" id="active" v-model="holiday_status" value="1">
                                            <label for="active">Active</label>
                                        </div>    
                                    </div>
                                    <div class="col-md-4">
                                        <div class="radio-custom radio-danger mb5">
                                            <input type="radio" name="holiday_status" id="inactive" v-model="holiday_status" value="0">
                                            <label for="inactive">Inactive</label>
                                        </div>    
                                    </div>
                                </div>     
                            </div>
                        </div>
                        <span class="text-danger"><b>** Note: Any Leave Application Previously Included This Date Will Be Canceled Automatically.</b></span>
                    </div>
                    <div class="modal-footer">
                    <button type="button" class="btn btn-default modal-close-btn" id="modal-close-btn" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- unitAdd modal end --> 

    <!-- salary Info Edit modal start -->
    <div class="modal fade bs-example-modal-lg dataEdit" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" id="modalDataEdit">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Edit Holiday Info</h4>
                </div>
                
                <form class="form-horizontal" @submit.prevent="updateData('updateFormData')" id="updateFormData">
                    <div class="modal-body">

                        <div id="edit-form-errors">
                        </div>

                        {{ csrf_field() }}

                        <input type="hidden" name="hdn_id" v-model="hdn_id">

                        <div class="form-group">
                            <label for="edit_holiday_name" class="col-md-3 control-label">Name</label>
                            <div class="col-md-9">
                                <input name="edit_holiday_name" class="form-control input-sm" v-model="edit_holiday_name" type="text" placeholder="Holiday name">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="edit_from_date" class="col-md-3 control-label">Date From</label>
                            <div class="col-md-4">
                                <input type="text" id="edit_from_date" name="edit_from_date" v-model="edit_from_date"  class="gui-input datepicker form-control input-sm jqueryDate" placeholder="From">
                            </div>
                            <label for="edit_to_date" class="col-md-1 control-label">To</label>
                            <div class="col-md-4">
                                <input type="text" id="edit_to_date" name="edit_to_date" v-model="edit_to_date" class="gui-input datepicker form-control input-sm jqueryDate" placeholder="To">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="" class="col-md-3 control-label">Note</label>
                            <div class="col-md-9">
                                <textarea name="edit_holiday_description" v-model="edit_holiday_description" class="form-control input-sm" placeholder="Write note"></textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="" class="col-md-3 control-label"></label>
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="radio-custom radio-success mb5">
                                            <input type="radio" name="edit_holiday_status" id="edit_active" v-model="edit_holiday_status" value="1">
                                            <label for="edit_active">Active</label>
                                        </div>    
                                    </div>
                                    <div class="col-md-4">
                                        <div class="radio-custom radio-danger mb5">
                                            <input type="radio" name="edit_holiday_status" id="edit_inactive" v-model="edit_holiday_status" value="0">
                                            <label for="edit_inactive">Inactive</label>
                                        </div>    
                                    </div>
                                </div>     
                            </div>
                        </div>
                        <span class="text-danger"><b>** Warning: Any Leave Application Previously Included This Date Will Be Canceled Automatically.</b></span>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default modal-close-btn" id="modal-edit-close-btn" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
              
            </div>
        </div>
    </div>
    <!-- salary Info Edit modal end --> 
</div>
@endsection

@section('script')

<script>
    $(document).ready(function() {
        $('.jqueryDate').change(function(event) {
            
            var from_date = $('#from_date').val();
            var to_date = $('#to_date').val();

            if(from_date.length > 0 && to_date.length > 0){
                var oneDay = 24*60*60*1000; // hours*minutes*seconds*milliseconds
                var firstDate = new Date(from_date);
                var secondDate = new Date(to_date);

                if(secondDate.getTime() >= firstDate.getTime() ){
                    var diffDays = Math.round(Math.abs((firstDate.getTime() - secondDate.getTime())/(oneDay))); 

                    var result = parseInt(diffDays+1); 
                    $('.result').text(result);
                }else{
                    $('.result').text('Invalid');
                }
            }
        });
    });
</script>

<script src="{{asset('/js/holiday.js')}}"></script>

@endsection
