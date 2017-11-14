@extends('layouts.app')

@section('title')
  Thankyou
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                {{-- <div class="panel-heading">Welcome</div> --}}

                <div class="panel-body">
                    <h4> Thank you </h4>
                    <p class="body text-justify">Your archive is in process. We will send you download link via email to your registerd email id as soon as your archive will be ready. </p>

                    <div>
                        <a href="{{ url('/copyright') }}">Copyright Infirngement</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection