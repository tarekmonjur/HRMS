@extends('layouts.hrms')
@section('content')

@section('style')
    <link rel="stylesheet" href="{{asset('orgChart/dist/css/jquery.orgchart.css')}}">
    <link rel="stylesheet" href="{{asset('orgChart/dist/css/style.css')}}">
    <link rel="stylesheet" href="{{asset('orgChart/style.css')}}">
@endsection

<!-- Begin: Content -->
<section id="content" class="animated fadeIn">

<!-- Sister Concern -->
@if(count($sisterConcern)>0)
<div class="panel">
  <div class="panel-heading">
    <span class="panel-title">Sister Concern  {{session('status')}}</span>
  </div>
  <div class="panel-body panel-scroller scroller-dark scroller-sm scroller-overlay scroller-pn pn">
    <table class="table mbn tc-list-1 tc-text-muted-2 tc-fw600-2">
      <thead>
        <tr>
          <th class="w30">#</th>
          <th>Company Code</th>
          <th>Company Name</th>
          <th>Company Address</th>
          <th>Created Date</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        @forelse($sisterConcern as $sinfo)
        <tr>
          <td>{{$loop->iteration}}.</td>
          <td>{{$sinfo->company_code}}</td>
          <td>{{$sinfo->company_name}}</td>
          <td>{{$sinfo->company_address}}</td>
          <td>{{$sinfo->created_at}}</td>
          <td>
            <form action="{{url('/switch/account/'.$sinfo->database_name.'/'.$sinfo->id)}}" method="post">
            {{csrf_field()}}
            <input type="submit" name="submit" value="Switch Account" class="btn btn-sm btn-info btn-gradient">
          </form>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="4"><h2>Data not available</h2></td>
        </tr>
        @endforelse
      </tbody>
    </table>

  </div>
</div>
@endif


<!-- Mother Concern -->
@if(count($motherConcern)>0)
<div class="panel">
  <div class="panel-heading">
    <span class="panel-title">Mother Concern</span>
  </div>
  <div class="panel-body panel-scroller scroller-dark scroller-sm scroller-overlay scroller-pn pn">
    <table class="table mbn tc-list-1 tc-text-muted-2 tc-fw600-2">
      <thead>
        <tr>
          <th class="w30">#</th>
          <th>Company Code</th>
          <th>Company Name</th>
          <th>Company Address</th>
          <th>Created Date</th>
           <th>Action</th>
        </tr>
      </thead>
      <tbody>
        @forelse($motherConcern as $minfo)
        <tr>
          <td>{{$loop->iteration}}.</td>
          <td>{{$minfo->company_code}}</td>
          <td>{{$minfo->company_name}}</td>
          <td>{{$minfo->company_address}}</td>
          <td>{{$minfo->created_at}}</td>
          <td>
            <form action="{{url('/switch/account/'.$minfo->database_name.'/'.$minfo->id)}}" method="post">
            {{csrf_field()}}
            <input type="submit" name="submit" value="Switch Account" class="btn btn-sm btn-info btn-gradient">
          </form>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="4"><h2>Data not available</h2></td>
        </tr>
        @endforelse
      </tbody>
    </table>

  </div>
</div>
@endif

<!-- organogram -->
<div class="panel">
  <div class="panel-heading">
    <span class="panel-title">Organogram</span>
  </div>
  <div class="panel-body">
    <div id="chart-container"></div>
  </div>
</div>


</section>
<!-- End: Content -->



  <script type="text/javascript">

    var data = '<?php echo json_encode($organogram);?>';
    var datascource = JSON.parse(data);
    var config_id = {{Session('config_id')}}
    // console.log(datascource);

  </script>
  <script type="text/javascript" src="{{asset('orgChart/dist/js/jquery-3.1.0.min.js')}}"></script>

  <script type="text/javascript" src="https://cdn.rawgit.com/stefanpenner/es6-promise/master/dist/es6-promise.auto.min.js"></script>
  <script type="text/javascript" src="https://cdn.rawgit.com/niklasvh/html2canvas/master/dist/html2canvas.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.2/jspdf.debug.js"></script>

  <script type="text/javascript" src="{{asset('orgChart/dist/js/jquery.orgchart.js')}}"></script>
  <script type="text/javascript" src="{{asset('orgChart/scripts.js')}}"></script>



@endsection