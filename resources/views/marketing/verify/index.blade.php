<!DOCTYPE html>
<html>

<head>
    <title>Upload Email List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light py-5">

    <div class="container">
        <div class="card shadow p-4 mx-auto" style="max-width: 500px;">
            <h4 class="text-center mb-3">Upload Email List (.txt)</h4>

            {{-- Success message --}}
            @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }} <br>
                <strong>File:</strong> {{ session('file') }}
            </div>
            @endif

            {{-- Upload form --}}
            <form action="{{ route('app.verify.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label for="file" class="form-label">Choose TXT File</label>
                    <input type="file" name="file" class="form-control" accept=".txt" required>
                    @error('file')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary w-100">Upload</button>
            </form>
        </div>
    </div>

</body>

</html>