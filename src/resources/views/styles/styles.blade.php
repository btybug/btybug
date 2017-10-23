@if(!isset($ajax))
    <div class="row modal-data">
        <div class="col-md-4 builder-modalleft">
            <ul class="filedcolumntype " role="tablist">
                @foreach($styles as $key => $style)
                    <li class="">
                        <a class="styles" data-id="{!! $key !!}" data-key="{!! $type !!}" data-action="styles"
                           href="javascript:void(0)">
                            <img src="/resources/assets/images/form-list.jpg">
                            <span>{!! $style !!}</span></a>
                    </li>
                @endforeach
            </ul>
        </div>
        @endif
        <div class="col-md-8 modal-data-items builder-modalright">
            <h5>Select Variation</h5>
            @if(count($items))
                <ul class="formlisting">
                    @foreach($items as $item)
                        <li>
                            <a class="btn item" href="javascript:void(0)">
                                <input type="hidden" data-action="styles" data-value="{!! $item->slug !!}"/>
                                <img src="images/form-list2.jpg"></a>
                            <span>{!! $item->name !!}<a
                                        href="{!! url('admin/resources/styles/style_preview',$item->id) !!}"
                                        target="_blank"><i class="fa fa-pencil pull-right"
                                                           aria-hidden="true"></i></a></span>

                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
        @if(!isset($ajax))
    </div>
@endif