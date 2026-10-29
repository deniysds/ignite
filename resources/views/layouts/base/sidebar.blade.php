<!-- Sidebar -->
<div class="kt-sidebar fixed bottom-0 top-0 z-20 hidden shrink-0 flex-col items-stretch border-e border-e-border bg-background [--kt-drawer-enable:true] lg:flex lg:[--kt-drawer-enable:false]"
    data-kt-drawer="true" data-kt-drawer-class="top-0 bottom-0 kt-drawer kt-drawer-start" id="sidebar">
    <div class="hidden relative justify-between items-center px-3 kt-sidebar-header shrink-0 lg:flex lg:px-6"
        id="sidebar_header">
        <a href="{{ route('dashboard') }}">
            <img class="max-w-none default-logo min-h-5.5 h-[53.55px]" src="{{ env('APP_LOGO') }}"/>
            <img class="max-w-none h-5.5 small-logo" src="{{ env('APP_LOGO_MINI') }}"/>
        </a>
        <button
            class="kt-btn kt-btn-outline kt-btn-icon absolute start-full top-2/4 size-[30px] -translate-x-2/4 -translate-y-2/4 rtl:translate-x-2/4"
            data-kt-toggle="body" data-kt-toggle-class="kt-sidebar-collapse" id="sidebar_toggle">
            <i
                class="transition-all duration-300 ki-filled ki-black-left-line kt-toggle-active:rotate-180 rtl:translate rtl:kt-toggle-active:rotate-0 rtl:rotate-180">
            </i>
        </button>
    </div>
    <div class="flex py-5 kt-sidebar-content shrink-0 grow pe-2" id="sidebar_content">
        <div class="flex kt-scrollable-y-hover shrink-0 grow pe-1 ps-2 lg:pe-3 lg:ps-5" data-kt-scrollable="true"
            data-kt-scrollable-dependencies="#sidebar_header" data-kt-scrollable-height="auto"
            data-kt-scrollable-offset="0px" data-kt-scrollable-wrappers="#sidebar_content" id="sidebar_scrollable">
            <!-- Sidebar Menu -->
            <div class="flex flex-col gap-1 kt-menu grow" data-kt-menu="true" data-kt-menu-accordion-expand-all="false"
                id="sidebar_menu">
                @include('layouts.base.sidebar-menu')
            </div>
            <!-- End of Sidebar Menu -->
        </div>
    </div>
</div>
<!-- End of Sidebar -->
