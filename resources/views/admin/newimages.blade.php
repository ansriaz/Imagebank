@extends('admin.app')

@section('title')
  New Images
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
            <div class="panel panel-default">
                @if(!isset($images) || count($images) == 0)
                    <div class="panel-body">
                        <h4 align="center">No New Images</h4>
                    </div>
                @else
                    <div class="panel-heading">New Images</div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="album">
                                    {{-- @include('fragment.album',['images' => $images]) --}}
                                    @foreach ($images as $image)
                                        <div class="img">
                                            <a target="_blank" href={{$image['uri'].$image['filename']}}>
                                                <img src={{ $image['uri'].$image['filename'] }} data-src={{ $image['uri'].$image['filename'] }} name={{ $image['name'] }} id="image" class="lazy img-responsive">
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="pagination">
                                    {!! $images !!}
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer')

@endsection
