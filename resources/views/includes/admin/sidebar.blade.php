<!-- Mobile Sidebar Overlay -->
<div id="mobileSidebarOverlay" class="fixed inset-0 bg-gray-600 bg-opacity-75 z-40 hidden md:hidden transition-opacity duration-300 opacity-0"></div>

<!-- Mobile Sidebar -->
<div id="mobileSidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-xl transform -translate-x-full transition-transform duration-300 ease-in-out md:hidden">
    <!-- Mobile Sidebar Header -->
    <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-br from-pink-500 to-pink-600 rounded-full flex items-center justify-center">
                <img class="rounded-full" src="{{ asset('imgs/logo_docedoce.jpeg') }}" alt="">
            </div>
            <span class="font-bold text-gray-800 text-sm">Doce Doce Brigaderia</span>
        </div>
        <button type="button" id="closeMobileMenuBtn" class="text-gray-500 hover:text-gray-700 focus:outline-none">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <!-- Mobile Navigation -->
    <nav class="flex-1 px-2 py-4 space-y-1 overflow-y-auto max-h-[calc(100vh-4rem)]">
        <a href="{{ route('gestor.dashboard') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50 hover:text-gray-900 {{ request()->routeIs('gestor.dashboard') ? 'bg-blue-50 text-blue-600' : '' }}">
            <span class="mr-3">📊</span>
            Dashboard
        </a>

        <a href="{{ route('cash-registers.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50 hover:text-gray-900 {{ request()->routeIs('cash-registers.*') ? 'bg-blue-50 text-blue-600' : '' }}">
            <span class="mr-3">💰</span>
            Caixas
        </a>

        <a href="{{ route('sales.pos') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50 hover:text-gray-900 {{ request()->routeIs('sales.pos') ? 'bg-blue-50 text-blue-600' : '' }}">
            <span class="mr-3">🛒</span>
            Vendas
        </a>

        <a href="{{ route('encomendas.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50 hover:text-gray-900 {{ request()->routeIs('encomendas.*') ? 'bg-blue-50 text-blue-600' : '' }}">
            <span class="mr-3">📝</span>
            Encomendas
        </a>

        <a href="{{ route('menu.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50 hover:text-gray-900 {{ request()->routeIs('menu.*') ? 'bg-blue-50 text-blue-600' : '' }}">
            <span class="mr-3">🍽️</span>
            Cardápio Semanal
        </a>

        <a href="{{ route('tables.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50 hover:text-gray-900 {{ request()->routeIs('tables.*') ? 'bg-blue-50 text-blue-600' : '' }}">
            <span class="mr-3">🪑</span>
            Mesas
        </a>

        <a href="{{ route('motoboys.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50 hover:text-gray-900 {{ request()->routeIs('motoboys.*') ? 'bg-blue-50 text-blue-600' : '' }}">
            <span class="mr-3">🏍️</span>
            Motoboys
        </a>

        <a href="{{ route('categories.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50 hover:text-gray-900 {{ request()->routeIs('categories.*') ? 'bg-blue-50 text-blue-600' : '' }}">
            <span class="mr-3">📂</span>
            Categorias
        </a>

        <a href="{{ route('products.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50 hover:text-gray-900 {{ request()->routeIs('products.*') ? 'bg-blue-50 text-blue-600' : '' }}">
            <span class="mr-3">📦</span>
            Produtos
        </a>

        <a href="{{ route('customers.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50 hover:text-gray-900 {{ request()->routeIs('customers.*') ? 'bg-blue-50 text-blue-600' : '' }}">
            <span class="mr-3">👥</span>
            Clientes
        </a>

        <!-- Divider -->
        <div class="border-t border-gray-200 my-4"></div>

        @if(auth()->user()->hasRole('admin'))
        <a href="{{ route('reports.dashboard') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50 hover:text-gray-900 {{ request()->routeIs('reports.*') ? 'bg-blue-50 text-blue-600' : '' }}">
            <span class="mr-3">📊</span>
            Relatórios
        </a>
        @endif

        @if(auth()->user()->hasRole('admin'))
        <a href="{{ route('expenses.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50 hover:text-gray-900 {{ request()->routeIs('expenses.*') ? 'bg-blue-50 text-blue-600' : '' }}">
            <span class="mr-3">💲</span>
            Entradas/Saídas
        </a>
        @endif

        @if(auth()->user()->hasRole('admin'))
        <a href="{{ route('users.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50 hover:text-gray-900 {{ request()->routeIs('users.*') ? 'bg-blue-50 text-blue-600' : '' }}">
            <span class="mr-3">👤</span>
            Usuários
        </a>
        @endif

        <a href="{{ route('settings.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50 hover:text-gray-900 {{ request()->routeIs('settings.*') ? 'bg-blue-50 text-blue-600' : '' }}">
            <span class="mr-3">⚙️</span>
            Configurações
        </a>
    </nav>
</div>

<!-- Desktop Sidebar -->
<div id="sidebar" class="hidden md:flex md:flex-shrink-0">
    <div class="flex flex-col sidebar-content sidebar-expanded bg-white shadow-lg">
        <!-- Logo Expanded -->
        <div id="sidebarLogoExpanded" class="flex items-center justify-center h-16 px-4 py-4 border-b border-gray-200">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-pink-500 to-pink-600 rounded-full flex items-center justify-center">
                    <img class="rounded-full" src="{{ asset('imgs/logo_docedoce.jpeg') }}" alt="">
                </div>
                <span class="font-bold text-gray-800">Doce Doce Brigaderia</span>
            </div>
        </div>

        <!-- Logo Collapsed -->
        <div id="sidebarLogoCollapsed" class="hidden flex items-center justify-center h-16 px-4 py-4 border-b border-gray-200">
            <div class="w-8 h-8 bg-gradient-to-br from-pink-500 to-pink-600 rounded-full flex items-center justify-center">
                <img class="rounded-full" src="{{ asset('imgs/logo_docedoce.jpeg') }}" alt="">
            </div>
        </div>

        <!-- Navigation -->
        <nav id="sidebarNav" class="flex-1 px-2 py-4 space-y-1">
            <a href="{{ route('gestor.dashboard') }}" class="sidebar-link group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50 hover:text-gray-900 {{ request()->routeIs('gestor.dashboard') ? 'bg-blue-50 text-blue-600' : '' }}" title="Dashboard">
                <span class="mr-3">📊</span>
                <span class="sidebar-text">Dashboard</span>
            </a>

            <a href="{{ route('cash-registers.index') }}" class="sidebar-link group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50 hover:text-gray-900 {{ request()->routeIs('cash-registers.*') ? 'bg-blue-50 text-blue-600' : '' }}" title="Caixas">
                <span class="mr-3">💰</span>
                <span class="sidebar-text">Caixas</span>
            </a>

            <a href="{{ route('sales.pos') }}" class="sidebar-link group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50 hover:text-gray-900 {{ request()->routeIs('sales.pos') ? 'bg-blue-50 text-blue-600' : '' }}" title="PDV - Pontos de Venda">
                <span class="mr-3">🛒</span>
                <span class="sidebar-text">Vendas</span>
            </a>

            <a href="{{ route('encomendas.index') }}" class="sidebar-link group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50 hover:text-gray-900 {{ request()->routeIs('encomendas.*') ? 'bg-blue-50 text-blue-600' : '' }}" title="Encomendas">
                <span class="mr-3">📝</span>
                <span class="sidebar-text">Encomendas</span>
            </a>

            <a href="{{ route('menu.index') }}" class="sidebar-link group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50 hover:text-gray-900 {{ request()->routeIs('menu.*') ? 'bg-blue-50 text-blue-600' : '' }}" title="Cardápio Semanal">
                <span class="mr-3">🍽️</span>
                <span class="sidebar-text">Cardápio Semanal</span>
            </a>

            <a href="{{ route('tables.index') }}" class="sidebar-link group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50 hover:text-gray-900 {{ request()->routeIs('tables.*') ? 'bg-blue-50 text-blue-600' : '' }}" title="Mesas">
                <span class="mr-3">🪑</span>
                <span class="sidebar-text">Mesas</span>
            </a>

            <a href="{{ route('motoboys.index') }}" class="sidebar-link group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50 hover:text-gray-900 {{ request()->routeIs('motoboys.*') ? 'bg-blue-50 text-blue-600' : '' }}" title="Motoboys">
                <span class="mr-3">🏍️</span>
                <span class="sidebar-text">Motoboys</span>
            </a>

            <a href="{{ route('categories.index') }}" class="sidebar-link group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50 hover:text-gray-900 {{ request()->routeIs('categories.*') ? 'bg-blue-50 text-blue-600' : '' }}" title="Categorias">
                <span class="mr-3">📂</span>
                <span class="sidebar-text">Categorias</span>
            </a>

            <a href="{{ route('products.index') }}" class="sidebar-link group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50 hover:text-gray-900 {{ request()->routeIs('products.*') ? 'bg-blue-50 text-blue-600' : '' }}" title="Produtos">
                <span class="mr-3">📦</span>
                <span class="sidebar-text">Produtos</span>
            </a>

            <a href="{{ route('customers.index') }}" class="sidebar-link group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50 hover:text-gray-900 {{ request()->routeIs('customers.*') ? 'bg-blue-50 text-blue-600' : '' }}" title="Clientes">
                <span class="mr-3">👥</span>
                <span class="sidebar-text">Clientes</span>
            </a>

            <!-- Divider -->
            <div class="sidebar-divider border-t border-gray-200 my-4"></div>

            @if(auth()->user()->hasRole('admin'))
            <a href="{{ route('reports.dashboard') }}" class="sidebar-link group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50 hover:text-gray-900 {{ request()->routeIs('reports.*') ? 'bg-blue-50 text-blue-600' : '' }}" title="Relatórios">
                <span class="mr-3">📊</span>
                <span class="sidebar-text">Relatórios</span>
            </a>
            @endif

            @if(auth()->user()->hasRole('admin'))
            <a href="{{ route('expenses.index') }}" class="sidebar-link group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50 hover:text-gray-900 {{ request()->routeIs('expenses.*') ? 'bg-blue-50 text-blue-600' : '' }}" title="Entradas e Saídas">
                <span class="mr-3">💲</span>
                <span class="sidebar-text">Entradas/Saídas</span>
            </a>
            @endif

            @if(auth()->user()->hasRole('admin'))
            <a href="{{ route('users.index') }}" class="sidebar-link group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50 hover:text-gray-900 {{ request()->routeIs('users.*') ? 'bg-blue-50 text-blue-600' : '' }}" title="Usuários">
                <span class="mr-3">👤</span>
                <span class="sidebar-text">Usuários</span>
            </a>
            @endif

            <a href="{{ route('settings.index') }}" class="sidebar-link group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50 hover:text-gray-900 {{ request()->routeIs('settings.*') ? 'bg-blue-50 text-blue-600' : '' }}" title="Configurações">
                <span class="mr-3">⚙️</span>
                <span class="sidebar-text">Configurações</span>
            </a>

        </nav>
    </div>
</div>
