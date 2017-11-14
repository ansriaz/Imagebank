@extends('layouts.app')

@section('head')
  {{-- {!! HTML::style('/packages/dropzone/dropzone.css') !!} --}}
  <link href="{{ asset('assets/css/fileinput.min.css')}}" media="all" rel="stylesheet" type="text/css" />
  <script src="{{ asset('assets/js/fileinput.min.js')}}"></script>
@endsection

@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-offset-1 col-md-10">
            <div class="panel panel-default">
                <div class="panel-heading">Upload Video</div>
                <div class="panel-body">
                    @if(Session::has('error_video'))
                    <div class="alert alert-danger">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        {{ Session::get('error_video') }}
                    </div>
                    @endif
                    {{-- <form action="/video/upload" id="myDropzone" class="dropzone" enctype="multipart/form-data"> --}}
                    <form action="/video/upload" id="form_dataset" method="POST" role="form" enctype="multipart/form-data">
                    {{csrf_field()}}
                        {{-- <div class="row">
                            <div class="dz-message"></div>

                            <div class="fallback">
                                <input name="file" type="file" multiple />
                            </div>

                            <div class="dropzone-previews" id="dropzonePreview"></div>

                            <h5 style="text-align: center;color:#428bca;">Drop videos in this area  <span class="glyphicon glyphicon-hand-down"></span></h5>
                            <h6 style="text-align: center;color:#428bca;">Maximum file size 20MB</h6>
                        </div> --}}
                        <div class="row" style="margin:20px">
                            <div class="form-group">
                                <label class="control-label">Video</label>
                                <input id="videoInput" name="video" type="file">
                                <h6>Maximum file size 20MB</h6>
                            </div>
                        </div>
                        <div class="row" id="fileExceedsLimit" style="display:none;">
                            <div class="col-md-12">
                                <hr/>
                                <div class="form-group">
                                    <label class="control-label">External source (Youtube) link</label>
                                    <input class="form-control" type="text" name="videolink" id="videolink" placeholder="Copy and paste link here">
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Name / Title of video</label>
                                    <input class="form-control" type="text" name="videotitle" id="videotitle" placeholder="Name / Title of video">
                                </div>
                            </div>
                        </div>
                        <hr/>
                        <div class="row" style="margin-top:5px;">
                            <div class="col-md-8">
                                <div class="col-md-12 form-group">
                                    <label class="control-label">Set new name for Dataset or select from list</label>
                                    <input class="form-control" type="text" name="datasetname" id="datasetname" placeholder="Write new name for dataset">
                                </div>
                                <div>
                                    <p align="center" class="">OR</p>
                                </div>
                                <div class="col-md-12 form-group">
                                    <div class="col-md-6 form-group">
                                        <select class="form-control" name="dataset" id="selDataset">
                                            <option id="select">Select Dataset</option>
                                            @foreach ($responseData['datasets'] as $value)
                                                <option id="{{ $value['title'] }}">{{ $value['title'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <select class="form-control" name="subdataset" id="selClasses">
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12 form-group">
                                        <label class="control-label">Class Labels</label>
                                        <input class="form-control" type="text" id="classlabels" name="classlabels" placeholder="Add comma separated Class labels">
                                </div>
                                <div class="col-md-12 form-group">
                                        <label class="control-label">Tags</label>
                                        <input class="form-control" type="text" id="tags" name="tags" placeholder="Add comma separated tags">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="checkbox">
                                    <label style="font-size:11px;" class="control-label text-justify">
                                        <input type="checkbox" name="terms" id="terms">
                                        By clicking "Upload" button, you electronically agree to our <a style="font-size:11px; text-align:left;" href="{{ url('/terms') }}">Privacy Policy</a> (the "Terms"); you acknowledge receipt of our Terms, and you agree to receive notices and disclosures from us electronically, including any updates of these Terms.
                                    </label>
                                </div>
                                <div class="pull-left bg-danger" id="error-terms" style="display: none; padding: 10px; margin-left: 10px; margin-bottom: 5px">
                                    </span><label style="font-size:11px;">Kindly accept Privacy Policy (Terms and Conditions).</label>
                                </div>
                                <div class="pull-right">
                                    <button id="upload" class="btn btn-primary" disabled><i class="fa fa-btn fa-upload"></i>Upload</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Dropzone Preview Template -->
    {{-- <div id="preview-template" style="display: none;">

        <div class="dz-preview dz-file-preview">
            <div class="dz-image"><img data-dz-thumbnail=""></div>

            <div class="dz-details">
                <div class="dz-size"><span data-dz-size=""></span></div>
                <div class="dz-filename"><span data-dz-name=""></span></div>
            </div>
            <div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress=""></span></div>
            <div class="dz-error-message"><span data-dz-errormessage=""></span></div>

            <div class="dz-success-mark">
                <svg width="54px" height="54px" viewBox="0 0 54 54" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:sketch="http://www.bohemiancoding.com/sketch/ns">
                    <!-- Generator: Sketch 3.2.1 (9971) - http://www.bohemiancoding.com/sketch -->
                    <title>Check</title>
                    <desc>Created with Sketch.</desc>
                    <defs></defs>
                    <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd" sketch:type="MSPage">
                        <path d="M23.5,31.8431458 L17.5852419,25.9283877 C16.0248253,24.3679711 13.4910294,24.366835 11.9289322,25.9289322 C10.3700136,27.4878508 10.3665912,30.0234455 11.9283877,31.5852419 L20.4147581,40.0716123 C20.5133999,40.1702541 20.6159315,40.2626649 20.7218615,40.3488435 C22.2835669,41.8725651 24.794234,41.8626202 26.3461564,40.3106978 L43.3106978,23.3461564 C44.8771021,21.7797521 44.8758057,19.2483887 43.3137085,17.6862915 C41.7547899,16.1273729 39.2176035,16.1255422 37.6538436,17.6893022 L23.5,31.8431458 Z M27,53 C41.3594035,53 53,41.3594035 53,27 C53,12.6405965 41.3594035,1 27,1 C12.6405965,1 1,12.6405965 1,27 C1,41.3594035 12.6405965,53 27,53 Z" id="Oval-2" stroke-opacity="0.198794158" stroke="#747474" fill-opacity="0.816519475" fill="#FFFFFF" sketch:type="MSShapeGroup"></path>
                    </g>
                </svg>
            </div>

            <div class="dz-error-mark">
                <svg width="54px" height="54px" viewBox="0 0 54 54" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:sketch="http://www.bohemiancoding.com/sketch/ns">
                    <!-- Generator: Sketch 3.2.1 (9971) - http://www.bohemiancoding.com/sketch -->
                    <title>error</title>
                    <desc>Created with Sketch.</desc>
                    <defs></defs>
                    <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd" sketch:type="MSPage">
                        <g id="Check-+-Oval-2" sketch:type="MSLayerGroup" stroke="#747474" stroke-opacity="0.198794158" fill="#FFFFFF" fill-opacity="0.816519475">
                            <path d="M32.6568542,29 L38.3106978,23.3461564 C39.8771021,21.7797521 39.8758057,19.2483887 38.3137085,17.6862915 C36.7547899,16.1273729 34.2176035,16.1255422 32.6538436,17.6893022 L27,23.3431458 L21.3461564,17.6893022 C19.7823965,16.1255422 17.2452101,16.1273729 15.6862915,17.6862915 C14.1241943,19.2483887 14.1228979,21.7797521 15.6893022,23.3461564 L21.3431458,29 L15.6893022,34.6538436 C14.1228979,36.2202479 14.1241943,38.7516113 15.6862915,40.3137085 C17.2452101,41.8726271 19.7823965,41.8744578 21.3461564,40.3106978 L27,34.6568542 L32.6538436,40.3106978 C34.2176035,41.8744578 36.7547899,41.8726271 38.3137085,40.3137085 C39.8758057,38.7516113 39.8771021,36.2202479 38.3106978,34.6538436 L32.6568542,29 Z M27,53 C41.3594035,53 53,41.3594035 53,27 C53,12.6405965 41.3594035,1 27,1 C12.6405965,1 1,12.6405965 1,27 C1,41.3594035 12.6405965,53 27,53 Z" id="Oval-2" sketch:type="MSShapeGroup"></path>
                        </g>
                    </g>
                </svg>
            </div>

        </div>
    </div> --}}
    <!-- End Dropzone Preview Template -->
</div>

{{-- <div id="linkDialog" class="modal fade" role="dialog" style="size:auto; backgroud-color:#FFFFFF">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Your file is bigger than 20MB</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="control-label">External source (Youtube) link</label>
                    <input class="form-control" type="text" name="videolink" id="videolink" placeholder="Copy and paste link here">
                </div>
                <div class="form-group">
                    <label class="control-label">Name / Title of video</label>
                    <input class="form-control" type="text" name="videotitle" id="videotitle" placeholder="Name / Title of video">
                </div>
            </div>
            <div class="modal-footer">
                <div class="row pull-right" style="margin-right: 10px">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Done</button>
                </div>
            </div>
        </div>
    </div>
</div> --}}

@endsection

@section('footer')
    {{-- {!! HTML::script('/packages/dropzone/dropzone.js') !!} --}}
    {{-- {!! HTML::script('/assets/js/dropzone-video-config.js') !!} --}}
    <script type="text/javascript">

        // with plugin options
        $(document).on('ready', function() {
            var filename = "";
            $("#videoInput").fileinput({
                uploadAsync: true,
                showUpload: false,
                showCancel: true,
                showUploadedThumbs: true,
                minFileCount: 1,
                maxFileSize: 20 * 1024,
                autoReplace: true,
                allowedFileTypes: ["video"],
                progress: '<div class="progress">\n' +
                        '    <div class="progress-bar progress-bar-success progress-bar-striped text-center" role="progressbar" aria-valuenow="{percent}" aria-valuemin="0" aria-valuemax="100" style="width:{percent}%;">\n' +
                        '        {percent}%\n' +
                        '     </div>\n' +
                        '</div>',
                deleteUrl: "/video/upload/delete",
                allowedFileExtensions: ['mp4','mkv','avi','mov','qt','wmv'],
                msgSizeTooLarge: 'File "{name}" ({size} KB) exceeds maximum allowed upload size of {maxSize} KB. Please copy the link of this video in the given field down.'
            });
            $('#videoInput').on('fileerror', function(event, params) {
                console.log(params.id);
                console.log(params.index);
                console.log(params.file);
                console.log(params.reader);
                console.log(params.files);
                filename = params.file.name;
                $('#videotitle').val(filename);
                $('#fileExceedsLimit').show();
            });
            $('#terms').change(function(e){
                if ($(this).is(":checked"))
                {
                    $("#error-terms").hide();
                   $('#upload').prop('disabled', false);
                } else {
                    $("#error-terms").show();
                    $('#upload').prop('disabled', true);
                };
            });
            $('#upload').click(function(e){
                console.log("videoinput: " + JSON.stringify( $('#videoInput').val() ));
                var videoLink = $('#videolink').val();
                var videoTitle = $('#videotitle').val();
                var datasetName = $('#datasetname').val();
                var classLabels = $('#classlabels').val();
                var tags = $('#tags').val();
                var selDataset = $("#selDataset :selected").text();
                var selClasses = $('#selClasses :selected').text();
                if (datasetName == '' && (selDataset == "" || selDataset == "Select Dataset"))
                {
                    alert("Kindly select dataset from the list or write new name for dataset.");
                    e.preventDefault();
                    return;
                };
                if (classLabels == '' && (selClasses == "" || selClasses == "Select Class"))
                {
                    alert("Kindly select class from the list or write new name for dataset.");
                    e.preventDefault();
                    return;
                }
                if($('#videoInput').val() == "" && videoLink == "")
                {
                    alert("Kindly add video or link.");
                    e.preventDefault();
                    return;
                }
                // e.preventDefault();
            });
        });

        $("#selDataset").change(function(){
            var dataset_name = $(this).find("option:selected").attr("id");

            if(dataset_name != 'select') {
                $.ajax({
                    type: "get",
                    url: '/advanceSearchGetClasslabelOfDataset',
                    data: {dataset : dataset_name},
                    timeout: 5000,
                    // dataType : 'json',
                    success: function(response) {
                        // alert(response);
                        var data = $.parseJSON(response);
                        var div = document.getElementById('selClasses');
                        var htmlString = "<option>Select Class</option>";
                        for (var i = 0; i < data.length ; i++) {
                            htmlString = htmlString + "<option>" + data[i].title + "</option>";
                        };
                        div.innerHTML = htmlString;
                    },
                    error: function(xhr, textStatus, errorThrown){
                        // alert(errorThrown);
                    }
                });
            }
        });
    </script>
@endsection