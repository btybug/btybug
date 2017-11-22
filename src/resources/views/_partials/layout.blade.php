<div class="form-group">
    <div class="col-md-12">
        <label>Header/Footer</label>
    </div>
    <div class="col-md-12">
        {{Form::hidden('header',0)}}
        {{Form::hidden('footer',0)}}
        <label class="bd_layout pull-left m-r-15">{!! Form::checkbox('header',1,null,['style' => 'position:initial;z-index:1;']) !!}
            <span class="labls">Header</span>

        </label>
        <label class="bd_layout"> {!! Form::checkbox('footer',1,null,['style' => 'position:initial;z-index:1;']) !!}
            <span class="labls">Footer</span>

        </label>
    </div>
</div>

<div class="form-group">
    <div class="col-md-12">
        <label>Select Page Layout</label>
    </div>
    <div class="col-md-12">
        <div class="col-md-12 m-b-10">
            {!! BBbutton2('layouts','page_layout','front_pages_layout',(isset($page) && $page->page_layout)?'Change':'Select',['class'=>'btn btn-default change-layout','model' =>(isset($page) && $page->page_layout)?$page->page_layout:null]) !!}
        </div>
    </div>
</div>

<div class="form-group">
    <div class="col-md-12">
        <label>Select Page Layout Placeholders</label>
    </div>
    <div class="col-md-12 layout-data">
        <div id="placeholders">
            @if(isset($_this->placeholders) && is_array($_this->placeholders))
                @foreach($_this->placeholders as $key=>$placeholder)
                    <div>
                        @if(isset($placeholder['tag']))

                            {!! BBbutton2('unit',$key,$placeholder['tag'],(isset($placeholder['title'])?$placeholder['title']:'Sidebar'),['class'=>'btn btn-default change-layout','data-type'=>$placeholder['tag'],'data-name-prefix'=>'page_layout_settings','model'=>($page->page_layout_settings[$key])??null]) !!}
                        @endif
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>


