
Vue.component('select2', {
   props: ['value'],
   template: '<select><slot></slot></select>',
   mounted: function() {
     var vm = this
     $(this.$el)
       .val(this.value).select2()
       .on('change', function() {
         vm.$emit('input', this.value)
       })
   },
   watch: {
     value: function(value) {
       $(this.$el).select2('val', value)
     }
   },
   destroyed: function() {
     $(this.$el).off().select2('destroy')
   }
 });

$(document).ready(function(){

var work = new Vue({
    el: '#payroll',
    data:{
      index:'',
      department_id:0,
      unit_id:0,
      branch_id:0,
      units:[],
      users:[],
      salary_type:'month',
      payRoll:[],
      payRolls:[],
      errors:[]
    },


    watch:{
      department_id: function(id){
        if(id !=0){
            this.getUnitByDepartmentId(id);
        }else{
          this.units = [];
        }
        this.getEmployee();
      },

      unit_id: function(){
        this.getEmployee();
      },

      branch_id: function(){
        this.getEmployee();
      },

    },


    methods:{

      dataTableCall(id){
        $(id).dataTable({
          "destroy": true,
          "paging":   true,
          "searching": true,
          "info": true,
          "sDom": '<"dt-panelmenu clearfix"lfr>t<"dt-panelfooter clearfix"ip>',
        }); 
      },
      

      dataTableDestroy(id){
        $(id).dataTable().fnDestroy(); 
      },


      dataTableGenerate(id='#datatableCall'){
        vueThis = this;
        if(this.dataTable){
          setTimeout(function(){vueThis.dataTableCall(id);}, 5);
          this.dataTable = false;
        }else{
          this.dataTableDestroy(id);
          setTimeout(function(){vueThis.dataTableCall(id);}, 5);
        }
      },


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


      loadinShow(idClass){
        $(idClass).LoadingOverlay("show",{color:"rgba(0, 0, 0, 0)"});
      },


      loadinHide(idClass){
        $(idClass).LoadingOverlay("hide",{color:"rgba(0, 0, 0, 0)"});
      },


      myMonthPicker(){
          $('.myMonthPicker').datetimepicker({
              format: 'YYYY-MM',
              minViewMode: 'months',
              viewMode: 'months',
              pickTime: false,
          });
      },


      myDatePicker(){
        $('.myDatePicker').datetimepicker({
              format: 'YYYY-MM-DD',
              // maxDate:new Date(),
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


      getUnitByDepartmentId(id){
          axios.get('/get-unit-by-department-id/'+id).then(response => {
              this.units = response.data;
              // console.log(this.designations);
          });
      },


      getEmployee(){
        axios.get('/payroll/index/'+this.branch_id+'/'+this.department_id+'/'+this.unit_id).then(response => {
            this.users = response.data;
            // console.log(this.supervisors);
        });
      },


      getFullName(data){
        if(data){
          let fullname = '<a href="/employee/view/'+data.employee_no+'" target="_blank">';
          fullname += (data.first_name)?data.first_name+' ':'';
          fullname += (data.last_name)?data.last_name:'';
          fullname +='</a>';
          return fullname;
        }
      },


      generateSalary(e){
        this.loadinShow('#payroll');
        let formData = new FormData(e.target);

        axios.post('/payroll/index', formData).then(response => {
          // console.log(response.data);
          this.payRolls = response.data;
          this.errors = [];
          this.loadinHide('#payroll');

        }).catch(error => {
          // console.log(error);
          this.loadinHide('#payroll');
          if(error.response.status == 500 || error.response.data.status == 'danger'){
              var error = error.response.data;
              this.showMessage(error);
          }else if(error.response.status == 422){
              this.errors = error.response.data;
          }
        });
      },


      editSalary(user_id, index, model_id){
        this.loadinShow(model_id);
        this.index = index;
        this.payRoll = this.payRolls[index];
        // console.log(this.payRoll.full_name);
        this.modal_open(model_id);
        this.loadinHide(model_id);
      },


      updateSalary(){
        this.payRoll.salary = Math.round(this.payRoll.perday_salary * this.payRoll.payment_days);
        this.payRoll.perhour_salary = Math.round(this.payRoll.perday_salary / this.payRoll.work_hour);
        this.payRoll.overtime_amount = Math.round(this.payRoll.perhour_salary * this.payRoll.overtime_hour);
        this.payRoll.gross_salary = Math.round(this.payRoll.salary + this.payRoll.total_allowance + this.payRoll.overtime_amount);
        this.payRoll.net_salary = Math.round(this.payRoll.salary - this.payRoll.total_deduction);
        this.payRoll.total_salary = Math.round((this.payRoll.salary + this.payRoll.overtime_amount + this.payRoll.total_allowance) - this.payRoll.total_deduction);
      },


      comfirmSalary(user_id, index){
        this.loadinShow('#payroll');
        let formData = this.payRolls[index];

        axios.post('/payroll/add', formData).then(response => {
          // console.log(response.data);
          // this.payRolls = response.data;
          this.errors = [];
          this.loadinHide('#payroll');
          this.showMessage(response.data);

        }).catch(error => {
          // console.log(error);
          this.loadinHide('#payroll');
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


  });



