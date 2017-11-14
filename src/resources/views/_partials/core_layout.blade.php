<div class="panel panel-default custompanel m-t-20">
    <div class="panel-heading">Select Page Layout</div>
    <div class="panel-body">
        <div class="row">
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
            <div class="col-md-12 m-b-10">
                {!! BBbutton2('layouts','layout_id','back_pages_layout',(isset($page) && $page->layout_id)?'Change':'Select',['class'=>'btn btn-default change-layout','model' =>(isset($page) && $page->layout_id)?$page->layout_id:null]) !!}
            </div>
        </div>
    </div>
</div>
