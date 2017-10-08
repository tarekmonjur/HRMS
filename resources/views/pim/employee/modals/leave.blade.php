<!-- showData modal start -->
<div class="modal fade bs-example-modal-lg showLeaveData" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog" role="document">
        <form class="form-horizontal department-create" action="{{url('employee/updateLeave')}}" method="post" id="department-create">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title detailsLink"></h4>
                </div>
                <div class="modal-body">
                    <div id="create-form-errors">
                    </div>
                    {{ csrf_field() }}

                    <input type="hidden" value="" name="hdn_id" class="hdn_id">
                    <div class="form-group">
                        <div class="col-md-9 col-md-offset-3">
                        @if(count($leave_types) > 0)
                            @foreach($leave_types as $info)
                                <div class="row">
                                    <input type="hidden" name="user_leaves[{{$info->id}}]" value="0">
                                    <input type="checkbox" name="user_leaves[{{$info->id}}]" value="{{$info->id}}"> 
                                    {{$info->leave_type_name}} 
                                </div>     
                            @endforeach
                        @endif
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    @if(in_array($chkUrl."/edit", session('userMenuShare')))
                        <button type="submit" class="btn btn-primary">Update Leave</button>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>
<!-- showData modal end -->  