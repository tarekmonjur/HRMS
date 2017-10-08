
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
    el: '#providentFund',
    data:{
      index:'',
      dataTable:true,
      user_id:null,
      pf_user_id:null, //check for edit
      users:[],
      hasFund:true,
      providentFund:[],
      providentFunds:[],
      user_name:null,
      providentfundDetails:[],
      total_debit:0,
      total_credit:0,
      errors:[]
    },


    created(){
      this.getEmployees();
    },


    mounted(){
      this.getProvidentFund();
    },


    watch:{
      user_id(id){
        if(id !=0){
          if(this.pf_user_id != id){
            // alert(id + '--' +this.pf_user_id);
            this.loadinShow('#provident_fund_modal .panel-body');
            axios.get('/providentfund/index/'+id).then(response => {
              //console.log(response.data);
              if(response.data){
                this.hasFund = true;
                this.errors.push({'has_fund':[]});

                if(response.data.pf_status == 1){
                  this.errors.has_fund = ['This employee already have a provident fund.'];
                }else{
                  this.errors.has_fund = ['This employee already have a provident fund. Please active that!'];
                }
              }else{
                this.errors = [];
                this.hasFund = false;
              }
            });
            this.loadinHide('#provident_fund_modal .panel-body');
          }else{
            this.errors = [];
            this.hasFund = false;
          }
        }
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


      getProvidentFund(){
        this.loadinShow('#datatableCall');
        axios.get('/providentfund/index').then((response) => {
          this.providentFunds = response.data;
          this.dataTableGenerate();
          this.loadinHide('#datatableCall');
        }).catch((error)=>{
          this.showMessage({'title':'Warning!','status':'warning','message':'If problem, Please reload page.'});
        });
        this.loadinHide('#datatableCall');
      },


      addProvidentFund(e){
        var formData = new FormData(e.target);
        this.loadinShow('#providentFund_modal');

        axios.post('/providentfund/add',formData).then((response) => {
          //console.log(response.data.data);
          this.providentFunds.unshift(response.data.data);
          this.dataTableGenerate();
          jQuery(".mfp-close").trigger("click");
          this.showMessage(response.data);
          this.loadinHide('#providentFund_modal');

        }).catch((error)=>{
          this.loadinHide('#providentFund_modal');

          if(error.response.status == 500 || error.response.data.status == 'danger'){
              var error = error.response.data;
              this.showMessage(error);
          }else if(error.response.status == 422){
              this.errors = error.response.data;
          }
        });
      },


      editProvidentFund(id, index, model_id){
        this.loadinShow(model_id);
        this.index = index;

        axios.get('/providentfund/edit/'+id).then((response) => {
          this.loadinHide(model_id);
          this.providentFund = response.data.data;
          this.user_id = this.providentFund.user_id;
          this.pf_user_id = this.user_id;
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


      updateProvidentFund(e){
        var formData = new FormData(e.target);
        this.loadinShow('#providentFund_modal');

        axios.post('/providentfund/edit',formData).then((response) => {
          this.$set(this.providentFunds,this.index,response.data.data);
          jQuery(".mfp-close").trigger("click");
          this.showMessage(response.data);
          this.loadinHide('#providentFund_modal');

        }).catch((error)=>{
          this.loadinHide('#providentFund_modal');
          if(error.response.status == 500 || error.response.data.status == 'danger'){
              var error = error.response.data;
              this.showMessage(error);
          }else if(error.response.status == 422){
              this.errors = error.response.data;
          }
        });
      },


      deleteProvidentFund(id,index){
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
            axios.delete('/providentfund/delete/'+id).then((response) => {
              vueThis.providentFunds.splice(vueThis.index,1);
              vueThis.dataTableGenerate();
              swal("Deleted!", response.data.message, "success");
            }).catch((error)=>{
              swal("Cancelled", error.response.data.message, "error");
            });
        });

      },


      approvedProvidentFund(providentFund_id, index){
        var vueThis = this;
        this.index = index;
        swal({
          title: "Are you sure approved?",
          text: "You will not be able to panding this imaginary data!",
          // type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#DD6B55",
          confirmButtonText: "Yes, approved it!",
          closeOnConfirm: false
        },
        function(){ 
            axios.post('/providentfund/edit',{providentFund_id:providentFund_id}).then((response) => {
              vueThis.$set(vueThis.providentFunds,vueThis.index,response.data.data);
              swal(response.data.message, "success");
            }).catch((error)=>{
              // console.log(error);
              swal("Cancelled", error.response.data.message, "error");
            });
        });
      },


      changeStatus(e,id){
        this.loadinShow('#datatableCall');
        var status = e.target.getAttribute('status');
        var status2;

        if(status == 1){
          status2 = 0;
        }else{
          status2 = 1;
        }
        
        axios.post('/providentfund/edit',{'id':id,'pf_status':status2}).then((response) => {
          e.target.setAttribute('status',status2);

          if(status == 0){
            e.target.classList.remove("text-primary");
            e.target.classList.add("text-danger");
            e.target.text = "Inactive";            
          }else{
            e.target.classList.remove("text-danger");
            e.target.classList.add("text-primary");
            e.target.text = "Active";
          }
          this.loadinHide('#datatableCall');
          this.showMessage(response.data);
        }).catch((error) => {

          if(error.response.status == 500 || error.response.data.status == 'danger'){
              this.showMessage(error.response.data);
          }
          this.loadinHide('#datatableCall');
        });
      },


      showDetails(providentfund_id, modal_id){
        this.loadinShow(modal_id);
        axios.get('/providentfund/index?pf_id='+providentfund_id).then(response => {
          this.providentfundDetails = response.data.details;
          this.total_debit = response.data.total_debit;
          this.total_credit = response.data.total_credit;
          // alert();
          this.modal_open(modal_id);
          this.dataTableGenerate('#datatableCall2');
          this.loadinHide(modal_id);
        });
      }



    }

  });



