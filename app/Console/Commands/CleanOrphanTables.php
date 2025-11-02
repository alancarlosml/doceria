<?php

namespace App\Console\Commands;

use App\Models\Table;
use App\Models\Sale;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanOrphanTables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tables:clean-orphans 
                            {--dry-run : Executa sem fazer alterações, apenas mostra o que seria feito}
                            {--force : Executa sem confirmação}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Limpa mesas órfãs (mesas ocupadas/reservadas sem vendas ativas relacionadas)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        $this->info('🔍 Procurando mesas órfãs...');
        $this->newLine();

        $orphanTables = $this->findOrphanTables();

        if ($orphanTables->isEmpty()) {
            $this->info('✅ Nenhuma mesa órfã encontrada!');
            return Command::SUCCESS;
        }

        $this->warn("⚠️  Encontradas {$orphanTables->count()} mesa(s) órfã(s):");
        $this->newLine();

        // Mostrar mesas órfãs
        $tableData = [];
        foreach ($orphanTables as $table) {
            $tableData[] = [
                'ID' => $table->id,
                'Número' => $table->number,
                'Status Atual' => ucfirst($table->status),
                'Status Correto' => 'Disponível',
            ];
        }

        $this->table(
            ['ID', 'Número', 'Status Atual', 'Status Correto'],
            $tableData
        );

        $this->newLine();

        if ($dryRun) {
            $this->info('🔍 Modo dry-run ativado. Nenhuma alteração será feita.');
            return Command::SUCCESS;
        }

        // Confirmação
        if (!$force) {
            if (!$this->confirm('Deseja corrigir o status dessas mesas para "disponível"?')) {
                $this->info('Operação cancelada.');
                return Command::SUCCESS;
            }
        }

        // Corrigir mesas
        $this->info('🔄 Corrigindo status das mesas...');
        $corrected = 0;

        DB::transaction(function () use ($orphanTables, &$corrected) {
            foreach ($orphanTables as $table) {
                $table->update(['status' => 'disponivel']);
                $corrected++;
            }
        });

        $this->newLine();
        $this->info("✅ {$corrected} mesa(s) corrigida(s) com sucesso!");
        
        return Command::SUCCESS;
    }

    /**
     * Encontra mesas órfãs
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function findOrphanTables()
    {
        $orphanTables = collect();

        // Mesas ocupadas sem vendas ativas
        $occupiedOrphans = Table::where('status', 'ocupada')
            ->where('active', true)
            ->get()
            ->filter(function ($table) {
                $activeSale = $table->sales()
                    ->where('status', '!=', 'finalizado')
                    ->where('status', '!=', 'cancelado')
                    ->first();
                
                return !$activeSale;
            });

        // Mesas reservadas sem vendas pendentes
        $reservedOrphans = Table::where('status', 'reservada')
            ->where('active', true)
            ->get()
            ->filter(function ($table) {
                $pendingSale = $table->sales()
                    ->where('status', 'pendente')
                    ->first();
                
                return !$pendingSale;
            });

        return $orphanTables
            ->merge($occupiedOrphans)
            ->merge($reservedOrphans)
            ->unique('id');
    }
}
