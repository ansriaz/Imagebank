@if(count($responseData['videos'])>0)
	@if(isset($responseData['classlabel']))
		<div class="playlist" id="playlist{{$responseData['classlabel']}}">
	@elseif(isset($responseData['q']))
		<div class="playlist" id="playlist{{$responseData['q']}}">
	@elseif(isset($responseData['query']))
		<div class="playlist" id="playlist{{$responseData['query']}}">
	@elseif(isset($responseData['search']))
		<div class="playlist" id="playlist{{$responseData['search']}}">
	@else
		<div class="playlist" id="playlist{{$responseData['id']}}">
	@endif

	    @foreach ($responseData['videos'] as $video)
	    {{-- style="width: 300px; height: auto; margin: 10px; display: inline-block;position: relative;" --}}
	        <div class="video" style="width: 300px;">
	            @if(isset($video['uri']) && !is_null($video['uri']))
		            <a target="_blank" href={{$video['uri'].$video['filename']}}>
		                <video width="300px" height="auto" controls>
							<source src={{ $video['uri'].$video['filename'] }} type="video/mp4" data-src={{ $video['uri'].$video['filename'] }} name={{ $video['name'] }} id="video">
							Your browser does not support the video tag.
						</video>
						{{-- <label>{{ $video['name'] }}</label> --}}
					</a>
				@else
					<a target="_blank" href={{$video['link']}}>
						<iframe width="300px" height="200px" src="https://www.youtube.com/embed/{{ explode('v=',$video['link'])[1] }}" allowfullscreen>
						</iframe>
						{{-- {{ $video['name'] }} --}}
					</a>
				@endif
	        </div>
	    @endforeach
	</div>

	@if(isset($responseData['classlabel']))
		<div class="pagination{{$responseData['classlabel']}}">
		{!! $responseData['videos']->appends(['classlabel'=>$responseData['classlabel'],'dataset_title'=>$responseData['dataset_title']])->render() !!}
		</div>
	@elseif(isset($responseData['q']))
		<div class="pagination">
		{!! $responseData['videos']->appends(['q'=>$responseData['q']])->render() !!}
		</div>
	@elseif(isset($responseData['query']))
		<div class="pagination">
		{!! $responseData['videos']->appends(['query'=>$responseData['query']])->render() !!}
		</div>
	@elseif(isset($responseData['search']))
		<div class="pagination">
		{!! $responseData['videos']->appends(['q'=>$responseData['search']])->render() !!}
		</div>
	@else
		<div class="pagination{{$responseData['id']}}">
		{!! $responseData['videos']->appends(['id'=>$responseData['id']])->render() !!}
		</div>
	@endif
@else
nodata
@endif
 {{-- {{ $responseData['q'] }} --}}