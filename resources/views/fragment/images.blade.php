{!! HTML::style('/assets/css/style.css') !!}
{!! HTML::style('/packages/pagination/simplePagination.css') !!}
{!! HTML::script('/packages/pagination/jquery.simplePagination.js') !!}

<form class="form-horizontal" id="detailform" role="form" method="GET" action="{{ url('/details/image') }}"> </form>
<form class="form-horizontal" id="downloadForm" role="form" method="GET" action="{{ url('/proceedToDownload') }}">
	{!! csrf_field() !!}

	@foreach ($responseData as $obj)
		{{-- @if(count($obj['obj']) > 0) --}}
			<div class="panel panel-default">
				<div class="panel-heading" style="background:#ffffff; padding:5px;">
					<div class="pull-left">
						<div class="checkbox">
							<label><input type="checkbox" name="dataset[]" id={{ $obj['title'] }} value={{$obj['title']}}>
								{{ $obj['title'] }}</label>
						</div>
					</div>
					@if( isset($page) && $page == 'user' )
						<button type="submit" class="btn btn-sm btn-default pull-right" id="{{$obj['title']}}btn" value="{{$obj['title']}}" name="dataset_title" form="detailform">Details</button>
					@else
						<input type="hidden" name="dataset_title" value="{{$title}}" />
					@endif
					<div class="clearfix"></div>
				</div>
				<div class="album{{ $obj['id'] }}">
					{{-- @include('fragment.album',['obj'=>$obj,'title'=>$obj['title'], 'page' => $responseData['page']]) --}}
				</div>
			</div>
		{{-- @endif --}}
	@endforeach

	<div class="pull-right">
			{{ Form::submit('Proceed to Download', array('class' => 'btn btn-primary', 'id'=>'downloadBtn', 'disabled')) }}
	</div>
</form>

{{-- {{ $obj['title'] }} --}}
{{-- {{ $obj['id'] }} --}}

<script>

	// $(document).ready(function() {

		@foreach ($responseData as $obj)
			var dataToSend = {};
			@if(isset($title))
				dataToSend = {classlabel : "{{$obj['id']}}", dataset_title : '{{$title}}'};
			@else
				dataToSend = {id : "{{$obj['id']}}"};
			@endif
			console.log(dataToSend);
			$.ajax({
                type: "get",
                url: '/getimages',
                data: dataToSend,
                timeout: 50000,
                success: function(response) {
                	// console.log(response);
                	if(response.search('nodata') != -1)
                	{
                		$(".album{{ $obj['id'] }}").parent().hide();
                	} else {
                		$(".album{{ $obj['id'] }}").html(response);
                	}
                },
                error: function(xhr, textStatus, errorThrown){
                    alert(errorThrown);
                }
            });
            $(document).on('click', ".pagination{{ $obj['id'] }} .pagination a", function (e) {
	            page = $(this).attr('href').split('page=')[1];
	            var dataToSend = {};
				@if(isset($title))
					dataToSend = {classlabel : '{{$obj['id']}}', dataset_title : '{{$title}}'};
				@else
					dataToSend = {id : '{{$obj['id']}}'};
				@endif
	            $.ajax({
		            url : '/getimages' + '?page=' + page,
		            data: dataToSend,
		        }).done(function (data) {
		            $('.album{{ $obj['id'] }}').html(data);
		            location.hash = page;
		        }).fail(function () {
		            alert('Images could not be loaded.');
		        });
	            e.preventDefault();
	        });
		@endforeach

    // });

	$('.checkbox').click(function() {
		$('#downloadBtn').attr('disabled', false);

		if ($("#downloadForm input:checkbox:checked").length == 0)
		{
			$('#downloadBtn').attr('disabled', true);
		}
	});
</script>