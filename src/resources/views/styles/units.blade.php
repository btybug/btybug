@if(!isset($ajax))
    <div class="row modal-data">
        <div class="col-md-4 builder-modalleft">
            <ul class="filedcolumntype list-group listdatagroup" role="tablist">
                @foreach($units as $tpl)
                    <li class="">
                        <a href="javascript:void(0)" type="button" data-id="{!! $tpl->slug !!}"
                           data-action="units" data-key="{!! $data['key'] !!}" class="styles">
                            <img src="{!! url('images/form-list.jpg') !!}">
                            <span>{!! $tpl->title !!}</span></a>
                    </li>
                @endforeach
            </ul>
        </div>
        @endif
        <div class="col-md-8 builder-modalright modal-data-items">
            <h5>Select Variation</h5>
            @if(!isset($items))
                <ul class="formlisting">
                    @foreach($tpl->variations() as $item)
                        <li>
                            <a href="javascript:void(0)" data-key="{!! $data['key'] !!}"
                               class="btn text-center item item-unit zzzzz" data-value="{!! $item->id !!}">
                                <input type="hidden" data-action="units" data-input-type="{!! $tpl->input_type !!}"
                                       data-value="{!! $item->id !!}"/><img src="{!! url('images/form-list2.jpg') !!}"></a>
                            <span>{!! $item->title !!}<a
                                        href="{!! url('admin/resources/units/live-settings',$item->id) !!}"
                                        target="_blank"><i class="fa fa-pencil pull-right"
                                                           aria-hidden="true"></i></a></span>
                        </li>
                    @endforeach
                </ul>
            @else
                <ul class="formlisting">
                    @foreach($items as $item)
                        <li>
                            <a class="btn text-center item item-unit oooo" data-key="{!! $key !!}"
                               data-value="{!! $item->id !!}">
                                <input type="hidden" data-action="units" data-value="{!! $item->id !!}"/><img
                                        src="{!! url('images/form-list2.jpg') !!}"> </a>
                            <span>{!! $item->title !!}<a
                                        href="{!! url('admin/resources/units/live-settings',$item->id) !!}"
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