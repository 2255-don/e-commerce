<?php

namespace Modules\Seller\Controllers\Api;

use App\Http\Controllers\Controller;
use Modules\Seller\Services\SellerProfileService;
use Modules\Seller\Services\SellerStatsService;
use Modules\Seller\DTOs\CreateSellerProfileDTO;
use Modules\Seller\DTOs\UpdateSellerProfileDTO;
use Illuminate\Http\Request;

class SellerProfileApiController extends Controller
{
    public function __construct(
        private readonly SellerProfileService $sellerProfileService,
        private readonly SellerStatsService $sellerStatsService,
    ) {}
    
    /**
     * Get seller profile
     */
    public function show(Request $request)
    {
        $profile = $this->sellerProfileService->getProfileByUserId($request->user()->id);
        
        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'Seller profile not found',
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $profile,
        ]);
    }
    
    /**
     * Create seller profile
     */
    public function create(Request $request)
    {
        $validated = $request->validate([
            'shop_name' => 'required|string|max:255',
            'business_name' => 'required|string|max:255',
            'business_license' => 'nullable|string|max:100',
        ]);
        
        try {
            $dto = CreateSellerProfileDTO::fromRequest($validated, $request->user()->id);
            $profile = $this->sellerProfileService->createProfile($dto);
            
            return response()->json([
                'success' => true,
                'message' => 'Seller profile created successfully',
                'data' => $profile,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
    
    /**
     * Update seller profile
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'shop_name' => 'sometimes|string|max:255',
            'business_name' => 'sometimes|string|max:255',
        ]);
        
        try {
            $dto = UpdateSellerProfileDTO::fromRequest($validated);
            $profile = $this->sellerProfileService->updateProfile($request->user()->id, $dto);
            
            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'data' => $profile,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
    
    /**
     * Get seller statistics
     */
    public function stats(Request $request)
    {
        try {
            $stats = $this->sellerStatsService->getStats($request->user()->id);
            
            return response()->json([
                'success' => true,
                'data' => $stats,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
