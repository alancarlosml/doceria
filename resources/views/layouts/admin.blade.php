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
        // Elements - Desktop Sidebar
        const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
        const sidebar = document.getElementById('sidebar');
        const toggleIcon = document.getElementById('toggleIcon');

        // Elements - Mobile Sidebar
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const closeMobileMenuBtn = document.getElementById('closeMobileMenuBtn');
        const mobileSidebar = document.getElementById('mobileSidebar');
        const mobileSidebarOverlay = document.getElementById('mobileSidebarOverlay');

        // Sidebar states
        const state = {
            isCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
            isMobileOpen: false
        };

        // Mobile Menu Functions
        function openMobileMenu() {
            state.isMobileOpen = true;
            mobileSidebar.classList.remove('-translate-x-full');
            mobileSidebar.classList.add('translate-x-0');
            mobileSidebarOverlay.classList.remove('hidden');
            setTimeout(() => {
                mobileSidebarOverlay.classList.remove('opacity-0');
                mobileSidebarOverlay.classList.add('opacity-100');
            }, 10);
            document.body.style.overflow = 'hidden';
        }

        function closeMobileMenu() {
            state.isMobileOpen = false;
            mobileSidebar.classList.remove('translate-x-0');
            mobileSidebar.classList.add('-translate-x-full');
            mobileSidebarOverlay.classList.remove('opacity-100');
            mobileSidebarOverlay.classList.add('opacity-0');
            setTimeout(() => {
                mobileSidebarOverlay.classList.add('hidden');
            }, 300);
            document.body.style.overflow = '';
        }

        // Desktop Sidebar Functions
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

        // Event listeners - Desktop
        if (sidebarToggleBtn) {
            sidebarToggleBtn.addEventListener('click', toggleSidebar);
        }

        // Event listeners - Mobile
        if (mobileMenuBtn) {
            mobileMenuBtn.addEventListener('click', openMobileMenu);
        }

        if (closeMobileMenuBtn) {
            closeMobileMenuBtn.addEventListener('click', closeMobileMenu);
        }

        if (mobileSidebarOverlay) {
            mobileSidebarOverlay.addEventListener('click', closeMobileMenu);
        }

        // Close mobile menu on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && state.isMobileOpen) {
                closeMobileMenu();
            }
        });

        // Close mobile menu when window is resized to desktop
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 768 && state.isMobileOpen) {
                closeMobileMenu();
            }
        });

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
