<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GoogleBookDto
{
    public function __construct(
        public string $title,
        public string $author,
        public string $isbn,
        public ?string $cover_url,
    ) {}

    /**
     * Map Google Books API response to our local Book schema.
     */
    public static function transformGoogleBookDto(array $googleBook)
    {
        $volumeInfo = $googleBook['volumeInfo'] ?? [];

        return new self(
            title: $volumeInfo['title'] ?? 'Unknown Title',
            author: implode(', ', $volumeInfo['authors'] ?? 'Unknown Author(s)'),
            isbn: self::extractIsbn($volumeInfo['industryIdentifiers'] ?? [], 'ISBN_13')
                    ?? self::extractIsbn($volumeInfo['industryIdentifiers'] ?? [], 'ISBN_10')
                    ?? self::extractIsbn($volumeInfo['industryIdentifiers'] ?? [], 'OTHER') ?? 'Unknown ISBN',
            cover_url: $volumeInfo['imageLinks']['thumbnail'] ?? null,
        );
    }

    /**
     * Convert DTO to array for serialization.
     */
    public function toArray()
    {
        return [
            'title' => $this->title,
            'author' => $this->author,
            'isbn' => $this->isbn,
            'cover_url' => $this->cover_url,
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
