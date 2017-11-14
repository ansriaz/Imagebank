@extends('layouts.app')

@section('title')
  Search Results
@endsection

@section('head')

<style>

div.img {
    margin: 5px;
    border: 1px solid #ccc;
    float: left;
}

div.img:hover {
    border: 1px solid #777;
}

div.img img {
    width: 100px;
    height: 100px;
    display: block;
    float: center;
}

div.gallery {
    margin: 5px;
    min-height: 110px;
    height: auto;
    max-height: 450px;
    overflow: hidden;
    white-space: nowrap;
}

</style>
@endsection

@section('content')

<div class="container-fluid" style="min-width: 1200px;">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Search Results</div>

                <div class="panel-body">
                    <section>
                        <ul class="nav nav-tabs nav-justified tabs">
                            <li class="active"><a data-toggle="tab" href="#divImages">Images</a></li>
                            <li><a data-toggle="tab" href="#divVideos">Videos</a></li>
                        </ul>
                    </section>
                    <section>
                        <div class="tab-content">
                            <div id="divImages" class="tab-pane fade active in">
                                <form class="form-horizontal" id="downloadForm" role="form" method="GET" action="{{ url('/proceedToDownload') }}">
                                    @if(isset($q))
                                        <input type="hidden" value="{{$q}}" name="searchTerm" />
                                    @else
                                        {{-- <input type="hidden" value="{{$search}}" name="searchTerm" /> --}}
                                    @endif
                                    <div class="album">
                                        <div style="height:50px;"></div>
                                    </div>
                                    <div id="downloadButttonDiv" class="pull-right">
                                        {{ Form::submit('Proceed to Download', array('class' => 'btn btn-primary', 'id'=>'downloadBtn')) }}
                                    </div>
                                </form>
                            </div>
                            <div id="divVideos" class="tab-pane fade">
                                {{-- <form class="form-horizontal" id="downloadForm" role="form" method="GET" action="{{ url('/proceedToDownload') }}"> --}}
                                    @if(isset($q))
                                        <input type="hidden" value="{{$q}}" name="searchTerm" />
                                    @else

                                    @endif
                                    <div style="height:20px;"></div>
                                    <div class="playlist">
                                        <div style="height:50px;"></div>
                                    </div>
                                    {{-- <div id="downloadButttonDiv" class="pull-right">
                                        {{ Form::submit('Proceed to Download', array('class' => 'btn btn-primary', 'id'=>'downloadBtn')) }}
                                    </div>
                                </form> --}}
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer')
{!! HTML::script('/packages/lazyloading/lazyload.js') !!}
<script>
    $(function() {
        var activeTab = '', isLoaded = false;
        function getData(source) {
            var targetUrl = '';
            if(source == "#divImages"){
                 targetUrl = '/searchimages';
            } else {
                targetUrl = '/searchvideos';
            }
            $.ajax({
                type: "get",
                url: targetUrl,
                data: { },
                timeout: 50000,
                success: function(response) {
                    console.log('[response]: '+response);
                    if(source == '#divImages'){
                        if(response.indexOf('No Image found') !== -1) {
                            // alert('[response]: '+response);
                            $(".album").html(response);
                            $('#downloadButttonDiv').remove();
                        }
                        $(".album").html(response);
                    } else {
                        $(".playlist").html(response);
                    }
                },
                error: function(xhr, textStatus, errorThrown){
                    alert(errorThrown);
                }
            });
        }
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            var target = $(e.target).attr("href") // activated tab
            console.log("active tab: " + target);
            activeTab = target;
            if(target == '#divVideos' && !isLoaded){
                isLoaded = true;
                getData(target);
            }
        });

        // for pagination
        $(document).on('click', '.pagination a', function (e) {
            console.log($(this).attr('href'));
            page = $(this).attr('href').split('page=')[1];
            $.ajax({
                url : (activeTab == "#divImages") ? '/searchimages' + '?page=' + page : '/searchvideos' + '?page=' + page,
                data: {},
            }).done(function (data) {
                if(activeTab == "#divImages")
                    $('.album').html(data);
                else
                    $('.playlist').html(data);
                location.hash = page;
            }).fail(function () {
                alert('Images could not be loaded.');
            });
            e.preventDefault();
        });
        getData("#divImages");
    });
        $(".list-group a").click(function(e) {
           $(".list-group a").removeClass("active");
           $(e.target).addClass("active");
        });
        $('.checkbox').click(function() {
            $('#downloadBtn').attr('disabled', false);

            if ($("#downloadForm input:checkbox:checked").length == 0)
            {
                $('#downloadBtn').attr('disabled', true);
            }
        });
    </script>
@endsection
