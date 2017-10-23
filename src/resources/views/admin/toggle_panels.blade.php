<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            @foreach($panels as $key=>$panel)
                <div class="jumbotron {!! (!$key)?'tab-pane active':'tab-pane' !!}" id="{!! $panel['id'] or null!!}">
                    @if(\view()->exists($panel['view']))
                        @include($panel['view'])
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>