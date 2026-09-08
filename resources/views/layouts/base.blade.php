@php
    use Nwidart\Modules\Module;
    use Illuminate\Support\Facades\Route;

    $route      = explode('.',Route::currentRouteName());
	$modules    = file_get_contents(dirname(__FILE__, 4) . '/modules_statuses.json');
    $module     = json_decode($modules);
@endphp

<!DOCTYPE html>
<html class="h-full" data-kt-theme="true" data-kt-theme-mode="light" dir="ltr" lang="en">
    <head>
        @include('layouts.partials.head')
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('styles')
    </head>
    <body class="flex h-full text-base antialiased metronic bg-background text-foreground @stack('body_classes')">
        @include('partials.theme-toggle')

        @yield('main')

        @include('layouts.partials.scripts')
        @stack('scripts')
    </body>
</html>
