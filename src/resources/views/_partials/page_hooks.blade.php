<table class="table table-striped">
    <thead>
    <tr>
        <th>Name</th>
        <th>Units</th>
    </tr>
    </thead>
    <tbody>
    @if(isset($layout->settings['hook']))
        @foreach($layout->settings['hook'] as $key=>$cms_hook)
            <tr>
                <td>{!! $key !!}</td>
                <td>
                    @if(isset($cms_hook['units']))
                        @foreach($cms_hook['units'] as $unit)
                            {!! BBgetUnitAttr($unit,'title').',' !!}
                        @endforeach
                    @endif
                </td>
            </tr>
        @endforeach
    @endif
    </tbody>
</table>
