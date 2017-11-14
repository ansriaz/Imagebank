@extends('layouts.app')

@section('title')
  Image Bank
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                {{-- <div class="panel-heading">Welcome</div> --}}

                <div class="panel-body">
                    <h3> Sorry </h3>
                    <p class="body text-justify">Your request has not been reviewed or confirmed yet. </p>
                    <p class="body text-justify">Kindly wait for some time. You will get confirmation via email as soon as your request is reviewed. </p>

                    <div>
                        <a href="{{ url('/copyright') }}">Copyright Infirngement</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection