<?php

namespace App\Repositories\Repository;

use App\Models\Book;
use App\Repositories\Interface\BookInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Throwable;

class BookRepository implements BookInterface

{
    public function getAllBooks()
    {
        try {
            return Cache::remember('all_books', 600, function () {
                Log::info('Cache Miss! Querying MySQL Database...');
                return Book::latest()->get();
            });
        } catch (Throwable $e) {
            throw $e;
        }
    }

    public function storeBook($data)
    {
        try {
            $book = Book::create([
                'title'  => $data['title'],
                'author' => $data['author'],
                'blurb'  => $data['blurb'],
                'rating' => $data['rating'],
            ]);

            // Track the book in our Redis Sorted Set Leaderboard
            // Syntax: Redis::zadd('key', score, value);
            Redis::zadd('books_leaderboard', $book->rating, $book->id);
            Cache::forget('all_books');

            $payload = json_encode([
                'title'  => $book->title,
                'author' => $book->author,
                'time'   => now()->format('H:i:s'),
            ]);

            Redis::publish('book-actions', $payload);

            return $book;
        } catch (Throwable $e) {
            throw $e;
        }
    }

    // Fetch the top-rated books directly from RAM
    public function getTopRatedBooks($limit = 5)
    {
        try {
            // zrevrange fetches items from highest score to lowest.
            // 0 means the 1st item, ($limit - 1) gets our cutoff (e.g., index 4 for top 5).
            $topBookIds = Redis::zrevrange('books_leaderboard', 0, $limit - 1);

            if (empty($topBookIds)) {
                return collect();
            }

            // Fetch the full book models from MySQL using the fast primary keys array.
            // To maintain the perfect Redis ranking order, we use field sorting.
            $idsString = implode(',', $topBookIds);
            $topRatedBook = Book::whereIn('id', $topBookIds)->orderByRaw("FIELD(id, $idsString)")->get();
            return $topRatedBook;
        } catch (Throwable $e) {
            throw $e;
        }
    }
}
