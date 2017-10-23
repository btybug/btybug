<ul class="nav icontabss nav-tabs" role="tablist">
    @foreach($fonts as $key => $font)
        <li role="presentation" class="loadtabdata{{ $key }} {{ ($key ==0) ? "active" : ""}}">
            <a href="#icontab{{ $key }}"
               aria-controls="icontab{{ $key }}"
               data-folder="{{ $font['folder'] }}"
               data-path="{{ $key }}" role="tab"
               title="{{ $font['title'] }}" data-toggle="tab">{{ $font['folder'] }}
            </a>
        </li>
    @endforeach
</ul>
<div class="tab-content iconmoduls">
    @foreach($fonts as $key => $font)
        @if(count($font['items']))
            <div role="tabpanel" class="tab-pane p-10 {{ ($key ==0) ? "active" : ""}}" id="icontab{{ $key }}">
                <div class="form-horizontal">
                    <div class="form-group">
                        <div class="col-sm-12">
                            <ul class="iconsliststduio">
                                @foreach($font['items']['list'] as $fkey => $fvalue)
                                    <li>
                                        <a href="#" class="selectIcons item" data-csspr="icon" data-csspro="icon"
                                           data-val="{{ $font['items']['config']->prefix }} {{ $fkey }}">
                                            <i class="{{ $font['items']['config']->prefix }} {{ $fkey }}"></i>
                                            <input type="hidden" data-action="icons"
                                                   data-value="{{ $font['items']['config']->prefix }} {{ $fkey }}"/>
                                        </a>

                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
</div>

<div class="m-t-20">
    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                <label for="textcoloricon">Color Icon</label>
                <input type="text" class="form-control" id="textcoloricon">
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <label for="iconRotation">Rotation</label>
                <div class="btn-group col-sm-12 colpadding0" data-toggle="buttons">
                    <label class="btn btn-primary btn-black">
                        <input type="radio" name="rotation" value="" autocomplete="off" checked=""> No
                    </label>
                    <label class="btn btn-primary btn-black">
                        <input type="radio" name="rotation" value="bb-rotate-90" autocomplete="off"> 90
                    </label>
                    <label class="btn btn-primary btn-black">
                        <input type="radio" name="rotation" value="bb-rotate-180" autocomplete="off"> 180
                    </label>
                    <label class="btn btn-primary btn-black active">
                        <input type="radio" name="rotation" value="bb-rotate-270" autocomplete="off"> 270
                    </label>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <label for="iconRotation">Icon Size</label>
        </div>
    </div>
</div>

<style>
    .colpadding0 {
        padding: 0;
    }

    .iconsliststduio {
        margin: 0 -4px;
        padding: 0;
        list-style: none;
        display: block;
        overflow: auto;
        max-height: 280px;
    }

    .iconsliststduio li {
        float: left;
        margin: 4px;
        text-align: center;
        border: 1px solid #ccc;
        border-radius: 2px;
        background-color: #fff;
    }

    .iconsliststduio li > a {
        width: 30px;
        height: 30px;
        line-height: 30px;
        display: block;
    }

    .iconsliststduio li > a > i {
        vertical-align: middle;
    }

    .nav-tabs > li {
        float: left;
        margin-bottom: -1px;
    }

    .fa, .bb {
        display: inline-block;
        font: normal normal normal 14px/1 FontAwesome;
        font-size: inherit;
        text-rendering: auto;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }
</style>
