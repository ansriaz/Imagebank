@extends('layouts.app')

@section('head')

<link href="{{ asset('assets/css/fileinput.min.css')}}" media="all" rel="stylesheet" type="text/css" />
<!-- the main fileinput plugin file -->
<script src="{{ asset('assets/js/fileinput.min.js')}}"></script>

@endsection

@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-offset-1 col-md-10">
            <div class="panel panel-default">
                <div class="panel-heading">Dataset</div>
                <div class="panel-body">

                <form class="form-horizontal" action="/postDataset" id="form_dataset" method="POST" role="form" enctype="multipart/form-data">
                {{ csrf_field() }}

                    <script type="text/javascript">
                        // initialize with defaults
                        $("#dataset_zip").fileinput();

                        // with plugin options
                        $("#dataset_zip").fileinput({
                            uploadAsync: true,
                            showUpload:true,
                            minFileCount: 1,
                            maxFileCount: 2
                        });
                    </script>

                    <div class="row" style="margin:20px">
                        <div class="form-group">
                            <label class="control-label">Dataset: </label>
                            <input id="dataset_zip" name="dataset_zip" class="file file-loading" type="file" data-allowed-file-extensions='["zip"]'>
                        </div>

                        {{-- <div class="form-group">
                            <label class="control-label">CSV File: </label>
                            <input name="dataset_csv" class="file" type="file">
                        </div> --}}

                    </div>
                    <div class="row" style="margin-left:10px; margin-right:10px">
                        {{-- <div class="col-md-10"> --}}
                            <label style="font-size:11px;" class="text-justify">
                                {{-- <input type="checkbox" name="terms" id="terms"> --}}
                                By clicking "Upload" button, you electronically agree to our <a style="font-size:11px; text-align:left;" href="{{ url('/about') }}">Privacy Policy</a> (the "Terms"); you acknowledge receipt of our Terms, and you agree to receive notices and disclosures from us electronically, including any updates of these Terms.
                            </label>
                        {{-- </div> --}}
                        {{-- <div class="pull-right" style="margin-top:10px">
                            <button type="submit" id="submit" class="btn btn-primary"><i class="fa fa-btn fa-upload"></i>Upload</button>
                        </div> --}}
                    </div>
                </form>

                @if(Session::has('flash_message'))
                    <div class="alert alert-success">
                        {{ Session::get('flash_message') }}
                    </div>
                @endif

                </div>
                <br />
                <br />
            </div>
        </div>
        <br />
        <br />
    </div>
</div>

@endsection

@section('footer')

<script type="text/javascript">

</script>

@endsection
