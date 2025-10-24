<!-- Sidebar -->
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

            <a href="{{route('cash-registers.index')}}" class="sidebar-link group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50 hover:text-gray-900" title="Caixas">
                <span class="mr-3">💰</span>
                <span class="sidebar-text">Caixas</span>
            </a>

            <a href="{{ route('sales.pos') }}" class="sidebar-link group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50 hover:text-gray-900" title="PDV - Pontos de Venda">
                <span class="mr-3">🛒</span>
                <span class="sidebar-text">Vendas</span>
            </a>

            <a href="{{ route('menus.manage') }}" class="sidebar-link group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50 hover:text-gray-900 {{ request()->routeIs('menus.*') ? 'bg-blue-50 text-blue-600' : '' }}" title="Cardápio Semanal">
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

            <a href="{{route('customers.index')}}" class="sidebar-link group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50 hover:text-gray-900" title="Clientes">
                <span class="mr-3">👥</span>
                <span class="sidebar-text">Clientes</span>
            </a>

            <!-- Divider -->
            <div class="sidebar-divider border-t border-gray-200 my-4"></div>

            <a href="#" class="sidebar-link group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50 hover:text-gray-900" title="Relatórios">
                <span class="mr-3">📊</span>
                <span class="sidebar-text">Relatórios</span>
            </a>

            <a href="{{ route('expenses.index') }}" class="sidebar-link group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50 hover:text-gray-900 {{ request()->routeIs('expenses.*') ? 'bg-blue-50 text-blue-600' : '' }}" title="Entradas e Saídas">
                <span class="mr-3">💲</span>
                <span class="sidebar-text">Entradas/Saídas</span>
            </a>

            <a href="#" class="sidebar-link group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50 hover:text-gray-900" title="Configurações">
                <span class="mr-3">⚙️</span>
                <span class="sidebar-text">Configurações</span>
            </a>

        </nav>
    </div>
</div>
