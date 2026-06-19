<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Books Collection')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
</head>

<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Books Collection</a>

            @auth
            <div class="d-flex align-items-center gap-3">
                <span class="text-light">
                    <strong>{{ auth()->user()->name ?? '' }}</strong>
                </span>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-light">
                        Logout
                    </button>
                </form>
            </div>
            @endauth
        </div>
    </nav>

    @yield('content')

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    @if(session('success'))
    <script>
        toastr.success(@json(session('success')));
    </script>
    @endif

    @if(session('error'))
    <script>
        toastr.error(@json(session('error')));
    </script>
    @endif

    @if($errors->any())
    <script>
        @foreach($errors->all() as $error)
                toastr.error(@json($error));
            @endforeach
    </script>
    @endif

    @yield('scripts')

</body>

</html>