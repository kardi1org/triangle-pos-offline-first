<?php

namespace Modules\Inventory\DataTables;

use Modules\Inventory\Entities\Inventory;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class InventoryDataTable extends DataTable
{

    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('total_amount', function ($data) {
                return format_currency($data->total_amount);
            })
            // ->addColumn('paid_amount', function ($data) {
            //     return format_currency($data->paid_amount);
            // })
            // ->addColumn('due_amount', function ($data) {
            //     return format_currency($data->due_amount);
            // })
            // ->addColumn('status', function ($data) {
            //     return view('Inventory::partials.status', compact('data'));
            // })
            // ->addColumn('payment_status', function ($data) {
            //     return view('Inventory::partials.payment-status', compact('data'));
            // })
            ->addColumn('action', function ($data) {
                return view('Inventory::partials.actions', compact('data'));
            });
    }

    public function query(Inventory $model)
    {
        return $model->newQuery();
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('Inventories-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom("<'row'<'col-md-3'l><'col-md-5 mb-2'B><'col-md-4'f>> .
                                'tr' .
                                <'row'<'col-md-5'i><'col-md-7 mt-2'p>>")
            ->orderBy(4)
            ->buttons(
                Button::make('excel')
                    ->text('<i class="bi bi-file-earmark-excel-fill"></i> Excel'),
                Button::make('print')
                    ->text('<i class="bi bi-printer-fill"></i> Print'),
                Button::make('reset')
                    ->text('<i class="bi bi-x-circle"></i> Reset'),
                Button::make('reload')
                    ->text('<i class="bi bi-arrow-repeat"></i> Reload')
            );
    }

    protected function getColumns()
    {
        return [
            Column::make('reference')
                ->className('text-center align-middle'),

            // Column::make('supplier_name')
            //     ->title('Supplier')
            //     ->className('text-center align-middle'),

            Column::computed('date')
                ->className('text-center align-middle'),

            Column::computed('total_amount')
                ->className('text-center align-middle'),

            // Column::computed('paid_amount')
            //     ->className('text-center align-middle'),

            // Column::computed('due_amount')
            //     ->className('text-center align-middle'),

            // Column::computed('payment_status')
            //     ->className('text-center align-middle'),

            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->className('text-center align-middle'),

            Column::make('created_at')
                ->visible(false)
        ];
    }

    protected function filename(): string
    {
        return 'Inventory_' . date('YmdHis');
    }
}
