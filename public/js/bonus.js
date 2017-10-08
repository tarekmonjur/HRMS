
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
    el: '#bonus',
    data:{
      index:'',
      dataTable:true,
      designation_id:0,
      designations:[],
      users:[],
      bonusTypes:[],
      bonuses:[],
      bonus:[],
      bonus_amount_type:'',
      bonus_type_amount:'',
      errors:[]
    },


    created(){
      this.getBonusType();
      this.getDesignations();
    },


    mounted(){
      this.getBonus();
    },


    watch:{
      designation_id: function(id){
        if(id !=0){
            this.loadinShow('#bonus_modal .panel-body');
            this.getEmployeeByDesignationId(id);
            this.loadinHide('#bonus_modal .panel-body');
        }else{
          this.users = [];
        }
      },

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
            setTimeout(function(){vueThis.dataTableCall();}, 1);
          this.dataTable = false;
        }else{
             this.dataTableDestroy();
             setTimeout(function(){vueThis.dataTableCall();}, 1);
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


      getDesignations(){
          axios.get('/get-designations').then(response => {
              this.designations = response.data;
              // console.log(this.designations);
          });
      },


      getEmployeeByDesignationId(designation_id){
           axios.get('/get-employee-by-deisgnation-id/'+designation_id).then(response => {
              this.users = response.data;
              // console.log(this.supervisors);
          });
      },


      getBonusType(){
        axios.get('/get-bonus-type').then(response => {
          this.bonusTypes = response.data;
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


      getBonus(){
        this.loadinShow('#datatableCall');
        axios.get('/bonus/index').then((response) => {
          this.bonuses = response.data;
          this.dataTableGenerate();
          this.loadinHide('#datatableCall');
        }).catch((error)=>{
          alert('please reload page.');
        });
        this.loadinHide('#datatableCall');
      },


      addBonus(e){
        var formData = new FormData(e.target);
        this.loadinShow('#bonus_modal');

        axios.post('/bonus/add',formData).then((response) => {
          this.getBonus();
          jQuery(".mfp-close").trigger("click");
          this.showMessage(response.data);
          this.loadinHide('#bonus_modal');

        }).catch((error)=>{
          this.loadinHide('#bonus_modal');

          if(error.response.status == 500 || error.response.data.status == 'danger'){
              var error = error.response.data;
              this.showMessage(error);
          }else if(error.response.status == 422){
              this.errors = error.response.data;
          }
        });
      },


      editBonus(id, index, model_id){
        this.loadinShow(model_id);
        this.index = index;

        axios.get('/bonus/edit/'+id).then((response) => {
          this.loadinHide(model_id);
          this.bonus = response.data.data;
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


      updateBonus(e){
        var formData = new FormData(e.target);
        this.loadinShow('#bonus_modal');

        axios.post('/bonus/edit',formData).then((response) => {
          this.$set(this.bonuses,this.index,response.data.data);
          jQuery(".mfp-close").trigger("click");
          this.showMessage(response.data);
          this.loadinHide('#bonus_modal');

        }).catch((error)=>{
          this.loadinHide('#bonus_modal');
          if(error.response.status == 500 || error.response.data.status == 'danger'){
              var error = error.response.data;
              this.showMessage(error);
          }else if(error.response.status == 422){
              this.errors = error.response.data;
          }
        });
      },


      deleteBonus(id,index){
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
            axios.delete('/bonus/delete/'+id).then((response) => {
              vueThis.bonuses.splice(vueThis.index,1);
              vueThis.dataTableGenerate();
              swal("Deleted!", response.data.message, "success");
            }).catch((error)=>{
              swal("Cancelled", error.response.data.message, "error");
            });
        });

      },


      changeStatus(bonus_id, index){
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
            axios.post('/bonus/edit',{bonus_id:bonus_id}).then((response) => {
              vueThis.$set(vueThis.bonuses,vueThis.index,response.data.data);
              swal(response.data.message, "success");
            }).catch((error)=>{
              // console.log(error);
              swal("Cancelled", error.response.data.message, "error");
            });
        });
      },



    }

  });

// work.$destroy();


