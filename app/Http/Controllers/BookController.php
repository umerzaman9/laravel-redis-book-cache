<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookRequest;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class BookController extends Controller
{
    // 1. Display the books (Checking Cache First!)
    public function index()
    {
        // Cache::remember checks Redis for a key named 'all_books'.
        // If it doesn't exist, it executes the function, stores the collection 
        // in Redis for 600 seconds (10 mins), and returns the data.
        $books = Cache::remember('all_books', 600, function () {
            Log::info('Cache Miss! Querying MySQL Database...');
            return Book::latest()->get();
        });

        return view('welcome', compact('books'));
    }

    public function create()
    {
        return view('create');
    }

    // 2. Save new book (Write to DB + Clear old Cache)
    public function store(BookRequest $request)
    {
        // 1. Permanently save the book to MySQL
        $book = Book::create([
            'title' => $request->input('title'),
            'author' => $request->input('author'),
            'blurb' => $request->input('blurb'),
            'rating' => $request->input('rating'),
        ]);

        // 2. CRUCIAL: Clear the Redis cache!
        // Because a new book was added, old cached list is outdated.
        Cache::forget('all_books');

        // 3. PUBLISH Real-Time Event to Redis
        // We compile a neat array, convert it to a JSON string, and blast it out.
        $payload = json_encode([
            'title' => $book->title,
            'author' => $book->author,
            'time' => now()->format('H:i:s')
        ]);

        Redis::publish('book-actions', $payload);

        return redirect()->route('books.index');
    }
}
