@extends('layouts.hrms')
@section('content')

<section id="content" class="animated fadeIn">
  <div class="page-heading">
      <div class="media clearfix">
        <div class="media-left pr30">
          <a href="#">
            @if($user->photo)
            <img class="media-object mw150" width="165px" src="{{$user->full_image}}" alt="{{$auth->fullname}}">
            @else
            <img class="media-object mw150" width="165px" src="{{asset('img/placeholder.png')}}" alt="{{$auth->fullname}}">
            @endif
          </a>
        </div>    

        <div class="media-body va-m">
          <h2 class="media-heading">{{$user->fullname}}
            <small> - Profile (@if($user->status ==1) active @else inactive @endif account )</small>
          </h2>
          <p class="lead">{{$user->designation->designation_name}} <small>( Joining Date - {{$user->details->joining_date_format or ''}}, Profile Opening Date - {{$user->created_at}} )</small></p>
          <h4 class="media-heading">Present Address
            <small> - {{$user->address->present_postoffice or ''}}, {{$user->address->presentPoliceStation->police_station_name or ''}}, {{$user->address->presentDistrict->district_name or ''}}, {{$user->address->presentDivision->division_name or ''}}.</small>
          </h4>
          <h4 class="media-heading">Parmanent Address
            <small> - {{$user->address->permanent_postoffice or ''}}, {{$user->address->permanentPoliceStation->police_station_name or ''}}, {{$user->address->permanentDistrict->district_name or ''}}, {{$user->address->permanentDivision->division_name or ''}}.</small>
          </h4>
        </div>

        <div class="media-right pr30 pl50 pull-right">
          <a href="{{url('/employee/view/'.$user->employee_no)}}" class="btn btn-dark btn-sm">View Profile</a>
        </div>
      </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="panel">
        <div class="panel-heading">
          <span class="panel-icon">
            <i class="glyphicons glyphicons-history"></i>
          </span>
          <span class="panel-title">Attendance</span>
        </div>
        <div class="panel-body pn" id="showAttendance">
          <div class="row pt10">
            <form v-on:submit.prevent="getAttendance">
              <input type="hidden" name="employee_no" :value="employee_no">
              <div class="col-md-3 col-md-offset-2">
                <div class="form-group" :class="{'has-error':errors.from_date}">
                  <label class="control-label">From Date :</label>
                  <input type="text" name="from_date" v-on:focusout="toDate" id="fromDate" v-model="from_date" class="form-control input-sm" placeholder="Form Date.." readonly="readonly">
                </div>
              </div>

              <div class="col-md-3">  
                <div class="form-group" :class="{'has-error':errors.to_date}">
                  <label class="control-label">To Date :</label>
                  <input type="text" name="to_date" id="toDate" v-on:mouseover="toDate" v-model="to_date" class="form-control input-sm" placeholder="To Date.." readonly="readonly">
                </div>
              </div>

              <div class="col-md-2">
                <div class="form-group">
                  <br>
                  <button type="submit" class="form-control input-sm btn btn-sm btn-dark">Submit</button>
                </div>
              </div>
            </form>
          </div>

          <div class="row">
            <div class="col-md-8">
              <table class="table">
                <thead class="bg-dark">
                  <tr class="text-center">
                    <th class="text-center" style="color: #fff;">SL</th>
                    <th class="text-center" style="color: #fff;">Date</th>
                    <th class="text-center" style="color: #fff;">Attendance</th>
                  </tr>
                </thead>

                <tbody>
                  <tr v-for="(attendance,index) in attendances" class="text-center">
                    <td v-text="index+1"></td>
                    <td v-text="attendance.date"></td>
                    <td v-on:click.prevent="addAttendance(index,attendance.user_id, attendance.id, attendance.date, userName,'#attendance_modal')">

                      <!-- attendance -->
                      <div v-if="attendance.observation == 1">

                        <span v-if="attendance.in_time" :class="{'text-danger':attendance.late_hour}">
                          <i class="glyphicons glyphicons-unshare" :class="(attendance.late_hour)?'text-danger':'text-success'"></i>
                          <span v-text="attendance.in_time"></span>
                        </span>

                        <span v-if="attendance.out_time">
                          <i class="glyphicons glyphicons-share text-info"></i>
                          <span v-text="attendance.out_time"></span>
                        </span>

                        <hr class="pn" style="margin: 2px!important">
                        <i class="glyphicons glyphicons-history text-primary"></i>
                        <span v-text="attendance.total_work_hour"></span>
                      </div>

                      <!-- leave -->
                      <div v-else-if="attendance.observation == 2">
                        <i class="imoon imoon-lanyrd text-warning"></i>
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
                        <hr class="pn" style="margin: 1px!important">

                        <span v-if="attendance.in_time !=''" :class="{'text-danger':attendance.late_hour}">
                          <i class="glyphicons glyphicons-unshare" :class="(attendance.late_hour)?'text-danger':'text-success'"></i>
                          <span v-text="attendance.in_time"></span>
                        </span>

                        <span v-if="attendance.out_time !=''">
                          <i class="glyphicons glyphicons-share text-info"></i>
                          <span v-text="attendance.out_time"></span>
                        </span>

                        <div v-if="attendance.total_work_hour !=''">
                          <hr class="pn" style="margin: 2px!important">
                          <i class="glyphicons glyphicons-history text-primary"></i>
                          <span v-text="attendance.total_work_hour"></span>
                        </div>
                      </div>

                      <!-- present weekend -->
                      <div v-if="attendance.observation == 6">
                        <i class="fa-2x text-danger text-strong">W</i>
                        <hr class="pn" style="margin: 1px!important">

                        <span v-if="attendance.in_time !=''" :class="{'text-danger':attendance.late_hour}">
                          <i class="glyphicons glyphicons-unshare" :class="(attendance.late_hour)?'text-danger':'text-success'"></i>
                          <span v-text="attendance.in_time"></span>
                        </span>

                        <span v-if="attendance.out_time !=''">
                          <i class="glyphicons glyphicons-share text-info"></i>
                          <span v-text="attendance.out_time"></span>
                        </span>

                        <div v-if="attendance.total_work_hour !=''">
                          <hr class="pn" style="margin: 2px!important">
                          <i class="glyphicons glyphicons-history text-primary"></i>
                          <span v-text="attendance.total_work_hour"></span>
                        </div>
                      </div>

                      <div v-else-if="attendance.observation == 0">
                        <i class="fa fa-font fa-2x text-danger"></i>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="col-md-4">
              <table class="table">
                <thead class="bg-dark">
                  <tr class="text-center">
                    <th class="text-center" style="color: #fff;">Report Name</th>
                    <th class="text-center" style="color: #fff;">Total</th>
                  </tr>
                </thead>
                <tbody>
                  <tr class="text-center">
                    <td class="text-success">Present</td>
                    <td v-text="report.present"></td>
                  </tr>
                  <tr class="text-center">
                    <td class="text-danger">Absent</td>
                    <td v-text="report.absent"></td>
                  </tr>
                  <tr class="text-center">
                    <td class="text-info">Leave</td>
                    <td v-text="report.leave"></td>
                  </tr>
          
                  <tr class="text-center">
                    <td class="text-danger">Holiday</td>
                    <td v-text="report.holiday"></td>
                  </tr>
                  <tr class="text-center">
                    <td class="text-danger">Weekend</td>
                    <td v-text="report.weekend"></td>
                  </tr>
                  <tr class="text-center">
                    <td><strong>Total Days</strong></td>
                    <td style="font-weight: bold" v-text="report.total"></td>
                  </tr>
                  <tr class="text-center">
                    <td class="text-warning">Late</td>
                    <td v-text="report.late"></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
  </div>

    <div id="attendance_modal" style="max-width:450px" class="popup-basic mfp-with-anim mfp-hide">
    <div class="panel">
        <div class="panel-heading">
            <span class="panel-title">
                <i class="fa fa-rocket"></i>Add Attendance <span class="text-info"></span>
            </span>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                  <form v-on:submit.prevent="saveAttendance">
                    <input type="hidden" name="user_id" v-model="attend.user_id">
                    <input type="hidden" name="time_sheet_id" v-model="attend.time_sheet_id">
                    <input type="hidden" name="observation" value="1">

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
  
</section>


@section('script')

<script type="text/javascript">

  var employee_no = "{{$employee_no}}";
  var from_date = "{{$from_date}}";
  var to_date = "{{$to_date}}";
  
</script>

<!-- Page Plugins -->
<script src="{{asset('vendor/plugins/magnific/jquery.magnific-popup.js')}}"></script>

<script type="text/javascript" src="{{asset('js/my_attendance.js')}}"></script>

@endsection

@endsection