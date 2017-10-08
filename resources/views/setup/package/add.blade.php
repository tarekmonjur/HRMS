@extends('layouts.setup')
@section('content')

<!-- Begin: Content -->
<div id="mainDiv">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                
                @if(empty($info))
                    <div class="panel" >
                        <div class="panel-heading">
                            <span class="panel-title">Add New Package</span>
                            <a href="{{url('packages/index')}}">
                                <button type="button" class="btn btn-xs btn-success pull-right" style="margin-top: 12px;">Back</button>
                            </a>
                        </div>
                        <div class="panel-body">
                            <div class="col-md-10 col-md-offset-2">
                                <div class="row">
                                    <div id="create-form-errors" class="col-md-10">
                                    </div>
                                </div>

                                <form class="form-horizontal" @submit.prevent="saveData('addFormData')" id="addFormData">

                                    {{ csrf_field() }}

                                    <div class="form-group">
                                        <label for="package_name" class="col-md-2 control-label">Name</label>

                                        <div class="col-md-6">
                                            <input id="package_name" type="text" class="form-control input-sm" name="package_name" value="{{ old('package_name') }}" autofocus>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="package_price" class="col-md-2 control-label">Package Price</label>

                                        <div class="col-md-6">
                                            <input id="package_price" type="number" class="form-control input-sm" name="package_price" value="{{ old('package_price') }}">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="package_duration" class="col-md-2 control-label">Duration(months)</label>

                                        <div class="col-md-6">
                                            <input id="package_duration" type="number" class="form-control input-sm" name="package_duration" value="{{ old('package_duration') }}">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="package_type" class="col-md-2 control-label">Package Type</label>

                                        <div class="col-md-6">
                                            <select name="package_type" id="package_type" class="form-control input-sm">
                                                <option value="">Select Type</option>
                                                <option value="1">Free</option>
                                                <option value="2">Paid</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="package_level_limit" class="col-md-2 control-label">Levels Limit</label>

                                        <div class="col-md-6">
                                            <input id="package_level_limit" type="number" class="form-control input-sm" name="package_level_limit" value="{{ old('package_level_limit') }}">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="package_user_limit" class="col-md-2 control-label">Users Limit</label>

                                        <div class="col-md-6">
                                            <input id="package_user_limit" type="number" class="form-control input-sm" name="package_user_limit" value="{{ old('package_user_limit') }}">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="name" class="col-md-2 control-label">Modules</label>
                                        <div class="col-md-10">
                                            @if($modules)
                                                <div class="row">
                                                @foreach($modules as $info)
                                                    <div class="col-md-4">
                                                        <input type="checkbox" name="modules[]" value="{{$info->id}}">
                                                        {{ $info->module_name }}
                                                    </div>
                                                @endforeach
                                                </div>
                                            @endif
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
                            <span class="panel-title">Edit Package</span>
                            <a href="{{url('packages/index')}}">
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

                                    <div class="form-group">
                                        <label for="package_name" class="col-md-2 control-label">Name</label>

                                        <div class="col-md-6">
                                            <input id="package_name" type="text" class="form-control input-sm" name="package_name" value="{{ $info->package_name }}" autofocus>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="package_price" class="col-md-2 control-label">Package Price</label>

                                        <div class="col-md-6">
                                            <input id="package_price" type="number" class="form-control input-sm" name="package_price" value="{{ $info->package_price }}">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="package_duration" class="col-md-2 control-label">Duration(months)</label>

                                        <div class="col-md-6">
                                            <input id="package_duration" type="number" class="form-control input-sm" name="package_duration" value="{{ $info->package_duration }}">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="package_type" class="col-md-2 control-label">Package Type</label>

                                        <div class="col-md-6">
                                            <select name="package_type" id="package_type" class="form-control input-sm">
                                                <option value="">Select Type</option>
                                                <option value="1" @if($info->package_type==1){{"selected"}}@endif>Free</option>
                                                <option value="2" @if($info->package_type==2){{"selected"}}@endif>Paid</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="package_level_limit" class="col-md-2 control-label">Levels Limit</label>

                                        <div class="col-md-6">
                                            <input id="package_level_limit" type="number" class="form-control input-sm" name="package_level_limit" value="{{ $info->package_level_limit }}">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="package_user_limit" class="col-md-2 control-label">Users Limit</label>

                                        <div class="col-md-6">
                                            <input id="package_user_limit" type="number" class="form-control input-sm" name="package_user_limit" value="{{ $info->package_user_limit }}">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="name" class="col-md-2 control-label">Modules</label>
                                        <div class="col-md-10">
                                            @if($modules)
                                                <div class="row">
                                                @foreach($modules as $info)
                                                    <div class="col-md-4">
                                                        <input type="checkbox" @if(in_array($info->id, $chked_modules)){{"checked"}} @endif name="modules[]" value="{{$info->id}}">
                                                        {{ $info->module_name }}
                                                    </div>
                                                @endforeach
                                                </div>
                                            @endif
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
    </div>
</div>
<!-- End: Content -->   
@endsection


@section('script')
<script type="text/javascript">
    
    new Vue({
        el: "#mainDiv",
        data:{

        },
        mounted(){

        },
        methods: {
            saveData: function(formId){

                var formData = $('#'+formId).serialize();

                axios.post('/packages/add', formData)
                .then((response) => { 

                    $('#create-form-errors').html('');

                    swal({
                        title: response.data.title,
                        text: response.data.message+" !",
                        type: response.data.title,
                        showCancelButton: false,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Done",
                        closeOnConfirm: false
                    },
                    function(){
                        window.location="{{url('/packages/index')}}";
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

                axios.post('/packages/edit', formData)
                .then((response) => { 

                    $('#edit-form-errors').html('');

                    swal({
                        title: response.data.title,
                        text: response.data.message+" !",
                        type: response.data.title,
                        showCancelButton: false,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Done",
                        closeOnConfirm: false
                    },
                    function(){
                        window.location="{{url('/packages/index')}}";
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
</script>
@endsection