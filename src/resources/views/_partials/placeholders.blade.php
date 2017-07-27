<div class="panel panel-default custompanel m-t-20">
    <div class="panel-heading">Select Page Layout</div>
    <div class="panel-body">
        <div class="row">
        
         <div class="col-md-12">
        {{Form::hidden('header',0)}}
        {{Form::hidden('footer',0)}}
        <label class="bd_layout pull-left m-r-15">{!! Form::checkbox('header',1,null,['style' => 'position:initial;z-index:1;']) !!} <span class="labls">Header</span>
            
        </label>
        <label class="bd_layout"> {!! Form::checkbox('footer',1,null,['style' => 'position:initial;z-index:1;']) !!} <span class="labls">Footer</span>
           
        </label>
		</div>
        <div class="col-md-12 m-b-10"> 
            <div class="col-sm-4 p-l-0">Page Layout</div>
            <div class="col-sm-5 p-l-0 p-r-10" >
                <input name="selcteunit" data-key="title"  readonly="readonly" class="page-layout-title form-control" value="@if($_this){!! $_this->title !!}@else{!! 'Nothing selected' !!} @endif">
            </div>
              {!! BBbutton('page_sections','page_section',(isset($page) && $page->page_layout)?'Change':'Select',['class'=>'btn btn-default change-layout','data-type'=>'frontend', 'model' =>(isset($page) && $page->page_layout)?$page->page_layout:null]) !!}

        </div>
        <div class="col-md-12 layout-data">
            <div id="placeholders">
            @if($_this)
                @foreach($_this->placeholders as $key=>$placeholder)

                   <div class="col-sm-4 p-l-0">{!! $placeholder['title'] or 'Sidebar' !!}</div>
                    <div class="col-sm-5 p-l-0 p-r-10" >
                        <input name="selcteunit" data-key="title"  readonly="readonly" data-id="{!!$key!!}" class="page-layout-title form-control" value="{!! BBgetUnitAttr($page->page_layout_settings[$key],'title') !!}">
                    </div>
                    {!! BBbutton($placeholder['self_type'],$key,"Change",['class'=>'btn btn-default change-layout','data-type'=>$placeholder['data-type'],'data-name-prefix'=>'placeholders','model'=>$page->page_layout_settings[$key]]) !!}
                @endforeach
            </div>
            @endif
        </div>
        </div>
    </div>
</div>