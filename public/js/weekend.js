new Vue({
	el: "#mainDiv",
	data:{
		msg: 'testing',
		weekend_status: '1',
		weekends: [],
        weekend_from_date: '',
        weekend_to_date: '',
		edit_weekends: [],
		edit_weekend_status: '',
        edit_weekend_from_date: '',
        edit_weekend_to_date: '',
		hdn_id: '',
        old_edit_weekend_from_date: '',
	},
	mounted(){

    	axios.get('/weekend/getAllData').then(response => this.weekends = response.data);
    },
	methods:{
		saveData(formId){
    		var formData = $('#'+formId).serialize();

            axios.post('/weekend/add', formData)
            .then((response) => { 
                
                swal({
	                title: response.data.title+"!",
	                text: response.data.message,
	                type: response.data.title,
	                showCancelButton: false,
	                confirmButtonColor: "#DD6B55",
	                confirmButtonText: "Done",
	                closeOnConfirm: false
	            },
	            function(){
	                location.href=location.href;
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
        deleteData(id, index){

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
                axios.get("/weekend/delete/"+id,{
                            
                })
                .then((response) => {
                    swal({
                        title: response.data.title+"!",
                        text: response.data.message,
                        type: response.data.title,
                        showCancelButton: false,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Done",
                        closeOnConfirm: false
                    },
                    function(){
                        location.href=location.href;
                    });
                })
                .catch(function (error) {
                    
                    swal('Error:','Edit function not working','error');
                });
            });
        },
    	// editData(id, index){

    	// 	axios.get("/weekend/edit/"+id,{
            
     //        })
     //        .then((response) => {

     //        	this.hdn_id =	response.data.hdn_id;
     //            this.edit_weekend_status = response.data.weekend_status;
     //            this.edit_weekend_from_date = response.data.weekend_from_date;
     //        	this.edit_weekend_to_date = response.data.weekend_to_date;
     //            this.old_edit_weekend_from_date = response.data.weekend_from_date;
                
     //            var weekendAry = response.data.weekend_name.split(',');

     //            if(weekendAry.length > 0){
		   //          jQuery.each(weekendAry, function(index, item) {
		   //              $('input[value='+item+']').prop("checked", true);
		   //          });
		   //      }else{
		   //          $('input:checkbox').removeAttr('checked');
		   //      }
     //        })
     //        .catch(function (error) {
                
     //            swal('Error:','Edit function not working','error');
     //        });
     //    },
     //    updateData: function(updateFormId){
            
     //        var formData = $('#'+updateFormId).serialize();

     //        axios.post('/weekend/edit', formData)
     //        .then(response => { 
               
     //            swal({
	    //             title: response.data.title+"!",
	    //             text: response.data.message,
	    //             type: response.data.title,
	    //             showCancelButton: false,
	    //             confirmButtonColor: "#DD6B55",
	    //             confirmButtonText: "Done",
	    //             closeOnConfirm: false
	    //         },
	    //         function(){
	    //             location.href=location.href;
	    //         });
     //        })
     //        .catch( (error) => {
     //            var errors = error.response.data;

     //            var errorsHtml = '<div class="alert alert-danger"><ul>';
     //            $.each( errors , function( key, value ) {
     //                errorsHtml += '<li>' + value[0] + '</li>';
     //            });
     //            errorsHtml += '</ul></di>';
     //            $( '#edit-form-errors' ).html( errorsHtml );
     //        });
     //    },
	}
});