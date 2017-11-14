@extends('layouts.app')

@section('title')
Dataset
@endsection

@section('head')
@endsection

@section('content')

<div class="container-fluid" style="min-width: 1200px;">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">{{ $responseData['title'] }}</div>

                <div class="panel-body">
                    @if($responseData['type'] == 'image')
                        @include('fragment.images',['responseData'=>$responseData['classlabels'], 'title'=>$responseData['title']])
                    @else
                        @include('fragment.videos',['responseData'=>$responseData['classlabels'], 'title'=>$responseData['title']])
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer')
{!! HTML::style('/packages/pagination/simplePagination.css') !!}
{!! HTML::script('/packages/pagination/jquery.simplePagination.js') !!}
@endsection