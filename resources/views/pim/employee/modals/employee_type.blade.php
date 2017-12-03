<div class="modal fade bs-example-modal-lg showEmpType" role="dialog" aria-labelledby="myLargeModalLabel" id="modalDataAdd">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">New Employee Type</h4>
            </div>
            
            <form class="form-horizontal" @submit.prevent="updateEmpType" id="addFormData" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group" style="padding-top: 0px;">
                        <div class="row">
                            <div class="col-md-12 control-label">
                                <div align="center"><h5 style="padding-top: 0px;margin-top: 0px;">Employee Type History</h5></div>
                            </div>
                        </div>
                        <div class="col-md-10 col-md-offset-1">
                            <table class="table">
                                <tr class="success">
                                    <th>Sl</th>
                                    <th>Emp Type</th>
                                    <th>From Date</th>
                                    <th>To Date</th>
                                    <th>Remarks</th>
                                    <th></th>
                                </tr>
                                <tr v-for="(info, index) in emp_type_history">
                                    <td v-text="index+1"></td>
                                    <td v-text="returnTypeName(info.employee_type_id)"></td>
                                    <td v-text="info.from_date"></td>
                                    <td v-text="info.to_date"></td>
                                    <td v-text="info.remarks"></td>
                                    <td v-if="info.id == up_coming_type && current_type!= 'Invalid'"><div class="btn btn-danger btn-xs" @click="deleteUpComming(info.id, 'EmpType')"><i class="fa fa-trash-o" aria-hidden="true"></i></div></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div id="create-form-errors" v-html="HTMLcontent">
                        
                    </div>

                    {{ csrf_field() }}

                    <input type="hidden" name="user_id" v-model="emp_type_user_id">

                    <div class="form-group">
                        <label for="type_name" class="col-md-3 control-label">Employee Types <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <select class="form-control input-sm" name="type_name" v-model="type_name">
                                <option value="">Select Employee Types</option>
                                <!-- @foreach($empTypes as $et)
                                    <option value="{{$et->id}}">{{$et->type_name}}</option>
                                @endforeach -->
                                <option v-for="type in emp_all_types" :value="type.id" v-if="emp_current_type!=type.id" v-text="type.type_name"></option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="from_date" class="col-md-3 control-label">Effective Date <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <input type="text" name="from_date" class="gui-input datepicker form-control input-sm" id="from_date" placeholder="Emp. Type Effective Date"></input>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="to_date" class="col-md-3 control-label">To Date <span class="text-danger" v-if="type_name == 2 || type_name == 4">*</span></label>
                        <div class="col-md-9">
                            <input type="text" name="to_date" class="gui-input datepicker form-control input-sm" id="to_date" placeholder="Emp. Type To Date" :disabled="type_name == 1 || type_name == 3"></input>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="" class="col-md-3 control-label">Remarks </label>
                        <div class="col-md-9">
                            <textarea name="type_remarks" class="form-control input-sm" placeholder="Write remarks"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-default modal-close-btn" id="modal-close-btn" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Process</button>
                </div>
            </form>
        </div>
    </div>
</div>