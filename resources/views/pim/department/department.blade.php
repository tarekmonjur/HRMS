@extends('layouts.hrms')

@section('content')
    <!-- Begin: Content -->
    <section id="content" class="animated fadeIn">
        <div class="row">

            <div class="col-md-12">
                <?php 
                    $msgs = ['success','danger']; 
                    foreach($msgs as $msg){ if(Session::has($msg)){?>
                    <div class="alert alert-{{$msg}} alert-dismissible" role="alert" style="margin-top:10px">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>{{ucfirst($msg)}}!</strong> {{Session::get($msg)}}
                    </div>
                <?php } } ?>

                <div class="panel">
                    <div class="panel-heading">
                        <span class="panel-title">All departments</span>
                    </div>
                    <div class="panel-body">
                        @if($departments->count() > 0)
                        <table class="table table-hover" id="datatable">
                        <thead>
                            <tr class="success">
                                <th>sl</th>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $sl=1; ?>
                            @foreach($departments as $department)
                            <tr>
                                <td>{{ $sl++ }}</td>
                                <td>{{ $department->department_name }}</td>
                                <td>{{$department->status==1?"Active":"Inactive"}}</td>
                                <td>
                                    <a href="{{url("department/edit/$department->id")}}" title="">
                                        <button type="button" class="btn btn-sm btn-primary">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                    </a>
                                    <a onclick="return confirm('Want to delete?');" href="{{url("department/delete/$department->id")}}" title="">
                                        <button type="button" class="btn btn-sm btn-danger">
                                            <i class="fa fa-trash-o"></i>
                                        </button>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        </table>
                        @else
                            {{"No level available..."}}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End: Content -->       

@endsection

@section('script')
<script type="text/javascript">
    
    jQuery(document).ready(function() {

    // Init DataTables
    $('#datatable').dataTable({
      "aoColumnDefs": [{
        'bSortable': false,
        'aTargets': [-1]
      }],
      "oLanguage": {
        "oPaginate": {
          "sPrevious": "",
          "sNext": ""
        }
      },
      "iDisplayLength": 25,
      "aLengthMenu": [
        [25, 50, -1],
        [25, 50, "All"]
      ],
      "sDom": '<"dt-panelmenu clearfix"lfr>t<"dt-panelfooter clearfix"ip>',
      "oTableTools": {
        "sSwfPath": "vendor/plugins/datatables/extensions/TableTools/swf/copy_csv_xls_pdf.swf"
      }
    });

    $(".pagination").addClass(" pull-right");

  });


</script>
@endsection