<!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">
    @foreach($tabs as $key=>$tab)
        <li role="presentation"
            class=@if(!$key)'active'@endif>
            <a
                    href="#{!! $tab['id'] or null !!}"
                    aria-controls="{!! $tab['id'] or null !!}"
                    role="tab"
                    data-toggle="tab">
                <i class="{!! $tab['icon'] or null!!}"></i>
                {!! $tab['title'] or null !!}</a>
        </li>
    @endforeach
</ul>

<!-- Tab panes -->
<div class="tab-content bg-silver p-10">
    @foreach($tabs as $key=>$tab)
        <div role="tabpanel" class="{!! (!$key)?'tab-pane active':'tab-pane' !!}" id="{!! $tab['id'] or null!!}">
            @if(\view()->exists($tab['view']))
                @include($tab['view'])
            @endif
        </div>
    @endforeach
</div>