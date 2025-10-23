@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100">
    <!-- Sidebar -->
    <div class="flex h-screen">
        @include('includes.admin.sidebar')

        <!-- Main content -->
        <div class="flex flex-col w-0 flex-1 overflow-hidden">
            @include('includes.admin.header')

            <!-- Flash Messages -->
            @include('includes.flash-messages')

            <!-- Main Content Area -->
            @yield('admin-content')
        </div>
    </div>
</div>
@endsection
