<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Books on Redis!</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light py-5">

    <main class="container" style="max-width: 700px;">
        <div class="card shadow-sm">
            <div class="card-body p-4">

                <nav class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                    <h1 class="h3 mb-0 text-dark fw-bold">Books on Redis!</h1>
                    <a href="{{ route('books.create') }}" class="btn btn-primary px-3 fw-semibold">
                        Add a new book
                    </a>
                </nav>

                <div class="d-flex flex-column gap-3">
                    @if(count($books) === 0)
                    <p class="text-muted text-center py-4 my-0">No books found in Redis. Try adding one!</p>
                    @else
                    @foreach($books as $book)
                    <div class="card bg-body-tertiary border p-3 shadow-sm">
                        <h2 class="h5 mb-1 text-primary fw-semibold">{{ $book['title'] ?? 'Unknown Title' }}</h2>
                        <p class="text-muted small fst-italic mb-2">By {{ $book['author'] ?? 'Unknown Author' }}</p>
                        <p class="text-dark mb-3">{{ $book['blurb'] ?? 'No description available.' }}</p>
                        <div>
                            <span class="badge text-bg-warning px-2.5 py-1.5 small fw-semibold">
                                Rating: {{ $book['rating'] ?? 'N/A' }} / 5
                            </span>
                        </div>
                    </div>
                    @endforeach
                    @endif
                </div>

            </div>
        </div>
    </main>

</body>

</html>