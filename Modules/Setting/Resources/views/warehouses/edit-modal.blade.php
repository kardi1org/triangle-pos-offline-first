<div class="modal fade" id="editModal{{ $warehouse->id }}" tabindex="-1" role="dialog" aria-labelledby="editModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Warehouse</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('warehouses.update', $warehouse->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    {{-- Dropdown Outlet dengan logika Auto-Selected --}}
                    <div class="form-group">
                        <label for="outlet_id">Outlet <span class="text-danger">*</span></label>
                        <select class="form-control" name="outlet_id" id="outlet_id" required>
                            <option value="" disabled>Select Outlet</option>
                            @foreach ($outlets as $outlet)
                                <option value="{{ $outlet->id }}"
                                    {{ $warehouse->outlet_id == $outlet->id ? 'selected' : '' }}>
                                    {{ $outlet->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="code">Warehouse Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="code" value="{{ $warehouse->code }}"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="name">Warehouse Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" value="{{ $warehouse->name }}"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="text" class="form-control" name="phone" value="{{ $warehouse->phone }}">
                    </div>
                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea class="form-control" name="address" rows="3">{{ $warehouse->address }}</textarea>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="is_active{{ $warehouse->id }}"
                                name="is_active" {{ $warehouse->is_active ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_active{{ $warehouse->id }}">Active
                                Status</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-info text-white">Update Warehouse <i
                            class="bi bi-check"></i></button>
                </div>
            </form>
        </div>
    </div>
</div>
