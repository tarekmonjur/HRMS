<div id="add_new_education_modal" style="max-width:700px" class="popup-basic mfp-with-anim mfp-hide">
    <div class="panel">
        <div class="panel-heading">
            <span class="panel-title" v-if="singleEducation,singleEducation !=''">
                <i class="fa fa-rocket"></i>Edit Education
            </span>
            <span v-else class="panel-title">
                <i class="fa fa-rocket"></i>Add New Education
            </span>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    <form id="add_new_education_form" method="post" enctype="multipart/form-data" v-on:submit.prevent="addNewEducation">
                        <input type="hidden" name="user_id" v-model="user_id">
                        
                        <div v-if="singleEducation,singleEducation !=''">
                            <input type="hidden" name="id" :value="singleEducation.id">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group" :class="{'has-error': errors.education_level_id}">
                                        <label class="control-label">Education Level : <span
                                                    class="text-danger">*</span></label>
                                        <select class="form-control input-sm" name="education_level_id" v-model="education_level_id = singleEducation.institute.education_level_id" v-on:change="getInstituteAndDegreeByEducationLevelId()">
                                            <option v-bind:value="''">---- Select Education Level ----</option>
                                            <option v-bind:value="education_level.id"
                                                    v-for="(education_level, index) in education_levels" v-text="education_level.education_level_name"></option>
                                        </select>
                                        <span v-if="errors.education_level_id"
                                              class="text-danger" v-text="errors.education_level_id[0]"></span>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group" :class="{'has-error': errors.institute_id}">
                                        <label class="control-label">Institute : <span class="text-danger">*</span></label>
                                        <select class="form-control input-sm" name="institute_id">
                                            <option v-bind:value="''">---- Select Institute ----</option>
                                            <option v-bind:value="institute.id"
                                                    v-for="(institute, index) in institutes" :selected="singleEducation.institute_id == institute.id" v-text="institute.institute_name"></option>
                                        </select>
                                        <span v-if="errors.institute_id" class="text-danger" v-text="errors.institute_id[0]"></span>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group" :class="{'has-error': errors.degree_id}">
                                        <label class="control-label">Degree : <span class="text-danger">*</span></label>
                                        <select class="form-control input-sm" name="degree_id">
                                            <option v-bind:value="''">---- Select Degree ----</option>
                                            <option v-bind:value="degree.id" v-for="(degree, index) in degrees" :selected="singleEducation.degree_id == degree.id" v-text="degree.degree_name"></option>
                                        </select>
                                        <span v-if="errors.degree_id"
                                              class="text-danger" v-text="errors.degree_id[0]"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group" :class="{'has-error': errors.pass_year}">
                                        <label class="control-label">Pass Year : <span class="text-danger">*</span></label>
                                        <input type="text" name="pass_year" v-on:mouseover="datePickerYear" class="date form-control input-sm" :value="singleEducation.pass_year"
                                               readonly="">
                                        <span v-if="errors.pass_year"
                                              class="text-danger" v-text="errors.pass_year[0]"></span>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group" :class="{'has-error': errors.result_type}">
                                        <label class="control-label">Result Type :</label>
                                        <div class="radio-custom mb5">
                                            <input id="result_type_cgpa" name="result_type" type="radio"
                                                   value="cgpa" v-model="showCgpa = singleEducation.result_type"
                                                   v-on:click="showCgpa='cgpa',showDivision=''">
                                            <label for="result_type_cgpa">CGPA</label>

                                            <input id="result_type_division" name="result_type" type="radio"
                                                   v-model="showDivision = singleEducation.result_type"
                                                   value="division" v-on:click="showCgpa='',showDivision='division'">
                                            <label for="result_type_division">Division</label>
                                            <span v-if="errors.result_type"
                                                  class="text-danger" v-text="errors.result_type[0]"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4" v-if="showCgpa=='cgpa'">
                                    <div class="form-group" :class="{'has-error' : errors.cgpa}">
                                        <label class="control-label">CGPA : <span
                                                    class="text-danger">*</span></label>
                                        <input type="text" name="cgpa" class="form-control input-sm"
                                               placeholder="Enter CGPA" :value="singleEducation.cgpa">
                                        <span v-if="errors.cgpa" class="text-danger" v-text="errors.cgpa[0]"></span>
                                    </div>
                                </div>

                                <div class="col-md-4" v-if="showDivision=='division'">
                                    <div class="form-group" :class="{'has-error': errors.division}">
                                        <label class="control-label">Division : <span
                                                    class="text-danger">*</span></label>
                                        <select name="division" class="form-control input-sm">
                                            <option value="1" :selected="singleEducation.division ==1">First Division</option>
                                            <option value="2" :selected="singleEducation.division ==2">Second Division</option>
                                            <option value="3" :selected="singleEducation.division ==3">Third Division</option>
                                        </select>
                                        <span v-if="errors.division" class="text-danger" v-text="errors.division[0]"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6" :class="{'has-error': errors.certificate_file}">
                                    <div class="form-group">
                                        <label class="control-label">Achievement:</label>
                                        <input type="text" name="achievement" :value="singleEducation.achievement" class="form-control input-sm">
                                        <span v-if="errors.achievement" class="text-danger" v-text="errors.achievement[0]"></span>
                                    </div>
                                </div>

                                <div class="col-md-6" :class="{'has-error': errors.certificate_file}">
                                    <div class="form-group">
                                        <label class="control-label">New Certificate:</label>
                                        <input type="file" name="certificate_file" class="form-control btn-primary input-sm">
                                        <span v-if="errors.certificate_file" class="text-danger" v-text="errors.certificate_file[0]"></span>
                                    </div>
                                </div>

                                <div class="col-md-6" v-if="singleEducation.certificate">
                                    <div class="form-group">
                                        <label class="control-label">Certificate:</label>
                                        <div>
                                            <a :href="'/files/'+config_id+'/'+singleEducation.user_id+'/'+singleEducation.certificate" target="_blank" class="text-success">
                                                <i class="fa fa-2x fa-file-image-o"></i>
                                                Click here to view certificate
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div v-else>
                            <div class="row">
                                <div class="col-md-4">
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
                                        <span v-if="errors.education_level_id"
                                              class="text-danger" v-text="errors.education_level_id[0]"></span>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group" :class="{'has-error': errors.institute_id}">
                                        <label class="control-label">Institute : <span class="text-danger">*</span></label>
                                        <select2 class="form-control input-sm" name="institute_id">
                                            <option v-bind:value="''">---- Select Institute ----</option>
                                            <option v-bind:value="institute.id"
                                                    v-for="(institute, index) in institutes" v-text="institute.institute_name"></option>
                                        </select2>
                                        <span v-if="errors.institute_id" class="text-danger" v-text="errors.institute_id[0]"></span>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group" :class="{'has-error': errors.degree_id}">
                                        <label class="control-label">Degree : <span class="text-danger">*</span></label>
                                        <select2 class="form-control input-sm" name="degree_id">
                                            <option v-bind:value="''">---- Select Degree ----</option>
                                            <option v-bind:value="degree.id" v-for="(degree, index) in degrees">@{{ degree.degree_name }}</option>
                                        </select2>
                                        <span v-if="errors.degree_id"
                                              class="text-danger" v-text="errors.degree_id[0]"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group" :class="{'has-error': errors.pass_year}">
                                        <label class="control-label">Pass Year : <span class="text-danger">*</span></label>
                                        <input type="text" name="pass_year" v-on:mouseover="datePickerYear" class="date form-control input-sm"
                                               readonly="">
                                        <span v-if="errors.pass_year"
                                              class="text-danger" v-text="errors.pass_year[0]"></span>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group" :class="{'has-error': errors.result_type}">
                                        <label class="control-label">Result Type :</label>
                                        <div class="radio-custom mb5">
                                            <input id="result_type_cgpa" name="result_type" type="radio"
                                                   value="cgpa" :checked="showCgpa"
                                                   v-on:click="showCgpa=true,showDivision=false">
                                            <label for="result_type_cgpa">CGPA</label>

                                            <input id="result_type_division" name="result_type" type="radio"
                                                   value="division" v-on:click="showCgpa=false,showDivision=true">
                                            <label for="result_type_division">Division</label>
                                            <span v-if="errors.result_type"
                                                  class="text-danger" v-text="errors.result_type[0]"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4" v-if="showCgpa">
                                    <div class="form-group" :class="{'has-error' : errors.cgpa}">
                                        <label class="control-label">CGPA : <span
                                                    class="text-danger">*</span></label>
                                        <input type="text" name="cgpa" class="form-control input-sm"
                                               placeholder="Enter CGPA">
                                        <span v-if="errors.cgpa" class="text-danger">@{{ errors.cgpa[0]}}</span>
                                    </div>
                                </div>

                                <div class="col-md-4" v-if="showDivision">
                                    <div class="form-group" :class="{'has-error': errors.division}">
                                        <label class="control-label">Division : <span
                                                    class="text-danger">*</span></label>
                                        <select name="division" class="form-control input-sm">
                                            <option value="">--- Select Division ---</option>
                                            <option value="1">First Division</option>
                                            <option value="2">Second Division</option>
                                            <option value="3">Third Division</option>
                                        </select>
                                        <span v-if="errors.division"
                                              class="text-danger" v-text="errors.division[0]"></span>
                                    </div>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Achievement:</label>
                                        <input type="text" name="achievement" class="form-control input-sm">
                                        <span v-if="errors.achievement" class="text-danger" v-text="errors.achievement[0]"></span>
                                    </div>
                                </div>

                                <div class="col-md-6" :class="{'has-error': errors.certificate_file}">
                                    <div class="form-group">
                                        <label class="control-label">Certificate:</label>
                                        <input type="file" name="certificate_file" class="form-control btn-primary input-sm">
                                        <span v-if="errors.certificate_file" class="text-danger" v-text="errors.certificate_file[0]"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="short alt">

                        <div class="section row mbn">
                            <div class="col-sm-4 pull-right">
                                <p class="text-left">
                                    <button type="submit" name="save_education" v-if="singleEducation,singleEducation !=''"
                                            class="btn btn-dark btn-gradient dark btn-block">
                                            <span class="glyphicons glyphicons-ok_2"></span> &nbsp; Update Education
                                    </button>
                                    <button v-else type="submit" name="save_education"
                                            class="btn btn-dark btn-gradient dark btn-block">
                                            <span class="glyphicons glyphicons-ok_2"></span> &nbsp; Add New
                                    </button>
                        
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
