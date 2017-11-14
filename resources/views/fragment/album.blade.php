@if(count($responseData['images']))
	@if(isset($responseData['classlabel']))
		<div class="gallery" id="gallery{{$responseData['classlabel']}}">
	@elseif(isset($responseData['q']))
		<div class="gallery" id="gallery{{$responseData['q']}}">
	@elseif(isset($responseData['query']))
		<div class="gallery" id="gallery{{$responseData['query']}}">
	@elseif(isset($responseData['search']))
		<div class="gallery" id="gallery{{$responseData['search']}}">
	@else
		<div class="gallery" id="gallery{{$responseData['id']}}">
	@endif

	    @foreach ($responseData['images'] as $image)
	        <div class="img">
	            <a target="_blank" href={{$image['uri'].$image['filename']}}>
	                <img src={{ $image['uri'].$image['filename'] }} data-src={{ $image['uri'].$image['filename'] }} name={{ $image['name'] }} id="image" class="lazy img-responsive">
	            </a>
	        </div>
	    @endforeach
	</div>

	@if(isset($responseData['classlabel']))
		<div class="pagination{{$responseData['classlabel']}}">
		{!! $responseData['images']->appends(['classlabel'=>$responseData['classlabel'],'dataset_title'=>$responseData['dataset_title']])->render() !!}
		</div>
	@elseif(isset($responseData['q']))
		<div class="pagination">
		{!! $responseData['images']->appends(['q'=>$responseData['q']])->render() !!}
		</div>
	@elseif(isset($responseData['query']))
		<div class="pagination">
		{!! $responseData['images']->appends(['query'=>$responseData['query']])->render() !!}
		</div>
	@elseif(isset($responseData['search']))
		<div class="pagination">
		{!! $responseData['images']->appends(['q'=>$responseData['search']])->render() !!}
		</div>
	@else
		<div class="pagination{{$responseData['id']}}">
		{!! $responseData['images']->appends(['id'=>$responseData['id']])->render() !!}
		</div>
	@endif
@else
nodata
@endif

 {{-- {{ $responseData['q'] }} --}}