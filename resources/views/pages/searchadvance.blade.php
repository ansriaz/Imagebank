@extends('layouts.app')

@section('title')
  Advance Search
@endsection

@section('content')

<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>

<style type="text/css">
    #editor {
        border:1px solid black;
        width: 650px;height: 200px;
    }
    .ul {
        display: block; position: static; margin-bottom: 5px; *width: 180px;
    }
</style>

<div class="container" style="min-width: 1200px;">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Advance Search</div>

                <div class="panel-body" style="min-height:400px;">
                    <section>
                        <ul class="nav nav-tabs nav-justified tabs">
                            <li class="active"><a data-toggle="tab" href="#divFilters">Filters</a></li>
                            <li><a data-toggle="tab" href="#divCutomQuery">Custom Search</a></li>
                        </ul>
                    </section>
                    <br/>
                    <section>
                        <div class="tab-content">
                            <div id="divFilters" class="tab-pane fade in active">
                                <form lass="form-horizontal" role="form" method="POST" action="{{ url('/advanceSearch') }}">
                                    {!! csrf_field() !!}
                                    <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
                                    <div>
                                        <div class="col-md-3">
                                        {{-- <div class="input-group inline"> --}}
                                            {{-- <input type="text" class="form-control" id="dataset" size="16" type="text"> --}}
                                            {{-- <div class="input-group-btn"> --}}
                                                <select class="form-control" name="dataset" id="selDataset">
                                                    <option id="select">Select Dataset</option>
                                                    @foreach ($responseData['datasets'] as $value)
                                                        <option id="{{ $value['title'] }}">{{ $value['title'] }}</option>
                                                    @endforeach
                                                </select>
                                            {{-- </div> --}}
                                        {{-- </div> --}}
                                        </div>
                                        <div class="col-md-3">
                                            <select class="form-control" name="classlabel" id="selClasses">
                                                {{-- <option>Select Class</option> --}}
                                                {{-- @foreach ($responseData['class'] as $value) --}}
                                                    {{-- <option id="{{ $value['title'] }}">{{ $value['title'] }}</option> --}}
                                                {{-- @endforeach --}}
                                            </select>
                                        </div>
                                        {{-- <div class="col-md-2">
                                            <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown"><span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a href="#">HTML</a></li>
                                                <li><a href="#">CSS</a></li>
                                                <li><a href="#">JavaScript</a></li>
                                            </ul>
                                        </div> --}}
                                        <div class="col-md-2">
                                            <input class="form-control" placeholder="From" type="text" name="dateFrom" id="dateFrom">
                                        </div>
                                        <div class="col-md-2">
                                            <input class="form-control" placeholder="To" type="text" name="dateTo" id="dateTo">
                                        </div>
                                    </div>
                                    <div class="row pull-right" style="margin-right: 10px">
                                        <button class="btn btn-default" id="search">Submit Query</button>
                                    </div>
                                </form>
                            </div>
                            <div id="divCutomQuery" class="tab-pane fade in">
                                <form lass="form-horizontal" id="queryForm" role="form" method="POST" action="{{ url('/advanceSearch') }}">
                                    {!! csrf_field() !!}
                                    <div id="row">Write your query here</div>
                                    <br />
                                    <div class="row">
                                        <input type="hidden" name="query" id="queryField" value="">
                                        <div id="main" class="col-md-9">
                                            {{-- // Write your query here --}}
                                            <div id="editor"></div>
                                            <script src="assets/js/ace/ace.js" type="text/javascript" charset="utf-8"></script>
                                            <script src="assets/js/ace/theme-sqlserver.js" type="text/javascript" charset="utf-8"></script>
                                            <script src="assets/js/ace/mode-mysql.js" type="text/javascript" charset="utf-8"></script>
                                            <script>
                                                var editor = ace.edit("editor");
                                                editor.setTheme("ace/theme/sqlserver");
                                                var MySQLMode = ace.require("ace/mode/mysql").Mode;
                                                editor.session.setMode(new MySQLMode());
                                                editor.getSession().setUseWrapMode(true);
                                                editor.setShowPrintMargin(false);
                                                document.getElementById("queryField").value = editor.getValue();
                                                // editor.getValue(); // or session.getValue
                                                editor.on("input", function() {
                                                    document.getElementById("queryField").value = editor.getValue();
                                                });
                                            </script>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="list-group">
                                                <label>images => images</label>
                                                <label>classlabel => classes</label>
                                            </div>
                                        </div>
                                    </div>
                                    <br />

                                    <div class="row" style="margin: 5px">
                                        <button class="btn btn-default pull-right" id="submitQuery">Submit Query</button>
                                    </div>

                                    <div class="row bg-info" style="margin: 5px">
                                        <div style="padding: 10px">
                                            <h4>Examples</h2>
                                            <ol class="">
                                            <li>SELECT * FROM images;</li>
                                            {{-- <li>SELECT * FROM web_mage;</li> --}}
                                            <li>SELECT `images`.* FROM `images` INNER JOIN `imageclasslabels` ON `images`.`id` = `imageclasslabels`.`image_id` INNER JOIN `classlabel` ON `imageclasslabels`.`class_id` = `classlabel`.`id` WHERE `images`.`dataset_id` = '1' AND (`classlabel`.`title` = 'reunion' OR `classlabel`.`title` = 'party');</li>
                                            </ol>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
            <div class="alert alert-danger" id="danger" style="display:none;">
            </div>
            <div class="flash-message">
                @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                  @if(Session::has('alert-' . $msg))

                  <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
                  @endif
                @endforeach
              </div> <!-- end .flash-message -->
        </div>
    </div>
</div>
@endsection

@section('footer')
    <script>

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

        $(function() {
            $( "#dateTo" ).datepicker();
            $( "#dateFrom" ).datepicker();
        });

    </script>
@endsection