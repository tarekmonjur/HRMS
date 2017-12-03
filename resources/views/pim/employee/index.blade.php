@extends('layouts.hrms')
@section('content')

<section class="p10" id="employee_list">
<div class="panel">
    <div class="panel-heading">
        <div class="panel-title">
            <span class="glyphicon glyphicon-tasks"></span>Employee Information
            <span class="pull-right">
            <?php 
              $chkUrl = \Request::segment(1);
            ?>
            @if(in_array($chkUrl."/add", session('userMenuShare')))
              <a href="{{url('employee/add')}}" class="btn btn-sm btn-dark btn-gradient dark"><span class="glyphicons glyphicons-user_add"></span> &nbsp; Add Employee</a>
            @endif
            </span>
        </div>
    </div>
    <div class="panel-body pn">
        <table class="table table-striped table-hover" id="datatable1" cellspacing="0" width="100%">
            <thead>
            <tr class="bg-dark">
                <th>SL:</th>
                <th>Employee No</th>
                <th>Employee Name</th>
                <th>Email Address</th>
                <th>Designation</th>
                <th>Department</th>
                <th>Image</th>
                <th>Created By</th>
                <th>Updated By</th>
                <th>Created Date</th>
                <th>Updated Date</th>
                <th>Action</th>
            </tr>
            </thead>
            <tfoot>
            <tr class="bg-dark">
                <th>SL:</th>
                <th>Employee No</th>
                <th>Employee Name</th>
                <th>Email Address</th>
                <th>Designation</th>
                <th>Department</th>
                <th>Image</th>
                <th>Created By</th>
                <th>Updated By</th>
                <th>Created Date</th>
                <th>Updated Date</th>
                <th>Action</th>
            </tr>
            </tfoot>
            <tbody>
            <?php $sl=1;?>
            @foreach($users as $user)
                <tr>
                   <td>{{$sl}}</td>
                   <td>{{$user->employee_no}}</td>
                   <td>{{$user->fullname}}</td>
                   <td>{{$user->email}}</td>
                   <td>{{$user->designation->designation_name}}</td>
                   <td>{{$user->designation->department->department_name}}</td>
              
                   <td>
                      @if($user->photo)
                       <img src="{{$user->full_image}}" alt="{{$user->fullname}}" width="50px">
                       @else
                       <img src="{{asset('img/placeholder.png')}}" alt="" width="50px">
                       @endif
                   </td>
                   <td>@if($user->createdBy) {{$user->createdBy->fullname}} @else Maybe system @endif</td>
                   <td>@if($user->updatedBy) {{$user->updatedBy->fullname}} @else Maybe system @endif</td>
                   <td>{{$user->created_at}}</td>
                   <td>{{$user->updated_at}}</td>
                   <td>
                       <div class="btn-group">
                           <a href="{{url('/employee/edit/'.$user->id)}}" class="btn btn-sm btn-primary">
                               <i class="glyphicons glyphicons-pencil"></i>
                           </a>
                       </div>
                       <div class="btn-group">
                           <a href="{{url('/employee/view/'.$user->employee_no)}}" class="btn btn-sm btn-success">
                               <i class="glyphicons glyphicons-eye_open"></i>
                           </a>
                       </div>
                       <div class="btn-group">
                           <button type="button" class="btn btn-info btn-sm" onclick="showLeaveData({{$user->id}})" data-toggle="modal" data-target=".showLeaveData">Leaves</button>
                       </div>
                       <div class="btn-group">
                        @if(in_array($chkUrl."/delete", session('userMenuShare')))
                           <button type="button" class="btn btn-warning btn-sm" @click="EmployeeStatus({{$user->id}})" data-toggle="modal" data-target=".EmployeeStatus">
                             @if($user->status==1)
                                {{"Active"}}
                             @elseif($user->status==2)
                                {{"Retired"}}
                             @elseif($user->status==3)
                                {{"Released"}}
                             @elseif($user->status==4)
                                {{"Resigned"}}
                             @elseif($user->status==5)
                                {{"Terminated"}}
                             @elseif($user->status==6)
                                {{"Dismissed"}}
                             @elseif($user->status==7)
                                {{"Contract Terminated"}}
                             @elseif($user->status==8)
                                {{"Abscond"}}
                             @elseif($user->status==9)
                                {{"Transfer"}}
                             @elseif($user->status==10)
                                {{"Deactive"}}
                             @else
                                {{"Undefined"}}
                             @endif
                           </button>
                        @endif
                       </div>
                      <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-success" onclick="showData({{$user->id}})" data-toggle="modal" data-target=".showData"><i class="fa fa-lock" aria-hidden="true"></i></button>
                      </div>
                      <div class="btn-group">
                        <!-- emp status change button -->
                        <button type="button" class="btn btn-sm btn-info" @click="showEmpType({{$user->id}})" data-toggle="modal" data-target=".showEmpType">Emp. Type</button>
                      </div>
                   </td>
                </tr>
                <?php $sl++;?>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

@include('pim.employee.modals.permission')
@include('pim.employee.modals.leave')
@include('pim.employee.modals.employee_status')
@include('pim.employee.modals.employee_type')

</section>



@section('script')

<script type="text/javascript">

//js code for permission start
function showData(id){
  
  $('.hdn_id').val('');
  $('input:checkbox').removeAttr('checked');
  //first it clean previous data....
  $('.hdn_id').val(id);

  $.ajax({
      url: "{{url('/employee/permission')}}/"+id,
      type: 'GET',
  })
  .done(function(data){
   
      if(data.length > 0){
          jQuery.each(data, function(index, item) {
              $('input[value='+item.menu_id+']').prop("checked", true);
          });
      }else{
          $('input:checkbox').removeAttr('checked');
      }
  })
  .fail(function(){
      swal("Error", "Data not removed.", "error");
  });
}
//js code permission finished

//js code for LEAVE start
function showLeaveData(id){
  
  $('.hdn_id').val('');
  $('input:checkbox').removeAttr('checked');
  // first it clean previous data....
  $('.hdn_id').val(id);

  $.ajax({
      url: "{{url('/employee/leave')}}/"+id,
      type: 'GET',
  })
  .done(function(data){
      var bUrl = "{{url('leave/details/')}}";
      $('.detailsLink').html("<a href='"+bUrl+"/"+data.personalInfo.employee_no+"'>"+data.personalInfo.first_name+" "+data.personalInfo.last_name+"</a>");

      if(data.individual_user_leaves.length > 0){
          jQuery.each(data.individual_user_leaves, function(index, item) {
              $('input[value='+item.leave_type_id+']').prop("checked", true);
          });
      }else{
          $('input:checkbox').removeAttr('checked');
      }
  })
  .fail(function(){
      swal("Error", "Leave Error ...", "error");
  });
}
//js code LEAVE finished


  new Vue({
    el: '#employee_list',
    data:{
      user_id: '',
      emp_type_user_id: '',
      type_name: '',
      HTMLcontent: '',
      show_history: [],
      emp_type_history: [],
      emp_all_types:[],
      emp_current_type: null,
      up_coming_type: null,
      upcomming_status: null,
      validity: null,
      final_or_current_type_id: null,
      final_or_current_type_from_date: null,
      final_or_current_type_to_date: null,
    },
    methods:{

      returnStatusName(id){

        var stName = "Undefined";

        if(id == 1){
          stName = "Active";
        }
        else if(id == 2){
          stName = "Retired";
        }
        else if(id == 3){
          stName = "Released";
        }
        else if(id == 4){
          stName = "Resigned";
        }
        else if(id == 5){
          stName = "Terminated";
        }
        else if(id == 6){
          stName = "Dismissed";
        }
        else if(id == 7){
          stName = "Contract Terminated";
        }
        else if(id == 8){
          stName = "Abscond";
        }
        else if(id == 9){
          stName = "Transfer";
        }
        else if(id == 10){
          stName = "Deactive";
        }

        return stName;
      },

      returnTypeName(id){
        if(id==1){
          return "Permanent";
        }
        else if(id == 2){
          return "Trainee/Probation";
        }
        else if(id == 3){
          return "Part time";
        }
        else if(id == 4){
          return "Special Contract";
        }
        else{
          return "Undefined";
        }
      },

      EmployeeStatus(data){

        this.user_id = data;

        //getEmployeeStatus get history .. if no history.. 
        //it generate one history Automatically 
        axios.get('/employee/get_employee_status/'+this.user_id).then(response => {
          
          this.show_history = response.data.status_history;
          this.upcomming_status = response.data.upcomming_status;
          this.validity = response.data.validity;
          this.final_or_current_type_id = response.data.final_or_current_type.employee_type_id;
          this.final_or_current_type_map_id = response.data.final_or_current_type.id;
          this.final_or_current_type_from_date = response.data.final_or_current_type.from_date;
          this.final_or_current_type_to_date = response.data.final_or_current_type.to_date;
        });
      },

      empUpdateStatus(e){
        
        // var pathArray = window.location.pathname.split( '/' );
        
        var formData = new FormData(e.target);

        // formData.append('file', document.getElementById('file').files[0]);

        axios.post("updateEmployeeStatus", formData, {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
        })
        .then((response) => { 
    
            this.HTMLcontent = null;

            if(response.data.title == 'error'){
              swal({
                title: response.data.title+"!",
                text: response.data.message,
                type: response.data.title,
                showCancelButton: false,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Done",
                closeOnConfirm: true
              });
            }
            else{
              swal({
                  title: response.data.title+"!",
                  text: response.data.message,
                  type: response.data.title,
                  showCancelButton: false,
                  confirmButtonColor: "#DD6B55",
                  confirmButtonText: "Done",
                  closeOnConfirm: false
              },
              function(){
                  location.href=location.href;
              });
            }
              
        })
        .catch((error) => {
            
            if(error.response.status != 200){ //error 422
                var errors = error.response.data;

                var errorsHtml = '<div class="alert alert-danger"><ul>';
                $.each( errors , function( key, value ) {
                    errorsHtml += '<li>' + value[0] + '</li>';
                });
                errorsHtml += '</ul></di>';
                
                this.HTMLcontent = errorsHtml;
            }
        });
      },

      showEmpType(id){

        this.emp_type_user_id = id;

        axios.get('/employee/get_employee_types_history/'+this.emp_type_user_id).then(response => {
          
          this.emp_type_history = response.data.history;
          this.emp_all_types = response.data.emp_types;
          this.emp_current_type = response.data.current_type;
          this.up_coming_type = response.data.up_coming_type;
        });
      },
      deleteUpComming(id, typee){
        swal({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Delete",
            closeOnConfirm: false
        },
        function(){

            //typee = EmpType or typee == Emp Stauts
            axios.get('/employee/delete_up_comming/'+id+'/'+typee).then(response => {
              
            });

            swal({
                title: "Deleted!",
                text: "Successfully Removed",
                type: "success",
                showCancelButton: false,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Done",
                closeOnConfirm: false
            },
            function(){
                location.href=location.href;
            });
            
        });
      },
      updateEmpType(e){
        
        // var pathArray = window.location.pathname.split( '/' );
        
        var formData = new FormData(e.target);

        // formData.append('file', document.getElementById('file').files[0]);

        axios.post("updateEmpType", formData, {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
        })
        .then((response) => { 

            this.HTMLcontent = null;

            if(response.data.title == 'error'){
              swal({
                title: response.data.title+"!",
                text: response.data.message,
                type: response.data.title,
                showCancelButton: false,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Done",
                closeOnConfirm: true
              });
            }
            else{
              swal({
                  title: response.data.title+"!",
                  text: response.data.message,
                  type: response.data.title,
                  showCancelButton: false,
                  confirmButtonColor: "#DD6B55",
                  confirmButtonText: "Done",
                  closeOnConfirm: false
              },
              function(){
                  location.href=location.href;
              });
            }
              
        })
        .catch((error) => {
            
            if(error.response.status != 200){ //error 422
                var errors = error.response.data;

                var errorsHtml = '<div class="alert alert-danger"><ul>';
                $.each( errors , function( key, value ) {
                    errorsHtml += '<li>' + value[0] + '</li>';
                });
                errorsHtml += '</ul></di>';
                
                this.HTMLcontent = errorsHtml;
            }
        });
      },
    }
  });
</script>

@endsection


@endsection