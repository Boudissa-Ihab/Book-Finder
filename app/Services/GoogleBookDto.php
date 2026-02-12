<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GoogleBookDto
{
    public function __construct(
        public string $title,
        public array $author,
        public string $isbn,
        public ?string $cover_url,
    ) {}
}
