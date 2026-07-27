<div>
    <!-- Logo -->
    <div class="flex items-center gap-2 lg:gap-5 2xl:-ml-[60px]">
        <a href="#">
            <img class="min-h-[42px] dark:hidden" src="{{ env('LOGO_LIGHT') }}" />
            <img class="hidden min-h-[42px] dark:inline-block" src="{{ env('LOGO_DARK') }}" />
        </a>
        <div class="flex items-center">
            <h3 class="hidden text-base text-secondary-foreground md:block">
                MetronicTeam
            </h3>
            <span class="hidden px-2.5 text-sm font-medium text-muted-foreground md:inline">
                /
            </span>
            <div class="kt-menu kt-menu-default" data-kt-menu="true">
                <div class="kt-menu-item" data-kt-menu-item-offset="0, 10px" data-kt-menu-item-placement="bottom-start"
                    data-kt-menu-item-toggle="dropdown" data-kt-menu-item-trigger="hover">
                    <button class="font-medium kt-menu-toggle text-mono">
                        Account
                        <span class="kt-menu-arrow">
                            <i class="ki-filled ki-down">
                            </i>
                        </span>
                    </button>
                    <div class="w-48 kt-menu-dropdown kt-menu-default">
                        <div class="kt-menu-item">
                            <a class="kt-menu-link" href="#"
                                tabindex="0">
                                <span class="kt-menu-icon">
                                    <i class="ki-filled ki-profile-circle">
                                    </i>
                                </span>
                                <span class="kt-menu-title">
                                    Public Profile
                                </span>
                            </a>
                        </div>
                        <div class="kt-menu-item active">
                            <a class="kt-menu-link" href="#" tabindex="0">
                                <span class="kt-menu-icon">
                                    <i class="ki-filled ki-setting-2">
                                    </i>
                                </span>
                                <span class="kt-menu-title">
                                    Account
                                </span>
                            </a>
                        </div>
                        <div class="kt-menu-item">
                            <a class="kt-menu-link" href="#" tabindex="0">
                                <span class="kt-menu-icon">
                                    <i class="ki-filled ki-users">
                                    </i>
                                </span>
                                <span class="kt-menu-title">
                                    Network
                                </span>
                            </a>
                        </div>
                        <div class="kt-menu-item">
                            <a class="kt-menu-link" href="#" tabindex="0">
                                <span class="kt-menu-icon">
                                    <i class="ki-filled ki-security-user">
                                    </i>
                                </span>
                                <span class="kt-menu-title">
                                    Authentication
                                </span>
                            </a>
                        </div>
                        <div class="kt-menu-item">
                            <a class="kt-menu-link" href="#" tabindex="0">
                                <span class="kt-menu-icon">
                                    <i class="ki-filled ki-handcart">
                                    </i>
                                </span>
                                <span class="kt-menu-title">
                                    Store - Client
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End of Logo -->
</div>
