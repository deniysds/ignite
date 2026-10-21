@php
    use Illuminate\Support\Facades\Route;

    $route = explode('.', Route::currentRouteName());
    $modules = file_get_contents(dirname(__FILE__, 4) . '/modules_statuses.json');
    $module = json_decode($modules);
@endphp

@extends('layouts.base', ['module' => $module])

@section('main')
    <!-- Page -->
    <!-- Main -->
    <div class="flex grow min-w-0 max-w-full">

        <!-- Wrapper -->
        <div class="flex flex-col kt-wrapper grow min-w-0 max-w-full">

            <!-- Content -->
            <main class="pt-0 sm:pt-5 grow min-w-0 max-w-full" id="content" role="content">
                <div class="kt-container-fluid min-w-0 max-w-full">
                    @yield('content')
                </div>
            </main>
            <!-- End of Content -->
        </div>
        <!-- End of Wrapper -->
    </div>
    <!-- End of Main -->
    <!-- End of Page -->
@endsection