<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModalLabel">Add Warehouse</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('warehouses.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    {{-- Dropdown Outlet --}}
                    <div class="form-group">
                        <label for="outlet_id">Outlet <span class="text-danger">*</span></label>
                        <select class="form-control" name="outlet_id" id="outlet_id" required>
                            <option value="" selected disabled>Select Outlet</option>
                            @foreach ($outlets as $outlet)
                                <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="code">Warehouse Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="code" required placeholder="Ex: WH-01">
                    </div>
                    <div class="form-group">
                        <label for="name">Warehouse Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" required
                            placeholder="Ex: Central Warehouse">
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="text" class="form-control" name="phone" placeholder="Ex: 0812345678">
                    </div>
                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea class="form-control" name="address" rows="3" placeholder="Warehouse location details..."></textarea>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active"
                                value="1" checked>
                            <label class="custom-control-label" for="is_active">Active Status</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Warehouse <i class="bi bi-check"></i></button>
                </div>
            </form>
        </div>
    </div>
</div>
