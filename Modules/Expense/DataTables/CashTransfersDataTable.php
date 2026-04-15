<?php

namespace Modules\Expense\DataTables;

use Modules\Expense\Entities\CashTransfer;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class CashTransfersDataTable extends DataTable
{

    public function dataTable($query) {
        return datatables()
            ->eloquent($query)
            ->addColumn('amount', function ($data) {
              //  return format_currency($data->amount);
                return number_format($data->amount*100);
            }) 
            ->addColumn('amount_transferfrom', function ($data) {
                return number_format($data->amount_transferfrom);
            })
            ->addColumn('amount_change', function ($data) {
                return number_format($data->amount_change);
            })
            ->addColumn('amount_transferto', function ($data) {
                return number_format($data->amount_transferto);
            })
            ->addColumn('action', function ($data) {
                return view('expense::cashtransfers.partials.actions', compact('data'));
            });
    }

    public function query(CashTransfer $model) {
        return $model->newQuery();//->with('category');
    }

    public function html() {
        return $this->builder()
            ->setTableId('cash_transfers-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom("<'row'<'col-md-3'l><'col-md-5 mb-2'B><'col-md-4'f>> .
                                'tr' .
                                <'row'<'col-md-5'i><'col-md-7 mt-2'p>>")
            ->orderBy(6)
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

    protected function getColumns() {
        return [
            Column::make('date')
                ->className('text-center align-middle'),

            Column::make('reference')
                ->className('text-center align-middle'),

           /*  Column::make('category.category_name')
                ->title('Category')
                ->className('text-center align-middle'), */

          //  Column::computed('amount')
            Column::make('receive_type_transferfrom')
               ->title('Transfer From')
               ->className('text-center align-middle'),

            Column::make('amount_transferfrom')
                ->title('From Amount')
                ->alignEnd() // Rata kanan
                ->className('text-center align-middle'),

            Column::make('amount_change')
                ->title('Charge Amount')
                ->className('text-center align-middle'),

            Column::make('receive_type_transferto')
                ->title('Transfer To')
                ->className('text-center align-middle'),

            Column::make('amount_transferto')
                ->title('To Amount')
                ->className('text-center align-middle'),
            
            Column::make('details')
                ->className('text-center align-middle'),  
                
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->className('text-center align-middle'),

            Column::make('created_at')
                ->visible(false)
        ];
    }

    protected function filename(): string {
        return 'Expenses_' . date('YmdHis');
    }
}
