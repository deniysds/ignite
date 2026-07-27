<!-- Header -->
<header class="flex fixed top-0 z-10 items-stretch kt-header end-0 start-0 shrink-0 bg-background" data-kt-sticky="true"
    data-kt-sticky-class="border-b border-border" data-kt-sticky-name="header" id="header">
    <!-- Container -->
    <div class="flex justify-between items-stretch kt-container-fixed lg:gap-4" id="headerContainer">
        <!-- Mobile Logo -->
        <div class="flex gap-2.5 items-center -ms-1 lg:hidden">
            <a class="shrink-0" href="#">
                <img class="max-h-[25px] w-full" src="assets/media/app/mini-logo.svg" />
            </a>
            <div class="flex items-center">
                <button class="kt-btn kt-btn-icon kt-btn-ghost" data-kt-drawer-toggle="#sidebar">
                    <i class="ki-filled ki-menu">
                    </i>
                </button>
                <button class="kt-btn kt-btn-icon kt-btn-ghost" data-kt-drawer-toggle="#mega_menu_wrapper">
                    <i class="ki-filled ki-burger-menu-2">
                    </i>
                </button>
            </div>
        </div>
        <!-- End of Mobile Logo -->
        @include('partials.mega-menu')
        <!-- Topbar -->
        <div class="flex gap-2.5 items-center">
            @include('partials.topbar-notification-dropdown')
            @include('partials.topbar-user-dropdown')
        </div>
        <!-- End of Topbar -->
    </div>
    <!-- End of Container -->
</header>
<!-- End of Header -->
