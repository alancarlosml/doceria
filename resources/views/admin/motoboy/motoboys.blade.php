@extends('layouts.admin')

@section('title', 'Motoboys - Doce Doce Brigaderia')

@section('admin-content')
<!-- Main content -->
<main class="flex-1 relative overflow-y-auto focus:outline-none" x-data="motoboysManager()">
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
            <!-- Page Header -->
            <div class="md:flex md:items-center md:justify-between mb-8">
                <div class="flex-1 min-w-0">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                        🏍️ Motoboys
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Gerencie todos os motoboys disponíveis para entregas.
                    </p>
                </div>

                <div class="mt-4 flex space-x-3 md:mt-0 md:ml-4">
                    <a href="{{ route('motoboys.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Novo Motoboy
                    </a>
                </div>
            </div>

            <!-- motoboys Table -->
            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                <div class="px-4 py-4 sm:px-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Listagem</h3>
                        <div class="text-sm text-gray-500">
                            <span id="motoboy-count">{{ \App\Models\Motoboy::count() }}</span> registros encontrados
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nome & Contato
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Documentos
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Veículo
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Ações
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach(\App\Models\Motoboy::orderBy('name')->paginate(15) as $motoboy)
                                <tr class="hover:bg-gray-50">
                                    <!-- Name & Phone -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $motoboy->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $motoboy->phone }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Documents -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            @if($motoboy->cpf)
                                                <div>CPF: {{ $motoboy->cpf }}</div>
                                            @endif
                                            @if($motoboy->cnh)
                                                <div>CNH: {{ $motoboy->cnh }}</div>
                                            @endif
                                            @if(!$motoboy->cpf && !$motoboy->cnh)
                                                <span class="text-gray-400">Sem documentos</span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Vehicle -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            @if($motoboy->placa_veiculo)
                                                <div>Placa: {{ $motoboy->placa_veiculo }}</div>
                                            @else
                                                <span class="text-gray-400">Sem veículo</span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Status -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <button
                                            @click="toggleStatus({{ $motoboy->id }}, '{{ $motoboy->active ? 'Desativar' : 'Ativar' }}')"
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ $motoboy->active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}"
                                        >
                                            <span class="w-2 h-2 mr-1 rounded-full {{ $motoboy->active ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                            {{ $motoboy->active ? 'Ativo' : 'Inativo' }}
                                        </button>
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-2">
                                            <a href="{{ route('motoboys.show', $motoboy) }}" class="text-green-600 hover:text-green-900">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>

                                            <a href="{{ route('motoboys.edit', $motoboy) }}" class="text-blue-600 hover:text-blue-900">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>

                                            <form method="POST" action="{{ route('motoboys.destroy', $motoboy) }}" class="inline" onsubmit="return confirm('Tem certeza que deseja excluir este motoboy?\n\nAtenção: Motoboys com entregas ativas não podem ser excluídos.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination -->
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="hidden sm:block">
                            <div class="text-sm text-gray-700">
                                Mostrando <span class="font-medium">{{ \App\Models\Motoboy::paginate(15)->firstItem() ?: 0 }}</span> a <span class="font-medium">{{ \App\Models\Motoboy::paginate(15)->lastItem() ?: 0 }}</span> de <span class="font-medium">{{ \App\Models\Motoboy::count() }}</span> resultados
                            </div>
                        </div>
                        <div class="flex space-x-1">
                            <!-- Simple pagination links -->
                            <div class="flex space-x-2">
                                @if(\App\Models\Motoboy::paginate(15)->hasPages())
                                    @if(\App\Models\Motoboy::paginate(15)->onFirstPage())
                                        <span class="px-3 py-2 rounded-md bg-gray-100 text-gray-400 cursor-not-allowed">
                                            Anterior
                                        </span>
                                    @else
                                        <a href="{{ \App\Models\Motoboy::paginate(15)->previousPageUrl() }}" class="px-3 py-2 rounded-md bg-white text-blue-600 hover:bg-blue-50 border">
                                            Anterior
                                        </a>
                                    @endif

                                    <span class="px-3 py-2 rounded-md bg-blue-600 text-white">
                                        {{ \App\Models\Motoboy::paginate(15)->currentPage() }}
                                    </span>

                                    @if(\App\Models\Motoboy::paginate(15)->hasMorePages())
                                        <a href="{{ \App\Models\Motoboy::paginate(15)->nextPageUrl() }}" class="px-3 py-2 rounded-md bg-white text-blue-600 hover:bg-blue-50 border">
                                            Próximo
                                        </a>
                                    @else
                                        <span class="px-3 py-2 rounded-md bg-gray-100 text-gray-400 cursor-not-allowed">
                                            Próximo
                                        </span>
                                    @endif
                                @else
                                    <span class="px-3 py-2 rounded-md bg-gray-100 text-gray-400">
                                        Sem páginas para navegar
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Alpine.js Motoboys Manager -->
<script>
function motoboysManager() {
    return {
        loading: false,

        async toggleStatus(motoboyId, action) {
            if (!confirm(`Tem certeza que deseja ${action.toLowerCase()} este motoboy?`)) {
                return;
            }

            this.loading = true;

            try {
                const response = await fetch(`/gestor/motoboys/${motoboyId}/toggle-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    },
                });

                if (response.ok) {
                    const result = await response.json();
                    this.showToast(result.message, result.active ? 'success' : 'warning');

                    // Update UI
                    window.location.reload();
                } else {
                    throw new Error('Falha ao alterar status');
                }
            } catch (error) {
                console.error('Erro ao toggle status:', error);
                this.showToast('Erro ao alterar status do motoboy.', 'error');
            } finally {
                this.loading = false;
            }
        },

        showToast(message, type) {
            // Simple toast implementation
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg transition-all duration-300 ${
                type === 'success' ? 'bg-green-500' :
                type === 'error' ? 'bg-red-500' :
                type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500'
            } text-white max-w-sm`;
            toast.innerHTML = `
                <div class="flex items-center">
                    <span class="text-lg mr-2">${
                        type === 'success' ? '✅' :
                        type === 'error' ? '❌' :
                        type === 'warning' ? '⚠️' : 'ℹ️'
                    }</span>
                    <span>${message}</span>
                </div>
            `;

            document.body.appendChild(toast);

            // Auto remove after 3 seconds
            setTimeout(() => {
                toast.classList.add('opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
    }
}
</script>
@endsection
