<?php

namespace Modules\Fintech\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Wallet Credited Event
 */
class WalletCredited
{
    use Dispatchable, SerializesModels;
    
    public function __construct(
        public readonly string $walletId,
        public readonly float $amount,
        public readonly string $transactionId,
    ) {}
}
