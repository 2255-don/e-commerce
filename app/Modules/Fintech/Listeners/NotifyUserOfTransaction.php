<?php

namespace Modules\Fintech\Listeners;

use Modules\Fintech\Events\TransactionCompleted;
use Illuminate\Support\Facades\Log;

/**
 * Notify User Of Transaction Listener
 */
class NotifyUserOfTransaction
{
    public function handle(TransactionCompleted $event): void
    {
        // TODO: Envoyer notification à l'utilisateur
        // - Email
        // - SMS
        // - Push notification
        
        Log::info('Transaction completed notification', [
            'transaction_id' => $event->transactionId,
            'type' => $event->type,
            'amount' => $event->amount,
        ]);
    }
}
