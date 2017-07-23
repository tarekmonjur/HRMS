import Vue from 'vue';
import VeeValidate from 'vee-validate';

Vue.use(VeeValidate);

new Vue({
	el: "#unitDiv",
    data: {
        message: 'Hello Vue!',
        unit_name: '',
        unit_parent_id: '',
        unit_department_id: '',
        unit_effective_date: '',
        unit_status: 1,
        unit_details: '',
        hdn_id: '',
        edit_unit_name: '',
        edit_unit_parent_id: '',
        edit_unit_department_id: '',
        edit_unit_status: 1,
        edit_unit_details: '',
        edit_unit_effective_date: '',
        departments: [],
        units: [],
        activeUnits: [],
        chk_parent: 0,
        unitIndex: null,
    },
    mounted(){
        //alert('tesss');
    	this.getAllUnit();  //call method
    	axios.get('/get-departments').then(response => this.departments = response.data);
    	axios.get('/get-units').then(response => this.activeUnits = response.data);
    },
    methods:{
    	getAllUnit: function(){

    		axios.get('/unit/getUnits').then(response => this.units = response.data);
    	},
    	saveUnit: function(formId){

    		var formData = $('#'+formId).serialize();

            axios.post('/unit/add', formData)
            .then((response) => { 

                this.getAllUnit();  //call method
                axios.get('/get-units').then(response => this.activeUnits = response.data);

                $('#create-form-errors').html('');
                location.href=location.href;
                document.getElementById("modal-close-btn").click();

                new PNotify({
                    title: response.data.title+' Message',
                    text: response.data.message,
                    shadow: true,
                    addclass: 'stack_top_right',
                    type: response.data.title,
                    width: '290px',
                    delay: 1500
                });
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
        editUnit: function(id, index){

        	this.unitIndex = index;
            this.hdn_id = id;
            this.edit_unit_name = this.units[index].unit_name;
	        this.edit_unit_parent_id = this.units[index].unit_parent_id;
	        this.edit_unit_department_id = this.units[index].unit_departments_id;
	        this.edit_unit_status= this.units[index].unit_status;
            this.edit_unit_details = this.units[index].unit_details;
	        this.edit_unit_effective_date = this.units[index].unit_effective_date;
	        this.chk_parent = this.units[index].unit_parent_id > 0 ? 1 : 0;
        },
        updateUnit: function(updateFormId){
            
        	var formData = $('#'+updateFormId).serialize();

            axios.post('/unit/edit', formData)
            .then(response => { 
               
               	$('#edit-form-errors').html('');
                document.getElementById("modal-edit-close-btn").click();
                
                this.getAllUnit();  //call method

                new PNotify({
                    title: response.data.title+' Message',
                    text: response.data.message,
                    shadow: true,
                    addclass: 'stack_top_right',
                    type: response.data.title,
                    width: '290px',
                    delay: 1500
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
        deleteUnit: function(id, index){

            var delUnits = this.units;

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
                
                swal("Deleted!", "Your imaginary file has been deleted.", "success");
                axios.get("/unit/delete/"+id+"/"+index,{
            
                })
                .then((response) => {
                    
                    delUnits.splice(response.data.indexId, 1);
                })
                .catch(function (error) {
                   
                    swal('Error:','Delete function not working','error');
                });
            });


        }
    }
});