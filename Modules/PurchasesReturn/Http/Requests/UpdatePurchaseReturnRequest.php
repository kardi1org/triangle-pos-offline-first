<?php

namespace Modules\PurchasesReturn\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdatePurchaseReturnRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // Ambil ID dari route (karena berupa string/integer biasa sekarang)
        $purchaseReturnId = $this->route('purchase_return');

        // Lakukan query manual untuk mengambil objek modelnya
        $purchaseReturn = \Modules\PurchasesReturn\Entities\PurchaseReturn::findOrFail($purchaseReturnId);

        return [
            'date' => 'required|date',
            'reference' => 'required|string|max:255',
            'tax_percentage' => 'required|integer|min:0|max:100',
            'discount_percentage' => 'required|integer|min:0|max:100',
            'shipping_amount' => 'required|numeric',
            'total_amount' => 'required|numeric',
            // Gunakan variabel objek baru ($purchaseReturn) yang aman dari error string
            'paid_amount' => 'required|numeric|max:' . $purchaseReturn->total_amount,
            'status' => 'required|string|max:255',
            'payment_method' => 'required|string|max:255',
            'note' => 'nullable|string|max:1000'
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('edit_purchase_returns');
    }
}
