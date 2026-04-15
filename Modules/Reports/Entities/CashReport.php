<?php

namespace Modules\Reports\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;
use Modules\MethodePay\Entities\MethodePay;
use App\Livewire\Reports\MutationCashReport;

class CashReport extends Model
{
    use HasFactory;

    protected $fillable = ['date','reference','details','debet','kredit','saldo'];
    protected $table = 'laporan_kas';

}