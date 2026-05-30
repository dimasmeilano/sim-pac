<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('progja-chat.{progjaId}', function ($user, $progjaId) {
    return $user !== null;
});
Broadcast::channel('global-notif', function ($user) {
    return $user !== null;
});
