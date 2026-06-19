<?php

namespace App\Repositories\Repository;

use App\Http\Requests\BookRequest;
use App\Models\Book;
use App\Repositories\Interface\BookInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class BookRepository implements BookInterface

{
    public function getAllBooks()
    {
        try {
            $books = Cache::remember('all_books', 600, function () {
                Log::info('Cache Miss! Querying MySQL Database...');
                return Book::latest()->get();
            });

            return view('welcome', compact('books'));
        } catch (\Exception $e) {
            Log::error('Error fetching books: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch books.'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    public function storeBook($request)
    {
        try {
            $book = Book::create([
                'title' => $request->input('title'),
                'author' => $request->input('author'),
                'blurb' => $request->input('blurb'),
                'rating' => $request->input('rating'),
            ]);

            Cache::forget('all_books');

            $payload = json_encode([
                'title' => $book->title,
                'author' => $book->author,
                'time' => now()->format('H:i:s'),
            ]);

            Redis::publish('book-actions', $payload);

            return redirect()->route('books.index');
        } catch (\Exception $e) {
            Log::error('Error creating book: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to create book.'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}
