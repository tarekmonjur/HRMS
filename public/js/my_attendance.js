  $(document).on('ready',function(){ 
    $('#fromDate').datetimepicker({
          format: 'YYYY-MM-DD',
          maxDate:new Date(),
          pickTime: false
      });
  });


new Vue({
  el: '#content',

  data:{
    employee_no:employee_no,
    from_date:from_date,
    to_date:to_date,
    index:null,
    hideShowId: '#showAttendance',
    attendances:[],
    report:[],
    userName:null,
    attend:[],
    errors:[]
  },


  mounted(){

    this.attendance();

  },


  methods:{

      showMessage(data){
        new PNotify({
            title: data.title,
            text: data.message,
            shadow: true,
            addclass: 'stack_top_right',
            type: data.status,
            width: '290px',
            delay: 2000,
            icon: false,
        });
      },


      loadinShow(id){
        $(id).LoadingOverlay("show",{color:"rgba(0, 0, 0, 0)"});
      },


      loadinHide(id){
        $(id).LoadingOverlay("hide",{color:"rgba(0, 0, 0, 0)"});
      },


        myTimePicker(){
          $('.myTimePicker').datetimepicker({
              autoclose:true,
                pickDate: false,
          });
      },


      myDatePicker(idClass){
        $(idClass).datetimepicker({
              format: 'YYYY-MM-DD',
              maxDate:new Date(),
              pickTime: false
        });
      },


      toDate(){
        var date = $('#fromDate').val();
          $('#toDate').datetimepicker({
            format: 'YYYY-MM-DD',
            maxDate: new Date(),
            minDate: date,
            pickTime: false
        });
      },


      modal_open(form_id) {
        this.errors = [];

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
      },


      attendance(){
        this.loadinShow(this.hideShowId);
        axios.post('/attendance/index',{
          employee_no:this.employee_no,
          from_date:this.from_date,
          to_date:this.to_date
        }).then((response) => {
          this.attendances = response.data.attendance;
          this.report = response.data.report;
          this.userName = response.data.attendance[0].first_name+' '+response.data.attendance[0].last_name+' ('+response.data.attendance[0].employee_no+')';
          // console.log(response.data.attendance[0].attendanceTimesheets);
          this.loadinHide(this.hideShowId);
        }).catch((error)=>{
            this.loadinHide(this.hideShowId);
            if(error.response.status == 500 || error.response.data.status == 'danger'){
                var error = error.response.data;
                this.showMessage(error);
            }else if(error.response.status == 422){
                this.errors = error.response.data;
            }
        });

      },


      getAttendance(e){
        var formData = new FormData(e.target);
        this.from_date = $('#fromDate').val();
        this.to_date = $('#toDate').val();
        this.errors = [];
        
        this.loadinShow(this.hideShowId);

        axios.post('/attendance/index',formData).then((response) => {
          this.errors = [];
            this.attendances = response.data.attendance;
            this.report = response.data.report;
            this.loadinHide(this.hideShowId);

        }).catch((error)=>{
            this.loadinHide(this.hideShowId);
            this.errors = [];

            if(error.response.status == 500 || error.response.data.status == 'danger'){
                var error = error.response.data;
                this.showMessage(error);
            }else if(error.response.status == 422){
                this.errors = error.response.data;
            }
        });

      },


      addAttendance(index,user_id,timeSheetId,date,observation,fullname,modal_id){
        this.index = index;
        this.attend = [];

        this.loadinShow(modal_id);

        this.attend = {
          'user_fullname':fullname,
          'user_id': user_id,
          'time_sheet_id': timeSheetId,
          'date':date,
          'in_time':null,
          'out_time': null,
          'observation': observation
        };

        this.modal_open(modal_id);
        this.loadinHide(modal_id);
      },


      saveAttendance(e){

        var formData = new FormData(e.target);
        this.errors = [];
        
        this.loadinShow(this.hideShowId);

        axios.post('/attendance/add',formData).then((response) => {
          this.errors = [];
          // console.log(response.data.data);
            this.attendances[this.index] = response.data.data;
            this.loadinHide(this.hideShowId);
            jQuery(".mfp-close").trigger("click");
          this.showMessage(response.data);

        }).catch((error)=>{
            this.loadinHide(this.hideShowId);
            this.errors = [];

            if(error.response.status == 500 || error.response.data.status == 'danger'){
                var error = error.response.data;
                this.showMessage(error);
            }else if(error.response.status == 422){
                this.errors = error.response.data;
            }
        });
      }


    }



});
