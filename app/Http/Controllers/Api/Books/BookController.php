<?php

namespace App\Http\Controllers\Api\Books;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use OpenApi\Attributes as OA;

class BookController extends Controller
{
    #[OA\Get(
        path: '/books',
        summary: 'Get all books',
        description: 'Get books of current authenticated user',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'OK',
                content: new OA\JsonContent(ref: '#/components/schemas/Books')
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 500, description: 'Could not get the books list'),
        ],
        tags: ['Books']
    )]
    public function index(Request $request)
    {
        $request->validate([
            'per_page' => 'nullable|integer',
        ]);

        try {
            // Adding simple pagination, with a maximum of 100 items per page
            $per_page = $request->per_page ?? 100;
            if ($per_page < 0 || $per_page > 100)
                $per_page = 100;

            $books = Book::take($per_page)
                ->orderBy('title', 'asc')->get();

            return BookResource::collection($books);
        } catch (\Exception $e) {
            Log::alert($e->getMessage());

            return response()->json([
                'message' => "Could not get the books list"
            ], 500);
        }
    }
}
