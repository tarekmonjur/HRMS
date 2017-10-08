new Vue({
    el: "#mainDiv",
    data:{
        msg: "tesign",
        module_name: '',
        module_icon_class: '',
        module_details: '',
        module_status: 1,
        modules: [],
        hdn_id: '',
        edit_module_name: '',
        edit_module_icon_class: '',
        edit_module_details: '',
        edit_module_status: '',
    },
    mounted(){
        this.getModuleAllData();    
    },
    methods:{
        getModuleAllData(){
            axios.get('/modules/getModule').then(response => this.modules = response.data);
        },
        saveData(formId){
            var formData = $('#'+formId).serialize();

            axios.post('/modules/add', formData)
            .then((response) => {
                $('#create-form-errors').html('');
                document.getElementById("modal-close-btn").click();
                swal(response.data.title, 'Message: '+response.data.message);

                this.module_name= '',
                this.module_icon_class= '',
                this.module_details= '',
                this.module_status= 1,
                //load all data
                this.getModuleAllData();
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
        editData(id, index){
            this.indexId = index;
            this.hdn_id = id;
            this.edit_module_name   = this.modules[index].module_name;
            this.edit_module_icon_class   = this.modules[index].module_icon_class;
            this.edit_module_details = this.modules[index].module_details;
            this.edit_module_status = this.modules[index].module_status;
        },
        updateData: function(updateFormId){
            
            var formData = $('#'+updateFormId).serialize();

            axios.post('/modules/edit', formData)
            .then(response => { 
               
                $('#edit-form-errors').html('');
                document.getElementById("modal-edit-close-btn").click();
                
                this.modules[this.indexId].module_name = this.edit_module_name;
                this.modules[this.indexId].module_details = this.edit_module_details;
                this.modules[this.indexId].module_status = this.edit_module_status;

                swal(response.data.title, 'Message: '+response.data.message);
                // new PNotify({
                //     title: response.data.title+' Message',
                //     text: response.data.message,
                //     shadow: true,
                //     addclass: 'stack_top_right',
                //     type: response.data.title,
                //     width: '290px',
                //     delay: 1500
                // });
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
        deleteData: function(id, index){

            var delModule = this.modules;

            swal({
                title: "Are you sure?",
                text: "You will not be able to recover this information!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!",
                closeOnConfirm: false
            },
            function(){
                
                swal("Deleted!", "Your data has been deleted.", "success");

                axios.get("/modules/delete/"+id+"/"+index,{
            
                })
                .then((response) => {

                    // new PNotify({
                    //     title: response.data.title+' Message',
                    //     text: response.data.message,
                    //     shadow: true,
                    //     addclass: 'stack_top_right',
                    //     type: response.data.title,
                    //     width: '290px',
                    //     delay: 1500
                    // });
                    
                    delModule.splice(response.data.indexId, 1);
                    swal(response.data.title,response.data.message,response.data.title);
                })
                .catch(function (error) {
                    
                    swal('Error:','Delete function not working','error');
                });
            });
        }
    }
});