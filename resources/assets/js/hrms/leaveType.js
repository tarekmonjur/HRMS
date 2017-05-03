new Vue({
  el: '#mainDiv',
  data: {
    msg: 'test type',
    leaveTypes:[],
    empTypes:[],
    //for edit
    hdn_id: '',
    type_name: '',
    duration: '',
    emp_type: [],
    type_details: '',
    carry_to_next_year: '',
    include_holiday: '',
    from_year: '',
    to_year: '',
    leave_type_status: '',
  },
  mounted(){

    axios.get('/leave/type/getAllData').then(response => this.leaveTypes = response.data);
    axios.get('/get-employee-type').then(response => this.empTypes = response.data);
  },
  methods:{
    uncheckAll(){
        //first unchecked all check box
        $('input[type=checkbox]').prop("checked", false);
    },
    returnEffectedEmpType(ary){

      var temp = new Array();
      var tempInt = new Array();
      var result = '';

      temp = ary.split(",");

      temp.forEach(function(element) {
          
          tempInt.push(parseInt(element));
      });

      this.empTypes.forEach(function(element) {
          
          if(tempInt.includes(element.id)){

              result += element.type_name+', ';
          }
      });

      return result;
    },
    saveData(formId){
        var formData = $('#'+formId).serialize();

        axios.post('/leave/type/add', formData)
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

      axios.get("/leave/type/edit/"+id,{
        
      })
      .then((response) => {

        this.hdn_id = response.data.hdn_id;
        this.type_name = response.data.leave_type_name;
        this.duration = response.data.leave_type_number_of_days;
        // response.data.leave_type_effective_for;
        var effectiveAry = response.data.leave_type_effective_for.split(',');

        //first unchecked all check box
        $('input[type=checkbox]').prop("checked", false);

        if(effectiveAry.length > 0){
          jQuery.each(effectiveAry, function(index, item) {
              $('input[value='+item+']').prop("checked", true);
          });
        }else{
          $('input:checkbox').removeAttr('checked');
        }

        this.type_details = response.data.leave_type_details;
        this.carry_to_next_year = response.data.leave_type_is_remain;
        this.include_holiday = response.data.leave_type_include_holiday;
        this.from_year = response.data.leave_type_active_from_year;
        this.to_year = response.data.leave_type_active_to_year;
        this.leave_type_status = response.data.leave_type_status;
      })
      .catch(function (error) {
          
          swal('Error:','Edit function not working','error');
      });
    },
    updateData: function(updateFormId){
            
        var formData = $('#'+updateFormId).serialize();

        axios.post('/leave/type/edit', formData)
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
})