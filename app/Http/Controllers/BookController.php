<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookRequest;
use App\Repositories\Interface\BookInterface;

class BookController extends Controller
{
    protected $bookRepo;

    public function __construct(BookInterface $bookRepo)
    {
        $this->bookRepo = $bookRepo;
    }

    public function index()
    {
        try {

            $books = $this->bookRepo->getAllBooks();

            return view('welcome', compact('books'));
        } catch (\Exception $e) {
            toastr()->error('An error has occurred please try again later.');

            return redirect()->back()->withInput();
        }
    }

    public function create()
    {
        return view('create');
    }

    public function store(BookRequest $request)
    {
        try {
            $this->bookRepo->storeBook($request->validated());
            toastr()->success('Book successfully added!');

            return redirect()->route('books.index');
        } catch (\Exception $e) {
            toastr()->error('Unable to add this book at the moment. Please try again later.');

            return redirect()->back()->withInput();
        }
    }
}
