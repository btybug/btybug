<div class="sidebar-nav navbar-collapse">
    <ul class="nav metismenu" id="side-menu">
        <li {{ (Request::is(
        '/admin/') ? 'class="active"' : '') }}> <a href="{{ url ('/admin/') }}"><i class="fa fa-dashboard fa-fw"></i>
            <span class="htext">&nbsp;{{trans('admin/lnav.dashboard')}}</span></a> </li>
        @if($menus)
        @foreach($menus as $menu)
        <li><a href="#"><i class="{{@$menu['icon']}}"></i> <span class="htext">&nbsp;{{@$menu['title']}}</span><span
                        class="fa arrow"></span></a> @if (count($menu['childs']))
            <ul class="nav nav-second-level " aria-expanded="true">
                @foreach($menu['childs'] as $key=>$val)
                <li><a href="/{{$val}}"><i class="fa fa-angle-right"></i> &nbsp;{{$key}}</a></li>
                @endforeach
            </ul>
            @endif
        </li>
        @endforeach
        @endif
    </ul>
</div>
