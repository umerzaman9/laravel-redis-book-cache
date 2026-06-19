<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookRequest;
use App\Repositories\Interface\BookInterface;
use Illuminate\Http\JsonResponse;

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
            return $this->bookRepo->getAllBooks();
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    public function create()
    {
        return view('create');
    }

    public function store(BookRequest $request)
    {
        try {
            return $this->bookRepo->storeBook($request);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}
