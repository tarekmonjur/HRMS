var work = new Vue({
    el: '#work_shift',
    data:{
      // index:null,
      workshifts:[],
      workshift:[],
      errors:[]
    },

    mounted(){
      this.getWorkShift();
    },

    methods:{

      dataTableCall(){
        $('#datatableCall').dataTable({
          "paging":   true,
          "searching": true,
          "info": true,
          "sDom": '<"dt-panelmenu clearfix"lfr>t<"dt-panelfooter clearfix"ip>',
        }); 
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


      getWorkShift(){
        this.loadinShow('#datatableCall');

        axios.get('/workshift/index').then((response) => {
          this.workshifts = response.data;
          this.loadinHide('#datatableCall');
        }).catch((error)=>{
          alert('please reload page.');
        });
        this.loadinHide('#datatableCall');
      },


      addWorkShift(e){
        var formData = new FormData(e.target);
        // var form_id = e.target.id;
        this.loadinShow('#workshift_modal');

        axios.post('/workshift/add',formData).then((response) => {
          // this.workshifts.push(response.data.data);
          this.getWorkShift();
          jQuery(".mfp-close").trigger("click");
          this.showMessage(response.data);
          this.loadinHide('#workshift_modal');

        }).catch((error)=>{
          this.loadinHide('#workshift_modal');

          if(error.response.status == 500 || error.response.data.status == 'danger'){
              var error = error.response.data;
              this.showMessage(error);
          }else if(error.response.status == 422){
              this.errors = error.response.data;
          }
        });
      },


      editWorkShift(id,model_id){
        this.loadinShow(model_id);
        // this.index = index;

        axios.get('/workshift/edit/'+id).then((response) => {
          this.loadinHide(model_id);
          this.workshift = response.data.data;
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


      updateWorkShift(e){
        var formData = new FormData(e.target);
        // var form_id = e.target.id;
        this.loadinShow('#workshift_modal');

        axios.post('/workshift/edit',formData).then((response) => {
          // this.$set(this.workshifts,this.index,this.workshift);
          this.getWorkShift();
          jQuery(".mfp-close").trigger("click");
          this.showMessage(response.data);
          this.loadinHide('#workshift_modal');

        }).catch((error)=>{
          this.loadinHide('#workshift_modal');
          if(error.response.status == 500 || error.response.data.status == 'danger'){
              var error = error.response.data;
              this.showMessage(error);
          }else if(error.response.status == 422){
              this.errors = error.response.data;
          }
        });
      },


      deleteWorkShift(id,index_id){
       var vueThis = this;

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
            axios.delete('/workshift/delete/'+id).then((response) => {
              vueThis.workshifts.splice(index_id,1);
              swal("Deleted!", response.data.message, "success");

            }).catch((error)=>{

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
        
        axios.post('/workshift/edit',{'id':id,'work_shift_status':status2}).then((response) => {
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



    }

  });
