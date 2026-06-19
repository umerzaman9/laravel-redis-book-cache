@extends('layouts.app')

@section('title', 'Books on Redis!')

@section('content')

<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1050;">
    <div id="liveToast" class="toast align-items-center text-bg-info border-0 shadow-lg" role="alert"
        aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body fw-medium text-white" id="toastMessage">
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                aria-label="Close"></button>
        </div>
    </div>
</div>

<main class="container py-3" style="max-width: 700px;">
    <div class="card shadow-sm">
        <div class="card-body p-4">

            <nav class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                <h1 class="h3 mb-0 text-dark fw-bold">Books Collection For You</h1>
                <a href="{{ route('books.create') }}" class="btn btn-primary px-3 fw-semibold">
                    Add a new book
                </a>
            </nav>

            <div class="d-flex flex-column gap-3">
                @if(count($books) === 0)
                <p class="text-muted text-center py-4 my-0">No books found. Try adding one!</p>
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

    <div class="card shadow-sm border-1 my-3 mb-4">
        <div class="card-header bg-warning text-dark font-weight-bold">
            Top Rated Books Leaderboard
        </div>
        <ul class="list-group list-group-flush">
            @forelse($topBooks as $index => $topBook)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <strong>#{{ $index + 1 }}</strong> {{ $topBook->title }}
                    <small class="text-muted">by {{ $topBook->author }}</small>
                </div>
                <span class="badge bg-primary rounded-pill">⭐ {{ $topBook->rating }}</span>
            </li>
            @empty
            <li class="list-group-item text-muted text-center">No ratings recorded yet!</li>
            @endforelse
        </ul>
    </div>
</main>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/socket.io-client@4.7.5/dist/socket.io.min.js"></script>
<script src="{{ asset('js/custom.js') }}"></script>
@endsection