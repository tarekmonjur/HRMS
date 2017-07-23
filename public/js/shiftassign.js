$(document).on('ready',function(){


  var work = new Vue({
    el: '#shiftassign',
    data:{
      index:null,
      isActive:0,
      buttonDisable:false,
      workshifts:[],
      // workshift:[],
      employeeShifts:[],
      employeeShift:[],
      histories:[],
      deleted:[],
      errors:[]
    },

    mounted(){
      this.getWorkShifts();
      this.getEmployeeShifts();
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


      myDatePicker(){
          $('.myDatePicker').datetimepicker({
              format: 'YYYY-MM-DD',
              // format: 'DD M YYYY',
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


      getWorkShifts(){
        this.loadinShow('#shiftassign');

        axios.get('/get-workshifts/null/1').then((response) => {
          this.workshifts = response.data;
          this.loadinHide('#shiftassign');
        }).catch((error)=>{
          alert('please reload page.');
        });
        this.loadinHide('#shiftassign');
      },


      getEmployeeShifts(id=0){
        this.loadinShow('#shiftassign');

        axios.get('/shiftassign/index/'+id).then((response) => {
          this.employeeShifts = response.data;
          this.loadinHide('#shiftassign');
        }).catch((error)=>{
          alert('please reload page.');
        });
        this.loadinHide('#shiftassign');
      },


      getWorkAssign(modal_id,index){
        this.index = index;
        this.employeeShift = $.extend(true,{},this.employeeShifts[index]);
        if(this.employeeShift.work_shift.length <= 0){
          this.buttonDisable = true;
        }
        this.modal_open(modal_id);
      },


      addMoreShift(){
        this.employeeShift.work_shift.push({
          'work_shift_id':'',
          'days':[1,2,3,4,5,6,7],
          'start_date':'',
          'end_date':'',
        });
        if(this.employeeShift.work_shift.length > 0){
          this.buttonDisable = false;
        }
      },


      deleteWorkShift(index, id){
        this.deleted.push(this.employeeShift.work_shift[index].id);
        this.employeeShift.work_shift.splice(index,1);
      },


      // inArray(value,arr){
      //   for(var data in arr){
      //     if(arr[data] == value){
      //       return true;
      //     }
      //   }
      // },


      // dayPush(index,value){
      //   this.employeeShift.work_shift[index].days.push(value);
      //   console.log(this.employeeShift.work_shift[index]);
      // },


      workShiftAssign(e){
        this.loadinShow('#shiftassign');

        var formData = new FormData(e.target);

        axios.post('/shiftassign/assign',formData).then((response) => {
          this.getEmployeeShifts();
          jQuery(".mfp-close").trigger("click");
          this.loadinHide('#shiftassign');
          this.showMessage(response.data);
        }).catch((error)=>{
          this.loadinHide('#shiftassign');
          if(error.response.status == 500 || error.response.data.status == 'danger'){
              var error = error.response.data;
              this.showMessage(error);
          }else if(error.response.status == 422){
              this.errors = error.response.data;
          }
        });
      },


      historyWorkShift(index, id){
        this.histories.push(this.employeeShift.work_shift[index].id);
        // this.employeeShifts[this.index].work_shift.splice(index,1);
        this.employeeShift.work_shift.splice(index,1);

        // var _this = this;
        // _this.employeeShifts[this.index].work_shift.splice(index,1);
        // _this.employeeShift.work_shift.splice(index,1);
        // swal({
        //   title: "Are you sure?",
        //   text: "History data not show here!",
        //   type: "warning",
        //   showCancelButton: true,
        //   confirmButtonColor: "#DD6B55",
        //   confirmButtonText: "Yes, history it!",
        //   cancelButtonText: "No, cancel please!",
        //   closeOnConfirm: false,
        //   closeOnCancel: false
        // },
        // function(isConfirm){
        //   if (isConfirm){
        //     axios.get('/shiftassign/delete/'+id).then((response) => {
        //       _this.employeeShift.work_shift.splice(index,1);
        //       alert(index);
        //       swal(response.data.message, "success");
        //       // _this.showMessage(response.data);
        //     }).catch((error)=>{
        //       if(error.response.status == 500 || error.response.data.status == 'danger'){
        //           var error = error.response.data;
        //           // _this.showMessage(error);
        //           swal("Cancelled", error.response.data.message, "error");
        //       }else if(error.response.status == 422){
        //           _this.errors = error.response.data;
        //       }
        //     });
        //   }else{
        //       swal("Cancelled", "Data cancel move to history", "error");
        //   }
        // });
      },


    }

  });


});