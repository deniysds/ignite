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
    </head>
    <body class="flex h-full text-base antialiased metronic kt-sidebar-fixed kt-header-fixed bg-background text-foreground">
        @include('partials.theme-toggle')

        <!-- Page -->
        <!-- Main -->
        <div class="flex grow">
            @include('layouts.base.sidebar')

            <!-- Wrapper -->
            <div class="flex flex-col kt-wrapper grow">
                @include('layouts.base.header')

                <!-- Content -->
                <main class="pt-5 grow" id="content" role="content">
                    @yield('content')
                </main>
                <!-- End of Content -->

                @include('layouts.base.footer')
            </div>
            <!-- End of Wrapper -->
        </div>
        <!-- End of Main -->
        <!-- End of Page -->

        @include('layouts.partials.scripts')
    </body>
</html>
