<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GoogleBookMapping
{
    /**
     * Map Google Books API response to our local Book schema.
     */
    public static function transformGoogleBookDto(array $googleBook)
    {
        $volumeInfo = $googleBook['volumeInfo'] ?? [];

        return [
            'title' => $volumeInfo['title'] ?? 'Unknown Title',
            'author' => implode(', ', $volumeInfo['authors'] ?? 'Unknown Author(s)'),
            'isbn' => self::extractIsbn($volumeInfo['industryIdentifiers'] ?? [], 'ISBN_13')
                    ?? self::extractIsbn($volumeInfo['industryIdentifiers'] ?? [], 'ISBN_10')
                    ?? self::extractIsbn($volumeInfo['industryIdentifiers'] ?? [], 'OTHER'),
            'cover_url' => $volumeInfo['imageLinks']['thumbnail'] ?? null,
        ];
    }

    private static function extractIsbn(array $identifiers, string $type)
    {
        foreach ($identifiers as $identifier) {
            if ($identifier['type'] === $type) {
                return $identifier['identifier'];
            }
        }
        return null;
    }
}
