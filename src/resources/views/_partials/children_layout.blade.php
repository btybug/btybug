<div class="panel panel-default custompanel m-t-20">
    <div class="panel-heading">Children Page layout</div>
    <div class="panel-body">
        <div class="form-group">
            <div class="col-md-12">
                <label>Select Layout</label>
            </div>
            <div class="col-md-12">
                <div class="col-md-12 m-b-10">
                    {!! BBbutton2('layouts','children_page_layout','front_pages_layout',(isset($page) && $page->page_layout)?'Change':'Select',['class'=>'btn btn-default change-layout','model' =>(isset($page) && $page->page_layout)?$page->page_layout:null]) !!}
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="col-md-12">
                <label>Select Layout Placeholders</label>
            </div>
            <div class="col-md-12 layout-data">
                <div id="placeholders">
                    @if(isset($_this->placeholders) && is_array($_this->placeholders))
                        @foreach($_this->placeholders as $key=>$placeholder)
                            <div>
                                @if(isset($placeholder['tag']))
                                    {!! BBbutton2('unit',$key,$placeholder['tag'],(isset($placeholder['title'])?$placeholder['title']:'Sidebar'),['class'=>'btn btn-default change-layout','data-type'=>$placeholder['tag'],'data-name-prefix'=>'children_page_layout_settings','model'=>($page->page_layout_settings[$key])??null]) !!}
                                @endif
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

