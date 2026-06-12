<?php

namespace Modules\SalesReturn\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateSaleReturnRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // 1. Ambil parameter id dari route
        $saleReturnParam = $this->route('sale_return');

        // 2. Antisipasi jika parameter berupa Object atau string ID murni
        $saleReturnId = is_object($saleReturnParam) ? $saleReturnParam->id : $saleReturnParam;

        // 3. Query manual ke database tenant untuk mengambil data aslinya
        $saleReturn = \Modules\SalesReturn\Entities\SaleReturn::find($saleReturnId);

        // 4. Fallback jika data tidak ditemukan (misal di-convert ke rupiah atau default 0)
        // Jika total_amount di DB disimpan dalam sen (*100), bagi 100 agar sinkron dengan input form
        $totalAmount = $saleReturn ? ($saleReturn->total_amount) : 0;

        return [
            'customer_id'         => 'required|numeric',
            'reference'           => 'required|string|max:255',
            'tax_percentage'      => 'required|integer|min:0|max:100',
            'discount_percentage' => 'required|integer|min:0|max:100',
            'shipping_amount'     => 'required|numeric',
            'total_amount'        => 'required|numeric',
            // 🎯 Gunakan variabel $totalAmount yang sudah kita cari secara aman di atas
            'paid_amount'         => 'required|numeric|max:' . $totalAmount,
            'status'              => 'required|string|max:255',
            'payment_method'      => 'required|string|max:255',
            'note'                => 'nullable|string|max:1000'
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('edit_sale_returns');
    }
}
