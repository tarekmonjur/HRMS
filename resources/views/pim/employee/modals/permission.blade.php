<!-- showData modal start -->
<div class="modal fade bs-example-modal-lg showData" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog" role="document">
        <form class="form-horizontal department-create" action="{{url('employee/updatePermission')}}" method="post" id="department-create">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">User's Permissions</h4>
                </div>
                <div class="modal-body">
                    <div id="create-form-errors">
                        
                    </div>
                    {{ csrf_field() }}

                    <input type="hidden" value="" name="hdn_id" class="hdn_id">
                    <div class="form-group">
                        <div class="col-md-11 col-md-offset-1">
                            @foreach($modules_permission as $info)
                                <div class="row">
                                    <label for="name">
                                        {{ $info->module_name }}
                                    </label>
                                </div>
                                <div class="row">
                                    @foreach($info->menus as $mInfo)
                                        @if($mInfo->menu_parent_id == 0)
                                        <div class="row">
                                            <div class="col-md-2">
                                                {{$mInfo->menu_section_name}}
                                            </div>
                                            <div class="col-md-10">
                                                <div class="col-md-2">
                                                    <input type="hidden" name="user_menus[{{$mInfo->id}}]" value="0">
                                                    <input type="checkbox" name="user_menus[{{$mInfo->id}}]" value="{{$mInfo->id}}"> 
                                                    {{$mInfo->menu_name}}
                                                </div>
                                            @foreach($mInfo->child_menu as $cInfo)
                                                <div class="col-md-2">
                                                    <input type="hidden" name="user_menus[{{$cInfo->id}}]" value="0">
                                                    <input type="checkbox" name="user_menus[{{$cInfo->id}}]" value="{{$cInfo->id}}"> 
                                                    {{$cInfo->menu_name}}
                                                </div>
                                            @endforeach
                                            </div>
                                        </div>
                                        @endif
                                    @endforeach      
                                </div>
                            @endforeach             
                        </div>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    @if(in_array($chkUrl."/edit", session('userMenuShare')))
                        <button type="submit" class="btn btn-primary">Update Permission</button>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>
<!-- showData modal end -->  