@extends('layouts.app')

@section('title', 'Create User')

@section('third_party_stylesheets')
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet" />
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
@endsection

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
        <li class="breadcrumb-item active">Create</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid mb-4">
        <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-lg-12">
                    @include('utils.alerts')
                    <div class="form-group">
                        <button class="btn btn-primary">Create User <i class="bi bi-check"></i></button>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="name">Name <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="name" required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="email">Email <span class="text-danger">*</span></label>
                                        <input class="form-control" type="email" name="email" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="password">Password <span class="text-danger">*</span></label>
                                        <input class="form-control" type="password" name="password" required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="password_confirmation">Confirm Password <span
                                                class="text-danger">*</span></label>
                                        <input class="form-control" type="password" name="password_confirmation" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="role">Role <span class="text-danger">*</span></label>
                                        <select class="form-control" name="role" id="role" required>
                                            <option value="" selected disabled>Select Role</option>
                                            @foreach (\Spatie\Permission\Models\Role::where('name', '!=', 'Super Admin')->get() as $role)
                                                <option value="{{ $role->name }}">{{ $role->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="date">Valid Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="valid_date" required
                                            value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                                    </div>
                                </div>

                            </div>

                            <div class="form-group">
                                <label for="outlets">Akses Outlet <span class="text-danger">*</span></label>
                                <div class="card border-light shadow-sm">
                                    <div class="card-body" style="max-height: 200px; overflow-y: auto;">
                                        <div class="row">
                                            @forelse($outlets as $outlet)
                                                <div class="col-md-6">
                                                    <div class="custom-control custom-checkbox mb-2">
                                                        <input type="checkbox" name="outlets[]" value="{{ $outlet->id }}"
                                                            class="custom-control-input" id="outlet_{{ $outlet->id }}"
                                                            @if (isset($user) && $user->outlets->contains($outlet->id)) checked @endif>
                                                        <label class="custom-control-label" for="outlet_{{ $outlet->id }}"
                                                            style="cursor: pointer;">
                                                            <strong>{{ $outlet->name }}</strong>
                                                            <br><small class="text-muted">{{ $outlet->address }}</small>
                                                        </label>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="col-12 text-center">
                                                    <span class="text-danger">Tidak ada outlet yang terdaftar untuk email
                                                        Anda.</span>
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                                @error('outlets')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                                <small class="text-muted mt-2 d-block">Centang outlet mana saja yang boleh diakses oleh user
                                    ini.</small>
                            </div>

                            <div class="form-group">
                                <label for="is_active">Status <span class="text-danger">*</span></label>
                                <select class="form-control" name="is_active" id="is_active" required>
                                    <option value="" selected disabled>Select Status</option>
                                    <option value="1">Active</option>
                                    <option value="2">Deactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="image">Profile Image <span class="text-danger">*</span></label>
                                <input id="image" type="file" name="image" data-max-file-size="500KB">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('third_party_scripts')
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endsection

@push('page_scripts')
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap4',
                placeholder: "Pilih Outlet...",
                allowClear: true
            });
        });
        FilePond.registerPlugin(
            FilePondPluginImagePreview,
            FilePondPluginFileValidateSize,
            FilePondPluginFileValidateType
        );
        const fileElement = document.querySelector('input[id="image"]');
        const pond = FilePond.create(fileElement, {
            acceptedFileTypes: ['image/png', 'image/jpg', 'image/jpeg'],
        });
        FilePond.setOptions({
            server: {
                url: "{{ route('filepond.upload') }}",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                }
            }
        });
    </script>
@endpush
