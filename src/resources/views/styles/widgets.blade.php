@if(!isset($ajax))
    <div class="row modal-data">
        <div class="col-md-4 builder-modalleft">
            <ul class="filedcolumntype" role="tablist">
                @foreach($templates as $tpl)
                    <li class="">
                        <a data-id="{!! $tpl->slug !!}" class="styles" data-action="widgets"
                           href="javascript:void(0)"> <img
                                    src="/resources/assets/images/form-list.jpg"><span>{!! $tpl->title !!}</span></a>
                    </li>
                @endforeach
            </ul>
        </div>
        @endif
        <div class="col-md-8 modal-data-items builder-modalright row">
            <h5>Select Variation</h5>
            @if(!isset($items))
                <ul class="formlisting">
                    @foreach($templates[0]->variations() as $item)
                        <li class="">

                            <a class="btn item" href="javascript:void(0)">
                                <input type="hidden" data-action="widgets"
                                       data-actiontype="{{ @$templates[0]->main_type }}"
                                       data-value="{!! $item->id !!}"/>
                                <img src="/resources/assets/images/form-list2.jpg"/>
                            </a>
                            <span>
                            {!! $item->title !!}
                                <a href="{!! url('admin/resources/widgets/settings-live',$item->id) !!}"
                                   target="_blank">
                                <i class="fa fa-pencil pull-right" aria-hidden="true"></i>
                            </a>
                        </span>
                        </li>
                    @endforeach
                </ul>
            @else
                <ul class="formlisting">
                    @foreach($items as $item)
                        <li class="">
                            <a class="btn item" href="javascript:void(0)">
                                <input type="hidden" data-action="widgets" data-actiontype="{{ $tpl->main_type }}"
                                       data-value="{!! $item->id !!}"/> <img
                                        src="/resources/assets/images/form-list2.jpg"></a>
                            <span>
                                {!! $item->title !!}
                                <a href="{!! url('admin/resources/widgets/settings-live',$item->id) !!}"
                                   target="_blank">
                                    <i class="fa fa-pencil pull-right" aria-hidden="true"></i>
                                </a>
                            </span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
        @if(!isset($ajax))
    </div>
@endif