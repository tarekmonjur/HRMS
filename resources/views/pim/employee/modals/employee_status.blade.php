<div class="modal fade bs-example-modal-lg EmployeeStatus" role="dialog" aria-labelledby="myLargeModalLabel" id="modalDataAdd">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Change Employee Status</h4>
            </div>
            
            <form class="form-horizontal" @submit.prevent="empUpdateStatus" id="addFormData" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group" style="padding-top: 0px;">
                        <div class="row">
                            <div class="col-md-12 control-label">
                                <div align="center"><h5 style="padding-top: 0px;margin-top: 0px;">Status History</h5></div>
                            </div>
                        </div>
                        <div class="col-md-10 col-md-offset-1">
                            <table class="table">
                                <tr class="success">
                                    <th>Sl</th>
                                    <th>Status</th>
                                    <th>From Date</th>
                                    <th>To Date</th>
                                    <th>Remarks</th>
                                </tr>
                                <tr v-for="(info, index) in show_history">
                                    <td v-text="index+1"></td>
                                    <td v-text="returnStatusName(info.employee_status_id)"></td>
                                    <td v-text="info.from_date"></td>
                                    <td v-text="info.to_date"></td>
                                    <td v-text="info.remarks"></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div id="create-form-errors" v-html="HTMLcontent">
                        
                    </div>

                    {{ csrf_field() }}

                    <input type="hidden" name="user_id" v-model="user_id">

                    <div class="form-group">
                        <label for="status_name" class="col-md-3 control-label">Employee Status <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <select class="form-control input-sm" name="status_name">
                                <option value="">Select Employee Status</option>
                                @foreach($allStatus as $st)
                                    <option value="{{$st->id}}">{{$st->status_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="status_eff_date" class="col-md-3 control-label">Effective Date <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <input type="text" name="status_eff_date" class="gui-input datepicker form-control input-sm" id="status_eff_date" placeholder="Status Effective Date"></input>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="" class="col-md-3 control-label">Remarks </label>
                        <div class="col-md-9">
                            <textarea name="status_remarks" class="form-control input-sm" placeholder="Application reason"></textarea>
                        </div>
                    </div>

                    <!-- <div class="form-group">
                        <label for="passport_no" class="col-md-3 control-label">Files</label>
                        <div class="col-md-9">
                            <input type="file" name="file[]" id="file" multiple="multiple"></input>
                        </div>
                    </div> -->
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-default modal-close-btn" id="modal-close-btn" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Process</button>
                </div>
            </form>
        </div>
    </div>
</div>