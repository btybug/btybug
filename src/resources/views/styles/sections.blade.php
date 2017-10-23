@if(!isset($ajax))
    <div class="row modal-data">
        <div class="col-md-4 builder-modalleft">
            <ul class="filedcolumntype list-group listdatagroup" role="tablist">
                @foreach($sections as $section)
                    <li class="">
                        <a href="javascript:void(0)" type="{!! $section->type !!}" data-id="{!! $section->slug !!}"
                           data-action="placeholder_section" class="styles">
                            <img src="{!! url('images/form-list.jpg') !!}">
                            <span>{!! $section->title !!}</span></a>
                    </li>
                @endforeach
            </ul>
        </div>
        @endif
        <div class="col-md-8 builder-modalright modal-data-items">
            <h5>Select Variation</h5>
            @if(!isset($items))
                <ul class="formlisting">
                    @foreach($section->variations() as $item)
                        <li>
                            <a href="javascript:void(0)" class="btn text-center item">
                                <input type="hidden" data-action="placeholder_section"
                                       data-value="{!! $item->id !!}"/><img src="{!! url('images/form-list2.jpg') !!}"></a>
                            <span>{!! $item->title !!}</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <ul class="formlisting">
                    @foreach($items as $item)
                        <li>
                            <a class="btn text-center item">
                                <input type="hidden" data-action="placeholder_section"
                                       data-value="{!! $item->id !!}"/><img src="{!! url('images/form-list2.jpg') !!}">
                            </a>
                            <span>{!! $item->title !!}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
        @if(!isset($ajax))
    </div>
@endif