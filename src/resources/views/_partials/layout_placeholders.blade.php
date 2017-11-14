@if(isset($_this->placeholders) && is_array($_this->placeholders))
    @foreach($_this->placeholders as $key=>$placeholder)
        <div>
            @if(isset($placeholder['tag']))

                {!! BBbutton2('unit',$key,$placeholder['tag'],(isset($placeholder['title'])?$placeholder['title']:'Sidebar'),['class'=>'btn btn-default change-layout','data-type'=>$placeholder['tag'],'data-name-prefix'=>'page_layout_settings','model'=>($page->page_layout_settings[$key])??null]) !!}
            @endif
        </div>
    @endforeach
@endif