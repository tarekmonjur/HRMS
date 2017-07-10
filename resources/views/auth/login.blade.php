@extends('layouts.auth')

@section('content')


    <div id="title_section">
        <img class="logo-img" src="{{asset('img/logo.png')}}" alt="Logo Icon">
        <div class="copy">
            <h1>[ LAMBA</h1>
            <p>Welcome to LAMBA. The Human Resource Management System from IDDL.</p>
        </div>
        <div style="clear:both"></div>
    </div>

    <div id="login_section">
        <div class="content">
            <h2>Sign In</h2>
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif
            <p>Sign in below:</p>
            <div style="clear:both"></div>
            <form id="login" class="form-horizontal" role="form" method="POST" action="{{ url('/login') }}">
                {{ csrf_field() }}

                <div>
                <input type="text" class="form-control" name="email" placeholder="email address" value="{{old('email')}}">
                @if ($errors->has('email'))
                    <span  class="error-login">
                        {{ $errors->first('email') }}
                    </span>
                @endif
                </div>

                <div>
                <input type="password" class="form-control" name="password" placeholder="password">
                @if ($errors->has('password'))
                    <span  class="error-login">
                        {{ $errors->first('password') }}
                    </span>
                @endif
                </div>

                <div id="remember_me">
                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : ''}}>
                <span>Remember Me</span>
                <a href="{{ url('/password/reset') }}">
                    Forgot Your Password?
                </a>
                </div>

                <button class="btn btn-primary btn-login" id="voyager-login-btn">
                    <span class="login_text"><i class="voyager-lock"></i> Login</span>
                    <span class="login_loader">
                        <i class="voyager-lock"></i> Logging in...
                    </span>
                </button>
                <img class="btn-loading" src="{{asset('img/logo_icon.png')}}" alt="Voyager Loader">

            </form>

            @if ($errors->has('unauthenticated'))
                <span  class="error-login">
                    {{ $errors->first('unauthenticated') }}
                </span>
            @endif

        </div>
    </div>

@endsection
