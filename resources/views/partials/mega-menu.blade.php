<!--Megamenu Contaoner-->
<div class="flex items-stretch" id="megaMenuContainer">
    <!--Megamenu Inner-->
    <div class="flex items-stretch [--kt-reparent-mode:prepend] [--kt-reparent-target:body] lg:[--kt-reparent-mode:prepend] lg:[--kt-reparent-target:#megaMenuContainer]"
        data-kt-reparent="true">
        <!--Megamenu Wrapper-->
        <div class="hidden [--kt-drawer-enable:true] lg:flex lg:items-stretch lg:[--kt-drawer-enable:false]"
            data-kt-drawer="true"
            data-kt-drawer-class="kt-drawer kt-drawer-start fixed z-10 top-0 bottom-0 w-full me-5 max-w-[250px] p-5 lg:p-0 overflow-auto"
            id="mega_menu_wrapper">
            <!--Megamenu-->
            <div class="flex-col gap-3 kt-menu lg:flex-row lg:gap-5 items-center" data-kt-menu="true" id="mega_menu">
                <!--Megamenu Item-->
                <div class="kt-menu-item {{ request()->routeIs('dashboard*') ? 'active' : '' }}">
                    <a class="text-sm font-semibold kt-menu-link kt-menu-item-hover:text-primary kt-menu-item-active:text-primary text-nowrap text-foreground flex items-center gap-1.5"
                        href="{{ route('dashboard') }}">
                        <i class="ki-filled ki-element-11 text-sm text-primary"></i>
                        <span class="kt-menu-title text-nowrap">
                            Dashboard
                        </span>
                    </a>
                </div>
                <!--End of Megamenu Item-->
                <div class="kt-menu-item">
                    <a class="text-xs font-medium kt-menu-link text-muted-foreground hover:text-primary text-nowrap flex items-center gap-1"
                        href="{{ route('website.home') }}" target="_blank">
                        <i class="ki-filled ki-exit-right-corner text-xs"></i>
                        <span class="kt-menu-title text-nowrap">
                            Portal Publik &rarr;
                        </span>
                    </a>
                </div>
            </div>
            <!--End of Megamenu-->
        </div>
        <!--End of Megamenu Wrapper-->
    </div>
    <!--End of Megamenu Inner-->
</div>
<!--End of Megamenu Contaoner-->
