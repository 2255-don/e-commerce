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
        \Illuminate\Support\Facades\Log::info('🎯 SellerDashboardController@index: REACHED!', [
            'user_id' => auth()->id(),
            'user_email' => auth()->user()?->email,
        ]);
        
        $stats = $this->sellerStatsService->getStats(auth()->id());
        
        return view('seller::dashboard', compact('stats'));
    }
}
