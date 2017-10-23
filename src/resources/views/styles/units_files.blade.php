<div class="row modal-data">
    <div class="col-md-4 builder-modalleft">
        <ul class="filedcolumntype" role="tablist">
            @foreach($files as $item)
                <li>
                    <a class="btn item file-item-dynamic" href="javascript:void(0)">
                        <input type="hidden" data-action="units" data-value="{!! $item->id !!}"/>
                        <img src="/resources/assets/images/form-list.jpg">
                        <span>{!! $item->title !!}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</div>