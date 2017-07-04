@extends('layouts.hrms')
@section('content')

<!-- Begin: Content -->
<div id="mainDiv">
    <section id="content" class="">
        <div class="row">
            <div class="col-md-12">
                
                @if(empty($info))
                    <div class="panel" >
                        <div class="panel-heading">
                            <span class="panel-title">Add Level</span>
                            <a href="{{url('levels/index')}}">
                                <button type="button" class="btn btn-xs btn-success pull-right" style="margin-top: 12px;">Back</button>
                            </a>
                        </div>
                        <div class="panel-body">
                            <div class="col-md-12">
                                <div id="create-form-errors">
                                </div>

                                <form class="form-horizontal" @submit.prevent="saveData('addFormData')" id="addFormData">

                                    {{ csrf_field() }}

                                    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                        <label for="name" class="col-md-2 control-label">Name</label>

                                        <div class="col-md-6">
                                            <input id="name" type="text" class="form-control input-sm" name="name" value="{{ old('name') }}" autofocus>

                                            @if ($errors->has('name'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('name') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="amount" class="col-md-2 control-label">
                                        </label>
                                        <div class="col-md-9">
                                            <div class="checkbox-custom mb5">
                                                <input type="checkbox" class="chk_parent" id="checkboxDefault3" name="chk_parent" {{ ($errors->has('parent_id') || old('parent_id')) ? "checked" : '' }} value="1">
                                                <label for="checkboxDefault3"> This level have parent</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group parent-cls{{ $errors->has('parent_id') ? ' has-error' : '' }}">
                                        <label for="parent_id" class="col-md-2 control-label">Parent Name</label>
                                        <div class="col-md-6">
                                            <select class="form-control input-sm" name="parent_id">
                                                <option value="">Select Level's Parent</option>
                                                @if($parents)
                                                    @foreach($parents as $info)
                                                        <option value="{{$info->id}}" 
                                                        {{($info->id == old('parent_id'))?"selected":''}}
                                                        >{{ $info->level_name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            @if ($errors->has('parent_id'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('parent_id') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group{{ $errors->has('salary_amount') ? ' has-error' : '' }}">
                                        <label for="salary_amount" class="col-md-2 control-label">Salary Amount</label>

                                        <div class="col-md-6">
                                            <input id="salary_amount" type="number" class="form-control input-sm" name="salary_amount" value="{{ old('salary_amount') }}" autofocus>

                                            @if ($errors->has('salary_amount'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('salary_amount') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="name" class="col-md-2 control-label">Salary Info</label>
                                        <div class="col-md-10">
                                            <?php $sl=0; ?>
                                            @foreach($salary_info as $sInfo)
                                                <div class="col-md-4" style="margin-top: 3px;">
                                                    <div class="col-md-7">
                                                        {{ $sInfo->salary_info_name}}
                                                        <span style="color:green;font-weight: bold;">
                                                            ({{$sInfo->salary_info_amount_status==0?"%":"$"}})
                                                        </span>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <input type="checkbox" name="salaryInfoChk[{{$sl}}][chk]" value="1">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input id="salryInfoPercent" type="text" class="form-control input-sm" name="salaryInfoChk[{{$sl}}][amount]" value="{{$sInfo->salary_info_amount}}">

                                                        <input type="hidden" name="salaryInfoChk[{{$sl}}][id]" value="{{$sInfo->id}}"> 
                                                        <input type="hidden" name="salaryInfoChk[{{$sl}}][name]" value="{{$sInfo->salary_info_name}}"> 
                                                    </div>
                                                </div>

                                                <?php $sl++; ?>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="name" class="col-md-2 control-label">Permission</label>
                                        <div class="col-md-10">
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
                                                            <div class="col-md-3">
                                                                {{$mInfo->menu_section_name}}
                                                            </div>
                                                            <div class="col-md-9">
                                                                <div class="col-md-2">
                                                                    <input type="checkbox" name="level_menus[]" value="{{$mInfo->id}}"> 
                                                                    {{$mInfo->menu_name}}
                                                                </div>
                                                            @foreach($mInfo->child_menu as $cInfo)
                                                                <div class="col-md-2">
                                                                    <input type="checkbox" name="level_menus[]" value="{{$cInfo->id}}"> 
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

                                    <div class="form-group{{ $errors->has('details') ? ' has-error' : '' }}">
                                        <label for="details" class="col-md-2 control-label">Details</label>

                                        <div class="col-md-6">
                                            <textarea id="details" class="form-control input-sm" name="details" autofocus>{{ old('details') }}</textarea>

                                            @if ($errors->has('details'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('details') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="level_effective_date" class="col-md-2 control-label">Effective Date</label>
                                        <div class="col-md-6">
                                            <input type="text" name="level_effective_date" class="gui-input datepicker form-control input-sm" placeholder="Select Effective Date">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-md-10 col-md-offset-2">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <div class="radio-custom radio-success mb5">
                                                        <input type="radio" id="active" name="status" checked="" value="1">
                                                        <label for="active">Active</label>
                                                    </div>    
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="radio-custom radio-danger mb5">
                                                        <input type="radio" id="inactive" name="status" value="0">
                                                        <label for="inactive">Inactive</label>
                                                    </div>    
                                                </div>
                                            </div>     
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-10 col-md-offset-2">
                                            <button type="submit" class="btn btn-sm btn-success">
                                                Save
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @elseif(count($info->id) > 0)
                    <div class="panel">
                        <div class="panel-heading">
                            <span class="panel-title">Edit Level</span>
                            <a href="{{url('levels/index')}}">
                                <button type="button" class="btn btn-xs btn-success pull-right" style="margin-top: 12px;">Back</button>
                            </a>
                        </div>
                        <div class="panel-body">
                            <div class="col-md-12">
                                <div id="edit-form-errors">
                                </div>

                                <form class="form-horizontal" @submit.prevent="updateData('updateFormData')" id="updateFormData">

                                    {{ csrf_field() }}

                                    <input type="hidden" name="id" value="{{$info->id}}">

                                    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                        <label for="name" class="col-md-2 control-label">Name</label>

                                        <div class="col-md-6">
                                            <input id="name" type="text" class="form-control input-sm" name="name" value="{{ $info->level_name }}" autofocus>

                                            @if ($errors->has('name'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('name') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="amount" class="col-md-2 control-label">
                                        </label>
                                        <div class="col-md-9">
                                            <div class="checkbox-custom mb5">
                                                <input type="checkbox" class="chk_parent" id="checkboxDefault3" name="chk_parent" {{ ($errors->has('parent_id') || $info->parent_id > 0) ? "checked" : '' }} value="1">
                                                <label for="checkboxDefault3"> This level have parent</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group parent-cls{{ $errors->has('parent_id') ? ' has-error' : '' }}">
                                        <label for="parent_id" class="col-md-2 control-label">Parent Name</label>
                                        <div class="col-md-6">
                                            <select class="form-control input-sm" name="parent_id">
                                                <option value="">Select Level's Parent</option>
                                                @if($parents)
                                                    @foreach($parents as $val)
                                                        @if($val->id != $info->id)
                                                            <option value="{{$val->id}}" 
                                                            {{($val->id == $info->parent_id)?"selected":''}}
                                                            >{{ $val->level_name }}</option>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </select>
                                            @if ($errors->has('parent_id'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('parent_id') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group{{ $errors->has('salary_amount') ? ' has-error' : '' }}">
                                        <label for="salary_amount" class="col-md-2 control-label">Salary Amount</label>

                                        <div class="col-md-6">
                                            <input id="salary_amount" type="number" class="form-control input-sm" name="salary_amount" value="{{ $info->level_salary_amount }}" autofocus>

                                            @if ($errors->has('salary_amount'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('salary_amount') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    @if($salary_info->count() > 0)
                                    <div class="form-group">
                                        <label for="name" class="col-md-2 control-label">
                                            Salary Info
                                        </label>
                                        <div class="col-md-10">
                                                <?php $sl=0; ?>
                                                @foreach($salary_info as $sInfo)
                                                    <div class="col-md-4" style="margin-top: 3px;">
                                                        <div class="col-md-7">
                                                            {{ $sInfo->salary_info_name}}
                                                            <span style="color:green;font-weight: bold;">
                                                                ({{$sInfo->salary_info_amount_status==0?"%":"$"}})
                                                            </span>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <input type="checkbox" name="salaryInfoChk[{{$sl}}][chk]" @if(in_array($sInfo->id, $level_salry_info_id)) {{"checked"}} @endif value="1">
                                                        </div>
                                                        <div class="col-md-3">
                                                            @if(in_array($sInfo->id, $level_salry_info_id))
                                                                <?php  
                                                                $val_amount = $level_salry_info_ary[$sInfo->id]['amount'];
                                                                ?>
                                                            @else
                                                                <?php $val_amount = $sInfo->salary_info_amount;?>
                                                            @endif
                                                            <input id="salryInfoPercent" type="text" class="form-control input-sm" name="salaryInfoChk[{{$sl}}][amount]" value="{{$val_amount}}">

                                                            <input type="hidden" name="salaryInfoChk[{{$sl}}][id]" value="{{$sInfo->id}}"> 
                                                            <input type="hidden" name="salaryInfoChk[{{$sl}}][name]" value="{{$sInfo->salary_info_name}}"> 
                                                        </div>
                                                    </div>

                                                    <?php $sl++; ?>
                                                @endforeach
                                        </div>
                                    </div>
                                    @endif

                                    <div class="form-group{{ $errors->has('details') ? ' has-error' : '' }}">
                                        <label for="details" class="col-md-2 control-label">Details</label>

                                        <div class="col-md-6">
                                            <textarea id="details" class="form-control input-sm" name="details" autofocus>{{ $info->description }}</textarea>

                                            @if ($errors->has('details'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('details') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="level_effective_date" class="col-md-2 control-label">Effective Date</label>
                                        <div class="col-md-6">
                                            <input type="text" name="level_effective_date" class="gui-input datepicker form-control input-sm" value="{{ $info->level_effective_date }}" placeholder="Select Effective Date">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-md-6 col-md-offset-2">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="radio-custom radio-success mb5">
                                                        <input type="radio" id="active" name="status" @if($info->status == 1) {{"checked"}}  @endif value="1">
                                                        <label for="active">Active</label>
                                                    </div>    
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="radio-custom radio-danger mb5">
                                                        <input type="radio" id="inactive" name="status" @if($info->status == 0) {{"checked"}}  @endif value="0">
                                                        <label for="inactive">Inactive</label>
                                                    </div>    
                                                </div>
                                            </div>     
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-6 col-md-offset-2">
                                            <button type="submit" class="btn btn-sm btn-success">
                                                Update
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
                
            </div>
        </div>
    </section>
</div>
<!-- End: Content -->   
@endsection


@section('script')
<script type="text/javascript">
    
    new Vue({
        el: "#mainDiv",
        data:{
            msg: 'testinggg',
        },
        methods: {
            saveData: function(formId){

                var formData = $('#'+formId).serialize();

                axios.post('/levels/add', formData)
                .then((response) => { 

                    $('#create-form-errors').html('');

                    swal({
                        title: "Success",
                        text: "Data added successfully!",
                        type: "success",
                        showCancelButton: false,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Done",
                        closeOnConfirm: false
                    },
                    function(){
                        window.location="{{url('/levels/index')}}";
                    });
                    
                })
                .catch( (error) => {
                    var errors = error.response.data;

                    var errorsHtml = '<div class="alert alert-danger"><ul>';
                    $.each( errors , function( key, value ) {
                        errorsHtml += '<li>' + value[0] + '</li>';
                    });
                    errorsHtml += '</ul></di>';
                    $( '#create-form-errors' ).html( errorsHtml );
                });
            },
            updateData: function(formId){

                var formData = $('#'+formId).serialize();

                axios.post('/levels/edit', formData)
                .then((response) => { 

                    $('#edit-form-errors').html('');

                    swal({
                        title: "Success",
                        text: "Data updated successfully!",
                        type: "success",
                        showCancelButton: false,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Done",
                        closeOnConfirm: false
                    },
                    function(){
                        window.location="{{url('/levels/index')}}";
                    });
                    
                })
                .catch( (error) => {
                    var errors = error.response.data;

                    var errorsHtml = '<div class="alert alert-danger"><ul>';
                    $.each( errors , function( key, value ) {
                        errorsHtml += '<li>' + value[0] + '</li>';
                    });
                    errorsHtml += '</ul></di>';
                    $( '#edit-form-errors' ).html( errorsHtml );
                });
            }
        }
    });

    $(document).ready(function() {
        
        if($('input.chk_parent').is(':checked')){  
            $('.parent-cls').show();
        }
        else{
            $('.parent-cls').hide();
        }

        $('.chk_parent').click(function(event){

            if($('input.chk_parent').is(':checked')){
                
                $('.parent-cls').show();
            }
            else{
                $('.parent-cls').hide();
            }
        });
    });
</script>
@endsection