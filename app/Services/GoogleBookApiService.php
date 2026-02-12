<?php

namespace App\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleBookApiService
{
    protected $baseUrl = 'https://www.googleapis.com';

    /**
     * Search books via Google Books API.
     *
     * @throws RequestException
     */
    public function search(string $query, int $maxResults = 10, int $startIndex = 0): array|JsonResponse
    {
        $endpoint = '/books/v1/volumes';
        $response = Http::get($this->baseUrl . $endpoint, [
            'q' => $query,
            'maxResults' => $maxResults,
            'startIndex' => $startIndex,
        ]);

        if ($response->failed()) {
            $response->throw();
        }

        return $response->json('items', []);
    }

    public function simplifySearchResults($books): array
    {
        if (! is_array($books) || count($books) === 0)
            return [];

        return collect($books)->map(function ($item) {
            return GoogleBookMapping::transformGoogleBookDto($item);
        })->values()->toArray();
    }
}
