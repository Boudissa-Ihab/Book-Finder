<?php

namespace App\Http\Controllers\Api\Books;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Books\FavoriteRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use App\Models\UserBook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use OpenApi\Attributes as OA;

class FavoriteController extends Controller
{
    #[OA\Get(
        path: '/favorites',
        summary: 'Get user favorite books',
        description: 'Get list of books added to user favorites',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'OK',
                content: new OA\JsonContent(ref: '#/components/schemas/Books')
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 500, description: 'Could not get the user\'s favorite books'),
        ],
        tags: ['Favorites']
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

            $user = $request->user();
            $favorites = $user->favorites()->take($per_page)
                ->orderBy('title', 'asc')->get();

            return BookResource::collection($favorites);

        } catch (\Exception $e) {
            Log::error("Error getting user's favorite books: \n" . $e->getMessage());
            return response()->json([
                'message' => "Could not get the user's favorite books.",
            ], 500);
        }
    }

    /**********************************/
    #[OA\Post(
        path: '/favorites/{book_id}',
        summary: 'Add book to favorites',
        description: 'Add a book to user favorites',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'book_id',
                description: 'Book ID',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(response: 201, description: 'Book added to favorites'),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 404, description: 'Book not found'),
            new OA\Response(response: 409, description: 'Book already in your favorites'),
            new OA\Response(response: 500, description: 'Could not add the book to your favorites'),
        ],
        tags: ['Favorites']
    )]
    public function store(FavoriteRequest $request, $book_id)
    {
        try {
            $user = $request->user();
            $book = Book::findOrFail($book_id);

            // Check if the book is in the user's favorites already (since we added a unique constraint)
            // If that's the case, we prevent attaching again and return a 409 Conflict response
            if ($user->alreadyFavorite($book)) {
                $favorite = UserBook::where('user_id', $user->id)
                    ->where('book_id', $book->id)
                    ->select('created_at')->first();

                return response()->json([
                    'message' => 'Book already in your favorites.',
                    'favorite' => [
                        'book' => new BookResource($book),
                        'added_at' => $favorite->created_at?->format('Y/m/d H:i:s'),
                    ],
                ], 409);
            }

            $user->favorites()->attach($book);
            $favorite = UserBook::where('user_id', $user->id)
                ->where('book_id', $book->id)
                ->select('created_at')->first();

            return response()->json([
                'message' => 'Book added to your favorites successfully.',
                'favorite' => [
                    'book' => new BookResource($book),
                    'added_at' => $favorite->created_at?->format('Y/m/d H:i:s'),
                ],
            ], 201);

        } catch (\Exception $e) {
            Log::error("Error adding book to user's favorites: \n" . $e->getMessage());
            return response()->json([
                'message' => "Could not add the book to your favorites.",
            ], 500);
        }
    }

    /**********************************/
    #[OA\Delete(
        path: '/favorites/{book_id}',
        summary: 'Remove book from favorites',
        description: 'Remove a book from user favorites',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'book_id',
                description: 'Book ID',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Book removed from your favorites successfully'),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 404, description: 'Book not in your favorites'),
            new OA\Response(response: 500, description: 'Could not remove the book from your favorites'),
        ],
        tags: ['Favorites']
    )]
    public function destroy(FavoriteRequest $request, $book_id)
    {
        try {
            $user = $request->user();
            $book = Book::findOrFail($book_id);

            // Check if the book is not in the user's favorites
            // If that's the case, we return a 404 Not Found response
            if (! $user->alreadyFavorite($book)) {
                return response()->json([
                    'message' => 'Book not in your favorites.',
                ], 404);
            }

            $user->favorites()->detach($book);

            return response()->json([
                'message' => 'Book removed from your favorites successfully.',
            ], 204);

        } catch (\Exception $e) {
            Log::error("Error removing book from user's favorites: \n" . $e->getMessage());
            return response()->json([
                'message' => "Could not remove the book from your favorites.",
            ], 500);
        }
    }
}
