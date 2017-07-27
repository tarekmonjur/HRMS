<!-- Start: Header -->
<header class="navbar navbar-fixed-top navbar-shadow">
    <div class="navbar-branding">
        <a class="navbar-brand" href="{{url("setup")}}">
            <b>IDDL</b> LTD.
        </a>
        {{-- <span id="toggle_sidemenu_l" class="ad ad-lines"></span> --}}
    </div>
    <ul class="nav navbar-nav navbar-left">
        {{-- <li class="dropdown menu-merge hidden-xs">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Setup
            <span class="caret caret-tp"></span>
          </a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="#">Action</a></li>
            <li><a href="#">Another action</a></li>
            <li><a href="#">Something else here</a></li>
            <li class="divider"></li>
            <li><a href="#">Separated link</a></li>
          </ul>
        </li> --}}
        <li class="hidden-xs">
            <a href="{{url('setup')}}" class="text-primary" data-toggle="tooltip" data-placement="top" title="Dashboard">
                <span class="glyphicon glyphicon-home"></span> Dashboard
            </a>
        </li>

        @if(Auth::user()->user_type == 1)
            <li class="hidden-xs">
                <a href="{{url('/config')}}" class="text-primary" data-toggle="tooltip" data-placement="top" title="Dashboard">
                    Add Company
                </a>
            </li>
            <li class="dropdown menu-merge hidden-xs">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Setup
                <span class="caret caret-tp"></span>
              </a>
              <ul class="dropdown-menu" role="menu">
                <li><a href="{{url('modules/index')}}">Modules</a></li>
                <li><a href="{{url('menus/index')}}">Menus</a></li>
                <li><a href="{{url('packages/index')}}">Packages</a></li>
              </ul>
            </li>
        @endif
        
    </ul>


    <ul class="nav navbar-nav navbar-right">
        <li class="menu-divider hidden-xs">
            <i class="fa fa-circle"></i>
        </li>
        <li class="dropdown menu-merge">
            <a href="#" class="dropdown-toggle fw600 p15" data-toggle="dropdown">
                <img src="{{asset('img/placeholder.png')}}" alt="avatar" class="mw30 br64">
                <span class="hidden-xs pl15"> {{Auth::user()->full_name}} </span>
                <span class="caret caret-tp hidden-xs"></span>
            </a>
            <ul class="dropdown-menu list-group dropdown-persist w250" role="menu">
                <li class="list-group-item">
                    <a href="#" class="animated animated-short fadeInUp">
                        <span class="fa fa-envelope"></span> Messages
                        <span class="label label-warning">2</span>
                    </a>
                </li>
                <li class="list-group-item">
                    <a href="#" class="animated animated-short fadeInUp">
                        <span class="fa fa-user"></span> Friends
                        <span class="label label-warning">6</span>
                    </a>
                </li>
                <li class="list-group-item">
                    <a href="#" class="animated animated-short fadeInUp">
                        <span class="fa fa-gear"></span> Account Settings </a>
                </li>
                <li class="dropdown-footer">
                    <a href="{{ url('/setup/logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                        <span class="fa fa-power-off pr5"></span> Logout
                    </a>
                    <form id="logout-form" action="{{ url('/setup/logout') }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
                    </form>
                </li>
            </ul>
        </li>
    </ul>
</header>
<!-- End: Header -->

