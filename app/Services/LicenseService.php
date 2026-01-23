<?php

namespace App\Services;

use App\Models\User;
use App\Models\SellerProfile;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LicenseService
{
    const LICENSE_COST = 5000.00;
    const DURATION_MONTHS = 3;

    /**
     * Activate or renew a seller license.
     */
    public function activateLicense(User $user)
    {
        return DB::transaction(function () use ($user) {
            $profile = $user->sellerProfile ?? new SellerProfile(['user_id' => $user->id]);

            $now = Carbon::now();
            
            // If license is still active, extend from expiry date, otherwise from now
            $startDate = ($profile->licence_expire_at && $profile->licence_expire_at->isFuture()) 
                ? $profile->licence_expire_at 
                : $now;

            $profile->licence_paid_at = $now;
            $profile->licence_expire_at = $startDate->addMonths(self::DURATION_MONTHS);
            $profile->is_active = true;
            $profile->save();

            return $profile;
        });
    }
}
