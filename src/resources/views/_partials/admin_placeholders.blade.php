<div class="panel panel-default custompanel m-t-20">
    <div class="panel-heading">
        Select Page Layout Placeholders
        <a href="javascript:void(0)" data-live="live_edit"
           class="btn btn-primary pull-right full-page-view">Live Edit</a>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-12 layout-data">
                <div id="placeholders">
                    @if($_this && isset($_this->placeholders))
                        @foreach($_this->placeholders as $key=>$placeholder)
                            <div class="col-md-12">
                                @if(isset($placeholder['tag']))
                                    {!! BBbutton2('unit',$key,$placeholder['tag'],(isset($placeholder['title'])?$placeholder['title']:'Sidebar'),
                                    ['class'=>'btn btn-default change-layout','data-type'=>$placeholder['tag'],
                                    'data-name-prefix'=>'placeholders',
                                    'model'=>($page->settings[$key])??null]) !!}

                                    <a data-view="{!! ($page->settings[$key])??null !!}" class="btn btn-primary view-placeholder">View</a>
                                    <a data-reset="{!! $key !!}" class="btn btn-danger reset-placeholder">Reset</a>
                                @endif
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
