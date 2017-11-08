@extends('layouts.hrms')
@section('content')

@section('style')

    <style type="text/css">
      .name_show{
        font-size: 14px;
        font-weight: bold;
        visibility: hidden;
        position: absolute;
        background: #70ca63;
        color: #fff;
        z-index: 10000;
        padding: 5px;
        margin: -10px;
        margin-top: -40px;
      }
      .show_name:hover .name_show{
        visibility: visible;
      }
    </style>
@endsection

<section id="attendanceID" class="p5 pt10">
  <div class="row">
    <div class="col-md-8 col-md-offset-2">
      <div class="panel">
        <div class="panel-heading">
            <span class="panel-title"><i class="glyphicons glyphicons-history"></i></span>
            <strong>Employee Attendance</strong>
            <span class="pull-right"><button class="btn btn-dark btn-sm" v-on:click.prevent="modal_open('#manual_attendance_modal')">Upload Attendance</button></span>
        </div>

        <div class="panel-body">
          <form v-on:submit.prevent="getAttendance">
            <div class="row">
              <div class="col-md-3 col-md-offset-1">
                <div class="form-group">
                  <label class="control-label">Department :</label>
                  <select name="department_id" class="form-control input-sm">
                    <option value="0">---- All Department ----</option>
                    <option v-for="department in departments" :value="department.id" v-text="department.department_name"></option>
                  </select>
                </div>
              </div>

              <div class="col-md-3">
                <div class="form-group" :class="{'has-error':errors.from_date}">
                  <label class="control-label">From Date :</label>
                  <input type="text" name="from_date" v-on:focusout="toDate" id="fromDate" class="form-control input-sm" placeholder="Form Date.." readonly="readonly">
                </div>
              </div>

              <div class="col-md-3">  
                <div class="form-group" :class="{'has-error':errors.to_date}">
                  <label class="control-label">To Date :</label>
                  <input type="text" name="to_date" id="toDate" v-on:mouseover="toDate" class="form-control input-sm" placeholder="To Date.." readonly="readonly">
                </div>
              </div>

              <div class="col-md-2">
                <div class="form-group">
                  <br>
                  <button type="submit" class="form-control input-sm btn btn-sm btn-dark">Show Attendance</button>
                </div>
              </div>

            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="panel" v-show="showAttendance">
        <div class="panel-heading">
          <span class="panel-title"><i class="glyphicons glyphicons-history"></i></span>
          <strong>Show Attendance</strong>
        </div>

        <div class="panel-body pn" id="showAttendance">
          <table class="table table-bordered" v-if="attendances !=''">
            <thead class="bg-dark">
              <tr class="text-center" style="font-size: 11px!important">
                <th style="vertical-align: middle; min-width:120px; max-width: 150px!important; color: #fff;">Employee Name</th>
                <th style="color: #fff;" v-for="day in days" v-html="day"></th>
              </tr>
            </thead>

            <tbody style="background: #fff!important; font-size: 11px!important">
              <tr v-for="(user,uIndex) in attendances" class="text-center">

                <td class="bg-dark" style="min-width:120px; max-width: 150px!important;">
                  <a style="color: #fff;" target="_blank" :href="'view/'+user.employee_no+'?from_date='+from_date+'&to_date='+to_date" v-text="user.first_name+' '+user.last_name+' ('+user.employee_no+')'"></a>
                </td>

                <td v-for="(attendance,aIndex) in user.attendanceTimesheets" class="text-center show_name" v-on:click.prevent="addAttendance(uIndex,aIndex,user.id, attendance.id, attendance.date, attendance.observation, user.first_name+' '+user.last_name+' ('+user.employee_no+')','#attendance_modal')">

                  <div style="position: relative!important;"></div>
                  <div class="name_show">
                    <a style="color: #fff;" target="_blank" :href="'view/'+user.employee_no+'?from_date='+from_date+'&to_date='+to_date" v-text="user.first_name+' '+user.last_name+' ('+user.employee_no+')'"></a>
                  </div>

                  <!-- attendance -->
                  <div v-if="attendance.observation == 1">
                    <div v-if="attendance.in_time" :class="{'text-danger':attendance.late_hour}">
                      <i class="glyphicons glyphicons-unshare" :class="(attendance.late_hour)?'text-danger':'text-success'"></i>
                      <span v-text="attendance.in_time"></span>
                    </div>
                    <div v-if="attendance.out_time">
                      <hr class="pn" style="margin: 2px!important">
                      <i class="glyphicons glyphicons-share text-info"></i>
                      <span v-text="attendance.out_time"></span>
                    </div>
                  </div>

                  <!-- leave -->
                  <div v-else-if="attendance.observation == 2">
                    <i class="imoon imoon-lanyrd fa-lg text-warning"></i>
                    <div v-text="attendance.leave_type"></div>
                  </div>

                  <!-- holiday -->
                  <div v-else-if="attendance.observation == 3">
                    <i class="fa fa-h-square fa-2x text-danger"></i>
                  </div>

                  <!-- weekend -->
                  <div v-else-if="attendance.observation == 4">
                    <i class="fa-2x text-danger text-strong">W</i>
                  </div>

                  <!-- present holiday -->
                  <div v-if="attendance.observation == 5">
                    <i class="fa fa-h-square fa-2x text-danger"></i>

                    <div v-if="attendance.in_time !=''" :class="{'text-danger':attendance.late_hour}">
                      <i class="glyphicons glyphicons-unshare" :class="(attendance.late_hour)?'text-danger':'text-success'"></i>
                      <span v-text="attendance.in_time"></span>
                    </div>

                    <div v-if="attendance.out_time !=''">
                      <hr class="pn" style="margin: 2px!important">
                      <i class="glyphicons glyphicons-share text-info"></i>
                      <span v-text="attendance.out_time"></span>
                    </div>
                  </div>

                  <!-- present weekend -->
                  <div v-if="attendance.observation == 6">
                    <i class="fa-2x text-danger text-strong">W</i>
                    
                    <div v-if="attendance.in_time !=''" :class="{'text-danger':attendance.late_hour}">
                      <i class="glyphicons glyphicons-unshare" :class="(attendance.late_hour)?'text-danger':'text-success'"></i>
                      <span v-text="attendance.in_time"></span>
                    </div>

                    <div v-if="attendance.out_time !=''">
                      <hr class="pn" style="margin: 2px!important">
                      <i class="glyphicons glyphicons-share text-info"></i>
                      <span v-text="attendance.out_time"></span>
                    </div>
                  </div>

                  <div v-else-if="attendance.observation == 0">
                    <i class="fa fa-font fa-2x text-danger"></i>
                  </div>

                </td>
              </tr>
            </tbody>
          </table>
          <h5 v-else style="padding: 10px;">Data is loading...</h5>
        </div>
      </div>
    </div>
  </div>



  <div id="attendance_modal" style="max-width:450px" class="popup-basic mfp-with-anim mfp-hide">
    <div class="panel">
        <div class="panel-heading">
            <span class="panel-title">
                <i class="fa fa-rocket"></i>Add Attendance <span class="text-info" v-text="attend.user_fullname"></span>
            </span>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                  <form v-on:submit.prevent="saveAttendance">
                    <input type="hidden" name="user_id" v-model="attend.user_id">
                    <input type="hidden" name="time_sheet_id" v-model="attend.time_sheet_id">
                    <input type="hidden" name="observation" v-model="attend.observation">

                    <div class="row">
                      <div class="col-md-12">
                          <div class="form-group" :class="{'has-error':errors.date}">
                              <label class="control-label">Attendance Date: <span class="text-danger">*</span></label>
                              <input type="text" name="date" v-model="attend.date" class="form-control input-sm" readonly="readonly">
                              <span v-if="errors.date" class="text-danger" v-text="errors.date[0]"></span>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group" :class="{'has-error':errors.in_time}">
                              <label class="control-label">In Time: <span class="text-danger">*</span></label>
                              <input type="text" name="in_time" v-model="attend.in_time" v-on:mouseover="myTimePicker" class="myTimePicker form-control input-sm" readonly="readonly">
                              <span v-if="errors.in_time" class="text-danger" v-text="errors.in_time[0]"></span>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group" :class="{'has-error':errors.out_time}">
                              <label class="control-label">Out Time: <span class="text-danger">*</span></label>
                              <input type="text" name="out_time" v-model="attend.out_time" v-on:mouseover="myTimePicker" class="myTimePicker form-control input-sm" readonly="readonly">
                              <span v-if="errors.out_time" class="text-danger" v-text="errors.out_time[0]"></span>
                          </div>
                      </div>
                      <div class="col-md-12">
                          <div class="form-group">
                              <label class="control-label text-danger"> Make employee absent:</label>
                              <input type="checkbox" name="make_absent" v-model="make_absent" v-on:click="makeAbsend(attend.observation)" class="input-sm">
                          </div>
                      </div>
                    </div>

                    <hr class="short alt">

                    <div class="section row mbn">
                        <div class="col-sm-6 pull-right">
                            <p class="text-left">
                                <button type="submit" name="add_attendance" class="btn btn-dark btn-gradient dark btn-block"><span class="glyphicons glyphicons-ok_2"></span> &nbsp; Add Attendance
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


  <div id="manual_attendance_modal" style="max-width:450px" class="popup-basic mfp-with-anim mfp-hide">
    <div class="panel">
        <div class="panel-heading">
            <span class="panel-title">
                <i class="fa fa-rocket"></i>Manual Attendance
            </span>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                  <form class="admin-form" method="post" action="{{url('/attendance/manual')}}" enctype="multipart/form-data">
                    {{csrf_field()}}
                    <div class="row">
                      <div class="col-md-12">
                          <div class="form-group" :class="{'has-error':errors.date}">
                            <label class="control-label pb5">Upload CSV file: <span class="text-danger">*</span></label>

                            <label class="field prepend-icon append-button file">
                              <span class="button btn-primary">Choose CSV File</span>
                              <input type="file" required="required" class="gui-file" name="csv_file" id="file1" onChange="document.getElementById('uploader1').value = this.value;">
                              <input type="text" class="gui-input" id="uploader1" placeholder="Please Select A File">
                              <label class="field-icon">
                                <i class="fa fa-upload"></i>
                              </label>
                            </label>
                            <span class="input-footer">
                              <strong>File format must be : </strong> employee no, date (Y-m-d), in time, out time. <a href="{{url('/attendance/demo')}}">Download Demo</a>
                            </span>
                              <span v-if="errors.date" class="text-danger" v-text="errors.date[0]"></span>
                          </div>
                      </div>
                    </div>

                    <hr class="short alt">

                    <div class="section row mbn">
                        <div class="col-sm-6 pull-right">
                            <p class="text-left">
                                <button type="submit" name="upload_attendance" class="btn btn-dark btn-gradient btn-sm"><i class="fa fa-upload"></i> &nbsp; Upload Attendance
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
</section>


@section('script')

  <script type="text/javascript" src="{{asset('js/attendance.js')}}"></script>

@endsection

@endsection