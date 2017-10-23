<div class="col-md-8 modal-data-items row">
    <h5>Select Variation</h5>
    <ul class="formlisting">
        @foreach($layouts as $k=>$item)
            <li class="">
                <a class="btn item" href="javascript:void(0)">
                    <input type="hidden" data-action="templates" data-value="{!! $k!!}"/>
                    <img src="/resources/assets/images/form-list2.jpg"/>
                </a>
                <span>
                                {!! $item->title !!}
                    <a href="{!! url('admin/settings/frontend/create-layout',$k) !!}" target="_blank">
                                    <i class="fa fa-pencil pull-right" aria-hidden="true"></i>
                                </a>
                            </span>
            </li>
        @endforeach
    </ul>
</div>