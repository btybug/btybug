<div class="row modal-data">
    <div class="col-md-8 builder-modalright modal-data-items">
        @if(count($menus))
            <ul class="formlisting">
            @foreach($menus as $item)
                <li>
                <a class="btn item" href="javascript:void(0)">
                    <input type="hidden" data-action="menus" data-value="{!! $item->id !!}"/>
                    <img src="/resources/assets/images/form-list2.jpg"></a>
                 <span>{!! $item->name !!}
                     @if($item->section == 'frontend')
                         <a href="{!! url('admin/create/front-menu/update',$item->id) !!}" target="_blank">
                     @else
                         <a href="{!! url('admin/create/menu/edit',$item->id) !!}" target="_blank">
                     @endif
                         <i class="fa fa-pencil pull-right" aria-hidden="true"></i>
                     </a>
                 </span>
                </li>
            @endforeach
            </ul>
        @endif
    </div>
</div>