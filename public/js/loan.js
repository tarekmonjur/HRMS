
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


var work = new Vue({
    el: '#loans',
    data:{
      index:'',
      dataTable:true,
      users:[],
      loanTypes:[],
      loan:[],
      loans:[],
      errors:[]
    },


    created(){
      this.getLoanType();
      this.getEmployees();
    },


    mounted(){
      this.getLoan();
    },


    methods:{

      dataTableCall(){
        $('#datatableCall').dataTable({
          "destroy": true,
          "paging":   true,
          "searching": true,
          "info": true,
          "sDom": '<"dt-panelmenu clearfix"lfr>t<"dt-panelfooter clearfix"ip>',
        }); 
      },
      

      dataTableDestroy(){
        $('#datatableCall').dataTable().fnDestroy(); 
      },


      dataTableGenerate(){
        vueThis = this;
        if(this.dataTable){
            setTimeout(function(){vueThis.dataTableCall();}, 5);
          this.dataTable = false;
        }else{
             this.dataTableDestroy();
             setTimeout(function(){vueThis.dataTableCall();}, 5);
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


      myTimePicker(){
          $('.myTimePicker').datetimepicker({
              autoclose:true,
              pickDate: false,
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


      getEmployees(){
           axios.get('/get-employees').then(response => {
              this.users = response.data;
              // console.log(this.supervisors);
          });
      },


      getLoanType(){
        axios.get('/get-loan-type').then(response => {
          this.loanTypes = response.data;
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


      getLoan(){
        this.loadinShow('#datatableCall');
        axios.get('/loan/index').then((response) => {
          this.loans = response.data;
          this.dataTableGenerate();
          this.loadinHide('#datatableCall');
        }).catch((error)=>{
          this.showMessage({'title':'Warning!','status':'warning','message':'If problem, Please reload page.'});
        });
        this.loadinHide('#datatableCall');
      },


      addLoan(e){
        var formData = new FormData(e.target);
        this.loadinShow('#loan_modal');

        axios.post('/loan/add',formData).then((response) => {
          this.loans.unshift(response.data.data);
          // console.log(response.data.data);
          jQuery(".mfp-close").trigger("click");
          this.showMessage(response.data);
          this.loadinHide('#loan_modal');
          this.dataTableGenerate();

        }).catch((error)=>{
          this.loadinHide('#loan_modal');

          if(error.response.status == 500 || error.response.data.status == 'danger'){
              var error = error.response.data;
              this.showMessage(error);
          }else if(error.response.status == 422){
              this.errors = error.response.data;
          }
        });
      },


      editLoan(id, index, model_id){
        this.loadinShow(model_id);
        this.index = index;

        axios.get('/loan/edit/'+id).then((response) => {
          this.loadinHide(model_id);
          this.loan = response.data.data;
          this.modal_open(model_id);

        }).catch((error)=>{
          this.loadinHide(model_id);
          if(error.response.status == 500 || error.response.data.status == 'danger'){
              var error = error.response.data;
              this.showMessage(error);
          }else if(error.response.status == 422){
              this.errors = error.response.data;
          }
        });
      },


      updateLoan(e){
        var formData = new FormData(e.target);
        this.loadinShow('#loan_modal');

        axios.post('/loan/edit',formData).then((response) => {
          this.$set(this.loans,this.index,response.data.data);
          jQuery(".mfp-close").trigger("click");
          this.showMessage(response.data);
          this.loadinHide('#loan_modal');

        }).catch((error)=>{
          this.loadinHide('#loan_modal');
          if(error.response.status == 500 || error.response.data.status == 'danger'){
              var error = error.response.data;
              this.showMessage(error);
          }else if(error.response.status == 422){
              this.errors = error.response.data;
          }
        });
      },


      deleteLoan(id,index){
       var vueThis = this;
       this.index = index;
       swal({
          title: "Are you sure delete?",
          text: "You will not be able to recover this imaginary data!",
          // type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#DD6B55",
          confirmButtonText: "Yes, delete it!",
          closeOnConfirm: false
        },
        function(){ 
            axios.delete('/loan/delete/'+id).then((response) => {
              vueThis.loans.splice(vueThis.index,1);
              vueThis.dataTableGenerate();
              swal("Deleted!", response.data.message, "success");
            }).catch((error)=>{
              swal("Cancelled", error.response.data.message, "error");
            });
        });

      },


      approvedLoan(loan_id, index){
        var vueThis = this;
        this.index = index;
        swal({
          title: "Are you sure approved?",
          text: "You will not be able to panding this imaginary data!",
          // type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#70ca63",
          confirmButtonText: "Yes, approved it!",
          closeOnConfirm: false
        },
        function(){ 
            axios.post('/loan/edit',{loan_id:loan_id}).then((response) => {
              vueThis.$set(vueThis.loans,vueThis.index,response.data.data);
              swal(response.data.message, "success");
            }).catch((error)=>{
              // console.log(error);
              swal("Cancelled", error.response.data.danger, "error");
            });
        });
      },


      changeStatus(id,index,status){
        this.loadinShow('#datatableCall');
        if(status == 1){
          status2 = 0;
        }else{
          status2 = 1;
        }
        
        axios.post('/loan/edit',{'id':id,'loan_status':status2}).then((response) => {
          this.$set(this.loans,index,response.data.data);
          this.loadinHide('#datatableCall');
          this.showMessage(response.data);
        }).catch((error) => {

          if(error.response.status == 500 || error.response.data.status == 'danger'){
              this.showMessage(error.response.data);
          }
          this.loadinHide('#datatableCall');
        });
      },



    }

  });



