Vue.component('v-select', VueSelect.VueSelect);

Vue.component('select2', {
    props: ['options', 'value'],
    template: '<select><slot></slot></select>',
    mounted: function () {
        var vm = this
        $(this.$el)
          .val(this.value)
          // init select2
          .select2({ data: this.options })
          // emit event on change.
          .on('change', function () {
            vm.$emit('input', this.value)
          })
    },
    watch: {
        value: function (value) {
          // update value
          $(this.$el).val(value)
        },
        options: function (options) {
          // update options
          $(this.$el).select2({ data: options })
        }
    },
    destroyed: function () {
        $(this.$el).off().select2('destroy')
    }
})

new Vue({
  el: '#mainDiv',
  data: {
    emp_name: '',
    emp_supervisor: '',
    emp_leave_type: '',
    emp_leave_type_id:null,
    from_date: '',
    to_date: '',
    date_diff: 0,
    leave_reason: '',
    leave_contact_address: '',
    leave_contact_number: '',
    responsible_emp: '',
    employee_leave_noc_required: '',
    passport_no: '',
    leave_half_or_full: 1,
    options: [],
    users: [],
    leaveType: [],
    userLeaveType: [],
    userHaveLeavs: [],
    userTakenLeave: [],
    userTakenLeaveId: [],
    userTakenLeaveName: [],
    userTakenLeaveDays: [],
    show_history: [],
    holidays: '',
    weekends: '',
    leaveStatus: '',
    hdn_id: '',
    edit_file_info: '',
    edit_emp_name: '',
    edit_leave_type_id: null,
    edit_from_date: '',
    edit_to_date: '',
    edit_date_diff: '',
    edit_leave_reason: '',
    edit_leave_contact_address: '',
    edit_leave_contact_number: '',
    edit_responsible_emp: null,
    edit_employee_leave_noc_required: '',
    edit_passport_no: '',
    edit_leave_half_or_full: '',
    demo_type: [],
    edit_leave_type: [],
    testLoop : [],
    want_to_forward: 0,
    edit_forward_to: '',
    forwards: [],
  },
  mounted(){
    axios.get('/get-employee').then(response => this.users = response.data);
    axios.get('/get-employee').then(response => this.options = response.data);
  },
  watch:{
    emp_name: function(id){

      this.showLeaveBalance(id);  //call function
    }
  },
  methods:{
    applicationInDetails: function(id){

      this.emp_name = id;
    },
    formatDate: function(date){
      var hours = date.getHours();
      var minutes = date.getMinutes();
      var ampm = hours >= 12 ? 'pm' : 'am';
      hours = hours % 12;
      hours = hours ? hours : 12; // the hour '0' should be '12'
      minutes = minutes < 10 ? '0'+minutes : minutes;
      var strTime = hours + ':' + minutes + ' ' + ampm;
      return date.getFullYear() + "-" + (date.getMonth()+1) + "-" +date.getDate();
    },
    returnOnlyYear: function(date){
      var hours = date.getHours();
      var minutes = date.getMinutes();
      var ampm = hours >= 12 ? 'pm' : 'am';
      hours = hours % 12;
      hours = hours ? hours : 12; // the hour '0' should be '12'
      minutes = minutes < 10 ? '0'+minutes : minutes;
      var strTime = hours + ':' + minutes + ' ' + ampm;
      return date.getFullYear();
    },
    date_diff_cal: function(){

      var emp_leave_type_js = this.emp_leave_type;
      
      if(this.emp_leave_type > 0){
        this.from_date = $('#from_date').val();
        this.to_date = $('#to_date').val();
        var date1 = new Date($('#from_date').val());
        var date2 = new Date($('#to_date').val());
        var timeDiff = Math.abs(date2.getTime() - date1.getTime());
        this.date_diff = Math.ceil((timeDiff / (1000 * 3600 * 24))+1);
        var include_holiday_weekend = 0;

        $.each( this.userHaveLeavs, function( key, value ) {
            if(emp_leave_type_js == value.leave_type_id){
              if(value.leave_type.leave_type_include_holiday == 1){
                include_holiday_weekend = 1;
              }
              else{
                include_holiday_weekend = 0;
              }
            }
        });

        var dateUrl1 = this.formatDate(date1);
        var dateUrl2 = this.formatDate(date2);
        var year1 = this.returnOnlyYear(date1);
        var year2 = this.returnOnlyYear(date2);

        if(year1 == year2){
          var date_diff_js = this.date_diff;
          
          axios.get('/leave/getWeekendHolidays/'+dateUrl1+'/'+dateUrl2+'/'+this.emp_name).then(response => {

            this.holidays = response.data.holidays;
            this.weekends = response.data.weekend;

            if(include_holiday_weekend == 0){
              var sum = this.holidays + this.weekends;
              date_diff_js = date_diff_js - sum;
            }
            
            $('#show_date_diff').html(date_diff_js); 

            if(Date.parse($('#to_date').val()) < Date.parse($('#from_date').val()))
            {
              $('#show_date_diff').html('Invalid');
              this.date_diff = "";
            }
            else{
              
                $.each( this.userLeaveType, function( key, value ) {
                    if(emp_leave_type_js == value.id){
                      var chk_day = value.days - date_diff_js;
                      
                      //value.days for undefined days
                      //wher balance minus value.days >= 0

                      if(chk_day < 0 && value.days != null && value.days >= 0){
                        $('#show_date_diff_msg').html("* You can only apply for "+value.days+" days or below "+value.days+" days leave.");
                      }
                      else{
                        $('#show_date_diff_msg').html("");
                      }
                    }
                });
            }
          });
        }
        else{
          swal("Invalid Date!", "From date and to date must have same year...", "error");
        }
      }
      else{
        swal("Try again!", "Please select leave type first...", "error");
      } 
    },
    edit_date_diff_cal: function(){
      var emp_leave_type_js = this.edit_leave_type_id;
      
      if(emp_leave_type_js > 0){
        this.edit_from_date = $('#edit_from_date').val();
        this.edit_to_date = $('#edit_to_date').val();
        var date1 = new Date($('#edit_from_date').val());
        var date2 = new Date($('#edit_to_date').val());
        var timeDiff = Math.abs(date2.getTime() - date1.getTime());
        var date_diff_js = Math.ceil((timeDiff / (1000 * 3600 * 24))+1);
        var include_holiday_weekend = 0;

        $.each( this.userHaveLeavs, function( key, value ) {
            if(emp_leave_type_js == value.leave_type_id){
              if(value.leave_type.leave_type_include_holiday == 1){
                include_holiday_weekend = 1;
              }
              else{
                include_holiday_weekend = 0;
              }
            }
        });

        var dateUrl1 = this.formatDate(date1);
        var dateUrl2 = this.formatDate(date2);
        var year1 = this.returnOnlyYear(date1);
        var year2 = this.returnOnlyYear(date2);

        if(year1 == year2){
          
          axios.get('/leave/getWeekendHolidays/'+dateUrl1+'/'+dateUrl2+'/'+this.edit_emp_name).then(response => {

            this.holidays = response.data.holidays;
            this.weekends = response.data.weekend;

            if(include_holiday_weekend == 0){
              var sum = this.holidays + this.weekends;
              date_diff_js = date_diff_js - sum;
            }
            
            this.edit_date_diff = date_diff_js;
            
            console.log('After sum deduction:'+ this.edit_date_diff);

            if(Date.parse($('#edit_to_date').val()) < Date.parse($('#edit_from_date').val()))
            {
              this.date_diff = "Invalid";
            }
            else{
              
                $.each( this.userLeaveType, function( key, value ) {
                    if(emp_leave_type_js == value.id){
                      var chk_day = value.days - date_diff_js;
                      
                      //value.days for undefined days
                      //wher balance minus value.days >= 0

                      if(chk_day < 0 && value.days != null && value.days >= 0){
                        $('#edit_show_date_diff_msg').html("* You can only apply for "+value.days+" days or below "+value.days+" days leave.");
                      }
                      else{
                        $('#edit_show_date_diff_msg').html("");
                      }
                    }
                });
            }
          });
        }
        else{
          swal("Invalid Date!", "From date and to date must have same year...", "error");
        }
      }
      else{
        swal("Try again!", "Please select leave type first...", "error");
      } 
    },
    saveData(e){

        var pathArray = window.location.pathname.split( '/' );
        
        var formData = new FormData(e.target);

        formData.append('file', document.getElementById('file').files[0]);

        axios.post("/"+pathArray[1]+"/add", formData, {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
        })
        .then((response) => { 
            $( '#create-form-errors' ).html('');

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
                $( '#create-form-errors' ).html( errorsHtml );
            }
        });
    },
    showLeaveBalance: function(id){

      this.userLeaveType = [];
      this.userHaveLeavs = [];
      this.userTakenLeave = [];
      $('#show_date_diff').html('');

      if(id > 0){
        axios.get('/user-taken-leave/'+id).then(response => {
          this.userLeaveType = response.data.user_leave_type;
          this.userHaveLeavs = response.data.userHaveLeavs;
          this.userTakenLeaveId = response.data.taken_leave_type_id;
          this.userTakenLeaveName = response.data.taken_leave_type_name;
          this.userTakenLeaveDays = response.data.taken_leave_type_days;
          this.userTakenLeave = response.data.taken_leave_ary;
          this.show_history = response.data.show_history;

          var ary_demo_type = [];
          $.each( this.userLeaveType, function( key, value ) {
              // console.log(key+'=='+value.id+"---"+value.name);
              var newElement = {};
              newElement['id'] = value.id;
              newElement['name'] = value.name;
              ary_demo_type.push(newElement);
          });
        });
      }
    },
    showLeaveStatus: function(id){

      var val = '';

      if(id > 0){
        if(id == 1){
          val = "Pending";
        }
        else if(id == 2){
          val = "Forward";
        }
        else if(id == 3){
          val = "Approved";
        }
        else if(id == 4){
          val = "Cancel";
        }
        else{
          val = "Invalid";
        }
      }
      else{
        val = "Invalid";
      }

      return val;
    },
    leaveStatusBtn: function(id){

      var val = '';

      if(id > 0){
        if(id == 1){
          val = "btn-warning";
        }
        else if(id == 2){
          val = "btn-info";
        }
        else if(id == 3){
          val = "btn-success";
        }
        else if(id == 4){
          val = "btn-danger";
        }
        else{
          val = "Invalid";
        }
      }
      else{
        val = "Invalid";
      }

      return val;
    },
    editData(id){

        var pathArray = window.location.pathname.split( '/' );
        // console.log(pathArray[1]+"--"+pathArray[2]);
        axios.get("/"+pathArray[1]+"/edit/"+id,{
        
        })
        .then((response) => {

          this.edit_emp_name = response.data.user_id;
          $('#edit_show_date_diff_msg').html("");
          this.userLeaveType = [];
          this.userHaveLeavs = [];
          this.userTakenLeave = [];
          $('#show_date_diff').html('');
          
          this.hdn_id = response.data.hdn_id;
          
          this.edit_leave_type_id = response.data.leave_type_id;
          this.edit_from_date =response.data.employee_leave_from;
          this.edit_to_date =response.data.employee_leave_to;
          this.edit_date_diff =response.data.employee_leave_total_days;
          this.edit_leave_reason =response.data.employee_leave_user_remarks;
          this.edit_leave_half_or_full =response.data.employee_leave_half_or_full;
          this.edit_leave_contact_address =response.data.employee_leave_contact_address;
          this.edit_leave_contact_number =response.data.employee_leave_contact_number;
          this.edit_employee_leave_noc_required =response.data.employee_leave_noc_required;
          this.edit_passport_no =response.data.employee_leave_passport_no;
          this.edit_responsible_emp =response.data.employee_leave_responsible_person;
          this.emp_supervisor = response.data.employee_leave_supervisor;
          this.leaveStatus = response.data.employee_leave_status;
          if(this.leaveStatus == 2){
            this.want_to_forward = true;
            this.edit_forward_to = response.data.employee_leave_recommend_to;
          }else{
            this.want_to_forward = false;
          }
          
          this.userLeaveType = response.data.user_leave_type;
          this.edit_leave_type = response.data.user_leave_type;
          this.userHaveLeavs = response.data.userHaveLeavs;
          this.userTakenLeaveId = response.data.taken_leave_type_id;
          this.userTakenLeaveName = response.data.taken_leave_type_name;
          this.userTakenLeaveDays = response.data.taken_leave_type_days;
          this.userTakenLeave = response.data.taken_leave_ary;
          this.show_history = response.data.show_history;
          
          var imgg = response.data.employee_leave_attachment;
          if(imgg != null){
            this.edit_file_info = "File already available";
          }
          else{
            this.edit_file_info = '';
          }

          // edit_file_info: '',
          // edit_emp_name: '',
          
        })
        .catch(function (error) {
            
            swal('Error:','Edit function not working','error');
        });
    },
    updateData: function(e){
            
        var formData = new FormData(e.target);
        formData.append('file', document.getElementById('file').files[0]);

        axios.post('/leave/edit', formData)
        .then(response => { 
           
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
    },

    changeStatus: function(id, stat){
        
        var btn_text='';
        var btn_color = '#fffff';
        if(stat == 3){
          btn_text = "Approve";
          btn_color = "#70ca63";
        }else if(stat == 4){
          btn_text = "Cancel";
          btn_color = "#df5640";
        }else{
          btn_text = "Invalid";
        }

        swal({
          title: "Are you sure?",
          text: "You will not be able to recover !",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: btn_color,
          confirmButtonText: "Yes, "+btn_text+" it!",
          closeOnConfirm: false
        },
        function(){
          // alert(id +'----'+ stat);
          // swal("Changed !", "Status changed successfully.", "success");
            axios.get("/leave/changeStatus/"+id+"/"+stat,{
      
            })
            .then(response => { 
               
              swal({
                title: "Changed !",
                text: "Status changed successfully.",
                type: "success",
                showCancelButton: false,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Done",
                closeOnConfirm: false
              },
              function(){
                  location.href=location.href;
              });
            })
            .catch( (error) => {
                var errors = error.response.data;
                console.log(error);
            });
        });
    },
    chResponsibleStatus: function(id, stat, loginEmp){

        axios.get("/leave/chResponsibleStatus/"+id+"/"+stat+"/"+loginEmp,{
      
        })
        .then(response => { 
           
          swal({
            title: "Changed !",
            text: "Status changed successfully.",
            type: "success",
            showCancelButton: false,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Done",
            closeOnConfirm: false
          },
          function(){
              location.href=location.href;
          });
        })
        .catch( (error) => {
            var errors = error.response.data;
            console.log(error);
        });
    }
  }
})