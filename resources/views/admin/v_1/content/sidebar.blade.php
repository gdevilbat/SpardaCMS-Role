<li class="m-menu__item  {{Route::current()->getName() == 'role' ? 'm-menu__item--active' : ''}}" aria-haspopup="true">
    <a href="{{action('\Gdevilbat\SpardaCMS\Modules\Role\Http\Controllers\RoleController@index')}}" class="m-menu__link ">
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