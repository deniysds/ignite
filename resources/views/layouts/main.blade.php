@php
    use Illuminate\Support\Facades\Route;

    $route      = explode('.',Route::currentRouteName());
	$modules    = file_get_contents(dirname(__FILE__, 4) . '/modules_statuses.json');
    $module     = json_decode($modules);
@endphp

@extends('layouts.base', ['module' => $module])

@section('main')

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

@endsection
