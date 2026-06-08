<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Outlet - {{ config('app.name') }}</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f4f6f9;
            height: 100vh;
            display: flex;
            align-items: center;
        }

        .card {
            border-radius: 15px;
            overflow: hidden;
        }

        .list-group-item:hover {
            background-color: #e9ecef;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">

                {{-- 🎯 DITAMBAHKAN: Menampilkan Pesan Error Validasi jika ada yang gagal --}}
                @if ($errors->any())
                    <div class="alert alert-danger shadow-sm">
                        <ul class="mb-0 pl-3 small">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('warning'))
                    <div class="alert alert-warning text-center small mb-3">{{ session('warning') }}</div>
                @endif

                <div class="card shadow-lg border-0">
                    <div class="card-header bg-primary text-white text-center py-3">
                        <h5 class="mb-0 font-weight-bold">PILIH OUTLET</h5>
                        <small>{{ auth()->user()->name }} ({{ auth()->user()->email }})</small>
                    </div>
                    <div class="card-body p-4">

                        {{-- FORM UTAMA --}}
                        <form action="{{ route('auth.select-outlet.post') }}" method="POST">
                            @csrf

                            {{-- 🎯 DITAMBAHKAN: Input hidden penampung ID outlet agar kirim data via button bekerja stabil di semua browser --}}
                            <input type="hidden" name="outlet_id" id="selected_outlet_id">

                            <div class="list-group mb-4">
                                @foreach ($outlets as $outlet)
                                    {{-- Mengubah button submit agar mengisi value ke input hidden sebelum form terkirim --}}
                                    <button type="submit"
                                        onclick="document.getElementById('selected_outlet_id').value='{{ $outlet->id }}';"
                                        class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3">
                                        <div>
                                            <span class="h6 mb-0">{{ $outlet->name }}</span>
                                            <br><small class="text-muted">{{ $outlet->address }}</small>
                                        </div>
                                        <i class="bi bi-arrow-right-circle text-primary h4 mb-0"></i>
                                    </button>
                                @endforeach
                            </div>
                        </form>

                        <form action="{{ route('auth.select-outlet.cancel') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-link btn-block text-danger">
                                <i class="bi bi-box-arrow-left"></i> Cancel & Logout
                            </button>
                        </form>
                    </div>
                </div>
                <p class="text-center mt-4 text-muted small">&copy; {{ date('Y') }} {{ config('app.name') }}</p>
            </div>
        </div>
    </div>
</body>

</html>
