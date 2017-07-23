@extends('layouts.hrms')


@section('content')
	<section id="content" class="">
        <div class="row">
            <div class="col-md-12">
                <div class="panel">
                    <div class="panel-heading">
                        <span class="panel-title">Leave Application</span>
                    </div>
                    <div class="panel-body">
						<b>Applicant Name:</b> {{$info->userName->first_name." ".$info->userName->last_name}}<br/>
						<b>Designation:</b> {{$info->userName->designation->designation_name}}<br/>
						<b>Department:</b> {{$info->userName->designation->department->department_name}}<br/>
						<b>Application Date:</b> {{date("d M, Y", strtotime($info->created_at))}} <br>
						<br><br>
						<b>Subject: </b>{{$info->leaveType->leave_type_name}} <br>
						<b>Status: </b>
							@if($info->employee_leave_status == 1)
								<button class="btn btn-xs btn-warning">Pending</button>
							@elseif($info->employee_leave_status == 2)
								<button class="btn btn-xs btn-info">Forward</button>
							@elseif($info->employee_leave_status == 3)
								<button class="btn btn-xs btn-success">Approved</button>
							@elseif($info->employee_leave_status == 4)
								<button class="btn btn-xs btn-danger">Canceled</button>
							@else
								Invalid
							@endif	
						<br>
						<b>Duration: </b>
							{{date("d M, Y", strtotime($info->employee_leave_from))}} -
							{{date("d M, Y", strtotime($info->employee_leave_to))}} <br>
						<b>Total: </b>{{$info->employee_leave_total_days}} day(s). <br>
						<br><br>
						<b>Reason:</b>
						<p>
							{{$info->employee_leave_user_remarks}}
						</p>
						<b>Attachment: </b>
						<?php 
                            $folderName = $info->userName->id;
                            $fileName = $info->employee_leave_attachment;
                        ?>
                        @if(!empty($fileName))
                            <a target="__blank" href="{{asset("files/leave_doc/$folderName/$fileName")}}" style="cursor: pointer;"><i class="fa fa-file fa-2x" aria-hidden="true"></i></a>
                        @endif
                        <br>
						<b>Contract Address:</b> {{$info->employee_leave_contact_address}}<br>
						<b>Contract No:</b> {{$info->employee_leave_contact_number}}<br>
						<b>Passport No:</b> {{$info->employee_leave_passport_no}}<br>
						<br><br>
						<b>Responsible Person:</b> 
						@if($info->employee_leave_responsible_person > 0)
						{{$info->responsibleUser->first_name." ".$info->responsibleUser->last_name}} - ({{$info->responsibleUser->designation->designation_name}}) - ({{$info->responsibleUser->designation->department->department_name}})

							@if($info->employee_leave_responsible_person_status == 0)
                                <div class="btn btn-warning btn-xs edit-btn-Cls">Pending</div>
                            @elseif($info->employee_leave_responsible_person_status == 1)
                                <div class="btn btn-success btn-xs edit-btn-Cls">Accepted</div>
                            @elseif($info->employee_leave_responsible_person_status == 2)
                                <div class="btn btn-danger btn-xs edit-btn-Cls">Canceled</div>
                            @else
                                {{"INVALID"}}
                            @endif
						@endif
						<br>
						<b>Supervisor:</b> 
						@if($info->employee_leave_supervisor > 0)
						{{$info->supervisorUser->first_name." ".$info->supervisorUser->last_name}} - ({{$info->supervisorUser->designation->designation_name}}) - ({{$info->supervisorUser->designation->department->department_name}})
						@endif
						<br>
						<b>Forward To:</b>
						@if($info->employee_leave_recommend_to > 0)
						{{$info->forwardUser->first_name." ".$info->forwardUser->last_name}} - ({{$info->forwardUser->designation->designation_name}}) - ({{$info->forwardUser->designation->department->department_name}})
						@endif
						<br>
						<b>Approved/Canceled By:</b> 
						@if($info->employee_leave_approved_by > 0)
						{{$info->approvedByUser->first_name." ".$info->approvedByUser->last_name}} - ({{$info->approvedByUser->designation->designation_name}}) - ({{$info->approvedByUser->designation->department->department_name}})
						@endif
						<br>
						<b>Approval/Cancel Date:</b>  
							@if(!empty($info->employee_leaves_approval_date))
								{{date("d M, Y", strtotime($info->employee_leaves_approval_date))}}<br>
							@else
								{{"..."}}
							@endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection