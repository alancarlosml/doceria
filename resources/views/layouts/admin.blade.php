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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Elements
        const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
        const sidebar = document.getElementById('sidebar');
        const toggleIcon = document.getElementById('toggleIcon');

        // Sidebar states
        const state = {
            isCollapsed: localStorage.getItem('sidebarCollapsed') === 'true'
        };

        function toggleSidebar() {
            state.isCollapsed = !state.isCollapsed;
            saveState();
            updateUI();
        }

        function saveState() {
            localStorage.setItem('sidebarCollapsed', state.isCollapsed);
        }

        function updateUI() {
            const sidebarContent = sidebar.querySelector('.sidebar-content');
            const sidebarLogoExpanded = document.getElementById('sidebarLogoExpanded');
            const sidebarLogoCollapsed = document.getElementById('sidebarLogoCollapsed');
            const sidebarTexts = document.querySelectorAll('.sidebar-text');

            if (state.isCollapsed) {
                // Collapsed state
                sidebarContent.classList.add('sidebar-collapsed');
                sidebarContent.classList.remove('sidebar-expanded');
                sidebarLogoExpanded.classList.add('hidden');
                sidebarLogoCollapsed.classList.remove('hidden');

                // Hide text but keep icons
                sidebarTexts.forEach(text => text.classList.add('hidden'));
                sidebarContent.style.width = '80px';

                // Update toggle icon
                toggleIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>';
            } else {
                // Expanded state
                sidebarContent.classList.add('sidebar-expanded');
                sidebarContent.classList.remove('sidebar-collapsed');
                sidebarLogoExpanded.classList.remove('hidden');
                sidebarLogoCollapsed.classList.add('hidden');

                // Show text
                sidebarTexts.forEach(text => text.classList.remove('hidden'));
                sidebarContent.style.width = '256px';

                // Update toggle icon
                toggleIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>';
            }
        }

        // Event listeners
        if (sidebarToggleBtn) {
            sidebarToggleBtn.addEventListener('click', toggleSidebar);
        }

        // Initialize
        updateUI();
    });
</script>

<style>
    /* Sidebar width transitions */
    .sidebar-content {
        transition: width 0.3s ease-in-out;
        min-width: 80px;
        max-width: 256px;
    }

    .sidebar-expanded {
        width: 256px;
    }

    .sidebar-collapsed {
        width: 80px;
    }

    /* Hide text when collapsed */
    .sidebar-collapsed .sidebar-text {
        display: none;
    }

    .sidebar-collapsed .sidebar-divider {
        margin: 1rem 0;
    }

    /* Adjust links when collapsed */
    .sidebar-collapsed .sidebar-link {
        justify-content: center;
        padding: 0.75rem 0.5rem;
    }
</style>
@endpush
