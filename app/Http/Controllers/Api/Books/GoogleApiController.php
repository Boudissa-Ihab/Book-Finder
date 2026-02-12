<?php

namespace App\Http\Controllers\Api\Books;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Books\SearchGoogleBooksRequest;
use App\Models\Book;
use App\Services\GoogleBookApiService;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GoogleApiController extends Controller
{
    public function __construct(private GoogleBookApiService $googleBookService) {}

    /**
     * GET /google/books/view?query={query}
     * Return Google Books API raw response for a given query.
     */
    public function view(SearchGoogleBooksRequest $request)
    {
        try {
            $validated = $request->validated();
            $books = $this->googleBookService->search(
                $validated['q'],
                $validated['maxResults'],
                $validated['startIndex'],
            );

            return response()->json([
                'data' => $books,
                'meta' => [
                    'query' => $validated['q'],
                    'count' => count($books),
                ],
            ], status: 200);

        } catch (RequestException $e) {
            $status = $e->response->status();

            if ($status === 429) {
                Log::error('Rate limit exceeded for Google Books API', [
                    'method' => __METHOD__,
                    'error' => $e->getMessage(),
                ]);
                return response()->json([
                    'message' => 'Google Books API rate limit exceeded.',
                ], 429);
            }

            if ($status >= 500) {
                Log::error('Google Books API search failed', [
                    'method' => __METHOD__,
                    'error' => $e->getMessage(),
                ]);
                return response()->json([
                    'message' => 'Google Books service unavailable.',
                ], 503);
            }

            throw $e;

        } catch (\Exception $e) {
            Log::error('Could not handle the current query', [
                'method' => __METHOD__,
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'message' => 'An unexpected error occurred.',
            ], 500);
        }
    }

    /**
     * GET /google/books/search?query={query}
     * Return simplified book data from Google Books API for a given query.
     */
    public function search(SearchGoogleBooksRequest $request)
    {
        try {
            $validated = $request->validated();
            $books = $this->googleBookService->search(
                $validated['q'],
                $validated['maxResults'],
                $validated['startIndex'],
            );
            // Simplify the data to match the local database schema
            $simplifiedBooks = $this->googleBookService->simplifySearchResults($books);

            return response()->json([
                'data' => $simplifiedBooks,
                'meta' => [
                    'query' => $validated['q'],
                    'count' => count($simplifiedBooks),
                ],
            ], 200);

        } catch (RequestException $e) {
            $status = $e->response->status();

            if ($status === 429) {
                Log::error('Rate limit exceeded for Google Books API', [
                    'method' => __METHOD__,
                    'error' => $e->getMessage(),
                ]);
                return response()->json([
                    'message' => 'Google Books API rate limit exceeded.',
                ], 429);
            }

            if ($status >= 500) {
                Log::error('Google Books API search failed', [
                    'method' => __METHOD__,
                    'error' => $e->getMessage(),
                ]);
                return response()->json([
                    'message' => 'Google Books service unavailable.',
                ], 503);
            }

            throw $e;

        } catch (\Exception $e) {
            Log::error('Could not handle the current query', [
                'method' => __METHOD__,
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'message' => 'An unexpected error occurred.',
            ], 500);
        }
    }

    /**
     * POST /google/books/import
     * Import a book from Google Books API into local database.
     */
    public function import(SearchGoogleBooksRequest $request)
    {
        try {
            $validated = $request->validated();
            // Fetch book data from Google Books
            $books = $this->googleBookService->search(
                $validated['q'],
                $validated['maxResults'],
                $validated['startIndex'],
            );
            // Simplify the data to match the local database schema
            $simplifiedBooks = $this->googleBookService->simplifySearchResults($books);

            // Process each book and save them to database (using ISBN as unique ID)
            $rows = collect($simplifiedBooks['data'])->map(fn ($book) => [
                'title' => $book['title'],
                'author' => $book['author'],
                'isbn' => $book['isbn'],
                'cover_url' => $book['cover_url'],
                'created_at' => now(),
                'updated_at' => now(),
            ])->toArray();

            Book::upsert(
                $rows,
                ['isbn'],
                ['title', 'author', 'cover_url', 'created_at', 'updated_at']
            );

            return response()->json([
                'message' => 'Books imported successfully.',
            ], 201);

        } catch (RequestException $e) {
            $status = $e->response->status();

            if ($status === 429) {
                Log::error('Rate limit exceeded for Google Books API', [
                    'method' => __METHOD__,
                    'error' => $e->getMessage(),
                ]);
                return response()->json([
                    'message' => 'Google Books API rate limit exceeded.',
                ], 429);
            }

            if ($status >= 500) {
                Log::error('Google Books API search failed', [
                    'method' => __METHOD__,
                    'error' => $e->getMessage(),
                ]);
                return response()->json([
                    'message' => 'Google Books service unavailable.',
                ], 503);
            }

            throw $e;

        } catch (\Exception $e) {
            Log::error('Could not handle the current query', [
                'method' => __METHOD__,
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'message' => 'An unexpected error occurred.',
            ], 500);
        }
    }
}
