<?php

namespace App\Repositories\Interface;

interface BookInterface
{
    public function getAllBooks();
    public function storeBook($request);
}
