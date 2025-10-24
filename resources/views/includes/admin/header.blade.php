<!-- Top Navigation -->
<div class="relative z-10 flex-shrink-0 flex h-16 bg-white shadow">
    <button type="button" class="px-4 border-r border-gray-200 text-gray-400 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500 md:hidden">
        <span class="sr-only">Open sidebar</span>
        📱
    </button>

    <div class="flex-1 px-4 flex justify-between">
        <div class="flex-1 flex">
            <!-- Toggle Sidebar Button -->
            <button
                id="sidebarToggleBtn"
                type="button"
                class="flex items-center justify-center w-10 h-full text-gray-500 hover:text-gray-700 hover:bg-gray-50 transition-colors rounded-md mr-4"
                title="Ocultar/Mostrar Menu Lateral">
                <svg xmlns="http://www.w3.org/2000/svg" id="toggleIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                    <path fill="#607D8B" d="M6 22H42V26H6zM6 10H42V14H6zM6 34H42V38H6z"></path>
                </svg>
            </button>
        </div>

        <div class="ml-4 flex items-center md:ml-6">
            <button type="button" class="bg-white p-1 rounded-full text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <span class="sr-only">View notifications</span>
                🔔
            </button>

            <!-- Profile dropdown -->
            <div class="ml-3 relative" x-data="{ open: false }">
                <button @click="open = !open" @click.away="open = false" type="button" class="flex items-center text-gray-700 hover:text-gray-900 focus:outline-none rounded-md p-1">
                    <div class="bg-blue-600 rounded-full h-8 w-8 flex items-center justify-center text-white font-semibold mr-2">
                        {{ substr(Auth::user()->name, 0, 2) }}
                    </div>
                    <span class="text-sm font-medium">{{ Auth::user()->name }}</span>
                    <span class="ml-2 text-xs bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full">
                        {{ Auth::user()->getRoleName() }}
                    </span>
                    <svg class="ml-2 h-4 w-4" :class="open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <!-- Dropdown menu -->
                <div x-show="open" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10" style="display: none;">
                    <!-- Profile -->
                    <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <span class="mr-3">👤</span>
                        Meu Perfil
                    </a>

                    <div class="border-t border-gray-100"></div>

                    <!-- Logout -->
                    <form method="POST" action="{{ route('gestor.logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <span class="mr-3">🚪</span>
                            Sair do Sistema
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
