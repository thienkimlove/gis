<nav class='navbar navbar-custom container-fluid' >
    <nav class="navbar container-fluid" style="background-color: #337AB7;min-width: 930px;">
      <ul class="nav navbar-nav navbar-left">
        <li><a href="{{url()}}" class="dropdown-toggle"><span class="glyphicon glyphicon-home"></span></a>
              </li>
      	<li class="dropdown ">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">{{trans("common.menu_managament_screen")}} <span class="caret"></span></a>
            <ul class="dropdown-menu" role="menu">
                    @if (!empty($menuPermissions['auth_user_registration']))
                    <li><a href="{{url('admin/users')}}">{{ trans("common.mana_2") }}</a></li>
                    <li class="divider"></li>
                    @endif

                    @if (!empty($menuPermissions['auth_change_username_password']))
                    <li><a href="{{ route('change-account') }}">{{ trans("common.mana_1") }}</a></li>
                    <li class="divider"></li>
                    @endif

                    @if (!empty($menuPermissions['auth_user_group']))
                    <li><a href="{{url('admin/groups')}}">{{ trans("common.mana_3") }}</a></li>
                    <li class="divider"></li>
                    @endif

                    @if (!empty($menuPermissions['auth_authorization']))
                        <li><a href="{{url('authorization')}}">{{ trans("common.mana_7") }}</a></li>
                        <li class="divider"></li>
                    @endif

                        @if (!empty($menuPermissions['auth_folder_layer']))
                            <li><a href="{{ route('admin.folders.index') }}">{{ trans("common.mana_6") }}</a></li>
                            <li class="divider"></li>
                        @endif

                        @if(!empty($menuPermissions['auth_user_fertilizer_definition']))
                            <li><a href="{{url('fertilizers')}}">{{ trans("common.mana_9") }}</a></li>
                            <li class="divider"></li>
                        @endif

                        @if (!empty($menuPermissions['auth_help']))
                            <li><a href="{{url('helplink')}}">{{ trans("common.mana_5") }}</a></li>
                            <li class="divider"></li>
                        @endif

                    @if (!empty($menuPermissions['auth_footer']))
                    <li><a href="{{url('footer')}}">{{ trans("common.mana_4") }}</a></li>
                    <li class="divider"></li>
                     @endif
                    <li><a href="{{ asset('helps/termOfUser.pdf') }}" target="_blank">{{ trans("common.mana_10") }}</a></li>
                    <li class="divider"></li>
            </ul>
        </li>
          @if(!empty($menuPermissions['auth_fertilizer_price']) || !empty($menuPermissions['auth_purchasing_management']))
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">{{trans("common.menu_maps")}} <span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
            <li class="divider"></li>
              @if(!empty($menuPermissions['auth_fertilizer_price']))
                  <li><a href="{{ route('admin.fertilizationprice.index') }}">{{ trans("common.maps_1") }}</a></li>
                  <li class="divider"></li>
              @endif
              @if(!empty($menuPermissions['auth_purchasing_management']))
                  <li><a href="{{ route('admin.downloadmanagement') }}">{{ trans("common.maps_2") }}</a></li>
                  <li class="divider"></li>
              @endif
          </ul>
        </li>
              @endif
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="" >
        	<span class="glyphicon glyphicon-user" aria-hidden="true"></span> {{session('user')->username}}</a></li>
        <li>
            <a href="{{url('/logout')}}">
        	<span class="glyphicon glyphicon-off"></span>
                 {{ trans("common.logout_label") }}
        	</a>
        </li>
        <li>
        <a href="javascript:void(0)" onclick="getHelp(0);">
            <span class="glyphicon glyphicon-question-sign"></span> {{ trans("common.help_label") }}</a>
        </li>
      </ul>
    </nav>
	</nav>

