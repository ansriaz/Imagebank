@extends('admin.app')

@section('title')
  Edit Images
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

</style>
@endsection

@section('content')

 {{-- style="min-width: 1200px;" --}}
<div class="container-fluid">
    <div class="row">
        <div class="text-center">
            <ul class="list-inline">
                <li><a href="{{ url('/admin/home') }}">Admin Home</a></li> ||
                <li><a href="{{ url('/admin/newimages') }}">New Images</a></li> ||
                <li><a href="{{ url('/admin/geteditimages') }}">Edit Images</a></li>
            </ul>
        </div>
    </div>
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            @if(!isset($responseData['images']) || count($responseData['images']) == 0)
                <div class="panel panel-default">
                    <div class="panel-body">
                        <h4 align="center">No New Images</h4>
                    </div>
                </div>
            @else
                <div class="panel-default">
                    <div class="panel-heading">Edit Images</div>

                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <form lass="form-horizontal" role="form" method="POST" action="{{ url('/editimages') }}">
                                <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
                                {{-- <div class="album"> --}}
                                    @foreach ($responseData['images'] as $image)
                                        {{-- @php --}}
                                            {{-- $image = imageRcv --}}
                                        {{-- @endphp --}}
                                        <div class="imgDiv panel panel-default col-md-4">
                                            <div class="panel-body">
                                                <div class="col-md-6">
                                                    <div class="img">
                                                        <a target="_blank" href={{$image['uri'].$image['filename']}}>
                                                            <img src={{ $image['uri'].$image['filename'] }} data-src={{ $image['uri'].$image['filename'] }} name={{ $image['name'] }} id="image" class="lazy img-responsive">
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <select class="form-control" name="selDataset" id="selDataset_{{ $image['id'] }}" data-image-id="{{ $image['id'] }}">
                                                        <option id="select">Select Dataset</option>
                                                        @foreach ($responseData['datasets'] as $value)
                                                            @if($value['id'] == $image['dataset_id'])
                                                                <option id="{{ $value['id'] }}" selected>{{ $value['title'] }}</option>
                                                            @else
                                                                <option id="{{ $value['id'] }}">{{ $value['title'] }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                    <script type="text/javascript">
                                                        $(document).ready(function(){
                                                            $("#selDataset_{{ $image['id'] }}").trigger("change");
                                                            // window.setTimeout(function() { jQuery("select[name='selDataset']").trigger("change");}, 1);
                                                        });
                                                    </script>
                                                    {{-- {{ $image->ClassLabel() }} --}}
                                                    <select class="form-control" name="selClasses" id="selClasses_{{ $image['id'] }}" data-image-id="{{ $image['id'] }}">
                                                        {{-- <option id="select">Select Class</option> --}}
                                                        {{-- @foreach ($responseData['classlabels'] as $value) --}}
                                                            {{-- @if($value['id'] == $image['dataset_id']) --}}
                                                                {{-- <option id="{{ $value['id'] }}" selected>{{ $value['title'] }}</option> --}}
                                                            {{-- @endif --}}
                                                            {{-- <option id="{{ $value['id'] }}">{{ $value['title'] }}</option> --}}
                                                        {{-- @endforeach --}}
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                {{-- </div> --}}
                                </form>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="pagination">
                                    {!! $responseData['images'] !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('footer')

<script type="text/javascript">
    $("select[name='selDataset']").change(function(){
        // console.log('even fired');
        var datasetSelection = $(this).attr("data-image-id");
        var dataset_name = $(this).find("option:selected").attr("id");
        var dataToSend = {dataset_id : dataset_name, image_id: datasetSelection}
        console.log(dataToSend);

        if(dataset_name != 'select') {
            $.ajax({
                type: "get",
                url: '/admin/getCurrentClassOfImage',
                data: dataToSend,
                timeout: 5000,
                // dataType : 'json',
                success: function(response) {
                    // alert(response);
                    var data = $.parseJSON(response);
                    var div = document.getElementById('selClasses_'+dataToSend.image_id);
                    var classes = data['classes'];
                    console.log((div));
                    var htmlString = "<option>Select Class</option>";
                    for (var i = 0; i < classes.length ; i++) {
                        if(data['img'] == classes[i].id) {
                            htmlString = htmlString + "<option id="+classes[i].id+" selected>" + classes[i].title + "</option>";
                        } else {
                            htmlString = htmlString + "<option id="+classes[i].id+">" + classes[i].title + "</option>";
                        }
                    };
                    div.innerHTML = htmlString;
                },
                error: function(xhr, textStatus, errorThrown){
                    // alert(errorThrown);
                }
            });
        }
    });
    $("select[name='selClasses']").change(function(){
        $.ajaxSetup(
        {
            headers:
            {
                'X-CSRF-Token': $('input[name="_token"]').val()
            }
        });
        var img_id = $(this).attr("data-image-id");
        var class_id = $(this).find("option:selected").attr("id");
        var selClass = $(this).find("option:selected").attr("id");
        var ds_id = $("#selDataset_"+img_id+" option:selected").attr('id');
        var dataToSend = {dataset_id : ds_id, image_id: img_id, class_id: class_id};
        console.log(JSON.stringify(dataToSend));

        if(selClass != 'select') {
            $.ajax({
                type: "post",
                url: '/admin/editimage',
                data: dataToSend,
                timeout: 5000,
                dataType : 'json',
                success: function(response) {
                    var res = JSON.stringify(response);
                    console.log('[response]: ' + res);
                    if(res['result'] == 'record updated')
                    {
                        console.log('[response]: ' + res['result']);
                    }
                    // alert(response);
                },
                error: function(xhr, textStatus, errorThrown){
                    alert(errorThrown);
                }
            });
        }
    });
</script>

@endsection