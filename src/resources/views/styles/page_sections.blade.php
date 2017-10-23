@if(!isset($ajax))
    <div class="row modal-data">
        <div class="col-md-4 builder-modalleft">
            <ul class="filedcolumntype " role="tablist">
                @foreach($layouts as $layout)
                    <li class="">
                        <a class="styles" data-id="{!! $layout->slug !!}" data-action="page_sections"
                           href="javascript:void(0)">
                            <img src="{!! url('images/form-list2.jpg') !!}">
                            <span>{!! $layout->title !!}</span></a>
                    </li>
                @endforeach
            </ul>
        </div>
        @endif
        <div class="col-md-8 modal-data-items builder-modalright">
            <h5>Select Variation</h5>
            @if(!isset($items))
                @foreach($layouts[0]->variations() as $item)
                    <ul class="formlisting">
                        <li>
                            <a class="btn item" data-value="{!! $item->id !!}" href="javascript:void(0)">
                                <input type="hidden" data-action="page_sections" data-value="{!! $item->id !!}"/> <img
                                        src="{!! url('images/form-list2.jpg') !!}">
                            </a>
                            <span>{!! $item->title !!}<a href="#" target="_blank"><i class="fa fa-pencil pull-right"
                                                                                     aria-hidden="true"></i></a></span>

                        </li>
                        @endforeach
                    </ul>
                    @else
                        <ul class="formlisting">
                            @foreach($items as $item)
                                <li>
                                    <a class="btn item" data-value="{!! $item->id !!}" href="javascript:void(0)">
                                        <input type="hidden" data-action="page_sections"
                                               data-value="{!! $item->id !!}"/>
                                        <img src="{!! url('images/form-list2.jpg') !!}"></a>
                                    <span>{!! $item->title !!}<a href="#" target="_blank"><i
                                                    class="fa fa-pencil pull-right" aria-hidden="true"></i></a></span>

                                </li>
                            @endforeach
                        </ul>
                    @endif
        </div>
        @if(!isset($ajax))
    </div>
@endif