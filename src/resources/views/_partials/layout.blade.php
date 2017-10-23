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
                {{--<div class="col-sm-4 p-l-0">Page Layout</div>--}}
                {{--<div class="col-sm-5 p-l-0 p-r-10">--}}
                    {{--<input name="selcteunit" data-key="title" readonly="readonly" class="page-layout-title form-control"--}}
                           {{--value="@if($_this){!! $_this->title !!}@else{!! 'Nothing selected' !!} @endif">--}}
                {{--</div>--}}
                {!! BBbutton2('layouts','page_layout','front_pages_layout',(isset($page) && $page->page_layout)?'Change':'Select',['class'=>'btn btn-default change-layout','model' =>(isset($page) && $page->page_layout)?$page->page_layout:null]) !!}

            </div>
        </div>
    </div>
</div>
