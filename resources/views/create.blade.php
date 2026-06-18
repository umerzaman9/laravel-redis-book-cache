<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Book</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light py-5">

    <main class="container" style="max-width: 500px;">
        <div class="card shadow-sm">
            <div class="card-body p-4">

                <div class="d-flex justify-content-between align-items-center border-b pb-3 mb-4">
                    <h1 class="h4 mb-0 text-dark font-weight-bold">Add a New Book</h1>
                    <a href="{{ route('books.index') }}"
                        class="btn btn-sm btn-link text-decoration-none text-muted">Back to List</a>
                </div>

                @if ($errors->any())
                <div class="alert alert-danger py-2 px-3 mb-4 small" role="alert">
                    <ul class="list-unstyled mb-0">
                        @foreach ($errors->all() as $error)
                        <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="{{ route('books.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-semibold">Book Title</label>
                        <input type="text" name="title" value="{{ old('title') }}" required
                            class="form-control bg-light">
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-semibold">Author</label>
                        <input type="text" name="author" value="{{ old('author') }}" required
                            class="form-control bg-light">
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-semibold">Blurb / Description</label>
                        <textarea name="blurb" rows="3" required
                            class="form-control bg-light">{{ old('blurb') }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-secondary small fw-semibold">Rating (1-5)</label>
                        <input type="number" name="rating" min="1" max="5" value="{{ old('rating', 5) }}" required
                            class="form-control bg-light">
                    </div>

                    <button type="submit" class="btn btn-primary w-full fw-semibold w-100 py-2">
                        Save Book to Redis
                    </button>
                </form>

            </div>
        </div>
    </main>

</body>

</html>