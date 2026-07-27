{{-- Menyiapkan urutan section, status aktif, dan helper format judul menu untuk sidebar dinamis. --}}
@php
    $menus = isset($menus) ? json_decode(json_encode($menus)) : new \stdClass();
    $mainSection = ['main'];
    $fixedEndSections = ['otorisator', 'laporan', 'master', 'system'];

    $allSections = array_keys((array) $menus);
    $dynamicSections = array_values(array_diff($allSections, array_merge($mainSection, $fixedEndSections)));
    $sectionOrder = array_merge($mainSection, $dynamicSections, $fixedEndSections);

    $defaultSectionTitles = [
        'main' => 'Apps',
        'otorisator' => 'Approval',
        'laporan' => 'Laporan',
        'master' => 'Master Data',
        'system' => 'Systems',
    ];

    $currentRouteName = request()->route()?->getName();
    $isHomeActive = url()->current() === url('/');

    $formatMenuTitle = function (?string $title): string {
        $title = trim((string) $title);

        if ($title === '') {
            return '';
        }

        if (mb_strlen($title) <= 30) {
            return e($title);
        }

        $words = preg_split('/\s+/', $title) ?: [];
        $lines = [];
        $currentLine = '';

        foreach ($words as $word) {
            $candidate = $currentLine === '' ? $word : $currentLine . ' ' . $word;

            if (mb_strlen($candidate) <= 30 || $currentLine === '') {
                $currentLine = $candidate;
                continue;
            }

            $lines[] = e($currentLine);
            $currentLine = $word;
        }

        if ($currentLine !== '') {
            $lines[] = e($currentLine);
        }

        return implode('<br>', $lines);
    };
@endphp

{{-- Merender menu dashboard dan seluruh menu dinamis berdasarkan role user aktif. --}}
<div class="kt-menu-item {{ $isHomeActive ? 'active' : '' }}">
    <a class="kt-menu-link kt-menu-item-active:bg-accent/60 dark:menu-item-active:border-border kt-menu-item-active:rounded-lg hover:bg-accent/60 grow items-center gap-[10px] border border-transparent py-[6px] pe-[10px] ps-[10px] hover:rounded-lg"
        href="{{ url('/') }}">
        <span
            class="kt-menu-icon w-[20px] items-start text-muted-foreground kt-menu-item-active:text-primary kt-menu-link-hover:!text-primary">
            <i class="text-lg ki-filled ki-element-11"></i>
        </span>
        <span
            class="kt-menu-title kt-menu-item-active:text-primary kt-menu-link-hover:!text-primary text-sm font-medium text-foreground">
            Dashboards
        </span>
    </a>
</div>

@foreach ($sectionOrder as $section)
    @if (!empty($menus->$section))
        @php
            $hasVisibleItems = false;

            foreach ($menus->$section as $menu) {
                if (auth()->user()->hasRole($menu->roles)) {
                    $hasVisibleItems = true;
                    break;
                }
            }
        @endphp

        @if ($hasVisibleItems)
            <div class="pb-px kt-menu-item pt-2.25">
                <span class="kt-menu-heading pe-[10px] ps-[10px] text-xs font-medium uppercase text-muted-foreground">
                    {{ $defaultSectionTitles[$section] ?? ucfirst($section) }}
                </span>
            </div>

            @foreach ($menus->$section as $menu)
                @if (auth()->user()->hasRole($menu->roles))
                    @if (isset($menu->sub))
                        <div class="kt-menu-item {{ request()->routeIs($menu->path) || request()->routeIs($menu->path . '.*') ? 'show active' : '' }}"
                            data-kt-menu-item-toggle="accordion" data-kt-menu-item-trigger="click">
                            <div class="kt-menu-link flex grow cursor-pointer items-center gap-[10px] border border-transparent py-[6px] pe-[10px] ps-[10px]"
                                tabindex="0">
                                <span class="kt-menu-icon w-[20px] items-start text-muted-foreground">
                                    <i class="{{ $menu->icon ?? 'ki-filled ki-element-11 text-lg' }}"></i>
                                </span>
                                <span
                                    class="kt-menu-title kt-menu-item-active:text-primary kt-menu-link-hover:!text-primary text-sm font-medium text-foreground">
                                    {!! $formatMenuTitle($menu->title) !!}
                                </span>
                                <span
                                    class="kt-menu-arrow me-[-10px] ms-1 w-[20px] shrink-0 justify-end text-muted-foreground">
                                    <span class="inline-flex kt-menu-item-show:hidden">
                                        <i class="ki-filled ki-plus text-[11px]"></i>
                                    </span>
                                    <span class="hidden kt-menu-item-show:inline-flex">
                                        <i class="ki-filled ki-minus text-[11px]"></i>
                                    </span>
                                </span>
                            </div>

                            @if (is_array($menu->sub))
                                <div
                                    class="kt-menu-accordion relative gap-1 ps-[10px] before:absolute before:bottom-0 before:start-[20px] before:top-0 before:border-s before:border-border">
                                    @foreach ($menu->sub as $sub)
                                        @if (auth()->user()->hasRole($sub->roles))
                                            <div
                                                class="kt-menu-item {{ request()->routeIs($sub->path . '.*') && in_array($currentRouteName, [$sub->path . '.index', $sub->path . '.create', $sub->path . '.edit', $sub->path . '.restore']) ? 'active' : '' }}">
                                                <a class="kt-menu-link kt-menu-item-active:bg-accent/60 dark:menu-item-active:border-border kt-menu-item-active:rounded-lg hover:bg-accent/60 grow items-center gap-[14px] border border-transparent py-[8px] pe-[10px] ps-[10px] hover:rounded-lg"
                                                    href="{{ $sub->path ? route($sub->path . '.index') : '' }}"
                                                    tabindex="0">
                                                    <span
                                                        class="kt-menu-bullet kt-menu-item-active:before:bg-primary kt-menu-item-hover:before:bg-primary relative -start-[3px] flex w-[6px] before:absolute before:top-0 before:size-[6px] before:-translate-y-1/2 before:rounded-full rtl:start-0 rtl:before:translate-x-1/2">
                                                    </span>
                                                    <span
                                                        class="kt-menu-title text-2sm kt-menu-item-active:text-primary kt-menu-item-active:font-semibold kt-menu-link-hover:!text-primary font-normal text-foreground">
                                                        {{ $sub->title }}
                                                    </span>
                                                </a>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="kt-menu-item {{ request()->routeIs($menu->path . '.*') ? 'active' : '' }}">
                            <a class="kt-menu-link kt-menu-item-active:bg-accent/60 dark:menu-item-active:border-border kt-menu-item-active:rounded-lg hover:bg-accent/60 grow items-center gap-[10px] border border-transparent py-[6px] pe-[10px] ps-[10px] hover:rounded-lg"
                                href="{{ $menu->path ? route($menu->path . '.index') : '' }}">
                                <span
                                    class="kt-menu-icon w-[20px] items-start text-muted-foreground kt-menu-item-active:text-primary kt-menu-link-hover:!text-primary">
                                    <i class="{{ $menu->icon ?? 'ki-filled ki-element-11 text-lg' }}"></i>
                                </span>
                                <span
                                    class="kt-menu-title kt-menu-item-active:text-primary kt-menu-link-hover:!text-primary text-sm font-medium text-foreground">
                                    {!! $formatMenuTitle($menu->title) !!}
                                </span>
                            </a>
                        </div>
                    @endif
                @endif
            @endforeach
        @endif
    @endif
@endforeach
