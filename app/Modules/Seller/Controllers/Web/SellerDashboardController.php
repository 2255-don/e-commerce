<?php

namespace Modules\Seller\Controllers\Web;

use App\Http\Controllers\Controller;
use Modules\Seller\Services\SellerStatsService;

class SellerDashboardController extends Controller
{
    public function __construct(
        private readonly SellerStatsService $sellerStatsService,
    ) {}
    
    public function index()
    {
        $stats = $this->sellerStatsService->getStats(auth()->id());
        
        return view('pages.seller.dashboard', compact('stats'));
    }
}
