<?php

namespace App\Exports;

//use App\Models\MutationCash;
use Modules\Reports\Entities\CashReport;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings; // Untuk menambahkan header kolom
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;


class MutationCashExport implements FromCollection, WithHeadings
{
    use Exportable;

    protected $table = 'laporan_kas';
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return CashReport::all();
    }

    public function headings(): array
    {
        return ['Date','Reference','Details','Debet','Kredit','Saldo'];
    }

    /* public function view(): View
    {
        // TODO: Implement view() method.
        return view('sales.SalesAllExcel',[
            'sales' => CashReport::all()
        ]);
    } */

}
