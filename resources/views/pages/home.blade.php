@extends('layout')


@section('content')

    @include('includes.status')


    <div class="row">
        <div class="col-md-6">
            <!-- {{ Form::open(['url'=> route('admin.esc.find-articles'), 'role'=>'form','class'=>'form-inline']) }}
            <div class="form-group">
                <label class="sr-only" for="search-query">Image alt text</label>
                {{ Form::text('term', null, array('class'=>'form-control', 'id'=>'search-term', 'placeholder'=>'Find Image')) }}
            </div>
            <button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-search"></span> Search</button>
            {{ Form::close() }}-->
        </div>
        <div class="col-md-offset-3 col-md-3">
            <a href="{{ route('admin.esc.images.upload') }}" class="add-exam-link btn btn-primary"><span class="glyphicon glyphicon-pencil"> </span>  &nbsp;Upload New Images</a>
        </div>
    </div>
    <br><br>

    {{ $images->links() }}

    <div class="table-responsive">

        <table class="table table-hover">
            <thead>
            <tr>
                <th>Icon</th>
                <th>File name</th>
                <th>Title</th>
                <th>Alt</th>
                <th>URL</th>
                <th>Upload date</th>
                <th>Edit</th>
                <th>Delete</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $page = Input::get('page');
            if(empty($page) || $page<=0)
            {
                $index = 1;
            }
            else
            {
                $paginator = Config::get('dynamic.paginator');
                $index = $paginator*($page-1) + 1;
            }
            ?>

            @foreach($images as $image)
                <tr>
                    <td>{{ HTML::image( route('icon-image', ['filename' => $image->filename ])) }}</td>
                    <td class="break-cell">{{ $image->filename }}</td>
                    <td class="break-cell">{{ $image->title }}</td>
                    <td class="break-cell">{{ $image->alt_text }}</td>
                    <td class="break-cell"><a target="_blank" href="{{ route('original-image', ['filename' => $image->filename ]) }}">{{ route('original-image', ['filename' => $image->filename ]) }}</a></td>
                    <td>{{ $image->created_at }}</td>
                    <td><a href="{{ route('admin.esc.images.edit', ['image_id' => $image->id ]) }}">Edit</a></td>
                    <td><a onclick="return confirm('Are you sure you want to delete this image?');" href="{{ route('admin.esc.images.delete', ['image_id' => $image->id ]) }}"><span class="glyphicon glyphicon-trash"> </span></a></td>
                </tr>

            @endforeach


            </tbody>
        </table>

    </div>

    {{ $images->links() }}

    <style>
        tr > td.break-cell {
            word-break: break-all;
        }
    </style>

@stop