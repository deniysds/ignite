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
    <div class="flex grow">

        <!-- Wrapper -->
        <div class="flex flex-col kt-wrapper grow">

            <!-- Content -->
            <main class="pt-5 grow" id="content" role="content">
                <div class="kt-container-fluid">
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