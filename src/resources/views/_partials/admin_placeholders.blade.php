<div class="panel panel-default custompanel m-t-20">
    <div class="panel-heading">Theme and Layout Options</div>
    <div class="panel-body">
        <div class="row">

            <div class="col-md-12">
                {{Form::hidden('header',0)}}
                <label class="bd_layout pull-left m-r-15">
                    {!! Form::checkbox('header',1,(isset($_this['header']) && $_this['header'])?1:0,['style' => 'position:initial;z-index:1;']) !!}
                    <span class="labls">Header</span>
                </label>
            </div>
            <div class="col-md-12 layout-data">
                <div>
                    <div class="col-sm-4 p-l-0">Header</div>
                    <div class="col-sm-5 p-l-0 p-r-10">
                        <input name="selcteunit" data-key="title" readonly="readonly" data-id=""
                               class="page-header-title form-control"
                               value="{!! (isset($_this['header_unit']) && $_this['header_unit'])?BBgetUnitAttr($_this['header_unit'],'title'):null !!}">
                    </div>
                    {!! BBbutton2('unit','header_unit','backend_header','Change',['class'=>'btn btn-info change-header','model' => (isset($_this['header_unit']) && $_this['header_unit'])?$_this['header_unit']:null]) !!}
                </div>
                <div>
                    <div class="col-sm-4 p-l-0">Content Layout</div>
                    <div class="col-sm-5 p-l-0 p-r-10">
                        <input name="selcteunit" data-key="title" readonly="readonly" data-id=""
                               class="page-layout-title form-control"
                               value="{!! (isset($_this['backend_page_section']) && $_this['backend_page_section'])?BBgetLayoutAttr($_this['backend_page_section'],'title'):null !!}">
                    </div>
                    {!! BBbutton2('layouts','backend_page_section','backend','Change',['class'=>'btn btn-info change-layout','model' => (isset($_this['backend_page_section']) && $_this['backend_page_section'])?$_this['backend_page_section']:null]) !!}
                </div>
                <div id="placeholders">
                    @if($_this)
                        @foreach($_this['placeholders'] as $key => $placeholder)
                            <div>
                                <div class="col-sm-4 p-l-0">
                                    {{Form::hidden("placeholders[".$key."][enable]",0)}}
                                    {{ Form::checkbox("placeholders[".$key."][enable]",1,($placeholder["enable"])? 1 : 0,['style' => 'position:initial;']) }}
                                    {!! $key !!}
                                </div>
                                <div class="col-sm-5 p-l-0 p-r-10">
                                    <input name="selcteunit" data-key="title" readonly="readonly" data-id="{!! $placeholder['value'] !!}"
                                           class="page-layout-title form-control"
                                           value="{!! BBgetUnitAttr($placeholder['value'],'title') !!}">
                                </div>
                                {!! BBbutton(BBgetUnitAttr($placeholder['value'],'self_type'),
                                    "placeholders[".$key."][value]","Change",
                                ['class'=>'btn btn-default change-layout','data-type'=>BBgetUnitAttr($placeholder['value'],'self_type'),
                                'model'=>$placeholder['value']]) !!}
                            </div>

                        @endforeach
                    @endif


                </div>
            </div>
        </div>
    </div>
</div>