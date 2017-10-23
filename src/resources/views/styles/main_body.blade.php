@if(!isset($ajax))
    <div class="row modal-data">
        <div class="col-md-4 builder-modalleft">
            <ul class="filedcolumntype list-group listdatagroup" role="tablist">
                @foreach($main_body as $main)
                    <li class="">
                        <a href="javascript:void(0)" type="{!! $main->type !!}" data-id="{!! $main->slug !!}"
                           data-action="main_body_modality" class="styles">
                            <img src="/resources/assets/images/form-list.jpg">
                            <span>{!! $main->title !!}</span></a>
                    </li>
                @endforeach
            </ul>
        </div>
        @endif
        <div class="col-md-8 builder-modalright modal-data-items">
            <h5>Select Variation</h5>
            @if(!isset($items))
                <ul class="formlisting">
                    @foreach($main->variations() as $item)
                        <li>
                            <a href="javascript:void(0)" class="btn text-center item m-item"
                               data-value="{!! $item->id !!}">
                                <input type="hidden" data-action="main_body_modality"
                                       data-value="{!! $item->id !!}"/><img
                                        src="/resources/assets/images/form-list2.jpg"></a>
                            <span>{!! $item->title !!}</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <ul class="formlisting">
                    @foreach($items as $item)
                        <li>
                            <a class="btn text-center item m-item" data-value="{!! $item->id !!}">
                                <input type="hidden" data-action="main_body_modality"
                                       data-value="{!! $item->id !!}"/><img
                                        src="/resources/assets/images/form-list2.jpg"> </a>
                            <span>{!! $item->title !!}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
        @if(!isset($ajax))
    </div>
@endif