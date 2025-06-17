<?php

declare(strict_types=1);

namespace Modules\BillingManager\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        return response()->view('billing::admin.dashboard');
    }
}
