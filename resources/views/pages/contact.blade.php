@extends('layouts.app')

@section('title')
  Contact Us
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="text-center">Team</div>
                </div>

                <div class="panel-body">
                    {{-- <div class="text-center"> --}}
                        <div class="text-center">
                            <img src="{{URL::asset('/assets/images/ansriaz.png')}}" alt="Ans Riaz" height="128px" width="128px">
                            <br/>
                            <a href="https://about.me/rizh">Ans Riaz</a><br/>
                            Software Engineer / Developer<br/>
                            Email: <a href="mailto:ansriazch@gmail.com">ansriazch@gmail.com</a>
                        </div>
                    {{-- </div> --}}
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="text-center">
            </div>
        </div>
    </div>
</div>
@endsection
