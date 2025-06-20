<?php

declare(strict_types=1);

namespace Modules\Billing\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;
use Modules\Billing\App\Models\Invoice;
use Modules\Billing\App\Models\Subscription;

class MetricsController extends Controller
{
    public function index(): View
    {
        $revenue = $this->monthlyRevenue();
        $churn = $this->churnRateData();

        return view('billing::metrics.index', [
            'revenue' => $revenue,
            'churn' => $churn,
        ]);
    }

    public function export(string $metric)
    {
        return match ($metric) {
            'revenue' => $this->csvResponse(
                $this->monthlyRevenue(),
                'monthly_revenue.csv'
            ),
            'churn' => $this->csvResponse(
                $this->churnRateData(),
                'churn_rate.csv'
            ),
            default => abort(404),
        };
    }

    private function monthlyRevenue(): array
    {
        return Invoice::where('status', 'paid')
            ->whereYear('updated_at', now()->year)
            ->selectRaw('MONTH(updated_at) as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->all();
    }

    private function churnRateData(): array
    {
        $total = Subscription::count();

        return Subscription::where('stripe_status', 'canceled')
            ->whereYear('updated_at', now()->year)
            ->selectRaw('MONTH(updated_at) as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->mapWithKeys(fn ($row) => [
                $row->month => $total > 0 ? round(($row->count / $total) * 100, 2) : 0,
            ])
            ->all();
    }

    private function csvResponse(array $data, string $filename)
    {
        $lines = ['Month,Value'];
        foreach ($data as $month => $value) {
            $lines[] = "$month,$value";
        }

        return Response::make(implode("\n", $lines), 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    }
}
