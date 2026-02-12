<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Books',
    type: 'array',
    items: new OA\Items(
        properties: [
            new OA\Property(
                property: 'title',
                type: 'string',
                description: 'Get book title',
                example: "The Great Gatsby"
            ),
            new OA\Property(
                property: 'author',
                type: 'string',
                description: 'Get book author',
                example: 'F. Scott Fitzgerald'
            ),
            new OA\Property(
                property: 'isbn',
                type: 'string',
                description: 'Get book ISBN-13 / ISBN-10 (International Standard Book Number)',
                example: '9780743273565'
            ),
            new OA\Property(
                property: 'cover_url',
                type: 'string',
                description: 'Get book cover URL',
                example: 'https://covers.openlibrary.org/b/isbn/978-0-7432-7356-5-L.jpg'
            ),
        ]
    )
)]
class Book {
    //
}
