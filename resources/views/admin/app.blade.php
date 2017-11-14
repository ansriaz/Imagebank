<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title')</title>

    <!-- Fonts -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css" rel='stylesheet' type='text/css'>
    <link href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700" rel='stylesheet' type='text/css'>

    <!-- Styles -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
    {{-- <link href="{{ elixir('/assets/css/style.css') }}" rel="stylesheet"> --}}
    {{-- <link href="{{ elixir('css/app.css') }}" rel="stylesheet"> --}}

    <!-- JavaScripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>

    <style>
        body {
            font-family: 'Lato';
        }

        .fa-btn {
            margin-right: 6px;
        }

        html, body {
            height: 100%;
        }
        .wrapper {
            min-height: 100%;
            height: auto;
            height: auto !important;
            margin: 0 auto -30px;
            padding-bottom: 30px;
        }
    </style>

    @yield('head')

</head>
<body id="app-layout">
    <div class="wrapper">
        <nav class="navbar navbar-default navbar-static-top">
            <div class="container">
                <div class="navbar-header">

                    <!-- Collapsed Hamburger -->
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                        <span class="sr-only">Toggle Navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                    <!-- Branding Image -->
                    <a class="navbar-brand" href="{{ url('/') }}">
                        Image Bank
                    </a>
                </div>

                <div class="collapse navbar-collapse" id="app-navbar-collapse">
                    <!-- Left Side Of Navbar -->
                    <ul class="nav navbar-nav">
                        <li><a href="{{ url('/admin/home') }}">Home</a></li>
                        <li><a href="{{ url('/about') }}">About</a></li>
                    </ul>

                    <div class="col-sm-6 col-md-6">  
                    <!-- style="text-align:center;" -->
                        <form action="/search" method="get" class="navbar-form" role="search">
                            <div class="input-group" style="width : 100%">
                                <input type="text" class="form-control" placeholder="Search by class label or tag" name="q" id="search-term">
                                <div class="input-group-btn">
                                    <button class="btn btn-default" type="submit"><i class="glyphicon glyphicon-search"></i></button>
                                    <a type="button" class="btn btn-default" href="{{ url('/advanceSearch') }}">Advance</a>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Right Side Of Navbar -->
                    <ul class="nav navbar-nav navbar-right">
                        <!-- Authentication Links -->
                        {{-- <li><a href="{{ url('/upload') }}">Upload</a></li> --}}
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                Upload <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                <li><a href="{{ url('/upload') }}"><i class="fa fa-btn glyphicon glyphicon-picture"></i>Images</a></li>
                                <li><a href="{{ url('/video/upload') }}"><i class="fa fa-btn glyphicon glyphicon-facetime-video"></i>Video</a></li>
                            </ul>
                        </li>
                        {{-- @if (Auth::guest()) --}}
                            {{-- <li><a href="{{ url('/login') }}">Login</a></li>
                            <li><a href="{{ url('/register') }}">Register</a></li> --}}
                        {{-- @else --}}
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu" role="menu">
                                    {{-- <li><a href="{{ url('/dashboard') }}"><i class="fa fa-btn fa-dashboard"></i>Dashboard</a></li> --}}
                                    {{-- <li><a href="{{ url('/matlab') }}"><i class="fa fa-btn glyphicon glyphicon-console"></i>Matlab</a></li> --}}
                                    <li><a href="{{ url('/logout') }}"><i class="fa fa-btn fa-sign-out"></i>Logout</a></li>
                                </ul>
                            </li>
                        {{-- @endif --}}
                    </ul>
                </div>
            </div>
        </nav>

        @yield('content')
    </div>

    <footer class="footer">
        <div class="container" style="height: 20px; border-top: 1px solid black; font-size: 13px; color: #F5F5F5;">
            <!-- <hr style="width: 100%; color: black; height: 1px; background: black" /> -->
            <div id="footer-menu">
                <ul class="list-unstyled list-inline text-center">
                    <li style="float: left;"><a href="{{ url('https://about.me/rizh') }}">About Me</a></li>
                    <li style="float: center;"> <label style="color:black;">Copyright © 2016 | <a href="{{ url('http://www.unitn.it') }}">University of Trento</a></label></li>
                    <li style="float: right;"><a href="{{ url('/contact') }}">Contact</a></li>
                </ul>
            </div>
        </div>
    </footer>

</body>

@yield('footer')

</html>
