@extends('layouts.hrms')
@section('content')

@section('style')
    <!-- Admin Forms CSS -->
    <link rel="stylesheet" type="text/css" href="{{asset('admin-tools/admin-forms/css/admin-forms.css')}}">

    <!-- Vendor CSS -->
    <link rel="stylesheet" type="text/css" href="{{asset('vendor/plugins/magnific/magnific-popup.css')}}">

    <style type="text/css">
        .odd{background: #f5f5f5;padding: 20px 15px 0px;margin: -20px -15px -0px;}
        .even{padding: 10px 15px 0px;margin: 0px -15px;}

        .select2-container .select2-selection--single{height:32px!important}
        .select2-container--default .select2-selection--single .select2-selection__rendered{line-height:30px!important}
        .select2-container--default .select2-selection--single .select2-selection__arrow{height:30px!important}
        .fileupload-preview img{max-width: 200px!important;}
    </style>

@endsection

<section class="animated fadeIn p10" id="employee">
    <div class="panel">
        <div class="panel-heading">
            <span class="panel-title hidden-xs"><span class="glyphicons glyphicons-user_add"></span> Edit Employee</span>
            <ul class="nav panel-tabs-border panel-tabs">
                <li class="@if($tab == '') active @endif">
                    <a id="" href="#tab1_1" v-on:click="tab = ''" data-toggle="tab" aria-expanded="true">Basic</a>
                </li>
                <li class="@if(isset($id) && $tab == 'personal') active @endif">
                    <a v-if="user_id" id="personal" href="#tab1_2" v-on:click="tab = 'personal'" data-toggle="tab" aria-expanded="true">Personal</a>
                    <a v-else id="personal" aria-expanded="true">Personal</a>
                </li>
                <li class="@if(isset($id) && $tab == 'education') active @endif">
                    <a v-if="user_id" id="education" href="#tab1_3" v-on:click="tab = 'education'" data-toggle="tab"  aria-expanded="true">Education</a>
                    <a v-else id="education" aria-expanded="true">Education</a>
                </li>
                <li class="@if(isset($id) && $tab == 'experience') active @endif">
                    <a v-if="user_id" id="experience" href="#tab1_4" v-on:click="tab = 'experience'" data-toggle="tab" aria-expanded="true">Experience</a>
                    <a v-else id="experience"  aria-expanded="true">Experience</a>
                </li>
                <li class="@if(isset($id) && $tab == 'salary') active @endif">
                    <a v-if="user_id" id="salary" href="#tab1_5" v-on:click="tab = 'salary'" data-toggle="tab" aria-expanded="true">Salary</a>
                    <a v-else id="salary" aria-expanded="true">Salary</a>
                </li>
                <li class="@if(isset($id) && $tab == 'nominee') active @endif">
                    <a v-if="user_id" id="nominee" href="#tab1_6" v-on:click="tab = 'nominee'" data-toggle="tab" aria-expanded="true">Nominee</a>
                    <a v-else id="nominee" aria-expanded="true">Nominee</a>
                </li>
                <li class="@if(isset($id) && $tab == 'training') active @endif">
                    <a v-if="user_id" id="training" href="#tab1_7" v-on:click="tab = 'training'" data-toggle="tab" aria-expanded="true">Training</a>
                    <a v-else id="training" aria-expanded="true">Training</a>
                </li>
                <li class="@if(isset($id) && $tab == 'reference') active @endif">
                    <a v-if="user_id" id="reference" href="#tab1_8" v-on:click="tab = 'reference'" data-toggle="tab" aria-expanded="true">Reference</a>
                    <a v-else id="reference" aria-expanded="true">Reference</a>
                </li>
                <li class="@if(isset($id) && $tab == 'children') active @endif">
                    <a v-if="user_id" id="children" href="#tab1_9" v-on:click="tab = 'children'" data-toggle="tab" aria-expanded="true">Children</a>
                    <a v-else id="children" aria-expanded="true">Children</a>
                </li>
                <li class="@if(isset($id) && $tab == 'language') active @endif">
                    <a v-if="user_id" id="language" href="#tab1_10" v-on:click="tab = 'language'" data-toggle="tab" aria-expanded="true">Language</a>
                    <a v-else id="language" aria-expanded="true">Language</a>
                </li>
            </ul>
        </div>

        <div class="panel-body">
            <div class="tab-content pn br-n">

                <!--- Basic Info -->
                <div id="tab1_1" class="tab-pane @if($tab == '') active @endif">
                    <div class="row mt20">
                        <div class="col-md-12">
                            <form method="post" v-on:submit.prevent="addEmployeeBasicInfo" enctype="multipart/form-data">
                                <input type="hidden" name="user_id" v-model="user_id" class="form-control input-sm">
                                
                                <div v-for="(basic,index) in basics" v-if="index =='id'">

                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group" :class="{'has-error': errors.employee_no}">
                                                <label class="control-label">Employee No : <span class="text-danger">*</span></label>
                                                <input type="text" name="employee_no" v-model="basics.employee_no" class="form-control input-sm" placeholder="Enter Employee No">
                                                <span v-if="errors.employee_no" class="help-block" v-text="errors.employee_no[0]"></span>
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-group" :class="{'has-error': errors.branch_id}">
                                                <label class="control-label">Branch : <span class="text-danger">*</span></label>
                                               <select class="form-control input-sm" id="branch_id" name="branch_id" v-model="basics.branch_id">
                                                    <option value="">...Select Branch...</option>
                                                    <option v-for="(branch,index) in branches" :value="branch.id" v-text="branch.branch_name"></option>
                                                </select>
                                                <span v-if="errors.branch_id" class="help-block" v-text="errors.branch_id[0]"></span>
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-group" :class="{'has-error': errors.employee_type_id}">
                                                <label class="control-label">Employee Type : <span class="text-danger">*</span></label>
                                                <select class="form-control input-sm" id="employee_type_id" name="employee_type_id" v-model="basics.employee_type_id">
                                                    <option value="">...Select Employee Type...</option>
                                                    <option v-for="(employeeType,index) in employeeTypes" :value="employeeType.id" v-text="employeeType.type_name"></option>
                                                </select>
                                                <span v-if="errors.employee_type_id" class="help-block" v-text="errors.employee_type_id[0]"></span>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group" :class="{'has-error': errors.designation_id}">
                                                <label class="control-label">Employee Designation : <span
                                                            class="text-danger">*</span></label>
                                                <select2 class="form-control input-sm" id="designation_id"
                                                        name="designation_id" v-model="designation_id = basics.designation_id">
                                                    <option value="">...Select Employee Designation...</option>
                                                    <option v-for="(designation,index) in designations" :value="designation.id" v-text="designation.designation_name+' - ('+designation.level.level_name+' ) - ( '+designation.department.department_name+' )'" :selected="basics.designation_id == designation.id"></option>
                                                </select2>
                                                <span v-if="errors.designation_id" class="help-block" v-text="errors.designation_id[0]"></span>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                                <div class="form-group" :class="{'has-error': errors.unit_id}">
                                                    <label class="control-label">Employee Unit : <span class="text-danger">*</span></label>
                                                    <select class="form-control input-sm" id="unit_id" name="unit_id" v-model="unit_id = basics.unit_id">
                                                    <option value="">...Select Unit...</option>
                                                    <option v-for="(unit,index) in units" :value="unit.id" v-text="unit.unit_name"></option>
                                                </select>
                                                <span v-if="errors.unit_id" class="help-block" v-text="errors.unit_id[0]"></span>
                                                </div>
                                            </div>

                                        <!-- <div class="col-md-2">
                                            <div class="form-group mt25">
                                                <button type="button" id="add_new_designation_button"
                                                        onclick="modal_open('#add_new_designation_button','#add_new_designation_modal')"
                                                        v-on:click="getDepartmentsAndLevels()"
                                                        class="btn btn-sm btn-dark btn-gradient dark btn-block"
                                                        data-effect="mfp-newspaper"><span
                                                            class="glyphicons glyphicons-briefcase"></span> &nbsp; Add
                                                    Designation
                                                </button>
                                            </div>
                                        </div> -->
                                    </div>

                                    <div class="row">

                                        <div class="col-md-3">
                                            <div class="form-group" :class="{'has-error': errors.supervisor_id}">
                                                <label class="control-label">Employee Supervisor : </label>
                                                <select class="form-control select-sm input-sm" id="supervisor_id" name="supervisor_id" v-model="supervisor_id = basics.supervisor_id">
                                                    <option value="0">...Select Employee Supervisor...</option>
                                                    <option v-for="(supervisor,index) in supervisors" :value="supervisor.id" v-text="supervisor.fullname+' - ('+supervisor.designation_name+' ) - ( '+supervisor.level_name+' )'"></option>
                                                </select>
                                                <span v-if="errors.supervisor_id" class="help-block" v-text="errors.supervisor_id[0]"></span>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group" :class="{'has-error': errors.first_name}">
                                                <label class="control-label">First Name : <span
                                                            class="text-danger">*</span></label>
                                                <input type="text" name="first_name"
                                                       v-model="basics.first_name"
                                                       class="form-control input-sm" placeholder="Enter First Name">
                                                <span v-if="errors.first_name" class="help-block" v-text="errors.first_name[0]"></span>
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label class="control-label">Middle Name : </label>
                                                <input type="text" name="middle_name"
                                                    v-model="basics.middle_name"
                                                       class="form-control input-sm" placeholder="Enter Middle Name">
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-group" :class="{'has-error': errors.last_name}">
                                                <label class="control-label">Last Name : <span
                                                            class="text-danger">*</span></label>
                                                <input type="text" name="last_name"
                                                       v-model="basics.last_name"
                                                       class="form-control input-sm" placeholder="Enter Last Name">
                                                <span v-if="errors.last_name" class="help-block" v-text="errors.last_name[0]"></span>
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label class="control-label">Nick Name</label>
                                                <input type="text" name="nick_name"
                                                       v-model="basics.nick_name"
                                                       class="form-control input-sm" placeholder="Enter Nick Name">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group" :class="{'has-error': errors.email}">
                                                <label class="control-label">Email Address : <span
                                                            class="text-danger">*</span></label>
                                                <input type="email" name="email" :value="basics.email" class="form-control input-sm" placeholder="Enter Email Address">
                                                <input type="hidden" name="old_email" :value="basics.email">

                                                <span v-if="errors.email" class="help-block" v-text="errors.email[0]"></span>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group" :class="{'has-error': errors.mobile_number}">
                                                <label class="control-label">Mobile Number : <span
                                                            class="text-danger">*</span></label>
                                                <input type="number" name="mobile_number"
                                                       v-model="basics.mobile_number"
                                                       class="form-control input-sm" placeholder="Enter Mobile Number">
                                                <span v-if="errors.mobile_number" class="help-block" v-text="errors.mobile_number[0]"></span>
                                            </div>
                                        </div>

                                        {{--<div class="col-md-3">--}}
                                            {{--<div class="form-group {{$errors->has('password')?'has-error':''}}">--}}
                                                {{--<label class="control-label">Password : <span--}}
                                                            {{--class="text-danger">*</span></label>--}}
                                                {{--<input type="password" name="password" value="basics.password"--}}
                                                       {{--class="form-control input-sm" placeholder="Enter Password">--}}
                                                {{--@if($errors->has('password'))<span--}}
                                                        {{--class="help-block">{{$errors->first('password')}}</span>@endif--}}
                                            {{--</div>--}}
                                        {{--</div>--}}

                                        {{--<div class="col-md-3">--}}
                                            {{--<div class="form-group {{$errors->has('retype_password')?'has-error':''}}">--}}
                                                {{--<label class="control-label">Retype Password : <span--}}
                                                            {{--class="text-danger">*</span></label>--}}
                                                {{--<input type="password" name="retype_password"--}}
                                                       {{--class="form-control input-sm"--}}
                                                       {{--placeholder="Enter Retype Password">--}}
                                                {{--@if($errors->has('retype_password'))<span--}}
                                                        {{--class="help-block">{{$errors->first('retype_password')}}</span>@endif--}}
                                            {{--</div>--}}
                                        {{--</div>--}}
                                    </div>

                                    <div class="row">
                                        <div class="col-md-2 pt50" :class="{'has-error': errors.image}">
                                            <label class="control-label">Employee Photo :</label>
                                            <div class="fileupload admin-form" :class="(basics.photo)?'fileupload-exists':'fileupload-new'"
                                                 data-provides="fileupload">
                                                <div class="fileupload-preview thumbnail mb15">
                                                    <img v-if="basics.photo" :src="'/files/'+basics.id+'/'+basics.photo" alt="holder">
                                                    <img v-else src="{{asset('img/placeholder.png')}}" alt="holder">
                                                </div>
                                                <span class="button btn btn-sm btn-dark btn-file btn-block ph5">
                                                    <span class="fileupload-exists"><span class="fa fa-user"></span> &nbsp; <strong>Change Photo</strong></span>
                                                    <span class="fileupload-new"><span class="fa fa-user"></span> &nbsp; <strong>Select Photo</strong></span>
                                                    <input type="file" name="image">
                                                    <input type="hidden" :value="basics.photo" name="old_image">
                                                </span>
                                            </div>
                                            <span v-if="errors.image" class="help-block" v-text="errors.image[0]"></span>
                                        </div>

                                    <div v-if="basics.address">
                                        
                                        <div class="col-md-10">
                                            <div class="admin-form">
                                                <div class="section-divider mb40">
                                                    <span class="bg-white">Present Address adsf</span>
                                                </div>
                                            </div>
                                            <!-- v-on:change="getDistrictByDivisionId($event.target.value,'present')" -->
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group" :class="{'has-error': errors.present_division_id}">
                                                        <label class="control-label">Division : <span
                                                                    class="text-danger">*</span></label>
                                                        <select class="form-control input-sm" name="present_division_id" v-model="present_division_id = basics.address.present_division_id">
                                                            <option value="">---- Select Division ----</option>
                                                            <option v-for="(division, index) in divisions" :value="division.id" v-text="division.division_name"></option>
                                                        </select>
                                                        <span v-if="errors.present_division_id" class="help-block" v-text="errors.present_division_id[0]"></span>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group" :class="{'has-error': errors.present_district_id}">
                                                        <label class="control-label">District : <span
                                                                    class="text-danger">*</span></label>
                                                        <select class="form-control input-sm" name="present_district_id"
                                                                v-model="present_district_id = basics.address.present_district_id">
                                                            <option :value="''">---- Select District ----</option>
                                                            <option v-for="(district, index) in districts"
                                                                    :value="district.id" v-text="district.district_name"></option>
                                                        </select> 
                                                        <span v-if="errors.present_district_id" class="help-block" v-text="errors.present_district_id[0]"></span>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group" :class="{'has-error': errors.present_policestation_id}">
                                                        <label class="control-label">Police Station : <span
                                                                    class="text-danger">*</span></label>
                                                        <select class="form-control input-sm" v-model="basics.address.present_policestation_id"
                                                                name="present_policestation_id">
                                                            <option :value="''">---- Select Police Station ----</option>
                                                            <option v-for="(policeStation,index) in policeStations" :value="policeStation.id" v-text="policeStation.police_station_name"></option>
                                                        </select>
                                                        <span v-if="errors.present_policestation_id" class="help-block" v-text="errors.present_policestation_id[0]"></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group" :class="{'has-error': errors.present_postoffice}">
                                                        <label class="control-label">Post Office : <span
                                                                    class="text-danger">*</span></label>
                                                        <input type="text" name="present_postoffice"
                                                               v-model="basics.address.present_postoffice"
                                                               class="form-control input-sm"
                                                               placeholder="Enter Post Office">
                                                        <span v-if="errors.present_postoffice" class="help-block" v-text="errors.present_postoffice[0]"></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-8">
                                                    <div class="form-group" :class="{'has-error': errors.present_address}">
                                                        <label class="control-label">Address : <span class="text-danger">*</span></label>
                                                        <textarea name="present_address" class="form-control input-sm"
                                                                  cols="60" rows="1"
                                                                  placeholder="House, Road, Village." v-text="basics.address.present_address"></textarea>
                                                        <span v-if="errors.present_address" class="help-block" v-text="errors.present_address"></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="admin-form">
                                                <div class="section-divider mb40">
                                                    <span class="bg-white">Permanent Address</span>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group" :class="{'has-error': errors.permanent_division_id}">
                                                        <label class="control-label">Division : <span
                                                                    class="text-danger">*</span></label>
                                                        <select class="form-control input-sm"
                                                                name="permanent_division_id"
                                                                v-model="permanent_division_id = basics.address.permanent_division_id">
                                                            <option :value="''">---- Select Division ----</option>
                                                            <option v-for="(division, index) in divisions"
                                                                    :value="division.id" v-text="division.division_name"></option>
                                                        </select>
                                                        <span v-if="errors.permanent_division_id" class="help-block" v-text="errors.permanent_division_id[0]"></span>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group" :class="{'has-error': errors.permanent_district_id}">
                                                        <label class="control-label">District : <span
                                                                    class="text-danger">*</span></label>
                                                        <select class="form-control input-sm"
                                                                name="permanent_district_id"
                                                                v-model="permanent_district_id = basics.address.permanent_district_id">
                                                            <option :value="''">---- Select District ----
                                                            </option>
                                                            <option v-for="(district, index) in permanentDistricts"
                                                                    :value="district.id" v-text="district.district_name"></option>
                                                        </select>
                                                        <span v-if="errors.permanent_district_id" class="help-block" v-text="errors.permanent_district_id[0]"></span>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group" :class="{'has-error': errors.permanent_policestation_id}">
                                                        <label class="control-label">Police Station : <span
                                                                    class="text-danger">*</span></label>
                                                        <select class="form-control input-sm" v-model="basics.address.permanent_policestation_id"
                                                                name="permanent_policestation_id">
                                                            <option :value="''">---- Select Police Station ----</option>
                                                            <option v-for="(policeStation,index) in permanentPoliceStations"
                                                                    :value="policeStation.id" v-text="policeStation.police_station_name"></option>
                                                        </select>
                                                        <span v-if="errors.permanent_policestation_id" class="help-block" v-text="errors.permanent_policestation_id[0]"></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group" :class="{'has-error': errors.permanent_postoffice}">
                                                        <label class="control-label">Post Office : <span
                                                                    class="text-danger">*</span></label>
                                                        <input type="text" name="permanent_postoffice"
                                                               v-model="basics.address.permanent_postoffice"
                                                               class="form-control input-sm"
                                                               placeholder="Enter Post Office">
                                                        <span v-if="errors.permanent_postoffice" class="help-block" v-text="errors.permanent_postoffice[0]"></span>
                                                    </div>
                                                </div>

                                                <div class="col-md-8">
                                                    <div class="form-group" :class="{'has-error': errors.permanent_address}">
                                                        <label class="control-label">Address : <span
                                                                    class="text-danger">*</span></label>
                                                        <textarea name="permanent_address" class="form-control input-sm"
                                                                  cols="60" rows="1"
                                                                  placeholder="House, Road, Village." v-text="basics.address.permanent_address"></textarea>
                                                        <span v-if="errors.permanent_address" class="help-block" v-text="errors.permanent_address[0]"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>

                                        <div v-else>
                                        <div class="col-md-10">
                                            <div class="admin-form">
                                                <div class="section-divider mb40">
                                                    <span class="bg-white">Present Address</span>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group" :class="{'has-error': errors.present_division_id}">
                                                        <label class="control-label">Division : <span
                                                                    class="text-danger">*</span></label>
                                                        <select class="form-control input-sm" name="present_division_id"
                                                                v-model="present_division_id" >
                                                            <option :value="''">---- Select Division ----</option>
                                                            <option v-for="(division, index) in divisions" :value="division.id" v-text="division.division_name"></option>
                                                        </select>
                                                         <span v-if="errors.present_division_id" class="help-block" v-text="errors.present_division_id[0]"></span>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group" :class="{'has-error': errors.present_district_id}">
                                                        <label class="control-label">District : <span
                                                                    class="text-danger">*</span></label>
                                                        <select class="form-control input-sm" name="present_district_id"
                                                                v-model="present_district_id">
                                                            <option :value="''">---- Select District ----</option>
                                                            <option v-for="(district, index) in districts" :value="district.id" v-text="district.district_name"></option>
                                                        </select>
                                                        <span v-if="errors.present_district_id" class="help-block" v-text="errors.present_district_id[0]"></span>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group" :class="{'has-error': errors.present_policestation_id}">
                                                        <label class="control-label">Police Station : <span
                                                                    class="text-danger">*</span></label>
                                                        <select class="form-control input-sm"
                                                                name="present_policestation_id">
                                                            <option :value="''">---- Select Police Station ----</option>
                                                            <option v-for="(policeStation,index) in policeStations"
                                                                    :value="policeStation.id" v-text="policeStation.police_station_name"></option>
                                                        </select>
                                                        <span v-if="errors.present_policestation_id" class="help-block" v-text="errors.present_policestation_id[0]"></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group" :class="{'has-error': errors.present_postoffice}">
                                                        <label class="control-label">Post Office : <span
                                                                    class="text-danger">*</span></label>
                                                        <input type="text" name="present_postoffice" 
                                                               class="form-control input-sm"
                                                               placeholder="Enter Post Office">
                                                        <span v-if="errors.present_postoffice" class="help-block" v-text="errors.present_postoffice[0]"></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-8">
                                                    <div class="form-group" :class="{'has-error': errors.present_address}">
                                                        <label class="control-label">Address : <span
                                                                    class="text-danger">*</span></label>
                                                        <textarea name="present_address" class="form-control input-sm"
                                                                  cols="60" rows="1"
                                                                  placeholder="House, Road, Village."></textarea>
                                                        <span v-if="errors.present_address" class="help-block" v-text="errors.present_address[0]"></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="admin-form">
                                                <div class="section-divider mb40">
                                                    <span class="bg-white">Permanent Address</span>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group" :class="{'has-error': errors.permanent_division_id}">
                                                        <label class="control-label">Division : <span
                                                                    class="text-danger">*</span></label>
                                                        <select class="form-control input-sm"
                                                                name="permanent_division_id"
                                                                v-model="permanent_division_id">
                                                            <option :value="''">---- Select Division ----</option>
                                                            <option v-for="(division, index) in divisions"
                                                                    :value="division.id" v-text="division.division_name"></option>
                                                        </select>
                                                        <span v-if="errors.permanent_division_id" class="help-block" v-text="errors.permanent_division_id[0]"></span>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group" :class="{'has-error': errors.permanent_district_id}">
                                                        <label class="control-label">District : <span
                                                                    class="text-danger">*</span></label>
                                                        <select class="form-control input-sm"
                                                                name="permanent_district_id"
                                                                v-model="permanent_district_id">
                                                            <option :value="''">---- Select Employee Designation ----
                                                            </option>
                                                            <option v-for="(district, index) in permanentDistricts"
                                                                    :value="district.id" v-text="district.district_name"></option>
                                                        </select>
                                                        <span v-if="errors.permanent_district_id" class="help-block" v-text="errors.permanent_district_id[0]"></span>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group" :class="{'has-error': errors.permanent_policestation_id}">
                                                        <label class="control-label">Police Station : <span
                                                                    class="text-danger">*</span></label>
                                                        <select class="form-control input-sm"
                                                                name="permanent_policestation_id">
                                                            <option :value="''">---- Select Police Station ----</option>
                                                            <option v-for="(policeStation,index) in permanentPoliceStations"
                                                                    :value="policeStation.id" v-text="policeStation.police_station_name"></option>
                                                        </select>
                                                        <span v-if="errors.permanent_policestation_id" class="help-block" v-text="errors.permanent_policestation_id[0]"></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group" :class="{'has-error': errors.permanent_postoffice}">
                                                        <label class="control-label">Post Office : <span
                                                                    class="text-danger">*</span></label>
                                                        <input type="text" name="permanent_postoffice"
                                                               class="form-control input-sm"
                                                               placeholder="Enter Post Office">
                                                        <span v-if="errors.permanent_postoffice" class="help-block" v-text="errors.permanent_postoffice[0]"></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-8">
                                                    <div class="form-group" :class="{'has-error': errors.permanent_address}">
                                                        <label class="control-label">Address : <span
                                                                    class="text-danger">*</span></label>
                                                        <textarea name="permanent_address" class="form-control input-sm"
                                                                  cols="60" rows="1"
                                                                  placeholder="House, Road, Village."></textarea>
                                                       <span v-if="errors.permanent_address" class="help-block" v-text="errors.permanent_address[0]"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>


                                    </div>

                                    <hr class="short alt">

                                <div class="section row mbn">
                                        <div class="col-sm-2 pull-right">
                                            <p class="text-left">
                                                <button type="submit"
                                                        name="update_employee_and_next" value="update_employee_and_next"
                                                        v-on:click="submit_button='update_employee_and_next'"
                                                        class="btn btn-dark btn-gradient dark btn-block"><span
                                                        class="glyphicons glyphicons-ok_2"></span> &nbsp; Update &
                                                    Next
                                                    <span class="glyphicons glyphicons-right_arrow"></span></button>
                                            </p>
                                        </div>
                                        <div class="col-sm-2 pull-right">
                                            <p class="text-left">
                                                <button type="submit"
                                                        name="update_employee" value="update_employee"
                                                        v-on:click="submit_button='update_employee'"
                                                        class="btn btn-dark btn-gradient dark btn-block"><span
                                                        class="glyphicons glyphicons-ok_2"></span> &nbsp; Update
                                                    Employee
                                                </button>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!--- Personal Info -->
                <div id="tab1_2" class="tab-pane @if(isset($id) && $tab == 'personal') active @endif">
                    <div v-if="personals">
                        <span class="text-info">Employee No : </span>
                        <span v-text="personals.employee_no+', '"></span>
                        <span class="text-info">Employee Full Name : </span>
                        <span v-text="personals.first_name+' '+personals.middle_name+' '+personals.last_name"></span>
                    </div>

                    <div class="row mt20">
                        <div class="col-md-12">
                            <form id="add_personal_info_form" v-on:submit.prevent="addPersonalInfo" method="post">
                                <input type="hidden" name="user_id" v-model="user_id" class="form-control input-sm">

                                    <div class="row">

                                        <div class="col-md-3">
                                            <div class="form-group" :class="{'has-error': errors.father_name}">
                                                <label class="control-label">Father Name : <span class="text-danger">*</span></label>

                                                <input v-if="personals.details" type="text" name="father_name" :value="personals.details.father_name" class="form-control input-sm" placeholder="Enter Father Name">
                                                <input v-else type="text" name="father_name" class="form-control input-sm" placeholder="Enter Father Name">

                                                <span v-if="errors.father_name" class="text-danger" v-text="errors.father_name[0]">
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group" :class="{'has-error': errors.mother_name}">
                                                <label class="control-label">Mother Name : <span class="text-danger">*</span></label>

                                                <input v-if="personals.details" type="text" name="mother_name" :value="personals.details.mother_name" class="form-control input-sm" placeholder="Enter Mother Name">
                                                <input v-else type="text" name="mother_name" class="form-control input-sm" placeholder="Enter Mother Name">

                                                <span v-if="errors.mother_name" class="text-danger" v-text="errors.mother_name[0]">
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group" :class="{'has-error': errors.spouse_name}">
                                                <label class="control-label">Spouse Name:</label>

                                                <input v-if="personals.details" type="text" name="spouse_name" :value="personals.details.spouse_name" class="form-control input-sm" placeholder="Enter Spouse Name">
                                                <input v-else type="text" name="spouse_name" class="form-control input-sm" placeholder="Enter Spouse Name">

                                                <span v-if="errors.spouse_name" class="text-danger" v-text=" errors.spouse_name[0]"></span>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group" :class="{'has-error': errors.national_id}">
                                                <label class="control-label">National Id : <span class="text-danger">*</span></label>

                                                <input v-if="personals.details" type="text" name="national_id" :value="personals.details.national_id" class="form-control input-sm" placeholder="Enter National Id">
                                                <input v-else type="text" name="national_id" class="form-control input-sm" placeholder="Enter National Id">

                                                <span v-if="errors.national_id" class="text-danger" v-text="errors.national_id[0]">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group" :class="{'has-error': errors.passport_no}">
                                                <label class="control-label">Passport No :</label>

                                                <input v-if="personals.details" type="text" name="passport_no" :value=" personals.details.passport_no" class="form-control input-sm" placeholder="Enter Passport No">
                                                <input v-else type="text" name="passport_no" class="form-control input-sm" placeholder="Enter Passport No">

                                                <span v-if="errors.passport_no" class="text-danger" v-text="errors.passport_no[0]">
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group" :class="{'has-error': errors.tin_no}">
                                                <label class="control-label">Tin No :</label>

                                                <input v-if="personals.details" type="text" name="tin_no" :value="personals.details.tin_no" class="form-control input-sm" placeholder="Enter Tin No">
                                                <input v-else type="text" name="tin_no" class="form-control input-sm" placeholder="Enter Tin No">

                                                <span v-if="errors.tin_no" class="text-danger" v-text="errors.tin_no[0]">
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group" :class="{'has-error': errors.personal_email}">
                                                <label class="control-label">Personal Email : <span class="text-danger">*</span></label>
                                                <input type="text" name="personal_email" :value="(personals.details)?personals.details.personal_email:personals.email" class="form-control input-sm" placeholder="Enter Personal Email">
                                                <span v-if="errors.personal_email" class="text-danger" v-text="errors.personal_email[0]">
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group" :class="{'has-error': errors.official_email}">
                                                <label class="control-label">Official Email : <span class="text-danger">*</span></label>
                                                <input type="text" name="official_email" :value="(personals.details)?personals.details.official_email:personals.email" class="form-control input-sm" placeholder="Enter Official Email">
                                                <span v-if="errors.official_email" class="text-danger" v-text="errors.official_email[0]">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group" :class="{'has-error': errors.phone_number}">
                                                <label class="control-label">Phone Number : <span class="text-danger">*</span></label>

                                                <input v-if="personals.details" type="text" name="phone_number" :value="personals.details.phone_number" class="form-control input-sm" placeholder="Enter Phone Number">
                                                <input v-else type="text" name="phone_number" class="form-control input-sm" placeholder="Enter Phone Number">

                                                <span v-if="errors.phone_number" class="text-danger" v-text="errors.phone_number[0]">
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group" :class="{'has-error': errors.birth_date}">
                                                <label class="control-label">Birth Date :</label>

                                                <input v-if="personals.details" type="text" name="birth_date" v-on:mouseover="myDatePicker" :value="personals.details.birth_date" class="mydatepicker form-control input-sm" placeholder="Enter Birth Date" readonly="readonly">
                                                <input v-else type="text" name="birth_date" v-on:mouseover="myDatePicker" class="mydatepicker form-control input-sm" placeholder="Enter Birth Date" readonly="readonly">

                                                <span v-if="errors.birth_date" class="text-danger" v-text="errors.birth_date[0]">
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group" :class="{'has-error': errors.joining_date}">
                                                <label class="control-label">Joining Date : <span class="text-danger">*</span></label>

                                                <input v-if="personals.details" type="text" name="joining_date" v-on:mouseover="myDatePicker" :value="personals.details.joining_date" class="mydatepicker form-control input-sm" placeholder="Enter Joining Date" readonly="readonly">
                                                <input v-else type="text" name="joining_date" v-on:mouseover="myDatePicker" class="mydatepicker form-control input-sm" placeholder="Enter Joining Date" readonly="readonly">

                                                <span v-if="errors.joining_date" class="text-danger" v-text="errors.joining_date[0]">
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group" :class="{'has-error': errors.blood_group_id}">
                                                <label class="control-label">Blood Group :</label>

                                                <select v-if="personals.details" class="form-control input-sm" name="blood_group_id" v-model="personals.details.blood_group_id">
                                                    <option value="0">---- Select Blood Group ----</option>
                                                    <option v-bind:value="blood_group.id" v-for="(blood_group, index) in blood_group" v-text="blood_group.blood_name"></option>
                                                </select>
                                                <select v-else class="form-control input-sm" name="blood_group_id">
                                                    <option value="0">---- Select Blood Group ----</option>
                                                    <option v-bind:value="blood_group.id" v-for="(blood_group, index) in blood_group" v-text="blood_group.blood_name"></option>
                                                </select>

                                                <span v-if="errors.blood_group_id" class="text-danger" v-text="errors.blood_group_id[0]">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group" :class="{'has-error': errors.gender}">
                                                <label class="control-label">Gender : <span class="text-danger">*</span></label>

                                                <div class="radio-custom mb5">
                                                    <input v-if="personals.details" id="male" name="gender" type="radio" value="male" :checked="personals.details.gender == 'male'">
                                                    <input v-else id="male" value="male" name="gender" type="radio">
                                                    <label for="male">Male</label>
                                                    
                                                    <input v-if="personals.details" id="female" name="gender" type="radio" value="female" :checked="personals.details.gender == 'female'">
                                                    <input v-else id="female" name="gender" type="radio" value="female">

                                                    <label for="female">Female</label>
                                                </div>

                                                <span v-if="errors.gender" class="text-danger" v-text="errors.gender[0]">
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group" :class="{'has-error': errors.marital_status}">
                                                <label class="control-label">Marital Status : <span class="text-danger">*</span></label>

                                                 <select v-if="personals.details" name="marital_status" class="form-control input-sm" v-model="personals.details.marital_status">
                                                    <option value="">...Select Marital Status...</option>
                                                    <option value="single">Single</option>
                                                    <option value="married">Married</option>
                                                    <option value="separated">Separated</option>
                                                    <option value="other">Other</option>
                                                </select>
                                                <select v-else name="marital_status" class="form-control input-sm">
                                                    <option value="">...Select Marital Status...</option>
                                                    <option value="single">Single</option>
                                                    <option value="married">Married</option>
                                                    <option value="separated">Separated</option>
                                                    <option value="other">Other</option>
                                                </select>

                                                <span v-if="errors.marital_status" class="text-danger" v-text="errors.marital_status[0]">
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group" :class="{'has-error': errors.religion_id}">
                                                <label class="control-label">Religion : <span class="text-danger">*</span></label>

                                               <select v-if="personals.details" class="form-control input-sm" name="religion_id" v-model="personals.details.religion_id">
                                                    <option value=" ">---- Select Religion ----</option>
                                                    <option v-bind:value="religion.id" v-for="(religion, index) in religions" v-text="religion.religion_name"></option>
                                                </select>
                                                <select v-else class="form-control input-sm" name="religion_id">
                                                    <option value=" ">---- Select Religion ----</option>
                                                    <option v-bind:value="religion.id" v-for="(religion, index) in religions" v-text="religion.religion_name"></option>
                                                </select>

                                                <span v-if="errors.religion_id" class="text-danger" v-text="errors.religion_id[0]">
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group" :class="{'has-error': errors.nationality}">
                                                <label class="control-label">Nationality :</label>

                                                <input v-if="personals.details" type="text" name="nationality" :value="personals.details.nationality" class="form-control input-sm" placeholder="Enter Nationality">
                                                <input v-else type="text" name="nationality" class="form-control input-sm" placeholder="Enter Nationality">

                                                <span v-if="errors.nationality" class="text-danger" v-text="errors.nationality[0]">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group" :class="{'has-error': errors.emergency_contact_person}">
                                                <label class="control-label">Emergency Contact Person :</label>

                                                <input v-if="personals.details" type="text" name="emergency_contact_person" :value="personals.details.emergency_contact_person" class="form-control input-sm" placeholder="Emergency Contact Person">
                                                <input v-else type="text" name="emergency_contact_person" class="form-control input-sm" placeholder="Emergency Contact Person">

                                                <span v-if="errors.emergency_contact_person" class="text-danger" v-text="errors.emergency_contact_person[0]">
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group" :class="{'has-error': errors.emergency_contact_number}">
                                                <label class="control-label">Emergency Contact Number :</label>

                                                <input v-if="personals.details" type="text" name="emergency_contact_number" :value="personals.details.emergency_contact_number" class="form-control input-sm" placeholder="Emergency Contact Number">
                                                <input v-else type="text" name="emergency_contact_number" class="form-control input-sm" placeholder="Emergency Contact Number">

                                                <span v-if="errors.emergency_contact_number" class="text-danger" v-text="errors.emergency_contact_number[0]"></span>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group" :class="{'has-error': errors.emergency_contact_address}">
                                                <label class="control-label">Emergency Contact Address :</label>

                                                <textarea v-if="personals.details" name="emergency_contact_address" class="form-control input-sm" cols="60" rows="1" placeholder="Emergency Contact Address" v-text="personals.details.emergency_contact_address"></textarea>
                                                <textarea v-else name="emergency_contact_address" class="form-control input-sm" cols="60" rows="1" placeholder="Emergency Contact Address"></textarea>

                                                <span v-if="errors.emergency_contact_address" class="text-danger" v-text="errors.emergency_contact_address[0]">
                                            </div>
                                        </div>
                                    </div>

                                <hr class="short alt">

                                <div class="section row mbn">
                                    <div class="col-sm-2 pull-right">
                                        <p class="text-left">
                                            <button type="submit"
                                                    name="save_personal_and_next" value="save_personal_and_next"
                                                    v-on:click="submit_button='save_personal_and_next'"
                                                    class="btn btn-dark btn-gradient dark btn-block">
                                                <span class="glyphicons glyphicons-ok_2"></span> &nbsp; Update & Next
                                                <span class="glyphicons glyphicons-right_arrow"></span>
                                            </button>
                                        </p>
                                    </div>

                                    <div class="col-sm-2 pull-right">
                                        <p class="text-left">
                                            <button type="submit" name="save_personal"
                                                    value="save_personal"
                                                    v-on:click="submit_button='save_personal'"
                                                    class="btn btn-dark btn-gradient dark btn-block">
                                                <span class="glyphicons glyphicons-ok_2"></span> &nbsp; Update Personal
                                                Info
                                            </button>
                                        </p>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>

                <!--- Education Info -->
                <div id="tab1_3" class="tab-pane @if(isset($id) && $tab == 'education') active @endif">
                    <div v-if="educations">
                        <span class="text-info">Employee No : </span>
                        <span v-text="educations.employee_no+', '"></span>
                        <span class="text-info">Employee Full Name : </span>
                        <span v-text="educations.first_name+' '+educations.middle_name+' '+educations.last_name"></span>
                    </div>
                    <div class="row mt20">
                        <div class="col-md-12">
                            <form id="add_education_from" v-on:submit.prevent="addNewEducation" method="post" enctype="multipart/form-data">

                                <!-- start v-if -->
                                <div v-if="educations.educations !=''">
                                    <div v-for="(education, index) in educations.educations" :class="index%2==0?'even':'odd'">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label">Education Level :</label>
                                                    <input type="text"
                                                           :value="education.institute.education_level.education_level_name"
                                                           class="form-control input-sm" disabled="disabled">
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label">Institute :</label>
                                                    <input type="text" :value="education.institute.institute_name"
                                                           class="form-control input-sm" disabled="disabled">
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label">Degree :</label>
                                                    <input type="text" :value="education.degree.degree_name"
                                                           class="form-control input-sm" disabled="disabled">
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label">Achievement:</label>
                                                    <input type="text" :value="education.achievement" class="form-control input-sm" disabled="disabled">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label">Pass Year : <span
                                                                class="text-danger">*</span></label>
                                                    <input type="text" :value="education.pass_year"
                                                           class="form-control input-sm" disabled="disabled">
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label">Result Type :</label>
                                                    <div class="radio-custom mb5">
                                                        <input id="result_type_cgpa"
                                                               :checked="education.result_type=='cgpa'" type="radio"
                                                               disabled="disabled" type="radio">
                                                        <label for="result_type_cgpa">CGPA</label>

                                                        <input id="result_type_division" type="radio"
                                                               :checked="education.result_type=='division'"
                                                               disabled="disabled">
                                                        <label for="result_type_division">Division</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-3" v-if="education.result_type=='cgpa'">
                                                <div class="form-group">
                                                    <label class="control-label">CGPA :</label>
                                                    <input type="text" :value="education.cgpa" disabled="disabled"
                                                           class="form-control input-sm">
                                                </div>
                                            </div>

                                            <div class="col-md-3" v-if="education.result_type=='division'">
                                                <div class="form-group">
                                                    <label class="control-label">Division :</label>
                                                    <select name="division" class="form-control input-sm" disabled="disabled">
                                                        <option value="1" :selected="education.division ==1">First Division</option>
                                                        <option value="2" :selected="education.division ==2">Second Division</option>
                                                        <option value="3" :selected="education.division ==3">Third Division</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label">Certificate:</label>
                                                    <div>
                                                        <a v-if="education.certificate" :href="'/files/'+education.user_id+'/'+education.certificate" target="_blank" class="text-success">
                                                            <i class="fa fa-2x fa-file-image-o"></i>
                                                            Click here to view certificate
                                                        </a>
                                                        <a v-else class="text-primary">
                                                            <i class="fa fa-2x fa-file-image-o"></i>
                                                            No certificate added.
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-1 pull-right">
                                                <div class="form-group mt5">
                                                    <button v-on:click.prevent="deleteEmployeeData(education.id,'education')"
                                                            class="btn btn-sm btn-danger btn-gradient dark btn-block"
                                                            data-effect="mfp-with-fade"><span
                                                            class="glyphicons glyphicons-bin"></span> &nbsp; Delete
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col-md-1 pull-right">
                                                <div class="form-group mt5">
                                                    <button id="add_education" v-on:click.prevent="getDataByTabAndId('education',education.id,'#add_new_education_modal'),errors=[]"
                                                            class="btn btn-sm btn-success btn-gradient dark btn-block"
                                                            data-effect="mfp-with-fade"><span
                                                            class="glyphicons glyphicons-edit"></span> &nbsp; Edit
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <hr class="short alt">
                                    </div>
                                </div>
                                <!-- end v-if -->

                                <!-- start v-else -->
                                <div v-else>
                                    <input type="hidden" name="user_id" :value="user_id">
                                    <div class="row">

                                        <div class="col-md-3">
                                            <div class="form-group" :class="{'has-error': errors.education_level_id}">
                                                <label class="control-label">Education Level : <span
                                                            class="text-danger">*</span></label>
                                                <select class="form-control input-sm" name="education_level_id"
                                                        v-on:change="getInstituteAndDegreeByEducationLevelId()"
                                                        v-model="education_level_id">
                                                    <option v-bind:value="''">---- Select Education Level ----</option>
                                                    <option v-bind:value="education_level.id"
                                                            v-for="(education_level, index) in education_levels" v-text="education_level.education_level_name"></option>
                                                </select>
                                                <span v-if="errors.education_level_id" class="text-danger" v-text="errors.education_level_id[0]"></span>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group" :class="{'has-error': errors.institute_id}">
                                                <label class="control-label">Institute : <span
                                                            class="text-danger">*</span></label>
                                                <select2 class="form-control input-sm" name="institute_id">
                                                    <option v-bind:value="''">---- Select Institute ----</option>
                                                    <option v-bind:value="institute.id"
                                                            v-for="(institute, index) in institutes" v-text="institute.institute_name"></option>
                                                </select2>
                                                <span v-if="errors.institute_id" class="text-danger" v-text="errors.institute_id[0]"></span>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group" :class="{'has-error': errors.degree_id}">
                                                <label class="control-label">Degree : <span
                                                            class="text-danger">*</span></label>
                                                <select2 class="form-control input-sm" name="degree_id">
                                                    <option v-bind:value="''">---- Select Degree ----</option>
                                                    <option v-bind:value="degree.id"
                                                            v-for="(degree, index) in degrees" v-text="degree.degree_name"></option>
                                                </select2>
                                                <span v-if="errors.degree_id" class="text-danger" v-text="errors.degree_id[0]"></span>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group" :class="{'has-error': errors.achievement}">
                                                <label class="control-label">Achievement:</label>
                                                <input type="text" name="achievement" class="form-control input-sm">
                                                
                                                <span v-if="errors.achievement" class="text-danger" v-text="errors.achievement[0]"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group" :class="{'has-error': errors.pass_year}">
                                                <label class="control-label">Pass Year : <span
                                                            class="text-danger">*</span></label>
                                                <input type="text" name="pass_year" class="date form-control input-sm" v-on:mouseover="datePickerYear"
                                                       readonly="">
                                                <span v-if="errors.pass_year" class="text-danger" v-text="errors.pass_year[0]"></span>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group" :class="{'has-error': errors.result_type}">
                                                <label class="control-label">Result Type : <span
                                                            class="text-danger">*</span></label>
                                                <div class="radio-custom mb5">
                                                    <input id="result_type_cgpa" name="result_type" type="radio"
                                                           value="cgpa"
                                                           v-on:click="showCgpa=true,showDivision=false">
                                                    <label for="result_type_cgpa">CGPA</label>

                                                    <input id="result_type_division" name="result_type" type="radio"
                                                           value="division"
                                                           v-on:click="showCgpa=false,showDivision=true">
                                                    <label for="result_type_division">Division</label>
                                                </div>
                                                <span v-if="errors.result_type" class="text-danger" v-text=" errors.result_type[0]"></span>
                                            </div>
                                        </div>

                                        <div class="col-md-3" v-if="showCgpa">
                                            <div class="form-group" :class="{'has-error': errors.cgpa}">
                                                <label class="control-label">CGPA : <span
                                                            class="text-danger">*</span></label>
                                                <input type="text" name="cgpa"
                                                       class="form-control input-sm" placeholder="Enter CGPA">
                                                <span v-if="errors.cgpa" class="text-danger" v-text="errors.cgpa[0]"></span>
                                            </div>
                                        </div>

                                        <div class="col-md-3" v-if="showDivision">
                                            <div class="form-group" :class="{'has-error': errors.division}">
                                                <label class="control-label">Division : <span
                                                            class="text-danger">*</span></label>
                                                <select name="division" class="form-control input-sm">
                                                    <option value="">--- Select Division ---</option>
                                                    <option value="1">
                                                        First Division
                                                    </option>
                                                    <option value="2" >
                                                        Second Division
                                                    </option>
                                                    <option value="3">
                                                        Third Division
                                                    </option>
                                                </select>
                                                <span v-if="errors.division" class="text-danger" v-text="errors.division[0]"></span>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group" :class="{'has-error': errors.certificate_file}">
                                                <label class="control-label">Certificate:</label>
                                                <input type="file" name="certificate_file"
                                                       class="form-control btn-primary input-sm">
                                                <span v-if="errors.certificate_file" class="text-danger" v-text="errors.certificate_file[0]"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- end v-else -->

                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group mt25">
                                            <button id="add_education" :disabled="educations.educations ==''" v-on:click="singleEducation=[],showCgpa=true,showDivision=false,errors=[]"
                                                    onclick="modal_open('#add_education','#add_new_education_modal')"
                                                    class="btn btn-sm btn-dark btn-gradient dark btn-block"
                                                    data-effect="mfp-with-fade"><span
                                                        class="glyphicons glyphicons-briefcase"></span> &nbsp; Add New
                                                Education
                                            </button>
                                        </div>
                                    </div>
                                </div>


                                <hr class="short alt">

                                <div class="section row mbn">
                                    <div class="col-sm-2 pull-right">
                                        <p class="text-left">
                                            <button type="submit" v-if="educations.educations ==''"
                                                    name="save_education_and_next" value="save_education_and_next" v-on:click="submit_button='save_education_and_next'"
                                                    class="btn btn-dark btn-gradient dark btn-block">
                                                <span class="glyphicons glyphicons-ok_2"></span> &nbsp; Update & Next
                                                <span class="glyphicons glyphicons-right_arrow"></span>
                                            </button>
                                        </p>
                                    </div>

                                    <div class="col-sm-2 pull-right">
                                        <p class="text-left">
                                            <button type="submit" v-if="educations.educations ==''"
                                                    name="save_education" value="save_education" v-on:click="submit_button='save_education'"
                                                    class="btn btn-dark btn-gradient dark btn-block"><span
                                                        class="glyphicons glyphicons-ok_2"></span> &nbsp; Update Education
                                            </button>
                                        </p>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>

                <!---  Experience Info -->
                <div id="tab1_4" class="tab-pane @if(isset($id) && $tab == 'experience') active @endif">
                    <div v-if="experiences">
                        <span class="text-info">Employee No : </span>
                        <span v-text="experiences.employee_no+', '"></span>
                        <span class="text-info">Employee Full Name : </span>
                        <span v-text="experiences.first_name+' '+experiences.middle_name+' '+experiences.last_name"></span>
                    </div>
                    <div class="row mt20">
                        <div class="col-md-12">
                            <form id="add_experience_form" v-on:submit.prevent="addNewExperience" method="post">

                                <!-- start v-if -->
                                <div v-if="experiences.experiences !=''">
                                    <!-- start v-for in experiences   -->
                                    <div v-for="(experience, index) in experiences.experiences" :class="index%2==0?'even':'odd'">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label">Company Name : <span
                                                                class="text-danger">*</span></label>
                                                    <input type="text" class="form-control input-sm"
                                                           :value="experience.company_name" disabled="disabled">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label">Position Held : <span
                                                                class="text-danger">*</span></label>
                                                    <input type="text" class="form-control input-sm"
                                                           :value="experience.position_held" disabled="disabled">
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="control-label">Job Start Date : <span
                                                                class="text-danger">*</span></label>
                                                    <input type="text" class="form-control input-sm"
                                                           :value="experience.job_start_date" disabled="disabled">
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="control-label">Job End Date : <span
                                                                class="text-danger">*</span></label>
                                                    <input type="text" class="form-control input-sm"
                                                           :value="experience.job_end_date" disabled="disabled">
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="control-label">Job Duration : <span
                                                                class="text-danger">*</span></label>
                                                    <input type="text" class="form-control input-sm"
                                                           :value="experience.job_duration"
                                                           disabled="disabled">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">Job Responsibility : <span class="text-danger">*</span></label>
                                                    <textarea type="text" class="form-control input-sm" disabled="disabled" v-text="experience.job_responsibility"></textarea>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">Job Location : <span class="text-danger">*</span></label>
                                                    <textarea type="text" class="form-control input-sm" disabled="disabled" v-text="experience.job_location"></textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-1 pull-right">
                                                <div class="form-group mt5">
                                                    <button v-on:click.prevent="deleteEmployeeData(experience.id,'experience')"
                                                            class="btn btn-sm btn-danger btn-gradient dark btn-block"
                                                            data-effect="mfp-with-fade"><span
                                                            class="glyphicons glyphicons-bin"></span> &nbsp; Delete
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col-md-1 pull-right">
                                                <div class="form-group mt5">
                                                    <button id="add_new_experience_button" v-on:click.prevent="getDataByTabAndId('experience',experience.id,'#add_new_experience_modal'),errors=[]" class="btn btn-sm btn-success btn-gradient dark btn-block"
                                                            data-effect="mfp-with-fade"><span
                                                            class="glyphicons glyphicons-edit"></span> &nbsp; Edit
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <hr class="short alt">
                                    </div>
                                    <!-- end v-for -->
                                </div>

                                <!-- start v-else -->
                                <div v-else>
                                    <input type="hidden" name="user_id" :value="user_id" class="form-control input-sm">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group" :class="{'has-error': errors.company_name}">
                                                <label class="control-label">Company Name : <span class="text-danger">*</span></label>
                                                <input type="text" name="company_name" class="form-control input-sm">
                                                <span v-if="errors.company_name" class="text-danger" v-text="errors.company_name[0]"></span>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group" :class="{'has-error': errors.position_held}">
                                                <label class="control-label">Position Held : <span class="text-danger">*</span></label>
                                                <input type="text" name="position_held" class="form-control input-sm">
                                                <span v-if="errors.position_held" class="text-danger" v-text="errors.position_held[0]"></span>
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-group" :class="{'has-error': errors.job_start_date}">
                                                <label class="control-label">Job Start Date : <span class="text-danger">*</span></label>
                                                <input type="text" id="job_start_date" v-on:mouseover="myDatePicker" v-on:change="theDuration3" name="job_start_date" class="mydatepicker form-control input-sm" readonly="readonly">
                                                <span v-if="errors.job_start_date" class="text-danger" v-text="errors.job_start_date[0]"></span>
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-group" :class="{'has-error': errors.job_end_date}">
                                                <label class="control-label">Job End Date : <span class="text-danger">*</span></label>
                                                <input type="text" name="job_end_date" v-on:mouseover="myDatePicker" id="job_end_date" class="mydatepicker form-control input-sm" readonly="readonly">
                                                <span v-if="errors.job_end_date" class="text-danger" v-text="errors.job_end_date[0]"></span>
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-group" :class="{'has-error': errors.job_duration}">
                                                <label class="control-label">Job Duration : <span class="text-danger">*</span></label>
                                                <input type="text" name="job_duration" v-on:click="theDuration" :value="job_duration" class="form-control input-sm">
                                                <span v-if="errors.job_duration" class="text-danger" v-text="errors.job_duration[0]"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group" :class="{'has-error': errors.job_responsibility}">
                                                <label class="control-label">Job Responsibility : <span class="text-danger">*</span></label>
                                                <textarea type="text" name="job_responsibility" class="form-control input-sm"></textarea>
                                                <span v-if="errors.job_responsibility" class="text-danger" v-text="errors.job_responsibility[0]"></span>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group" :class="{'has-error': errors.job_location}">
                                                <label class="control-label">Job Location : <span class="text-danger">*</span></label>
                                                <textarea type="text" name="job_location" class="form-control input-sm"></textarea>
                                                <span v-if="errors.job_location" class="text-danger" v-text="errors.job_location[0]"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- end v-else -->

                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group mt25">
                                            <button id="add_new_experience_button" :disabled="experiences.experiences ==''" v-on:click="singleExperience=[],errors=[]"
                                                    onclick="modal_open('#add_new_experience_button','#add_new_experience_modal')" class="btn btn-sm btn-dark btn-gradient dark btn-block"
                                                    data-effect="mfp-with-fade"><span class="glyphicons glyphicons-briefcase"></span> &nbsp; Add New
                                                Experience
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <hr class="short alt">

                                <div class="section row mbn" v-if="experiences.experiences ==''">
                                    <div class="col-sm-2 pull-right">
                                        <p class="text-left">
                                            <button type="submit"
                                                    name="save_experience_and_next" v-on:click="submit_button='save_experience_and_next'"
                                                    class="btn btn-dark btn-gradient dark btn-block"><span
                                                        class="glyphicons glyphicons-ok_2"></span> &nbsp; Update & Next
                                                <span class="glyphicons glyphicons-right_arrow"></span>
                                            </button>
                                        </p>
                                    </div>

                                    <div class="col-sm-2 pull-right">
                                        <p class="text-left">
                                            <button type="submit"
                                                    name="save_experience" v-on:click="submit_button='save_experience'"
                                                    class="btn btn-dark btn-gradient dark btn-block"><span
                                                        class="glyphicons glyphicons-ok_2"></span> &nbsp; Update Experience
                                            </button>
                                        </p>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>

                <!---  Salary Info -->
                <div id="tab1_5" class="tab-pane @if(isset($id) && $tab == 'salary') active @endif">
                    <div v-if="salaries">
                        <span class="text-info">Employee No : </span>
                        <span v-text="salaries.employee_no+', '"></span>
                        <span class="text-info">Employee Full Name : </span>
                        <span v-text="salaries.first_name+' '+salaries.middle_name+' '+salaries.last_name"></span>
                    </div>
                    <div class="row mt20">
                        <div class="col-md-12">
                            <form id="add_salary_form" v-on:submit.prevent="addSalary" method="post">
                                <input type="hidden" name="user_id" :value="user_id" class="form-control input-sm">

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group" :class="{'has-error': errors.basic_salary}">
                                            <label class="control-label">Basic Salary Amount: <span class="text-danger">*</span></label>
                                            <input type="text" name="basic_salary" class="form-control input-sm" :value="(salaries.basic_salary)?salaries.basic_salary:levelSalaryInfos.level_salary_amount">
                                            <span v-if="errors.basic_salary" class="help-block" v-text="errors.basic_salary[0]"></span>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group" :class="{'has-error': errors.salary_in_cache}">
                                            <label class="control-label">Salary in Cache:</label>
                                            <input type="text" name="salary_in_cache" class="form-control input-sm" :value="salaries.salary_in_cache">
                                            <span v-if="errors.salary_in_cache" class="help-block" v-text="errors.salary_in_cache[0]"></span>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group" :class="{'has-error': errors.effective_date}">
                                            <label class="control-label">Salary Effective Date: <span class="text-danger">*</span></label>
                                            <input type="text" name="effective_date" v-model="salaries.effective_date" class="mydatepicker form-control input-sm" v-on:mouseover="myDatePicker" readonly="readonly">
                                            <span v-if="errors.effective_date" class="help-block" v-text="errors.effective_date[0]"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="admin-form">
                                    <div class="section-divider mb40">
                                        <span class="bg-white">Salary Allowance Information</span>
                                    </div>
                                </div>

                                <div class="row" v-if="salaries.salaries ==''">
                                    <div class="col-md-3 " v-for="levelSalaryInfo in levelSalaryInfos.salary_info">
                                        <div class="checkbox-custom mb5 pull-left">
                                            <input :id="levelSalaryInfo.basic_salary_info.id" type="checkbox" :name="'salary_info['+levelSalaryInfo.basic_salary_info.id+'][id]'" :value="levelSalaryInfo.basic_salary_info.id">

                                            <label :for="levelSalaryInfo.basic_salary_info.id" v-text="(levelSalaryInfo.basic_salary_info.salary_info_amount_status==0)?levelSalaryInfo.basic_salary_info.salary_info_name+' (%) ':levelSalaryInfo.basic_salary_info.salary_info_name+' ($) '"></label>

                                        </div>

                                        <select class="col-md-2 form-control input-sm mb5" :name="'salary_info['+levelSalaryInfo.basic_salary_info.id+'][type]'">
                                            <option value="percent" :selected="levelSalaryInfo.basic_salary_info.salary_info_amount_status ==0">Percent</option>
                                            <option value="fixed" :selected="levelSalaryInfo.basic_salary_info.salary_info_amount_status ==1">Fixed</option>
                                        </select>

                                        <input type="number" :name="'salary_info['+levelSalaryInfo.basic_salary_info.id+'][amount]'" :value="levelSalaryInfo.amount" class="col-md-2 form-control input-sm mb5">

                                        <input type="text" :name="'salary_info['+levelSalaryInfo.basic_salary_info.id+'][effective_date]'" v-on:mouseover="myDatePicker" class="mydatepicker form-control input-sm">
                                    </div>

                                        <!-- add other allowance -->
                                    <div class="col-md-3 mb15" v-for="allowance in otherAllowance">
                                        <div class="checkbox-custom mb5 pull-left">

                                            <input :id="allowance.id" type="checkbox" :name="'salary_info['+allowance.id+'][id]'" :value="allowance.id" checked="checked">

                                            <label :for="allowance.id" v-text="(allowance.salary_info_amount_status==0)?allowance.salary_info_name+' (%) ':allowance.salary_info_name+' ($) '"></label>
                                        </div>

                                        <select class="col-md-2 form-control input-sm mb5" :name="'salary_info['+allowance.id+'][type]'">
                                            <option value="percent" :selected="allowance.salary_info_amount_status ==0">Percent</option>
                                            <option value="fixed" :selected="allowance.salary_info_amount_status ==1">Fixed</option>
                                        </select>

                                        <input type="number" :name="'salary_info['+allowance.id+'][amount]'" :value="allowance.salary_info_amount" class="col-md-2 form-control input-sm mb5">

                                        <input type="text" :name="'salary_info['+allowance.id+'][effective_date]'" class="mydatepicker form-control input-sm" v-on:mouseover="myDatePicker">
                                    </div>

                                </div>

                                <div class="row" v-else>
                                    <div class="col-md-3 " v-for="salary in salaries.salaries">
                                        <div class="checkbox-custom mb5 pull-left">
                                            <input :id="salary.basic_salary_info_id" type="checkbox" :name="'salary_info['+salary.basic_salary_info_id+'][id]'" checked="checked" :value="salary.basic_salary_info_id">

                                            <label :for="salary.basic_salary_info_id" v-text="(salary.salary_amount_type=='percent')?salary.basic_salary_info.salary_info_name+' (%) ':salary.basic_salary_info.salary_info_name+' ($) '"></label>
                                        </div>

                                        <input type="hidden" :name="'salary_info['+salary.basic_salary_info_id+'][salary_id]'" :value="salary.id">

                                        <select class="col-md-2 form-control input-sm mb5" :name="'salary_info['+salary.basic_salary_info_id+'][type]'">
                                            <option value="percent" :selected="salary.salary_amount_type =='percent'">Percent</option>
                                            <option value="fixed" :selected="salary.salary_amount_type =='fixed'">Fixed</option>
                                        </select>

                                        <input type="number" :name="'salary_info['+salary.basic_salary_info_id+'][amount]'" :value="salary.salary_amount" class="col-md-2 form-control input-sm mb5">

                                        <input type="text" :value="salary.salary_effective_date" :name="'salary_info['+salary.basic_salary_info_id+'][effective_date]'" class="mydatepicker form-control input-sm" v-on:mouseover="myDatePicker">
                                    </div>

                                     <!-- add other allowance -->
                                    <div class="col-md-3 mb15" v-for="allowance in otherAllowance">
                                        <div class="checkbox-custom mb5 pull-left">

                                            <input :id="allowance.id" type="checkbox" :name="'salary_info['+allowance.id+'][id]'" :value="allowance.id" checked="checked">

                                            <label :for="allowance.id" v-text="(allowance.salary_info_amount_status==0)?allowance.salary_info_name+' (%) ':allowance.salary_info_name+' ($) '"></label>
                                        </div>

                                        <select class="col-md-2 form-control input-sm mb5" :name="'salary_info['+allowance.id+'][type]'">
                                            <option value="percent" :selected="allowance.salary_info_amount_status ==0">Percent</option>
                                            <option value="fixed" :selected="allowance.salary_info_amount_status ==1">Fixed</option>
                                        </select>

                                        <input type="number" :name="'salary_info['+allowance.id+'][amount]'" :value="allowance.salary_info_amount" class="col-md-2 form-control input-sm mb5">

                                        <input type="text" :name="'salary_info['+allowance.id+'][effective_date]'" class="mydatepicker form-control input-sm" v-on:mouseover="myDatePicker">
                                    </div>
                                </div>



                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group mt25">
                                            <button id="add_more_allownce_button" v-on:click.prevent="getAllowanceNotinLevel('#add_more_allownce_modal')" class="btn btn-sm btn-dark btn-gradient dark btn-block"
                                                    data-effect="mfp-with-fade"><span class="glyphicons glyphicons-briefcase"></span> &nbsp; Add More Allowance
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="admin-form">
                                        <div class="section-divider mb40">
                                            <span class="bg-white">Salary Account Information</span>
                                        </div>
                                    </div>

                                    <input type="hidden" name="salary_account_id" :value="(salaries.salary_account)?salaries.salary_account.id:''">

                                    <div class="col-md-2">
                                        <div class="form-group" :class="{'has-error': errors.bank_id}">
                                            <label class="control-label">Bank Name : </label>
                                            <select v-if="salaries.salary_account" class="form-control input-sm" name="bank_id">
                                            <option :value="''">---- Select Bank Name ----</option>
                                            <option v-for="(bank,index) in banks"
                                                    :value="bank.id" :selected="salaries.salary_account.bank_id">@{{ bank.bank_name }}</option>
                                            </select>
                                            <select v-else class="form-control input-sm" name="bank_id">
                                                <option :value="''">---- Select Bank Name ----</option>
                                                <option v-for="(bank,index) in banks"
                                                        :value="bank.id">@{{ bank.bank_name }}</option>
                                            </select>

                                            <span v-if="errors.bank_id" class="help-block">@{{errors.bank_id[0]}}</span>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group" :class="{'has-error': errors.bank_account_no}">
                                            <label class="control-label">Account No : </label>
                                            
                                            <input v-if="salaries.salary_account" type="text" name="bank_account_no" class="form-control input-sm" :value="salaries.salary_account.bank_account_no">
                                            <input v-else type="text" name="bank_account_no" class="form-control input-sm">
                                            
                                            <span v-if="errors.bank_account_no" class="help-block">@{{errors.bank_account_no[0]}}</span>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group" :class="{'has-error': errors.bank_account_name}">
                                            <label class="control-label">Account Name : </label>
                                            <input v-if="salaries.salary_account" type="text" name="bank_account_name" class="form-control input-sm" :value="salaries.salary_account.bank_account_name">
                                        <input v-else type="text" name="bank_account_name" class="form-control input-sm">
                                            <span v-if="errors.bank_account_name" class="help-block">@{{errors.bank_account_name[0]}}</span>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group" :class="{'has-error': errors.bank_branch_name}">
                                            <label class="control-label">Bank Branch : </label>
                                            <input v-if="salaries.salary_account" type="text" name="bank_branch_name" class="form-control input-sm" :value="salaries.salary_account.bank_branch_name">
                                            <input v-else type="text" name="bank_branch_name" class="form-control input-sm">
                                            <span v-if="errors.bank_branch_name" class="help-block">@{{errors.bank_branch_name[0]}}</span>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group" :class="{'has-error': errors.bank_branch_address}">
                                            <label class="control-label">Bank Branch Address: </label>
                                            <textarea v-if="salaries.salary_account" type="text" name="bank_branch_address" class="form-control input-sm" v-text="salaries.salary_account.bank_branch_name"></textarea>
                                            <textarea v-else type="text" name="bank_branch_address" class="form-control input-sm"></textarea>
                                            <span v-if="errors.bank_branch_address" class="help-block">@{{errors.bank_branch_address[0]}}</span>
                                        </div>
                                    </div>
                                </div>

                                <hr class="short alt">

                                <div class="section row mbn">
                                    <div class="col-sm-2 pull-right">
                                        <p class="text-left">
                                            <button type="submit"
                                                    name="save_salary_and_next" v-on:click="submit_button='save_salary_and_next'"
                                                    class="btn btn-dark btn-gradient dark btn-block"><span
                                                        class="glyphicons glyphicons-ok_2"></span> &nbsp; Update & Next
                                                <span class="glyphicons glyphicons-right_arrow"></span>
                                            </button>
                                        </p>
                                    </div>

                                    <div class="col-sm-2 pull-right">
                                        <p class="text-left">
                                            <button type="submit"
                                                    name="save_salary" v-on:click="submit_button='save_salary'"
                                                    class="btn btn-dark btn-gradient dark btn-block"><span
                                                        class="glyphicons glyphicons-ok_2"></span> &nbsp; Update Salary
                                            </button>
                                        </p>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>

                <!---  Nominee Info -->
                <div id="tab1_6" class="tab-pane @if(isset($id) && $tab == 'nominee') active @endif">
                    <div v-if="nominees">
                        <span class="text-info">Employee No : </span>
                        <span v-text="nominees.employee_no+', '"></span>
                        <span class="text-info">Employee Full Name : </span>
                        <span v-text="nominees.first_name+' '+nominees.middle_name+' '+nominees.last_name"></span>
                    </div>
                    <div class="row mt20">
                        <div class="col-md-12">
                            <form id="add_nominee_form" v-on:submit.prevent="addNomineeInfo" method="post">
                                <input type="hidden" name="user_id" :value="user_id">

                                <div v-if="nominees.nominees !=''">
                                    <div v-for="(nominee,index) in nominees.nominees" :class="index%2==0?'even':'odd'">
                                    <div class="row">
                                    <div class="col-md-2">
                                        <label class="control-label">Nominee Photo :</label>

                                        <div class="fileupload-new admin-form" data-provides="fileupload">
                                            <div class="fileupload-preview thumbnail mb5">
                                                 <img v-if="nominee.nominee_photo" :src="'/files/'+nominee.user_id+'/'+nominee.nominee_photo" alt="holder">
                                                <img v-else src="{{asset('img/placeholder.png')}}" alt="holder">
                                            </div>
                                            <!-- <span class="button btn btn-sm btn-dark btn-file btn-block ph5">
                                                <span class="fileupload-exists"><span class="fa fa-user"></span> &nbsp; <strong>Change Photo</strong></span>
                                                <span class="fileupload-new"><span class="fa fa-user"></span> &nbsp; <strong>Select Photo</strong></span>
                                                <input type="file" name="image">
                                            </span> -->
                                        </div>
                                    </div>

                                    <div class="col-md-10 mt40">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="control-label">Nominee Name : <span class="text-danger">*</span></label>
                                                    <input type="text" name="nominee_name" :value="nominee.nominee_name" class="form-control input-sm" disabled="disabled">
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="control-label">Nominee Relation : <span class="text-danger">*</span></label>
                                                    <input type="text" name="nominee_relation" class="form-control input-sm" :value="nominee.nominee_relation" disabled="disabled">
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="control-label">Nominee Birth Date : <span class="text-danger">*</span></label>
                                                    <input type="text" name="nominee_birth_date" class="form-control input-sm" readonly="readonly" :value="nominee.nominee_birth_date">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="control-label">Nominee Distribution : <span class="text-danger">*</span></label>
                                                    <input type="text" name="nominee_distribution" class="form-control input-sm" :value="nominee.nominee_distribution" readonly="readonly">
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="control-label">Nominee Rest Distribution : <span class="text-danger">*</span></label>
                                                    <input type="text" name="nominee_rest_distribution" class="form-control input-sm" :value="nominee.nominee_rest_distribution" readonly="readonly">
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="control-label">Nominee Address : <span class="text-danger">*</span></label>
                                                    <input type="text" name="nominee_address" class="form-control input-sm" :value="nominee.nominee_address" readonly="readonly">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-sm-2 pull-right">
                                                <div class="form-group mt5">
                                                    <button v-on:click.prevent="deleteEmployeeData(nominee.id,'nominee')"
                                                            class="btn btn-sm btn-danger btn-gradient dark btn-block"
                                                            data-effect="mfp-with-fade"><span
                                                            class="glyphicons glyphicons-bin"></span> &nbsp; Delete
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col-sm-2 pull-right">
                                                <div class="form-group mt5">
                                                    <button id="add_new_nominee_button" v-on:click.prevent="getDataByTabAndId('nominee',nominee.id,'#add_new_nominee_modal'),errors=[]" class="btn btn-sm btn-success btn-gradient dark btn-block"
                                                            data-effect="mfp-with-fade"><span
                                                            class="glyphicons glyphicons-edit"></span> &nbsp; Edit
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <hr class="short alt">
                                    </div>
                                    </div>
                                    </div>
                                </div>

                                <div v-else>
                                    <div class="row">
                                    <div class="col-md-2" :class="{'has-error': errors.image}">
                                        <label class="control-label">Nominee Photo :</label>
                                        <div class="fileupload-new admin-form" data-provides="fileupload">
                                            <div class="fileupload-preview thumbnail mb5">
                                                <img src="{{asset('img/placeholder.png')}}" alt="holder">
                                            </div>
                                            <span class="button btn btn-sm btn-dark btn-file btn-block ph5">
                                            <span class="fileupload-exists"><span class="fa fa-user"></span> &nbsp; <strong>Change Photo</strong></span>
                                            <span class="fileupload-new"><span class="fa fa-user"></span> &nbsp; <strong>Select Photo</strong></span>
                                            <input type="file" name="image">
                                        </span>
                                        </div>
                                        <span v-if="errors.image" class="help-block" v-text="errors.image[0]"></span>
                                    </div>

                                    <div class="col-md-10 mt40">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group" :class="{'has-error': errors.nominee_name}">
                                                    <label class="control-label">Nominee Name : <span class="text-danger">*</span></label>
                                                    <input type="text" name="nominee_name" class="form-control input-sm">
                                                    <span v-if="errors.nominee_name" class="help-block" v-text="errors.nominee_name[0]"></span>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group" :class="{'has-error': errors.nominee_relation}">
                                                    <label class="control-label">Nominee Relation : <span class="text-danger">*</span></label>
                                                    <input type="text" name="nominee_relation" class="form-control input-sm">
                                                    <span v-if="errors.nominee_relation" class="help-block" v-text="errors.nominee_relation[0]"></span>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group" :class="{'has-error': errors.nominee_birth_date}">
                                                    <label class="control-label">Nominee Birth Date : <span class="text-danger">*</span></label>
                                                    <input type="text" name="nominee_birth_date" v-on:mouseover="myDatePicker" class="mydatepicker form-control input-sm" readonly="readonly">
                                                    <span v-if="errors.nominee_birth_date" class="help-block" v-text="errors.nominee_birth_date[0]"></span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group" :class="{'has-error': errors.nominee_distribution}">
                                                    <label class="control-label">Nominee Distribution : <span class="text-danger">*</span></label>
                                                    <input type="text" name="nominee_distribution" v-on:keyup="nomineeDistribution" v-model="nominee_distribution" class="form-control input-sm">
                                                    <span v-if="errors.nominee_distribution" class="help-block" v-text="errors.nominee_distribution[0]"></span>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group" :class="{'has-error': errors.nominee_rest_distribution}">
                                                    <label class="control-label">Nominee Rest Distribution : <span class="text-danger">*</span></label>
                                                    <input type="text" name="nominee_rest_distribution" :value="nominee_rest_distribution" class="form-control input-sm" readonly="readonly">
                                                    <span v-if="errors.nominee_rest_distribution" class="help-block" v-text="errors.nominee_rest_distribution[0]"></span>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group" :class="{'has-error': errors.nominee_address}">
                                                    <label class="control-label">Nominee Address : <span class="text-danger">*</span></label>
                                                    <input type="text" name="nominee_address" class="form-control input-sm">
                                                    <span v-if="errors.nominee_address" class="help-block" v-text="errors.nominee_address[0]"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <hr class="short alt">
                                    </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group mt25">
                                            <button id="add_new_nominee_button" :disabled="nominees.nominees ==''" v-on:click="singleNominee=[],errors=[]"
                                                    onclick="modal_open('#add_new_nominee_button','#add_new_nominee_modal')" class="btn btn-sm btn-dark btn-gradient dark btn-block"
                                                    data-effect="mfp-with-fade"><span class="glyphicons glyphicons-briefcase"></span> &nbsp; Add New
                                                Nominee
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <hr class="short alt">

                                <div class="section row mbn">
                                    <div class="col-sm-2 pull-right">
                                        <p class="text-left">
                                            <button type="submit" :disabled="nominees.nominees !=''" 
                                                    name="save_nominee_and_next" v-on:click="submit_button='save_nominee_and_next'"
                                                    class="btn btn-dark btn-gradient dark btn-block"><span
                                                        class="glyphicons glyphicons-ok_2"></span> &nbsp; Update & Next
                                                <span class="glyphicons glyphicons-right_arrow"></span>
                                            </button>
                                        </p>
                                    </div>

                                    <div class="col-sm-2 pull-right">
                                        <p class="text-left">
                                            <button type="submit" :disabled="nominees.nominees !=''"
                                                    name="save_nominee" v-on:click="submit_button='save_nominee'"
                                                    class="btn btn-dark btn-gradient dark btn-block"><span
                                                        class="glyphicons glyphicons-ok_2"></span> &nbsp; Update Nominee
                                            </button>
                                        </p>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!---  Training Info -->
                <div id="tab1_7" class="tab-pane @if(isset($id) && $tab == 'training') active @endif">
                    <div v-if="trainings">
                        <span class="text-info">Employee No : </span>
                        <span v-text="trainings.employee_no+', '"></span>
                        <span class="text-info">Employee Full Name : </span>
                        <span v-text="trainings.first_name+' '+trainings.middle_name+' '+trainings.last_name"></span>
                    </div>
                    <div class="row mt20">
                        <div class="col-md-12">
                            <form id="add_training_form" v-on:submit.prevent="addNewTraining" method="post">
                                <input type="hidden" name="user_id" :value="user_id">

                                <div v-if="trainings.trainings !=''">
                                    <div v-for="(training,index) in trainings.trainings" :class="index%2==0?'even':'odd'">
                                        <div class="row">
                                            <!-- <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label">Training Code : <span class="text-danger">*</span></label>
                                                    <input type="text" :value="training.training_code" class="form-control input-sm" readonly="readonly">
                                                </div>
                                            </div> -->

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="control-label">Training Title  : <span class="text-danger">*</span></label>
                                                    <input type="text" :value="training.training_title" readonly="readonly" class="form-control input-sm">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="control-label">Training Institute : <span class="text-danger">*</span></label>
                                                    <input type="text" :value="training.training_institute" readonly="readonly" class="form-control input-sm">
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="control-label">Training From Date : <span class="text-danger">*</span></label>
                                                    <input type="text" :value="training.training_from_date" class="datepicker form-control input-sm" disabled="disabled">
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="control-label">Training From Date : <span class="text-danger">*</span></label>
                                                    <input type="text" :value="training.training_to_date" class="datepicker form-control input-sm" disabled="disabled">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label">Training Passed Date : <span class="text-danger">*</span></label>
                                                    <input type="text" :value="training.training_passed_date" class="datepicker form-control input-sm" disabled="disabled">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label">Training Participation Date : <span class="text-danger">*</span></label>
                                                    <input type="text" :value="training.training_participation_date" class="datepicker form-control input-sm" disabled="disabled">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">Training remarks : <span class="text-danger">*</span></label>
                                                    <textarea type="text" :value="training.training_remarks" readonly="readonly" class="form-control input-sm"></textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-1 pull-right">
                                                <div class="form-group mt5">
                                                    <button v-on:click.prevent="deleteEmployeeData(training.id,'training')"
                                                            class="btn btn-sm btn-danger btn-gradient dark btn-block"
                                                            data-effect="mfp-with-fade"><span
                                                            class="glyphicons glyphicons-bin"></span> &nbsp; Delete
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col-md-1 pull-right">
                                                <div class="form-group mt5">
                                                    <button id="add_new_training_button" v-on:click.prevent="getDataByTabAndId('training',training.id,'#add_new_training_modal'),errors=[]"
                                                            class="btn btn-sm btn-success btn-gradient dark btn-block"
                                                            data-effect="mfp-with-fade"><span
                                                            class="glyphicons glyphicons-edit"></span> &nbsp; Edit
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <hr class="short alt">
                                    </div>
                                </div>

                                <div v-else>
                                    <div class="row">
                                        <!-- <div class="col-md-3">
                                            <div class="form-group" :class="{'has-error': errors.training_code}">
                                                <label class="control-label">Training Code : <span class="text-danger">*</span></label>
                                                <input type="text" name="training_code" class="form-control input-sm">
                                                <span v-if="errors.training_code" class="text-danger">@{{ errors.training_code[0]}}</span>
                                            </div>
                                        </div> -->

                                        <div class="col-md-4">
                                            <div class="form-group" :class="{'has-error': errors.training_title}">
                                                <label class="control-label">Training Title  : <span class="text-danger">*</span></label>
                                                <input type="text" name="training_title" class="form-control input-sm">
                                                <span v-if="errors.training_title" class="text-danger" v-text="errors.training_title[0]"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group" :class="{'has-error': errors.training_institute}">
                                                <label class="control-label">Training Institute : <span class="text-danger">*</span></label>
                                                <input type="text" name="training_institute" class="form-control input-sm">
                                                <span v-if="errors.training_institute" class="text-danger" v-text="errors.training_institute[0]"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group" :class="{'has-error': errors.training_from_date}">
                                                <label class="control-label">Training From Date : <span class="text-danger">*</span></label>
                                                <input type="text" name="training_from_date" v-on:mouseover="myDatePicker" class="mydatepicker form-control input-sm" readonly="readonly">
                                                <span v-if="errors.training_from_date" class="text-danger" v-text="errors.training_from_date[0]"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group" :class="{'has-error': errors.training_to_date}">
                                                <label class="control-label">Training To Date : <span class="text-danger">*</span></label>
                                                <input type="text" name="training_to_date" v-on:mouseover="myDatePicker" class="mydatepicker form-control input-sm" readonly="readonly">
                                                <span v-if="errors.training_to_date" class="text-danger" v-text="errors.training_to_date[0]"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group" :class="{'has-error': errors.training_passed_date}">
                                                <label class="control-label">Training Passed Date : <span class="text-danger">*</span></label>
                                                <input type="text" name="training_passed_date" v-on:mouseover="myDatePicker" class="mydatepicker form-control input-sm" readonly="readonly">
                                                <span v-if="errors.training_passed_date" class="text-danger" v-text="errors.training_passed_date[0]"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group" :class="{'has-error': errors.training_participation_date}">
                                                <label class="control-label">Participation Date : <span class="text-danger">*</span></label>
                                                <input type="text" name="training_participation_date" v-on:mouserover="myDatePicker" class="mydatepicker form-control input-sm" readonly="readonly">
                                                <span v-if="errors.training_participation_date" class="text-danger" v-text="errors.training_participation_date[0]"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group" :class="{'has-error': errors.training_remarks}">
                                                <label class="control-label">Training remarks : <span class="text-danger">*</span></label>
                                                <textarea type="text" name="training_remarks" class="form-control input-sm"></textarea>
                                                <span v-if="errors.training_remarks" class="text-danger" v-text="errors.training_remarks[0]"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group mt25">
                                            <button id="add_new_training_button" :disabled="trainings.trainings ==''" v-on:click="singleTraining=[],errors=[]"
                                                    onclick="modal_open('#add_new_training_button','#add_new_training_modal')" class="btn btn-sm btn-dark btn-gradient dark btn-block"
                                                    data-effect="mfp-with-fade"><span class="glyphicons glyphicons-briefcase"></span> &nbsp; Add New Training
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <hr class="short alt">

                                <div class="section row mbn">
                                    <div class="col-sm-2 pull-right">
                                        <p class="text-left">
                                            <button type="submit" :disabled="trainings.trainings !=''"
                                                    name="save_training_and_next" v-on:click="submit_button='save_training_and_next'"
                                                    class="btn btn-dark btn-gradient dark btn-block"><span
                                                        class="glyphicons glyphicons-ok_2"></span> &nbsp; Update & Next
                                                <span class="glyphicons glyphicons-right_arrow"></span>
                                            </button>
                                        </p>
                                    </div>

                                    <div class="col-sm-2 pull-right">
                                        <p class="text-left">
                                            <button type="submit" :disabled="trainings.trainings !=''"
                                                    name="save_training" v-on:click="submit_button='save_training'"
                                                    class="btn btn-dark btn-gradient dark btn-block"><span
                                                        class="glyphicons glyphicons-ok_2"></span> &nbsp; Update Training
                                            </button>
                                        </p>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!---  Reference Info -->
                <div id="tab1_8" class="tab-pane @if(isset($id) && $tab == 'reference') active @endif">
                    <div v-if="references">
                        <span class="text-info">Employee No : </span>
                        <span v-text="references.employee_no+', '"></span>
                        <span class="text-info">Employee Full Name : </span>
                        <span v-text="references.first_name+' '+references.middle_name+' '+references.last_name"></span>
                    </div>
                    <div class="row mt20">
                        <div class="col-md-12">
                            <form id="add_reference_form" v-on:submit.prevent="addNewReference" method="post">
                                <input type="hidden" name="user_id" :value="user_id">

                                <div v-if="references.references !=''">
                                    <div v-for="reference in references.references">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="control-label">Reference Name : <span class="text-danger">*</span></label>
                                                    <input type="text" :value="reference.reference_name" class="form-control input-sm" readonly="readonly">
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="control-label">Reference Email  : <span class="text-danger">*</span></label>
                                                    <input type="text" :value="reference.reference_email" class="form-control input-sm" readonly="readonly">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="control-label">Reference Department : <span class="text-danger">*</span></label>
                                                    <input type="text" :value="reference.reference_department" class="form-control input-sm" readonly="readonly">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="control-label">Reference Organization : <span class="text-danger">*</span></label>
                                                    <input type="text" :value="reference.reference_organization" class="form-control input-sm" readonly="readonly">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="control-label">Reference Phone : <span class="text-danger">*</span></label>
                                                    <input type="text" :value="reference.reference_phone" class="form-control input-sm" readonly="readonly">
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="control-label">Reference Address : <span class="text-danger">*</span></label>
                                                    <textarea type="text" :value="reference.reference_address" class="form-control input-sm" readonly="readonly"></textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <hr class="short alt">

                                        <div class="row">
                                            <div class="col-md-1 pull-right">
                                                <div class="form-group mt5">
                                                    <button v-on:click.prevent="deleteEmployeeData(reference.id,'reference')"
                                                            class="btn btn-sm btn-danger btn-gradient dark btn-block"
                                                            data-effect="mfp-with-fade"><span
                                                            class="glyphicons glyphicons-bin"></span> &nbsp; Delete
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col-md-1 pull-right">
                                                <div class="form-group mt5">
                                                    <button id="add_new_reference_button" v-on:click.prevent="getDataByTabAndId('reference',reference.id,'#add_new_reference_modal'),errors=[]"
                                                            class="btn btn-sm btn-success btn-gradient dark btn-block"
                                                            data-effect="mfp-with-fade"><span
                                                            class="glyphicons glyphicons-edit"></span> &nbsp; Edit
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div v-else>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group" :class="{'has-error': errors.reference_name}">
                                                <label class="control-label">Reference Name : <span class="text-danger">*</span></label>
                                                <input type="text" name="reference_name" class="form-control input-sm">
                                                <span v-if="errors.reference_name" class="text-danger">@{{ errors.reference_name[0]}}</span>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group" :class="{'has-error': errors.reference_email}">
                                                <label class="control-label">Reference Email  : <span class="text-danger">*</span></label>
                                                <input type="text" name="reference_email" class="form-control input-sm">
                                                <span v-if="errors.reference_email" class="text-danger">@{{ errors.reference_email[0]}}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group" :class="{'has-error': errors.reference_department}">
                                                <label class="control-label">Reference Department : <span class="text-danger">*</span></label>
                                                <input type="text" name="reference_department" class="form-control input-sm">
                                                <span v-if="errors.reference_department" class="text-danger">@{{ errors.reference_department[0]}}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group" :class="{'has-error': errors.reference_organization}">
                                                <label class="control-label">Reference Organization : <span class="text-danger">*</span></label>
                                                <input type="text" name="reference_organization" class="form-control input-sm">
                                                <span v-if="errors.reference_organization" class="text-danger">@{{ errors.reference_organization[0]}}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group" :class="{'has-error': errors.reference_phone}">
                                                <label class="control-label">Reference Phone : <span class="text-danger">*</span></label>
                                                <input type="text" name="reference_phone" class="form-control input-sm">
                                                <span v-if="errors.reference_phone" class="text-danger">@{{ errors.reference_phone[0]}}</span>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group" :class="{'has-error': errors.reference_address}">
                                                <label class="control-label">Reference Address : <span class="text-danger">*</span></label>
                                                <textarea type="text" name="reference_address" class="form-control input-sm"></textarea>
                                                <span v-if="errors.reference_address" class="text-danger">@{{ errors.reference_address[0]}}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group mt25">
                                            <button id="add_new_reference_button" :disabled="references.references ==''" v-on:click="singleReference=[],errors=[]"
                                                    onclick="modal_open('#add_new_reference_button','#add_new_reference_modal')" class="btn btn-sm btn-dark btn-gradient dark btn-block"
                                                    data-effect="mfp-with-fade"><span class="glyphicons glyphicons-briefcase"></span> &nbsp; Add New Reference
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <hr class="short alt">

                                <div class="section row mbn">
                                    <div class="col-sm-2 pull-right">
                                        <p class="text-left">
                                            <button type="submit" :disabled="references.references !=''"
                                                    name="save_reference_and_next" v-on:click="submit_button='save_reference_and_next'"
                                                    class="btn btn-dark btn-gradient dark btn-block"><span
                                                        class="glyphicons glyphicons-ok_2"></span> &nbsp; Update & Next
                                                <span class="glyphicons glyphicons-right_arrow"></span>
                                            </button>
                                        </p>
                                    </div>

                                    <div class="col-sm-2 pull-right">
                                        <p class="text-left">
                                            <button type="submit" :disabled="references.references!=''"
                                                    name="save_reference" v-on:click="submit_button='save_reference'"
                                                    class="btn btn-dark btn-gradient dark btn-block"><span
                                                        class="glyphicons glyphicons-ok_2"></span> &nbsp; Update Reference
                                            </button>
                                        </p>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!---  Children Info -->
                <div id="tab1_9" class="tab-pane @if(isset($id) && $tab == 'children') active @endif">
                    <div v-if="childrens">
                        <span class="text-info">Employee No : </span>
                        <span v-text="childrens.employee_no+', '"></span>
                        <span class="text-info">Employee Full Name : </span>
                        <span v-text="childrens.first_name+' '+childrens.middle_name+' '+childrens.last_name"></span>
                    </div>
                    <div class="row mt20">
                        <div class="col-md-12">
                            <form id="add_children_form" v-on:submit.prevent="addNewChildren" method="post">
                                <input type="hidden" name="user_id" :value="user_id">

                                <div v-if="childrens.childrens !=''">
                                    <div v-for="children in childrens.childrens">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="control-label">Children Name : <span class="text-danger">*</span></label>
                                                    <input type="text" :value="children.children_name" class="form-control input-sm" readonly="readonly">
                                                </div>
                                            </div>

                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <label class="control-label">Children Education Level : <span class="text-danger">*</span></label>
                                                    <input type="text" :value="children.children_education_level" class="form-control input-sm" readonly="readonly">
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label">Children Birth Date  : <span class="text-danger">*</span></label>
                                                    <input type="text" :value="children.children_birth_date" class="datepicker form-control input-sm" readonly="readonly">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="control-label">Children Gender : <span class="text-danger">*</span></label>
                                                    <div class="radio-custom mb5">
                                                        <input :checked="children.children_gender=='male'" type="radio" readonly="readonly">
                                                        <label for="children_male">Male</label>

                                                        <input :checked="children.children_gender=='female'" type="radio" readonly="readonly">
                                                        <label for="children_female">Female</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-8">
                                                <div class="form-group" :class="{'has-error': errors.children_remarks}">
                                                    <label class="control-label">Children Remarks : <span class="text-danger">*</span></label>
                                                    <textarea name="children_remarks" class="form-control input-sm" readonly="readonly">@{{children.children_remarks}}</textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-1 pull-right">
                                                <div class="form-group mt5">
                                                    <button v-on:click.prevent="deleteEmployeeData(children.id,'children')"
                                                            class="btn btn-sm btn-danger btn-gradient dark btn-block"
                                                            data-effect="mfp-with-fade"><span
                                                            class="glyphicons glyphicons-bin"></span> &nbsp; Delete
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col-md-1 pull-right">
                                                <div class="form-group mt5">
                                                    <button id="add_new_children_button" v-on:click.prevent="getDataByTabAndId('children',children.id,'#add_new_children_modal'),errors=[]"
                                                            class="btn btn-sm btn-success btn-gradient dark btn-block"
                                                            data-effect="mfp-with-fade"><span
                                                            class="glyphicons glyphicons-edit"></span> &nbsp; Edit
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <hr class="short alt">
                                    </div>
                                </div>

                                <div v-else>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group" :class="{'has-error': errors.children_name}">
                                                <label class="control-label">Children Name : <span class="text-danger">*</span></label>
                                                <input type="text" name="children_name" class="form-control input-sm">
                                                <span v-if="errors.children_name" class="text-danger">@{{ errors.children_name[0]}}</span>
                                            </div>
                                        </div>

                                        <div class="col-md-5">
                                            <div class="form-group" :class="{'has-error': errors.children_education_level}">
                                                <label class="control-label">Children Education Level : <span class="text-danger">*</span></label>
                                                <input type="text" name="children_education_level" class="form-control input-sm">
                                                <span v-if="errors.children_education_level" class="text-danger">@{{ errors.children_education_level[0]}}</span>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group" :class="{'has-error': errors.children_birth_date}">
                                                <label class="control-label">Children Birth Date  : <span class="text-danger">*</span></label>
                                                <input type="text" name="children_birth_date" v-on:mouseover="myDatePicker" class="mydatepicker form-control input-sm" readonly="readonly">
                                                <span v-if="errors.children_birth_date" class="text-danger">@{{ errors.children_birth_date[0]}}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group" :class="{'has-error': errors.children_gender}">
                                                <label class="control-label">Children Gender : <span class="text-danger">*</span></label>
                                                <div class="radio-custom mb5">
                                                    <input id="children_male" name="children_gender" type="radio" value="male">
                                                    <label for="children_male">Male</label>
                                                    <input id="children_female" name="children_gender" type="radio" value="female">
                                                    <label for="children_female">Female</label>
                                                </div>
                                                <span v-if="errors.children_gender" class="text-danger">@{{ errors.children_gender[0]}}</span>
                                            </div>
                                        </div>

                                        <div class="col-md-8">
                                            <div class="form-group" :class="{'has-error': errors.children_remarks}">
                                                <label class="control-label">Children Remarks : <span class="text-danger">*</span></label>
                                                <textarea name="children_remarks" class="form-control input-sm"></textarea>
                                                <span v-if="errors.children_remarks" class="text-danger">@{{ errors.children_remarks[0]}}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group mt25">
                                            <button id="add_new_children_button" :disabled="childrens.childrens ==''" v-on:click="singleChildren=[],errors=[]"
                                                    onclick="modal_open('#add_new_children_button','#add_new_children_modal')" class="btn btn-sm btn-dark btn-gradient dark btn-block"
                                                    data-effect="mfp-with-fade"><span class="glyphicons glyphicons-briefcase"></span> &nbsp; Add New Children
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <hr class="short alt">

                                <div class="section row mbn">
                                    <div class="col-sm-2 pull-right">
                                        <p class="text-left">
                                            <button type="submit" :disabled="childrens.childrens !=''"
                                                    name="save_children_and_next" v-on:click="submit_button='save_children_and_next'"
                                                    class="btn btn-dark btn-gradient dark btn-block"><span
                                                        class="glyphicons glyphicons-ok_2"></span> &nbsp; Update & Next
                                                <span class="glyphicons glyphicons-right_arrow"></span>
                                            </button>
                                        </p>
                                    </div>

                                    <div class="col-sm-2 pull-right">
                                        <p class="text-left">
                                            <button type="submit" :disabled="childrens.childrens !=''"
                                                    name="save_children" v-on:click="submit_button='save_children'"
                                                    class="btn btn-dark btn-gradient dark btn-block"><span
                                                        class="glyphicons glyphicons-ok_2"></span> &nbsp; Update Children
                                            </button>
                                        </p>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!---  Language Info -->
                <div id="tab1_10" class="tab-pane @if(isset($id) && $tab == 'language') active @endif">
                    <div v-if="languages">
                        <span class="text-info">Employee No : </span>
                        <span v-text="languages.employee_no+', '"></span>
                        <span class="text-info">Employee Full Name : </span>
                        <span v-text="languages.first_name+' '+languages.middle_name+' '+languages.last_name"></span>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <form id="add_language_form" v-on:submit.prevent="addNewLanguage" method="post">
                                <input type="hidden" name="user_id" :value="user_id">

                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group mt20 mb25">
                                            <button type="button" id="add_new_language_modal_button" onclick="modal_open('#add_new_language_modal_button','#add_new_language_button_modal')" class="btn btn-sm btn-dark btn-gradient dark btn-block" data-effect="mfp-newspaper"><span class="glyphicons glyphicons-briefcase"></span> &nbsp; Add Language
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div v-if="languages.languages !=''">
                                    <div v-for="language in languages.languages">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label">Language Name :</label>
                                                    <input :value="language.language.language_name" class="form-control input-sm" readonly="readonly">
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label">Speaking Skill :</label>
                                                    <input :value="language.speaking" class="form-control input-sm" readonly="readonly">
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label">Reading Skill :</label>
                                                    <input :value="language.reading" class="form-control input-sm" readonly="readonly">
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label">Writing Skill :</label>
                                                    <input :value="language.writing" class="form-control input-sm" readonly="readonly">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-1 pull-right">
                                                <div class="form-group mt5">
                                                    <button v-on:click.prevent="deleteEmployeeData(language.id,'language')"
                                                            class="btn btn-sm btn-danger btn-gradient dark btn-block"
                                                            data-effect="mfp-with-fade"><span
                                                            class="glyphicons glyphicons-bin"></span> &nbsp; Delete
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col-md-1 pull-right">
                                                <div class="form-group mt5">
                                                    <button id="add_new_language_button" v-on:click.prevent="getDataByTabAndId('language',language.id,'#add_new_language_modal'),errors=[]"
                                                            class="btn btn-sm btn-success btn-gradient dark btn-block"
                                                            data-effect="mfp-with-fade"><span
                                                            class="glyphicons glyphicons-edit"></span> &nbsp; Edit
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <hr class="short alt">
                                    </div>
                                </div>

                                <div v-else>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group" :class="{'has-error': errors.language_id}">
                                                <label class="control-label">Language Name : <span class="text-danger">*</span></label>
                                                <select name="language_id" class="form-control input-sm">
                                                    <option value="">...Select Language Name...</option>
                                                    <option v-for="lan in language" :value="lan.id" v-text="lan.language_name"></option>
                                                </select>
                                                <span v-if="errors.language_id" class="text-danger">@{{ errors.language_id[0]}}</span>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group" :class="{'has-error': errors.speaking}">
                                                <label class="control-label">Speaking Skill : <span class="text-danger">*</span></label>
                                                <select name="speaking" class="form-control input-sm">
                                                    <option value="">...Select Speaking Skill...</option>
                                                    <option>Bad</option>
                                                    <option>Medium</option>
                                                    <option>Good</option>
                                                    <option>excelent</option>
                                                </select>
                                                <span v-if="errors.speaking" class="text-danger">@{{ errors.speaking[0]}}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group" :class="{'has-error': errors.reading}">
                                                <label class="control-label">Reading Skill : <span class="text-danger">*</span></label>
                                                <select name="reading" class="form-control input-sm">
                                                    <option value="">...Select Reading Skill...</option>
                                                    <option>Bad</option>
                                                    <option>Medium</option>
                                                    <option>Good</option>
                                                    <option>excelent</option>
                                                </select>
                                                <span v-if="errors.reading" class="text-danger">@{{ errors.reading[0]}}</span>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group" :class="{'has-error': errors.writing}">
                                                <label class="control-label">Writing Skill : <span class="text-danger">*</span></label>
                                                <select name="writing" class="form-control input-sm">
                                                    <option value="">...Select Writing Skill...</option>
                                                    <option>Bad</option>
                                                    <option>Medium</option>
                                                    <option>Good</option>
                                                    <option>excelent</option>
                                                </select>
                                                <span v-if="errors.writing" class="text-danger">@{{ errors.writing[0]}}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group mt25">
                                            <button id="add_new_language_button" :disabled="languages.languages ==''" v-on:click="singleLanguage=[],errors=[]"
                                                    onclick="modal_open('#add_new_language_button','#add_new_language_modal')" class="btn btn-sm btn-dark btn-gradient dark btn-block"
                                                    data-effect="mfp-with-fade"><span class="glyphicons glyphicons-briefcase"></span> &nbsp; Add New Language
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <hr class="short alt">

                                <div class="section row mbn">
                                    <div class="col-sm-2 pull-right">
                                        <p class="text-left">
                                            <button type="submit" :disabled="languages.languages !=''"
                                                    name="save_language" v-on:click="submit_button='save_language'"
                                                    class="btn btn-dark btn-gradient dark btn-block"><span
                                                        class="glyphicons glyphicons-ok_2"></span> &nbsp; Update Language
                                            </button>
                                        </p>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>


    <!-- Add Designation Form Popup -->
    @include('pim.employee.modals.designation')

    <!-- Add Education Form Popup -->
    @include('pim.employee.modals.education')

    <!-- Add Experience Form Popup -->
    @include('pim.employee.modals.experience')

    <!-- Add Allowance Form Popup -->
    @include('pim.employee.modals.allowance')

    <!-- Add Nominee Form Popup -->
    @include('pim.employee.modals.nominee')

    <!-- Add Training Form Popup -->
    @include('pim.employee.modals.training')

    <!-- Add Reference Form Popup -->
    @include('pim.employee.modals.reference')

    <!-- Add Children Form Popup -->
    @include('pim.employee.modals.children')

    <!-- Add Language Form Popup -->
    @include('pim.employee.modals.language')

    <!-- New Language Form Popup -->
    @include('pim.employee.modals.new_language')    

    </div>
</section>



@section('script')
    <!-- FileUpload JS -->
    <script src="{{asset('vendor/plugins/fileupload/fileupload.js')}}"></script>

    <!-- Page Plugins -->
    <script src="{{asset('vendor/plugins/magnific/jquery.magnific-popup.js')}}"></script>


    <script type="text/javascript">

        // var diff = Math.abs(new Date('2010-10-09') - new Date('2011-11-09'));
        // var year = 1000 * 60 * 60 * 24 * 30 * 12;
        // var years = Math.abs(diff/year);
        // alert(years.toFixed(1));

        var base_url = "{{url('/')}}";
        var add_edit = "edit";
        var current_tab = "{{($tab)?$tab:''}}";
        var id = "{{(isset($id))?base64_encode($id).'/':''}}";
        var user_id = "{{(isset($id))?$id:''}}";

        // Modal Start
        function modal_open(id, form_id) {
            $(document).on('click', id, function (e) {
                e.preventDefault();
                $(this).removeClass('active-animation');
                $(this).addClass('active-animation item-checked');

                $.magnificPopup.open({
                    removalDelay: 300,
                    items: {
                        src: form_id
                    },
                    callbacks: {
                        beforeOpen: function (e) {
                            var Animation = "mfp-zoomIn";
                            this.st.mainClass = Animation;
                        }
                    },
                    midClick: true
                });

            });
        }
        //Modal End
    </script>

    <script type="text/javascript" src="{{asset('/js/employee.js')}}"></script>

@endsection


@endsection