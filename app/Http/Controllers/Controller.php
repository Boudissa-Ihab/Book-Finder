<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Book Finder',
    description: 'Book Finder is an API-based application that allows users login and register in order to search for books, view book details, and manage their favorite books. With the possiblity to import books from external sources like Google Books API',
    contact: new OA\Contact(email: 'boudissa.ihab@gmail.com'),
    license: new OA\License(name: 'Apache 2.0', url: 'http://www.apache.org/licenses/LICENSE-2.0.html')
)]
#[OA\Server(url: 'http://localhost:8000/api')]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
)]
abstract class Controller
{
    //
}
