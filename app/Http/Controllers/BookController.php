<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class BookController extends Controller
{
    // 1. Display the books on the homepage
    public function index()
    {
        // Fetch all items from the sorted set using -inf and +inf
        // Redis::zrangebyscore returns an array of values (the book titles)
        $titles = Redis::zrangebyscore('books', '-inf', '+inf');

        $books = [];

        foreach ($titles as $title) {
            // If storing by title, the key looks like "books:The Hobbit"
            $bookData = Redis::hgetall("books:$title");

            if (!empty($bookData)) {
                $books[] = $bookData;
            }
        }

        return view('welcome', compact('books'));
    }

    // 2. Show the "Add New Book" view
    public function create()
    {
        return view('create');
    }

    // 3. Handle the form submission (Server Action equivalent)
    public function store(BookRequest $request)
    {
        $title = $request->input('title');
        $id = now()->timestamp; // Clean integer timestamp

        // Try adding the title to the Sorted Set (NX option means "Only add if it doesn't exist")
        // Predis syntax for options passes them as an array or trailing arguments
        $unique = Redis::zadd('books', $id, $title);

        if (!$unique) {
            return back()->withErrors(['title' => 'That book has already been added.']);
        }

        // Save the full book details into a Redis Hash
        Redis::hmset("books:$title", [
            'title' => $title,
            'author' => $request->input('author'),
            'blurb' => $request->input('blurb'),
            'rating' => $request->input('rating'),
        ]);

        return redirect()->route('books.index');
    }
}
