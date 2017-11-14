{!! HTML::style('/assets/css/style.css') !!}
{!! HTML::style('/packages/pagination/simplePagination.css') !!}
{!! HTML::script('/packages/pagination/jquery.simplePagination.js') !!}

	<form class="form-horizontal" id="detailform" role="form" method="GET" action="{{ url('/getDetails') }}"> </form>
	<form class="form-horizontal" id="downloadForm" role="form" method="GET" action="{{ url('/proceedToDownload') }}">
		{!! csrf_field() !!}
		@foreach ($responseData['datasets'] as $dataset)
			@if(count($dataset['dataset']) > 0)
				<div class="panel panel-default">
					<div class="panel-heading" style="background:#ffffff; padding:5px;">
						<div class="pull-left">
							<div class="checkbox">
								<label><input type="checkbox" name="dataset[]" id={{ $dataset['title'] }} value={{$dataset['title']}}>
									{{ $dataset['title'] }}</label>
							</div>
						</div>
						@if( $responseData['page'] == 'user' )
							<button type="submit" class="btn btn-sm btn-default pull-right" id="{{$dataset['title']}}btn" value="{{$dataset['title']}}" name="dataset_title" form="detailform">Details</button>
						@endif
						<div class="clearfix"></div>
					</div>
					<div>
						@include('pages.album',['dataset'=>$dataset,'title'=>$dataset['title'], 'page' => $responseData['page']])
					</div>
				</div>
			@endif
		@endforeach
		<div class="pull-right">
				{{ Form::submit('Proceed to Download', array('class' => 'btn btn-primary', 'id'=>'downloadBtn', 'disabled')) }}
		</div>
	</form>

<script>
	$('.checkbox').click(function() {
		$('#downloadBtn').attr('disabled', false);

		if ($("#downloadForm input:checkbox:checked").length == 0)
		{
			$('#downloadBtn').attr('disabled', true);
		}
	});
</script>