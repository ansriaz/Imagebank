@extends('layouts.app')

@section('title')
  User Dashboard
@endsection

@section('content')
<div class="container" style="min-width: 1200px;">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            @if(Session::has('success_message'))
            <div class="alert alert-success">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                {{ Session::get('success_message') }}
            </div>
            @endif
            <div class="panel panel-default">
                <div class="panel-heading">My Account</div>

                <div class="panel-body">
                    <section id="menu" class="col-md-3 span6">
                        <div class="list-group" style="padding-right:20px; border-right: 1px solid #ccc; border-top: 0 none;">
                            <a href="#" class="list-group-item active" div_rel="profile_div">Profile</a>
                            <a href="#" class="list-group-item" div_rel="change_password_div">Change Password</a>
                            <!-- <a href="#" class="list-group-item">Access Permission Status</a> -->
                            <div href="#" class="list-group-item" data-parent="#nav-menu" data-toggle="collapse" data-target="#submenu_dataset">Datasets <span class="caret"></span></div>
                            <div class="submenu-body collapse" id="submenu_dataset">
                                <div class="list-group">
                                    <a href="#" class="list-group-item" div_rel="datasets_div">Images</a>
                                    <a href="#" class="list-group-item" div_rel="video_div">Videos</a>
                                </div>
                            </div>
                        </div>
                    </section>
                    <section id="main" class="col-md-9 span6">
                        <div id="profile_div">
                            @if (Auth::user())
                            <form lass="form-horizontal" role="form" method="POST" action="{{ url('/updateinfo') }}">
                                {!! csrf_field() !!}

                                <div class="row form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                    <label class="col-md-3 control-label">Name</label>

                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="name" value="{{ Auth::user()->name }}">

                                        @if ($errors->has('name'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('name') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="row form-group">
                                    <label class="col-md-3 control-label">E-Mail</label>

                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="email" value="{{ Auth::user()->email }}">
                                    </div>
                                </div>

                                <div class="row form-group">
                                    <label class="col-md-3 control-label">Organization</label>

                                    <div class="col-md-6">
                                        <input type="organization" class="form-control" name="organization" value="{{ Auth::user()->organization }}">
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-7 col-md-offset-7">
                                        <button type="submit" class="btn btn-primary">Update
                                        </button>
                                    </div>
                                </div>
                            </form>
                            @endif
                        </div>
                        <div id="change_password_div" style="display: none;">
                            @if (Auth::user())
                            <form lass="form-horizontal" role="form" method="POST" action="{{ url('/updatepassword') }}">
                                {!! csrf_field() !!}

                                <div class="row form-group">
                                    <label class="col-md-3 control-label">Old Password</label>

                                    <div class="col-md-6">
                                        <input type="password" class="form-control" name="old_password">
                                    </div>
                                </div>

                                <div class="row form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                    <label class="col-md-3 control-label">New Password</label>

                                    <div class="col-md-6">
                                        <input type="password" class="form-control" name="new_password">

                                        @if ($errors->has('password'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('password') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="row form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                                    <label class="col-md-3 control-label">Confirm Password</label>

                                    <div class="col-md-6">
                                        <input type="password" class="form-control" name="password_confirmation">

                                        @if ($errors->has('password_confirmation'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('password_confirmation') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-6 col-md-offset-6">
                                        <button type="submit" class="btn btn-primary">Update Password</button>
                                    </div>
                                </div>
                            </form>
                            @endif
                        </div>
                        <div class="row" id="datasets_div" style="display: none;">
                            <div class="datasets">
                                <!-- <div>
                                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/download') }}">
                                        {!! csrf_field() !!}
                                        <div id="responsedata"></div>
                                        <div class="row" style="padding-right:5px; padding-top:10px; float:right;">
                                                {{-- {{ Form::submit('Download', array('class' => 'btn')) }} --}}
                                        </div>
                                    </form>
                                </div> -->
                            </div>
                        </div>
                        <div class="row" id="video_div" style="display:none;">
                            <div class="dataset_vids">
                            </div>
                        </div>
                        <div id="permission_div" style="display: none;"></div>
                    </section>
                </div>
            </div>
        </div>
    </div>

    @if(Session::has('flash_message'))
    <div class="alert alert-success">
        {{ Session::get('flash_message') }}
    </div>
    @endif
</div>
@endsection

@section('footer')
    <script>
        function populateData(type) {
            $.ajax({
                type: "get",
                url: '/getDatasets',
                data: {type: type},
                timeout: 50000,
                // dataType : 'json',
                success: function(response) {
                    // console.log(response);
                    if(type == 'images')
                        $(".datasets").html(response);
                    else
                        $(".dataset_vids").html(response);
                },
                error: function(xhr, textStatus, errorThrown){
                    alert(errorThrown);
                }
            })
        };
        $('.list-group-item').on('click', function(){
           var target = $(this).attr('div_rel');
           $("#"+target).show().siblings("div").hide();
           if(target == 'datasets_div' && !($('#datasets').css('display') == 'none')) {
                populateData('images');
           }
           if(target == 'video_div' && !($('#dataset_vids').css('display') == 'none')) {
                populateData('videos');
           }
        });
        $(".list-group a").click(function(e) {
           $(".list-group a").removeClass("active");
           $(e.target).addClass("active");
        });

    </script>
@endsection
