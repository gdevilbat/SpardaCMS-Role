@can('menu-role')
    <li class="m-menu__item  {{strstr(Route::current()->getName(), 'cms.role') ? 'm-menu__item--active' : ''}}" aria-haspopup="true">
        <a href="{{route('cms.role.master')}}" class="m-menu__link ">
            <i class="m-menu__link-icon flaticon-safe-shield-protection"></i>
            <span class="m-menu__link-title"> 
                <span class="m-menu__link-wrap"> 
                    <span class="m-menu__link-text">
                        Role
                    </span>
                 </span>
             </span>
         </a>
    </li>
@endcan