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
}
