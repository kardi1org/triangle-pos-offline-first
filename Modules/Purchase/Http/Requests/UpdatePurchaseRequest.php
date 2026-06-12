<?php

namespace Modules\Purchase\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdatePurchaseRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // 1. Ambil ID purchase dari parameter route
        // Jika di route tertulis {purchase}, gunakan $this->route('purchase')
        $purchaseParam = $this->route('purchase');

        // 2. Jika parameter berupa Object Model, ambil ID-nya. Jika berupa string/ID langsung gunakan.
        $purchaseId = is_object($purchaseParam) ? $purchaseParam->id : $purchaseParam;

        // 3. Query manual ke database untuk mendapatkan nilai total_amount asli
        $purchase = \Modules\Purchase\Entities\Purchase::find($purchaseId);

        // Fallback jika data purchase tidak ditemukan di DB agar tidak memicu crash baru
        $totalAmount = $purchase ? $purchase->total_amount : 0;

        return [
            'supplier_id'         => 'required|numeric',
            'reference'           => 'required|string|max:255',
            'tax_percentage'      => 'required|integer|min:0|max:100',
            'discount_percentage' => 'required|integer|min:0|max:100',
            'shipping_amount'     => 'required|numeric',
            'total_amount'        => 'required|numeric',
            // 🎯 Gunakan variabel $totalAmount yang sudah aman di sini
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
        return Gate::allows('edit_purchases');
    }
}
