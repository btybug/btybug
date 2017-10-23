<div class="sidebar-nav navbar-collapse">
    <ul class="nav metismenu" id="side-menu">
        <li {{ (Request::is(
        '/admin/') ? 'class="active"' : '') }}> <a href="{{ url ('/admin/') }}"><i class="fa fa-dashboard fa-fw"></i>
            <span class="htext">&nbsp;{{trans('admin/lnav.dashboard')}}</span></a> </li>
        <li><a href="#"><i class="fa fa-cubes"></i> <span
                        class="htext">&nbsp;{{trans('admin/lnav.packeges')}}</span><span class="fa arrow"></span></a>
            <ul class="nav nav-second-level " aria-expanded="true">
                <li><a href="{{ url('admin/frontend/templates') }}"><i class="fa fa-angle-right"></i>&nbsp;{{trans('admin/lnav.templates')}}</a>
                </li>
                <li><a href="{{ url('admin/frontend/templates/email') }}"><i class="fa fa-angle-right"></i>&nbsp;{{trans('admin/lnav.email_templates')}}</a>
                </li>
                <li><a href="{{ url('admin/modules') }}"><i class="fa fa-angle-right"></i>&nbsp;{{trans('admin/lnav.modules')}}</a>
                </li>
                <li><a href="{{ url('admin/plugins') }}"><i class="fa fa-angle-right"></i>&nbsp;{{trans('admin/lnav.plugins')}}</a>
                </li>
            </ul>
        </li>
        @if(Auth::user()->can('users'))
        <li {{( $segment=='users') ?
        'class=active' : '' }}> <a href="#"><i class="fa fa-users"></i> <span class="htext">&nbsp;{{trans('admin/lnav.users')}}</span><span
                    class="fa arrow"></span></a>
        <ul class="nav nav-second-level " aria-expanded="true">
            @if(Auth::user()->can('users.site_users'))
            <li><a href="{{ url('admin/users') }}"><i class="fa fa-angle-right"></i> &nbsp;{{trans('admin/lnav.site_users')}}</a>
            </li>
            @endif

            @if(Auth::user()->can('users.admins'))
            <li><a href="{{ url('admin/users/admins') }}"><i class="fa fa-angle-right"></i> &nbsp;{{trans('admin/lnav.admins')}}</a>
            </li>
            @endif
            <li><a href="{{ url('admin/users/configuration') }}"><i class="fa fa-angle-right"></i> &nbsp;{{trans('admin/lnav.admin_role')}}</a>
            </li>
        </ul>
        <!-- /.nav-second-level -->
        </li>
        @endif
        <li {{( $segment=='media') ?
        'class=active' : '' }}> <a href="#"><i class="fa fa-film"></i> <span class="htext">&nbsp;{{trans('admin/lnav.media')}}</span><span
                    class="fa arrow"></span></a>
        <ul class="nav nav-second-level " aria-expanded="true">
            <li><a href="{{ url('admin/media/setting') }}"><i class="fa fa-angle-right"></i> &nbsp;{{trans('admin/lnav.settings')}}</a>
            </li>
            <li><a href="{{ url('admin/media/') }}"><i class="fa fa-angle-right"></i> &nbsp;{{trans('admin/lnav.all_media')}}</a>
            </li>
        </ul>
        <!-- /.nav-second-level -->
        </li>
        <li {{( $segment=='create') ?
        'class=active' : '' }}> <a href="#"><i class="fa fa-laptop"></i> <span class="htext">&nbsp;{{trans('admin/lnav.create')}}</span><span
                    class="fa arrow"></span></a>
        <ul class="nav nav-second-level " aria-expanded="true">
            <li><a href="{{ url('admin/create/form') }}"><i class="fa fa-angle-right"></i> &nbsp;{{trans('admin/lnav.forms')}}</a>
            </li>
            <li><a href="{{ url('admin/create/field') }}"><i class="fa fa-angle-right"></i> &nbsp;{{trans('admin/lnav.fields')}}</a>
            </li>
            <li><a href="{{ url('admin/create/assests') }}"><i class="fa fa-angle-right"></i> &nbsp;{{trans('admin/lnav.assets')}}</a>
            </li>
            <li><a href="{{ url('admin/create/custompage') }}"><i class="fa fa-angle-right"></i> &nbsp;{{trans('admin/lnav.custom_pages')}}</a>
            </li>
            <li><a href="{{ url('admin/create/widgets') }}"><i class="fa fa-angle-right"></i> &nbsp;{{trans('admin/lnav.widgets')}}</a>
            </li>
            <li><a href="{{ url('admin/create/sidebar') }}"><i class="fa fa-angle-right"></i> &nbsp;{{trans('admin/lnav.side_bars')}}</a>
            </li>
            <li><a href="{{ url('admin/create/menu') }}"><i class="fa fa-angle-right"></i> &nbsp;{{trans('admin/lnav.menus')}}</a>
            </li>
            <li><a href="{{ url('admin/create/amenu') }}"><i class="fa fa-angle-right"></i> &nbsp;Admin Menu</a></li>
        </ul>
        </li>
        <li {{( $segment=='settings') ?
        'class=active' : '' }}> <a href="#"><i class="fa fa-cog"></i><span class="htext">&nbsp; {{trans('admin/lnav.settings')}}</span><span
                    class="fa arrow"></span></a>
        <ul class="nav nav-second-level " aria-expanded="true">
            <li><a href="{{ url('admin/settings/setting') }}"><i class="fa fa-angle-right"></i> &nbsp;{{trans('admin/lnav.front_end')}}</a>
            </li>
            <li><a href="{{ url('admin/settings/backsetting') }}"><i class="fa fa-angle-right"></i> &nbsp;{{trans('admin/lnav.back_end')}}</a>
            </li>
            <li><a href="{{ url('admin/settings/shortcode') }}"><i class="fa fa-angle-right"></i> &nbsp;{{trans('admin/lnav.short_codes')}}</a>
            </li>
            <li><a href="{{ url('admin/settings/coreassets') }}"><i class="fa fa-angle-right"></i> &nbsp;{{trans('admin/lnav.core_assets')}}</a>
            </li>
            <li><a href="{{ url('admin/settings/email') }}"><i class="fa fa-angle-right"></i> &nbsp;{{trans('admin/lnav.emails')}}</a>
            </li>
            <li><a href="{{ url('admin/settings/lang') }}"><i class="fa fa-angle-right"></i> &nbsp;{{trans('admin/lnav.language')}}</a>
            </li>
            <li><a href="{{ url('admin/settings/system') }}"><i class="fa fa-angle-right"></i> &nbsp;{{trans('admin/lnav.system')}}</a>
            </li>
        </ul>
        <!-- /.nav-second-level -->
        </li>
        <li {{( $segment=='assets') ?
        'class=active' : '' }}> <a href="#"><i class="fa fa-pencil-square-o"></i><span class="htext"> &nbsp;{{trans('admin/lnav.assets')}}</span><span
                    class="fa arrow"></span></a>
        <ul class="nav nav-second-level " aria-expanded="true">
            <li><a href="{{ url('admin/assets/core_assest') }}"><i class="fa fa-angle-right"></i> &nbsp;{{trans('admin/lnav.core_Assests_Libs')}}</a>
            </li>
            <li><a href="{{ url('admin/assets/page') }}"><i class="fa fa-angle-right"></i> &nbsp;{{trans('admin/lnav.core_pages')}}</a>
            </li>
            <li><a href="{{ url('admin/assets/tag') }}"><i class="fa fa-angle-right"></i> &nbsp;{{trans('admin/lnav.tags')}}</a>
            </li>
            <li><a href="{{ url('admin/assets/blog') }}"><i class="fa fa-angle-right"></i> &nbsp;{{trans('admin/lnav.blog')}}</a>
            </li>
            <li><a href="{{ url('admin/assets/taxonomy') }}"><i class="fa fa-angle-right"></i> &nbsp;{{trans('admin/lnav.taxonomy')}}</a>
            </li>
        </ul>
        <!-- /.nav-second-level -->
        </li>
        <li {{( $segment=='tools') ?
        'class=active' : '' }}> <a href="#"><i class="fa fa-wrench"></i><span class="htext"> &nbsp;{{trans('admin/lnav.tools')}}</span><span
                    class="fa arrow"></span></a>
        <ul class="nav nav-second-level " aria-expanded="true">
            <li><a href="{{ url('admin/tools/all-contents') }}"><i class="fa fa-angle-right"></i> &nbsp;{{trans('admin/lnav.contents')}}</a>
            </li>
            <li><a href="{{ url('admin/tools/shortcodes') }}"><i class="fa fa-angle-right"></i> &nbsp;{{trans('admin/lnav.short_codes')}}</a>
            </li>
            <li><a href="{{ url('admin/tools/button-maker') }}"><i class="fa fa-angle-right"></i> &nbsp;
                    {{trans('admin/lnav.button_maker')}}</a></li>
        </ul>
        </li>
        <li {{( $segment=='extra') ?
        'class=active' : '' }}> <a href="#"><i class="fa fa-pencil-square-o"></i><span class="htext"> &nbsp;Extra</span><span
                    class="fa arrow"></span></a>
        <ul class="nav nav-second-level " aria-expanded="true">
            @if($menus)
            @foreach($menus as $menu)
            <li><a href="{!!@$menu['link']!!}"><i class="{{@$menu['icon']}}"></i> {{@$menu['title']}}</a>
                <?php
                /*@if (isset($menu['childs']))<span class="fa arrow"></span>@endif
                @if (count($menu['childs']))
                   <ul class="nav nav-second-level " aria-expanded="true">
                      @foreach($menu['childs'] as $key=>$val)
                        <li><a href="{{$val['link']}}"><i class="fa fa-angle-right"></i> &nbsp;{{$val['title']}}</a></li>
                      @endforeach 
                   </ul>  
                @endif*/
                ?>
            </li>
            @endforeach
            @endif
        </ul>
        </li>
    </ul>
</div>
