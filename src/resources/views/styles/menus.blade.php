<div class="row modal-data">
    <div class="col-md-8 builder-modalright modal-data-items">
        @if(count($menus))
            <ul class="formlisting">
                @foreach($menus as $key=> $item)
                    <li>
                        <a class="btn item" href="javascript:void(0)">
                            <input type="hidden" data-action="menus" data-value="{!! $item->id !!}"/>
                            <img src="{!! url('images/form-list2.jpg') !!}"></a>
                        <span>{!! $item->name !!}</span>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>