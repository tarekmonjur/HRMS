new Vue({

	el: "#mainDiv",
	data:{
		menus: [],
		activeModules: [],
		activeMenus: [],
		menu_name: '',
		module_id: '',
		menu_parent_id: '',
		chk_parent: 0,
		menu_url: '',
		menu_section_name: '',
        menu_icon_class: '',
		menu_status: 1,
		hdn_id: '',
		edit_menu_name: '',
		edit_module_id: '',
		edit_menu_parent_id: '',
		edit_menu_url: '',
		edit_menu_section_name: '',
        edit_menu_icon_class: '',
		edit_menu_status: 1,
		menuIndex: null,
	},
	mounted(){
		this.getAllData();
		axios.get('/menus/get-Module').then(response => this.activeModules = response.data);
		axios.get('/menus/getActiveMenus').then(response => this.activeMenus = response.data);
	},
	methods:{
		getAllData: function(){
    		axios.get('/menus/getMenus').then(response => this.menus = response.data);
    	},
    	saveData: function(formId){

    		var formData = $('#'+formId).serialize();

            axios.post('/menus/add', formData)
            .then((response) => { 

                this.getAllData();;  //call method
                axios.get('/menus/getActiveMenus').then(response => this.activeMenus = response.data);

                $('#create-form-errors').html('');
                document.getElementById("modal-close-btn").click();

                swal(response.data.title, 'Message: '+response.data.message, response.data.title);

                this.menu_name = '',
				this.module_id = '',
				this.menu_parent_id = '',
				this.chk_parent = '',
				this.menu_url = '',
				this.menu_section_name = '',
				this.menu_status = ''     

				document.getElementById("chk_parent").checked = false;    
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
        editData: function(id, index){

        	this.menuIndex = index;
            this.hdn_id = id;
            this.edit_menu_name = this.menus[index].menu_name;
	        this.edit_menu_parent_id = this.menus[index].menu_parent_id;
	        this.edit_module_id = this.menus[index].module_id;
	        this.edit_menu_url = this.menus[index].menu_url;
            this.edit_menu_section_name = this.menus[index].menu_section_name;
	        this.edit_menu_icon_class = this.menus[index].menu_icon_class;
	        this.edit_menu_status= this.menus[index].menu_status;
	        this.chk_parent = this.menus[index].menu_parent_id > 0 ? 1 : 0;
        },
        updateData: function(updateFormId){
            
        	var formData = $('#'+updateFormId).serialize();

            axios.post('/menus/edit', formData)
            .then(response => { 
               
               	$('#edit-form-errors').html('');
                document.getElementById("modal-edit-close-btn").click();
                
                this.getAllData();;  //call method
                axios.get('/menus/getActiveMenus').then(response => this.activeMenus = response.data);

                swal(response.data.title, 'Message: '+response.data.message, response.data.title);
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

            var delMenus = this.menus;

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
                axios.get("/menus/delete/"+id+"/"+index,{
            
                })
                .then((response) => {
                    
                    delMenus.splice(response.data.indexId, 1);
                })
                .catch(function (error) {
                   
                    swal('Error:','Delete function not working','error');
                });
            });


        }
	}
});