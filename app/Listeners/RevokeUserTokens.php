<?php

namespace App\Listeners;

use App\Events\OrganizationApproved;
use Illuminate\Contracts\Queue\ShouldQueue;

class RevokeUserTokens implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrganizationApproved $event): void
    {
        $user = $event->user;
        $user->tokens()->delete();
    }
}
