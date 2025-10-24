<!-- Sidebar -->
<div class="hidden md:flex md:flex-shrink-0">
    <div class="flex flex-col w-64 bg-white shadow-lg">
        <!-- Logo -->
        <div class="flex items-center justify-center h-16 px-4 py-4 border-b border-gray-200">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-pink-500 to-pink-600 rounded-full flex items-center justify-center">
                    <img class="rounded-full" src="{{ asset('imgs/logo_docedoce.jpeg') }}" alt="">
                </div>
                <span class="font-bold text-gray-800">Doce Doce Brigaderia</span>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-2 py-4 space-y-1">
            <a href="{{ route('gestor.dashboard') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50 hover:text-gray-900 {{ request()->routeIs('gestor.dashboard') ? 'bg-blue-50 text-blue-600' : '' }}">
                <span class="mr-3">📊</span>
                Dashboard
            </a>

            <a href="{{route('cash-registers.index')}}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50 hover:text-gray-900">
                <span class="mr-3">💰</span>
                Caixas
            </a>

            <a href="{{ route('sales.pos') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50 hover:text-gray-900">
                <span class="mr-3">🛒</span>
                Vendas
            </a>

            <a href="{{ route('menus.manage') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50 hover:text-gray-900 {{ request()->routeIs('menus.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                <span class="mr-3">🍽️</span>
                Cardápio Semanal
            </a>

            <a href="{{ route('motoboys.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50 hover:text-gray-900 {{ request()->routeIs('motoboys.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                <span class="mr-3">🏍️</span>
                Motoboys
            </a>

            <a href="{{ route('categories.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50 hover:text-gray-900 {{ request()->routeIs('categories.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                <span class="mr-3">📂</span>
                Categorias
            </a>

            <a href="{{ route('products.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50 hover:text-gray-900 {{ request()->routeIs('products.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                <span class="mr-3">📦</span>
                Produtos
            </a>

            <a href="{{route('customers.index')}}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50 hover:text-gray-900">
                <span class="mr-3">👥</span>
                Clientes
            </a>
            
            <!-- Divider -->
            <div class="border-t border-gray-200 my-4"></div>

            <a href="#" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50 hover:text-gray-900">
                <span class="mr-3">📊</span>
                Relatórios
            </a>

            <a href="{{ route('expenses.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50 hover:text-gray-900 {{ request()->routeIs('expenses.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                <span class="mr-3">💲</span>
                Entradas/Saídas
            </a>

            <a href="#" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50 hover:text-gray-900">
                <span class="mr-3">⚙️</span>
                Configurações
            </a>

        </nav>
    </div>
</div>
