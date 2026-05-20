<?php

namespace App\Events;

use App\Models\PhotoAccessRequest;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PhotoAccessResponded
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly PhotoAccessRequest $accessRequest,
    ) {}
}
