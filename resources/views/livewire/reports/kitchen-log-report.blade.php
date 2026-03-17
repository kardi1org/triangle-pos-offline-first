<div>
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="form-row">
                        <div class="col-md-6">
                            <label>Tanggal Mulai</label>
                            <input wire:model.live="start_date" type="date" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label>Tanggal Selesai</label>
                            <input wire:model.live="end_date" type="date" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <table class="table table-bordered table-striped text-center">
                        <thead>
                            <tr>
                                <th>Tgl & Jam</th>
                                <th>No Meja</th>
                                <th>Ref</th>
                                <th>Nama Item</th>
                                <th>Aksi</th>
                                <th>Qty</th>
                                <th>Alasan</th>
                                <th>Approve By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kitchenLogs as $log)
                                <tr>
                                    <td>{{ $log->created_at->format('d-m-Y H:i') }}</td>
                                    <td>
                                        @php
                                            $selectedTables = $log->sale->selected_table_ids ?? [];

                                            // Jika datanya masih berupa string (belum ter-cast), kita decode.
                                            // Jika sudah array, kita pakai langsung.
                                            if (is_string($selectedTables)) {
                                                $tableNames = json_decode($selectedTables, true) ?? [];
                                            } else {
                                                $tableNames = $selectedTables;
                                            }

                                            echo is_array($tableNames) && count($tableNames) > 0
                                                ? implode(', ', $tableNames)
                                                : '-';
                                        @endphp
                                    </td>
                                    <td>{{ $log->reference }}</td>
                                    <td>{{ $log->product_name }}</td>
                                    <td>
                                        <span
                                            class="badge {{ $log->type == 'void' ? 'badge-danger' : 'badge-success' }}">
                                            {{ strtoupper($log->type) }}
                                        </span>
                                    </td>
                                    <td>{{ $log->qty }}</td>
                                    <td>{{ $log->note }}</td>
                                    <td>{{ $log->approvedBy->name ?? 'System' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8">Data tidak ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div @class(['mt-3' => $kitchenLogs->hasPages()])>
                        {{ $kitchenLogs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
