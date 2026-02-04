@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100">
    <!-- Sidebar -->
    <div class="flex h-screen overflow-hidden">
        @include('includes.admin.sidebar')

        <!-- Main content -->
        <div class="flex flex-col flex-1 min-w-0 overflow-hidden w-full md:w-auto">
            @include('includes.admin.header')

            <!-- Flash Messages -->
            @include('includes.flash-messages')

            <!-- Main Content Area -->
            <div class="flex-1 overflow-y-auto">
                @yield('admin-content')
            </div>
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
    /* Enhanced Sidebar Styles */
    .sidebar-content {
        transition: width var(--transition-base), box-shadow var(--transition-base);
        min-width: 80px;
        max-width: 256px;
        background: linear-gradient(180deg, #ffffff 0%, #fafafa 100%);
        border-right: 1px solid rgba(0, 0, 0, 0.05);
    }
    
    /* Responsive sidebar adjustments */
    @media (max-width: 767px) {
        .sidebar-content {
            min-width: 0;
            max-width: 100%;
        }
    }

    .sidebar-expanded {
        width: 256px;
        box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05);
    }

    .sidebar-collapsed {
        width: 80px;
    }

    /* Enhanced Sidebar Links */
    .sidebar-link {
        position: relative;
        transition: all var(--transition-base);
        border-radius: 0.5rem;
        margin: 0.25rem 0.5rem;
    }

    .sidebar-link::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 3px;
        height: 0;
        background: linear-gradient(180deg, var(--color-primary-500), var(--color-accent-500));
        border-radius: 0 3px 3px 0;
        transition: height var(--transition-base);
    }

    .sidebar-link:hover {
        background: rgba(236, 72, 153, 0.08);
        transform: translateX(2px);
    }

    .sidebar-link:hover::before {
        height: 60%;
    }

    .sidebar-link.bg-blue-50::before,
    .sidebar-link.bg-blue-50 {
        background: linear-gradient(135deg, rgba(236, 72, 153, 0.15), rgba(245, 158, 11, 0.1));
        color: var(--color-primary-700);
    }

    .sidebar-link.bg-blue-50::before {
        height: 70%;
        background: linear-gradient(180deg, var(--color-primary-500), var(--color-accent-500));
    }

    /* Hide text when collapsed */
    .sidebar-collapsed .sidebar-text {
        display: none;
        opacity: 0;
    }

    .sidebar-collapsed .sidebar-divider {
        margin: 1rem 0;
    }

    /* Adjust links when collapsed */
    .sidebar-collapsed .sidebar-link {
        justify-content: center;
        padding: 0.75rem 0.5rem;
        margin: 0.25rem;
    }

    .sidebar-collapsed .sidebar-link::before {
        left: 50%;
        top: auto;
        bottom: 0;
        transform: translateX(-50%);
        width: 0;
        height: 3px;
    }

    .sidebar-collapsed .sidebar-link:hover::before,
    .sidebar-collapsed .sidebar-link.bg-blue-50::before {
        width: 60%;
        height: 3px;
    }

    /* Enhanced Logo */
    #sidebarLogoExpanded,
    #sidebarLogoCollapsed {
        transition: all var(--transition-base);
    }

    /* Smooth Header */
    .bg-white.shadow {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        backdrop-filter: blur(10px);
        background: rgba(255, 255, 255, 0.95);
    }

    /* Enhanced Main Content */
    .flex-1.relative.overflow-y-auto {
        background: var(--color-neutral-50);
    }
</style>
@endpush
