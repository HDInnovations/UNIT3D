<?php

declare(strict_types=1);

return [
    'auto_close_reminder_days' => env('TICKET_AUTO_CLOSE_REMINDER_DAYS', 3),
    'auto_close_grace_hours'   => env('TICKET_AUTO_CLOSE_GRACE_HOURS', 24),
];
