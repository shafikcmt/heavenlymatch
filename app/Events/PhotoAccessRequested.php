<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PhotoAccessRequested
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly string $requesterId,
        public readonly string $profileId,
    ) {}
}
