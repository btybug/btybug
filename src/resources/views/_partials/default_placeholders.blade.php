<div class="panel panel-default custompanel m-t-20">
    <div class="panel-heading">Select Page Layout</div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-12 m-b-10">
                {!! BBbutton2('layouts','page_layout','front_pages_layout',(isset($system) && $system['page_layout'])?'Change':'Select',['class'=>'form-control input-md btn btn-info change-layout','model' =>(isset($system) && $system['page_layout'])?$system['page_layout']:null]) !!}
            </div>
        </div>
    </div>
</div>

<div class="panel panel-default custompanel m-t-20">
    <div class="panel-heading">Select Page Layout Placeholders</div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-12 layout-data">
                <div id="placeholders">
                    @if($_this)
                        @foreach($_this->placeholders as $key=>$placeholder)
                                                       @if(isset($placeholder['tag']))
                                {!! BBbutton2('unit',$key,$placeholder['tag'],"Change",['class'=>'form-control input-md btn btn-info change-layout','data-type'=>$placeholder['tag'],'data-name-prefix'=>'placeholders','model'=>($system['placeholders'][$key])??null]) !!}
                            @endif
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

