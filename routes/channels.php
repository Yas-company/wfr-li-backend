<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::routes(['middleware' => ['auth:sanctum']]);
Broadcast::channel('buyer.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});