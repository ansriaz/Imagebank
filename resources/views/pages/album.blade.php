<div class="gallery" id="gallery">
    @foreach ($dataset['dataset'] as $image)
        <div class="img {{$title}}img">
            <a target="_blank" href={{$image['uri'].$image['filename']}}>
                <img src={{ $image['uri'].$image['filename'] }} data-src={{ $image['uri'].$image['filename'] }} name={{ $image['name'] }} id="image" class="img-responsive">
            </a>
        </div>
    @endforeach
</div>
<div class="pagination {{$title}}" id="{{$title}}">
    <script>
        $(function() {

            var items = $(".gallery .{{$title}}img");
            var numItems = {{ count($dataset['dataset']) }};
            var perPage = 20;
            @if( $page == 'user' )
                perPage = 12;
            @endif

            items.slice(perPage).hide();

            $(".{{$title}}").pagination({
                items: numItems,
                itemsOnPage: perPage,
                cssStyle: 'light-theme',
                onPageClick: function(pageNumber) {
                    // pageNumber.preventDefault();
                    var showFrom = perPage * (pageNumber - 1);
                    var showTo = showFrom + perPage;

                    items.hide() // first hide everything, then show for the new page
                         .slice(showFrom, showTo).show();
                    return false;
                }
            });
        });
    </script>
</div>
