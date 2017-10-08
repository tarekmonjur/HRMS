new Vue({
	el: "#mainDiv",
	data:{
		msg: 'testing',
        holidays: [],
        holiday_status: 1,
        holiday_name: '',
        from_date: '',
        to_date: '',
        total_days: 0,
        hdn_id: '',
        edit_holiday_status: '',
        edit_holiday_name: '',
        edit_from_date: '',
        edit_to_date: '',
        edit_holiday_description: '',
	},
	mounted(){

    	axios.get('/holiday/getAllData').then(response => this.holidays = response.data);
    },
	methods:{
		calculateTotal(fromDate, toDate){
			if(fromDate.length > 0 && toDate.length > 0){
                var oneDay = 24*60*60*1000; // hours*minutes*seconds*milliseconds
                var firstDate = new Date(fromDate);
                var secondDate = new Date(toDate);

                if(secondDate.getTime() >= firstDate.getTime() ){
                    var diffDays = Math.round(Math.abs((firstDate.getTime() - secondDate.getTime())/(oneDay))); 

                    var result = parseInt(diffDays+1); 
                }else{
                    var result = "Invalid";
                }

                return result;
            }
		},
        saveData(formId){
            var formData = $('#'+formId).serialize();

            axios.post('/holiday/add', formData)
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
        editData(id, index){

    		axios.get("/holiday/edit/"+id,{
            
            })
            .then((response) => {

            	this.hdn_id =	response.data.hdn_id;
            	this.edit_holiday_name = response.data.holiday_name;
		        this.edit_from_date = response.data.holiday_from; 
		        this.edit_to_date = response.data.holiday_to; 
		        this.edit_holiday_description = response.data.holiday_details;
		        this.edit_holiday_status = response.data.holiday_status;
            })
            .catch(function (error) {
                
                swal('Error:','Edit function not working','error');
            });
        },
        updateData: function(updateFormId){
            
            var formData = $('#'+updateFormId).serialize();

            axios.post('/holiday/edit', formData)
            .then(response => { 
               
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
	}
});

