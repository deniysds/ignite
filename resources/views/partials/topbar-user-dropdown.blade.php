<!-- User -->
<div class="shrink-0" data-kt-dropdown="true" data-kt-dropdown-offset="10px, 10px" data-kt-dropdown-offset-rtl="-20px, 10px"
    data-kt-dropdown-placement="bottom-end" data-kt-dropdown-placement-rtl="bottom-start" data-kt-dropdown-trigger="click">
    <div class="cursor-pointer shrink-0" data-kt-dropdown-toggle="true">
        <img alt="" class="rounded-full border-2 border-green-500 size-9 shrink-0"
            src="{{ asset('assets/media/avatars/300-2.png') }}" />
    </div>
    <div class="kt-dropdown-menu w-[250px]" data-kt-dropdown-menu="true">
        <div class="flex gap-1.5 justify-between items-center px-2.5 py-1.5">
            <div class="flex gap-2 items-center">
                <img alt="" class="rounded-full border-2 border-green-500 size-9 shrink-0"
                    src="{{ asset('assets/media/avatars/300-2.png') }}" />
                <div class="flex flex-col gap-1.5 max-w-[150px]">
                    <span class="text-sm font-semibold leading-none text-foreground truncate">
                        {{ auth()->user()->name ?? 'Pengguna' }}
                    </span>
                    <a class="text-xs font-medium leading-none hover:text-primary text-secondary-foreground truncate"
                        href="{{ route('users.profile') }}">
                        {{ auth()->user()->email ?? '' }}
                    </a>
                </div>
            </div>
            <span class="kt-badge kt-badge-sm kt-badge-primary kt-badge-outline capitalize">
                {{ auth()->user()?->roles?->pluck('name')->first() ?? 'Member' }}
            </span>
        </div>
        <ul class="kt-dropdown-menu-sub">
            <li>
                <div class="kt-dropdown-menu-separator">
                </div>
            </li>
            <li>
                <a class="kt-dropdown-menu-link" href="{{ route('dashboard') }}">
                    <i class="ki-filled ki-element-11">
                    </i>
                    Dashboard
                </a>
            </li>
            <li>
                <a class="kt-dropdown-menu-link" href="{{ route('users.profile') }}">
                    <i class="ki-filled ki-profile-circle">
                    </i>
                    Profil Saya
                </a>
            </li>
            <li data-kt-dropdown="true" data-kt-dropdown-placement="right-start" data-kt-dropdown-trigger="hover">
                <button class="kt-dropdown-menu-toggle" data-kt-dropdown-toggle="true">
                    <i class="ki-filled ki-setting-2">
                    </i>
                    My Account
                    <span class="kt-dropdown-menu-indicator">
                        <i class="text-xs ki-filled ki-right">
                        </i>
                    </span>
                </button>
                <div class="kt-dropdown-menu w-[220px]" data-kt-dropdown-menu="true">
                    <ul class="kt-dropdown-menu-sub">
                        <li>
                            <a class="kt-dropdown-menu-link" href="#">
                                <i class="ki-filled ki-coffee">
                                </i>
                                Get Started
                            </a>
                        </li>
                        <li>
                            <a class="kt-dropdown-menu-link" href="#">
                                <i class="ki-filled ki-some-files">
                                </i>
                                My Profile
                            </a>
                        </li>
                        <li>
                            <a class="kt-dropdown-menu-link" href="#">
                                <span class="flex gap-2 items-center">
                                    <i class="ki-filled ki-icon">
                                    </i>
                                    Billing
                                </span>
                                <span class="inline-flex items-center ms-auto" data-kt-tooltip="true"
                                    data-kt-tooltip-placement="top">
                                    <i class="text-base ki-filled ki-information-2 text-muted-foreground">
                                    </i>
                                    <span class="kt-tooltip" data-kt-tooltip-content="true">
                                        Payment and subscription info
                                    </span>
                                </span>
                            </a>
                        </li>
                        <li>
                            <a class="kt-dropdown-menu-link" href="#">
                                <i class="ki-filled ki-medal-star">
                                </i>
                                Security
                            </a>
                        </li>
                        <li>
                            <a class="kt-dropdown-menu-link" href="#">
                                <i class="ki-filled ki-setting">
                                </i>
                                Members & Roles
                            </a>
                        </li>
                        <li>
                            <a class="kt-dropdown-menu-link" href="#">
                                <i class="ki-filled ki-switch">
                                </i>
                                Integrations
                            </a>
                        </li>
                        <li>
                            <div class="kt-dropdown-menu-separator">
                            </div>
                        </li>
                        <li>
                            <a class="kt-dropdown-menu-link" href="#">
                                <span class="flex gap-2 items-center">
                                    <i class="ki-filled ki-shield-tick">
                                    </i>
                                    Notifications
                                </span>
                                <input checked="" class="kt-switch ms-auto" name="check" type="checkbox"
                                    value="1" />
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <li data-kt-dropdown="true" data-kt-dropdown-placement="right-start" data-kt-dropdown-trigger="hover">
                <button class="py-1 kt-dropdown-menu-toggle" data-kt-dropdown-toggle="true">
                    <span class="flex gap-2 items-center">
                        <i class="ki-filled ki-icon">
                        </i>
                        Language
                    </span>
                    <span class="kt-badge kt-badge-stroke ms-auto shrink-0">
                        English
                        <img alt="" class="inline-block rounded-full size-3.5"
                            src="{{ asset('assets/media/flags/united-states.svg') }}" />
                    </span>
                </button>
                <div class="kt-dropdown-menu w-[180px]" data-kt-dropdown-menu="true">
                    <ul class="kt-dropdown-menu-sub">
                        <li class="active">
                            <a class="kt-dropdown-menu-link" href="?dir=ltr">
                                <span class="flex gap-2 items-center">
                                    <img alt="" class="inline-block rounded-full size-4"
                                        src="{{ asset('assets/media/flags/united-states.svg') }}" />
                                    <span class="kt-menu-title">
                                        English
                                    </span>
                                </span>
                                <i class="text-base text-green-500 ki-solid ki-check-circle ms-auto">
                                </i>
                            </a>
                        </li>
                        <li class="">
                            <a class="kt-dropdown-menu-link" href="?dir=rtl">
                                <span class="flex gap-2 items-center">
                                    <img alt="" class="inline-block rounded-full size-4"
                                        src="{{ asset('assets/media/flags/indonesia.svg') }}" />
                                    <span class="kt-menu-title">
                                        Indonesia
                                    </span>
                                </span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <li>
                <div class="kt-dropdown-menu-separator">
                </div>
            </li>
        </ul>
        <div class="flex flex-col gap-3.5 px-2.5 pt-1.5 mb-2.5">
            <div class="flex gap-2 justify-between items-center">
                <span class="flex gap-2 items-center">
                    <i class="text-base ki-filled ki-moon text-muted-foreground">
                    </i>
                    <span class="font-medium text-2sm">
                        Dark Mode
                    </span>
                </span>
                <input class="kt-switch" data-kt-theme-switch-state="dark" data-kt-theme-switch-toggle="true"
                    name="check" type="checkbox" value="1" />
            </div>
            <a class="justify-center w-full kt-btn kt-btn-outline"
                href="{{ route('logout') }}">
                Log out
            </a>
        </div>
    </div>
</div>
<!-- End of User -->
