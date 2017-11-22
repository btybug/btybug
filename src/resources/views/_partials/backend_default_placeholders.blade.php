<div class="form-group">
    <div class="col-md-12">
        <label>Select Page Layout</label>
    </div>
    <div class="col-md-12">
        <div class="col-md-12 m-b-10">
            {!! BBbutton2('layouts','backend_page_section','back_pages_layout',(isset($system) && $system['backend_page_section'])?'Change':'Select',['class'=>'form-control input-md btn btn-info change-layout','model' =>(isset($system) && $system['backend_page_section'])?$system['backend_page_section']:null]) !!}
        </div>
    </div>
</div>

@if($_this)
    <div class="form-group">
        <div class="col-md-12">
            <label>Select Page Layout Placeholders</label>
        </div>
        <div class="col-md-12 layout-data">
            <div id="placeholders">

                @foreach($_this->placeholders as $key=>$placeholder)
                    @if(isset($placeholder['tag']))
                        {!! BBbutton2('unit',$key,$placeholder['tag'],isset($placeholder['title'])?$placeholder['title']:'Title Missing!!!',['class'=>'form-control input-md btn btn-info change-layout','data-type'=>$placeholder['tag'],'data-name-prefix'=>'placeholders','model'=>($system['placeholders'][$key])??null]) !!}
                    @endif
                @endforeach
            </div>
        </div>
    </div>
@endif

