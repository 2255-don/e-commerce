<?php

namespace Modules\Fintech\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Transaction Completed Event
 */
class TransactionCompleted
{
    use Dispatchable, SerializesModels;
    
    public function __construct(
        public readonly string $transactionId,
        public readonly string $type,
        public readonly float $amount,
    ) {}
}
