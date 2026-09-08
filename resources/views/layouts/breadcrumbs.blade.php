@unless ($breadcrumbs->isEmpty())
    <ol class="kt-breadcrumb mb-5">
        <li class="kt-breadcrumb-item">
            <a href="{{ url('/') }}" class="kt-breadcrumb-link">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-house" aria-hidden="true">
                    <path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"></path>
                    <path
                        d="M3 10a2 2 0 0 1 .709-1.528l7-6a2 2 0 0 1 2.582 0l7 6A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z">
                    </path>
                </svg>
            </a>
        </li>
        <li class="kt-breadcrumb-separator">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-chevron-right" aria-hidden="true">
                <path d="m9 18 6-6-6-6"></path>
            </svg>
        </li>
        @foreach ($breadcrumbs as $breadcrumb)
            @if ($loop->first && ($breadcrumb->url === url('/') || strtolower($breadcrumb->title) === 'home'))
                @continue
            @endif

            @if (!$loop->last)
                <li class="kt-breadcrumb-item">
                    @if ($breadcrumb->url)
                        <a href="{{ $breadcrumb->url }}" class="kt-breadcrumb-link">{{ $breadcrumb->title }}</a>
                    @else
                        <span class="kt-breadcrumb-link">{{ $breadcrumb->title }}</span>
                    @endif
                </li>
                <li class="kt-breadcrumb-separator">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-chevron-right" aria-hidden="true">
                        <path d="m9 18 6-6-6-6"></path>
                    </svg>
                </li>
            @else
                <li class="kt-breadcrumb-item">
                    <span class="kt-breadcrumb-page">{{ $breadcrumb->title }}</span>
                </li>
            @endif
        @endforeach
    </ol>
@endunless