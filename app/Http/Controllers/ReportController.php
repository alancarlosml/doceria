<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\User;
use App\Models\Customer;
use App\Models\Expense;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class ReportController extends Controller
{
    /**
     * Exibe o dashboard de relatórios
     */
    public function dashboard(Request $request)
    {
        // Período padrão: último mês
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());

        $period = [
            'start' => $startDate,
            'end' => $endDate
        ];

        // KPIs principais
        $kpis = $this->calculateKPIs($startDate, $endDate);

        // Dados para gráficos
        $salesByDay = Sale::selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(total) as total')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', 'cancelado')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        $topProducts = Product::with(['category', 'saleItems'])
            ->select('products.id', 'products.name', 'products.category_id')
            ->selectRaw('
                COALESCE(SUM(sale_items.quantity), 0) as total_sold,
                COALESCE(SUM(sale_items.subtotal), 0) as revenue
            ')
            ->leftJoin('sale_items', 'products.id', '=', 'sale_items.product_id')
            ->leftJoin('sales', function($join) use ($startDate, $endDate) {
                $join->on('sale_items.sale_id', '=', 'sales.id')
                    ->whereBetween('sales.created_at', [$startDate, $endDate])
                    ->where('sales.status', '!=', 'cancelado');
            })
            ->groupBy('products.id', 'products.name', 'products.category_id')
            ->orderByDesc('total_sold')
            ->limit(10)
            ->get();

        $salesByEmployee = User::with(['sales'])
            ->select('users.id', 'users.name')
            ->selectRaw('COUNT(sales.id) as total_sales, COALESCE(SUM(sales.total), 0) as total_revenue')
            ->join('sales', function($join) use ($startDate, $endDate) {
                $join->on('users.id', '=', 'sales.user_id')
                    ->whereBetween('sales.created_at', [$startDate, $endDate])
                    ->where('sales.status', '!=', 'cancelado');
            })
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total_revenue')
            ->get();

        // Vendas por categoria (método de pagamento e tipo de venda)
        $salesByCategory = $this->calculateSalesByCategory($startDate, $endDate);

        return view('admin.reports.dashboard', compact(
            'kpis',
            'salesByDay',
            'topProducts',
            'salesByEmployee',
            'salesByCategory',
            'period'
        ));
    }

    /**
     * Relatório de vendas por produto
     */
    public function products(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());

        $period = [
            'start' => $startDate,
            'end' => $endDate
        ];

        $products = Product::with(['category', 'saleItems'])
            ->select('products.id', 'products.name', 'products.category_id', 'products.price', 'products.active', 'products.created_at', 'products.updated_at')
            ->selectRaw('
                COALESCE(SUM(sale_items.quantity), 0) as quantity_sold,
                COALESCE(SUM(sale_items.subtotal), 0) as revenue,
                COUNT(DISTINCT sales.id) as sales_count
            ')
            ->leftJoin('sale_items', 'products.id', '=', 'sale_items.product_id')
            ->leftJoin('sales', function($join) use ($startDate, $endDate) {
                $join->on('sale_items.sale_id', '=', 'sales.id')
                    ->whereBetween('sales.created_at', [$startDate, $endDate])
                    ->where('sales.status', '!=', 'cancelado');
            })
            ->groupBy('products.id', 'products.name', 'products.category_id', 'products.price', 'products.active', 'products.created_at', 'products.updated_at')
            ->orderByDesc('revenue')
            ->paginate(20);

        $totalStats = [
            'total_revenue' => $products->sum('revenue'),
            'total_quantity' => $products->sum('quantity_sold'),
            'total_products' => $products->total(),
        ];

        return view('admin.reports.products', compact('products', 'period', 'totalStats'));
    }

    /**
     * Relatório de fluxo de caixa
     */
    public function cashFlow(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());

        $period = [
            'start' => $startDate,
            'end' => $endDate
        ];

        // Receitas (Vendas)
        $revenues = Sale::selectRaw('DATE(created_at) as date, COUNT(*) as transaction_count, SUM(total) as revenue, SUM(total) as paid_total')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', 'cancelado')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        // Despesas
        $expenses = Expense::selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(amount) as total')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        // Consolidar por dia
        $days = [];
        $currentDate = Carbon::parse($startDate);
        $endDateCarbon = Carbon::parse($endDate);

        while ($currentDate <= $endDateCarbon) {
            $dateString = $currentDate->toDateString();
            $revenue = $revenues->firstWhere('date', $dateString);
            $expense = $expenses->firstWhere('date', $dateString);

            $days[] = [
                'date' => $dateString,
                'formatted_date' => $currentDate->format('d/m'),
                'revenue' => $revenue ? $revenue->revenue : 0,
                'paid_revenue' => $revenue ? $revenue->paid_total : 0,
                'expenses' => $expense ? $expense->total : 0,
                'net' => ($revenue ? $revenue->revenue : 0) - ($expense ? $expense->total : 0),
                'transactions' => $revenue ? $revenue->transaction_count : 0,
            ];

            $currentDate->addDay();
        }

        $totals = [
            'total_revenue' => $revenues->sum('revenue'),
            'total_paid' => $revenues->sum('paid_total'),
            'total_expenses' => $expenses->sum('total'),
            'net_result' => $revenues->sum('revenue') - $expenses->sum('total'),
            'avg_daily_revenue' => count($days) > 0 ? $revenues->sum('revenue') / count($days) : 0,
        ];

        return view('admin.reports.cash-flow', compact('days', 'period', 'totals'));
    }

    /**
     * Relatório de performance de atendentes
     */
    public function employees(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());

        $period = [
            'start' => $startDate,
            'end' => $endDate
        ];

        $employees = User::with(['sales.customer', 'sales.items'])
            ->select('users.id', 'users.name')
            ->selectRaw('
                COUNT(sales.id) as total_sales,
                COALESCE(SUM(sales.total), 0) as total_revenue,
                AVG(sales.total) as avg_ticket,
                COUNT(DISTINCT sales.customer_id) as unique_customers,
                COUNT(CASE WHEN sales.status = \'cancelado\' THEN 1 END) as canceled_sales,
                MIN(sales.created_at) as first_sale,
                MAX(sales.created_at) as last_sale
            ')
            ->join('sales', function($join) use ($startDate, $endDate) {
                $join->on('users.id', '=', 'sales.user_id')
                    ->whereBetween('sales.created_at', [$startDate, $endDate]);
            })
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total_revenue')
            ->paginate(20);

        $totals = [
            'total_employees' => $employees->count(),
            'total_revenue' => $employees->sum('total_revenue'),
            'total_sales' => $employees->sum('total_sales'),
            'avg_ticket' => $employees->avg('avg_ticket'),
            'best_performer_revenue' => $employees->max('total_revenue'),
            'worst_performer_revenue' => $employees->min('total_revenue'),
        ];

        return view('admin.reports.employees', compact('employees', 'period', 'totals'));
    }

    /**
     * Relatório de clientes frequentes
     */
    public function customers(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());

        $period = [
            'start' => $startDate,
            'end' => $endDate
        ];

        $customers = Customer::with('sales')
            ->select('customers.id', 'customers.name', 'customers.email', 'customers.phone')
            ->selectRaw('
                COUNT(sales.id) as total_orders,
                COALESCE(SUM(sales.total), 0) as total_spent,
                AVG(sales.total) as avg_ticket,
                MAX(sales.created_at) as last_purchase,
                MIN(sales.created_at) as first_purchase,
                CONCAT(
                    CASE
                        WHEN DATEDIFF(NOW(), MAX(sales.created_at)) <= 30 THEN \'A\'
                        WHEN DATEDIFF(NOW(), MAX(sales.created_at)) <= 90 THEN \'B\'
                        ELSE \'C\'
                    END,
                    CASE
                        WHEN COUNT(sales.id) >= 10 THEN \'A\'
                        WHEN COUNT(sales.id) >= 5 THEN \'B\'
                        ELSE \'C\'
                    END,
                    CASE
                        WHEN COALESCE(SUM(sales.total), 0) >= 500 THEN \'A\'
                        WHEN COALESCE(SUM(sales.total), 0) >= 200 THEN \'B\'
                        ELSE \'C\'
                    END
                ) as rfm_segment,
                DATEDIFF(NOW(), MAX(sales.created_at)) as recency
            ')
            ->join('sales', function($join) use ($startDate, $endDate) {
                $join->on('customers.id', '=', 'sales.customer_id')
                    ->whereBetween('sales.created_at', [$startDate, $endDate])
                    ->where('sales.status', '!=', 'cancelado');
            })
            ->groupBy('customers.id', 'customers.name', 'customers.email', 'customers.phone')
            ->orderByDesc('total_spent')
            ->paginate(20);

        // Calculate RFM distribution counts
        $rfmCounts = [
            'vip' => 0,  // AAA
            'gold' => 0, // A{A,C} - starts with A, not AAA
            'silver' => 0, // starts with B
            'bronze' => 0, // starts with C
        ];

        foreach ($customers as $customer) {
            $rfm = $customer->rfm_segment ?? 'CCC';
            if ($rfm === 'AAA') {
                $rfmCounts['vip']++;
            } elseif (str_starts_with($rfm, 'A')) {
                $rfmCounts['gold']++;
            } elseif (str_starts_with($rfm, 'B')) {
                $rfmCounts['silver']++;
            } elseif (str_starts_with($rfm, 'C')) {
                $rfmCounts['bronze']++;
            }
        }

        $totals = [
            'total_customers' => $customers->count(),
            'total_revenue' => $customers->sum('total_spent'),
            'avg_ticket' => $customers->avg('avg_ticket'),
            'total_orders' => $customers->sum('total_orders'),
            'best_customer' => $customers->max('total_spent'),
            'rfm_counts' => $rfmCounts,
        ];

        return view('admin.reports.customers', compact('customers', 'period', 'totals'));
    }

    /**
     * Exportar relatório para CSV
     */
    public function exportCSV(Request $request)
    {
        $reportType = $request->get('type', 'dashboard');
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());

        $filename = "relatorio-{$reportType}-" . now()->format('d-m-Y') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($reportType, $startDate, $endDate) {
            $file = fopen('php://output', 'w');

            // UTF-8 BOM
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            switch ($reportType) {
                case 'products':
                    $this->exportProductsCSV($file, $startDate, $endDate);
                    break;
                case 'cash_flow':
                    $this->exportCashFlowCSV($file, $startDate, $endDate);
                    break;
                case 'customers':
                    $this->exportCustomersCSV($file, $startDate, $endDate);
                    break;
                case 'employees':
                    $this->exportEmployeesCSV($file, $startDate, $endDate);
                    break;
                default:
                    $this->exportDashboardCSV($file, $startDate, $endDate);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    private function exportDashboardCSV($file, $startDate, $endDate)
    {
        // Cabeçalho
        fputcsv($file, ['Relatório - Dashboard', '', '']);
        fputcsv($file, ['Período', $startDate . ' a ' . $endDate, '']);
        fputcsv($file, ['Gerado em', now()->format('d/m/Y H:i:s'), '']);
        fputcsv($file, ['', '', '']);

        // KPIs
        fputcsv($file, ['KPIs Principais', '', '']);
        $kpis = $this->calculateKPIs($startDate, $endDate);

        fputcsv($file, ['Receita Total', 'R$ ' . number_format($kpis['total_revenue'], 2, ',', '.'), '']);
        fputcsv($file, ['Total de Vendas', $kpis['total_sales'], '']);
        fputcsv($file, ['Ticket Médio', 'R$ ' . number_format($kpis['avg_ticket'], 2, ',', '.'), '']);
        fputcsv($file, ['Total Despesas', 'R$ ' . number_format($kpis['total_expenses'], 2, ',', '.'), '']);
        fputcsv($file, ['Lucro Líquido', 'R$ ' . number_format($kpis['net_profit'], 2, ',', '.'), '']);
        fputcsv($file, ['Margem de Lucro', number_format($kpis['profit_margin'], 1) . '%', '']);
    }

    private function exportProductsCSV($file, $startDate, $endDate)
    {
        fputcsv($file, ['Relatório - Vendas por Produto', '', '', '', '']);
        fputcsv($file, ['Período', $startDate . ' a ' . $endDate, '', '', '']);
        fputcsv($file, ['Gerado em', now()->format('d/m/Y H:i:s'), '', '', '']);
        fputcsv($file, ['', '', '', '', '']);

        fputcsv($file, ['Produto', 'Categoria', 'Quantidade Vendida', 'Receita Total', 'Nº Vendas']);
        fputcsv($file, ['', '', '', '', '']);

        $products = $this->getProductsData($startDate, $endDate);
        foreach ($products as $product) {
            fputcsv($file, [
                $product->name,
                $product->categories->first()->name ?? '',
                $product->quantity_sold,
                'R$ ' . number_format($product->revenue, 2, ',', '.'),
                $product->sales_count
            ]);
        }

        fputcsv($file, ['', '', '', '', '']);
        fputcsv($file, ['Totais', '', $products->sum('quantity_sold'), 'R$ ' . number_format($products->sum('revenue'), 2, ',', '.'), '']);
    }

    private function exportCashFlowCSV($file, $startDate, $endDate)
    {
        fputcsv($file, ['Relatório - Fluxo de Caixa Diário', '', '', '', '']);
        fputcsv($file, ['Período', $startDate . ' a ' . $endDate, '', '', '']);
        fputcsv($file, ['Gerado em', now()->format('d/m/Y H:i:s'), '', '', '']);
        fputcsv($file, ['', '', '', '', '']);

        fputcsv($file, ['Data', 'Transações', 'Receita', 'Despesas', 'Resultado Líquido']);
        fputcsv($file, ['', '', 'R$', 'R$', 'R$']);

        $revenues = Sale::selectRaw('DATE(created_at) as date, COUNT(*) as transaction_count, SUM(total) as revenue')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', 'cancelado')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        $expenses = Expense::selectRaw('DATE(created_at) as date, SUM(amount) as total')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        $currentDate = Carbon::parse($startDate);
        $endDateCarbon = Carbon::parse($endDate);

        while ($currentDate <= $endDateCarbon) {
            $dateString = $currentDate->toDateString();
            $revenue = $revenues->firstWhere('date', $dateString);
            $expense = $expenses->firstWhere('date', $dateString);

            fputcsv($file, [
                $currentDate->format('d/m/Y'),
                $revenue ? $revenue->transaction_count : 0,
                number_format($revenue ? $revenue->revenue : 0, 2, ',', '.'),
                number_format($expense ? $expense->total : 0, 2, ',', '.'),
                number_format(($revenue ? $revenue->revenue : 0) - ($expense ? $expense->total : 0), 2, ',', '.')
            ]);

            $currentDate->addDay();
        }
    }

    private function exportCustomersCSV($file, $startDate, $endDate)
    {
        fputcsv($file, ['Relatório - Clientes Frequentes', '', '', '', '']);
        fputcsv($file, ['Período', $startDate . ' a ' . $endDate, '', '', '']);
        fputcsv($file, ['Gerado em', now()->format('d/m/Y H:i:s'), '', '', '']);
        fputcsv($file, ['', '', '', '', '']);

        fputcsv($file, ['Cliente', 'E-mail', 'Total Pedidos', 'Valor Total Gasto', 'Ticket Médio', 'Última Compra', 'Segmento RFM']);
        fputcsv($file, ['', '', '', 'R$', 'R$', '', '']);

        $customers = $this->getCustomersData($startDate, $endDate);
        foreach ($customers as $customer) {
            // Calcular RFM
            $daysSinceLast = Carbon::parse($customer->last_purchase)->diffInDays(now());
            $recency_score = $daysSinceLast <= 30 ? 'A' : ($daysSinceLast <= 90 ? 'B' : 'C');
            $frequency_score = $customer->total_orders >= 10 ? 'A' : ($customer->total_orders >= 5 ? 'B' : 'C');
            $monetary_score = $customer->total_spent >= 500 ? 'A' : ($customer->total_spent >= 200 ? 'B' : 'C');
            $rfm = $recency_score . $frequency_score . $monetary_score;

            fputcsv($file, [
                $customer->name,
                $customer->email,
                $customer->total_orders,
                number_format($customer->total_spent, 2, ',', '.'),
                number_format($customer->avg_ticket, 2, ',', '.'),
                $customer->last_purchase ? Carbon::parse($customer->last_purchase)->format('d/m/Y') : '',
                $rfm
            ]);
        }
    }

    private function exportEmployeesCSV($file, $startDate, $endDate)
    {
        fputcsv($file, ['Relatório - Performance de Atendentes', '', '', '', '']);
        fputcsv($file, ['Período', $startDate . ' a ' . $endDate, '', '', '']);
        fputcsv($file, ['Gerado em', now()->format('d/m/Y H:i:s'), '', '', '']);
        fputcsv($file, ['', '', '', '', '']);

        fputcsv($file, ['Atendente', 'Total Vendas', 'Receita Total', 'Ticket Médio', 'Clientes Atendidos', 'Vendas Canceladas']);
        fputcsv($file, ['', '', 'R$', 'R$', '', '']);

        $employees = $this->getEmployeesData($startDate, $endDate);
        foreach ($employees as $employee) {
            fputcsv($file, [
                $employee->name,
                $employee->total_sales,
                number_format($employee->total_revenue, 2, ',', '.'),
                number_format($employee->avg_ticket, 2, ',', '.'),
                $employee->unique_customers,
                $employee->canceled_sales
            ]);
        }
    }

    private function getCustomersData($startDate, $endDate)
    {
        return Customer::select('customers.*')
            ->selectRaw('
                COUNT(sales.id) as total_orders,
                COALESCE(SUM(sales.total), 0) as total_spent,
                AVG(sales.total) as avg_ticket,
                MAX(sales.created_at) as last_purchase
            ')
            ->join('sales', function($join) use ($startDate, $endDate) {
                $join->on('customers.id', '=', 'sales.customer_id')
                    ->whereBetween('sales.created_at', [$startDate, $endDate])
                    ->where('sales.status', '!=', 'cancelado');
            })
            ->groupBy('customers.id')
            ->orderByDesc('total_spent')
            ->get();
    }

    private function getEmployeesData($startDate, $endDate)
    {
        return User::select('users.*')
            ->selectRaw('
                COUNT(sales.id) as total_sales,
                COALESCE(SUM(sales.total), 0) as total_revenue,
                AVG(sales.total) as avg_ticket,
                COUNT(DISTINCT sales.customer_id) as unique_customers,
                COUNT(CASE WHEN sales.status = \'cancelado\' THEN 1 END) as canceled_sales
            ')
            ->join('sales', function($join) use ($startDate, $endDate) {
                $join->on('users.id', '=', 'sales.user_id')
                    ->whereBetween('sales.created_at', [$startDate, $endDate]);
            })
            ->groupBy('users.id')
            ->orderByDesc('total_revenue')
            ->get();
    }

    /**
     * Calcular vendas por categoria (método de pagamento e tipo de venda)
     */
    private function calculateSalesByCategory($startDate, $endDate)
    {
        $sales = Sale::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', 'cancelado')
            ->get();

        $stats = [
            // Métodos de pagamento
            'pix' => 0,
            'cartao' => 0,
            'dinheiro' => 0,
            // Tipos de venda
            'balcao' => 0,
            'delivery' => 0,
            'encomenda' => 0,
        ];

        foreach ($sales as $sale) {
            /** @var \App\Models\Sale $sale */
            // Subtrair frete do total (consistente com cálculo do caixa)
            $total = (float) $sale->total - (float) ($sale->delivery_fee ?? 0);

            // Processar métodos de pagamento
            $isSplitPayment = !empty($sale->payment_methods_split) && is_array($sale->payment_methods_split) && count($sale->payment_methods_split) > 0;
            
            if ($isSplitPayment) {
                // Pagamento dividido - processar cada método
                // Em pagamentos divididos, os valores já estão sem frete
                foreach ($sale->payment_methods_split as $payment) {
                    $method = $payment['method'] ?? '';
                    $value = (float) ($payment['value'] ?? 0);

                    if ($method === 'pix') {
                        $stats['pix'] += $value;
                    } elseif (in_array($method, ['cartao_debito', 'cartao_credito'])) {
                        $stats['cartao'] += $value;
                    } elseif ($method === 'dinheiro') {
                        $stats['dinheiro'] += $value;
                    }
                }
            } else {
                // Pagamento único
                $method = $sale->payment_method instanceof \App\Enums\PaymentMethod 
                    ? $sale->payment_method->value 
                    : $sale->payment_method;

                if ($method === 'pix') {
                    $stats['pix'] += $total;
                } elseif (in_array($method, ['cartao_debito', 'cartao_credito'])) {
                    $stats['cartao'] += $total;
                } elseif ($method === 'dinheiro') {
                    $stats['dinheiro'] += $total;
                }
            }

            // Processar tipos de venda (usando total sem frete para consistência)
            $type = $sale->type instanceof \App\Enums\SaleType 
                ? $sale->type->value 
                : $sale->type;

            if ($type === 'balcao') {
                $stats['balcao'] += $total;
            } elseif ($type === 'delivery') {
                $stats['delivery'] += $total;
            } elseif ($type === 'encomenda') {
                $stats['encomenda'] += $total;
            }
        }

        return $stats;
    }

    /**
     * Calcular KPIs principais
     */
    private function calculateKPIs($startDate, $endDate)
    {
        // Receita total
        $totalRevenue = Sale::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', 'cancelado')
            ->sum('total');

        // Número de vendas
        $totalSales = Sale::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', 'cancelado')
            ->count();

        // Ticket médio
        $avgTicket = $totalSales > 0 ? $totalRevenue / $totalSales : 0;

        // Despesas totais
        $totalExpenses = Expense::whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');

        // Lucro líquido
        $netProfit = $totalRevenue - $totalExpenses;

        // Margem de lucro
        $profitMargin = $totalRevenue > 0 ? ($netProfit / $totalRevenue) * 100 : 0;

        // Novos clientes
        $newCustomers = Customer::whereBetween('created_at', [$startDate, $endDate])
            ->count();

        // Itens vendidos
        $totalItems = SaleItem::join('sales', function($join) use ($startDate, $endDate) {
            $join->on('sale_items.sale_id', '=', 'sales.id')
                ->whereBetween('sales.created_at', [$startDate, $endDate])
                ->where('sales.status', '!=', 'cancelado');
        })->sum('quantity');

        return [
            'total_revenue' => $totalRevenue,
            'total_sales' => $totalSales,
            'avg_ticket' => $avgTicket,
            'total_expenses' => $totalExpenses,
            'net_profit' => $netProfit,
            'profit_margin' => $profitMargin,
            'new_customers' => $newCustomers,
            'total_items' => $totalItems,
        ];
    }

    /**
     * Obter dados para export do dashboard
     */
    private function getDashboardData($startDate, $endDate)
    {
        return [
            'kpis' => $this->calculateKPIs($startDate, $endDate),
            'date' => now()->format('d/m/Y H:i'),
            'period' => [
                'start' => Carbon::parse($startDate)->format('d/m/Y'),
                'end' => Carbon::parse($endDate)->format('d/m/Y')
            ]
        ];
    }

    /**
     * Obter dados para export do relatório de produtos
     */
    private function getProductsReportData($startDate, $endDate)
    {
        $products = $this->getProductsData($startDate, $endDate);

        return [
            'products' => $products,
            'date' => now()->format('d/m/Y H:i'),
            'period' => [
                'start' => Carbon::parse($startDate)->format('d/m/Y'),
                'end' => Carbon::parse($endDate)->format('d/m/Y')
            ],
            'totals' => [
                'total_revenue' => $products->sum('revenue'),
                'total_quantity' => $products->sum('quantity_sold'),
            ]
        ];
    }

    // Métodos auxiliares para obter dados dos relatórios
    private function getCashFlowReportData($startDate, $endDate)
    {
        // Implementar se necessário
        return [];
    }

    private function getCustomersReportData($startDate, $endDate)
    {
        // Implementar se necessário
        return [];
    }

    private function getProductsData($startDate, $endDate)
    {
        return Product::with(['categories', 'saleItems'])
            ->select('products.id', 'products.name', 'products.category_id', 'products.price', 'products.active', 'products.created_at', 'products.updated_at')
            ->selectRaw('
                COALESCE(SUM(sale_items.quantity), 0) as quantity_sold,
                COALESCE(SUM(sale_items.subtotal), 0) as revenue,
                COUNT(DISTINCT sales.id) as sales_count
            ')
            ->leftJoin('sale_items', 'products.id', '=', 'sale_items.product_id')
            ->leftJoin('sales', function($join) use ($startDate, $endDate) {
                $join->on('sale_items.sale_id', '=', 'sales.id')
                    ->whereBetween('sales.created_at', [$startDate, $endDate])
                    ->where('sales.status', '!=', 'cancelado');
            })
            ->groupBy('products.id', 'products.name', 'products.category_id', 'products.price', 'products.active', 'products.created_at', 'products.updated_at')
            ->orderByDesc('revenue')
            ->get();
    }
}
