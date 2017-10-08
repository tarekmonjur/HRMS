
var work = new Vue({
    el: '#bonus_type',
    data:{
      index:'',
      dataTable:true,
      bonusTypes:[],
      bonusType:[],
      errors:[]
    },

    mounted(){
      this.getBonusType();
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


      loadinShow(id){
        $(id).LoadingOverlay("show",{color:"rgba(0, 0, 0, 0)"});
      },


      loadinHide(id){
        $(id).LoadingOverlay("hide",{color:"rgba(0, 0, 0, 0)"});
      },


      myTimePicker(){
          $('.myTimePicker').datetimepicker({
              pickDate: false,
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


      getBonusType(){
        this.loadinShow('#datatableCall');

        axios.get('/bonustype/index').then((response) => {
          this.bonusTypes = response.data;
          this.dataTableGenerate();
          this.loadinHide('#datatableCall');
        }).catch((error)=>{
          alert('please reload page.');
        });
        this.loadinHide('#datatableCall');
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


      addBonusType(e){
        var formData = new FormData(e.target);
        this.loadinShow('#bonus_type_modal');

        axios.post('/bonustype/add',formData).then((response) => {
          this.bonusTypes.unshift(response.data.data);
          this.dataTableGenerate();
          jQuery(".mfp-close").trigger("click");
          this.showMessage(response.data);
          this.loadinHide('#bonus_type_modal');

        }).catch((error)=>{
          this.loadinHide('#bonus_type_modal');

          if(error.response.status == 500 || error.response.data.status == 'danger'){
              var error = error.response.data;
              this.showMessage(error);
          }else if(error.response.status == 422){
              this.errors = error.response.data;
          }
        });
      },


      editBonusType(id, index, model_id){
        this.loadinShow(model_id);
        this.index = index;

        axios.get('/bonustype/edit/'+id).then((response) => {
          this.loadinHide(model_id);
          this.bonusType = response.data.data;
          this.modal_open(model_id);

        }).catch((error)=>{
          this.loadinHide(model_id);
          if(error.response.status == 500 || error.response.data.status == 'danger'){
              var error = error.response.data;
              this.showMessage(error);
              this.getBonusType();
          }else if(error.response.status == 422){
              this.errors = error.response.data;
          }
        });
      },


      updateBonusType(e){
        var formData = new FormData(e.target);
        this.loadinShow('#bonus_type_modal');

        axios.post('/bonustype/edit',formData).then((response) => {
          this.$set(this.bonusTypes, this.index, response.data.data);
          jQuery(".mfp-close").trigger("click");
          this.showMessage(response.data);
          this.loadinHide('#bonus_type_modal');

        }).catch((error)=>{
          this.loadinHide('#bonus_type_modal');
          if(error.response.status == 500 || error.response.data.status == 'danger'){
              var error = error.response.data;
              this.showMessage(error);
          }else if(error.response.status == 422){
              this.errors = error.response.data;
          }
        });
      },


      deleteBonusType(id,index){
       var vueThis = this;
       vueThis.index = index;

       swal({
          title: "Are you sure?",
          text: "You will not be able to recover this imaginary data!",
          // type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#DD6B55",
          confirmButtonText: "Yes, delete it!",
          closeOnConfirm: false
        },
        function(){ 
            axios.delete('/bonustype/delete/'+id).then((response) => {
              vueThis.bonusTypes.splice(vueThis.index,1);
              vueThis.dataTableGenerate();
              swal("Deleted!", response.data.message, "success");
            }).catch((error)=>{
              swal("Cancelled", error.response.data.message, "error");
            });
        });

      },


      changeStatus(id,index,status){
        this.loadinShow('#datatableCall');
        this.index = index;
        var status2;

        if(status == 1){
          status2 = 0;
        }else{
          status2 = 1;
        }
        
        axios.post('/bonustype/edit',{'id':id,'bonus_type_status':status2}).then((response) => {
          this.$set(this.bonusTypes, this.index, response.data.data);
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

