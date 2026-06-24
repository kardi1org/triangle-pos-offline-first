<?php

namespace Modules\Expense\Http\Controllers;

use Modules\Expense\DataTables\ExpenseCategoriesDataTable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Modules\Expense\Entities\ExpenseCategory;

class ExpenseCategoriesController extends Controller
{

    public function index(ExpenseCategoriesDataTable $dataTable)
    {
        abort_if(Gate::denies('access_expense_categories'), 403);

        return $dataTable->render('expense::categories.index');
    }

    public function store(Request $request)
    {
        abort_if(Gate::denies('access_expense_categories'), 403);

        $request->validate([
            'category_name' => 'required|string|max:255|unique:expense_categories,category_name',
            'category_description' => 'nullable|string|max:1000'
        ]);

        ExpenseCategory::create([
            'category_name' => $request->category_name,
            'category_description' => $request->category_description
        ]);

        toast('Expense Category Created!', 'success');

        return redirect()->route('expense-categories.index');
    }


    public function edit($id)
    {
        abort_if(Gate::denies('access_expense_categories'), 403);

        // Ambil data kategori secara manual menggunakan $id untuk menghindari 404 Model Binding di Linux
        $expenseCategory = ExpenseCategory::findOrFail($id);

        return view('expense::categories.edit', compact('expenseCategory'));
    }


    public function update(Request $request, $id)
    {
        abort_if(Gate::denies('access_expense_categories'), 403);

        // Ambil data kategori secara manual menggunakan $id untuk menghindari 404 Model Binding di Linux
        $expenseCategory = ExpenseCategory::findOrFail($id);

        $request->validate([
            'category_name' => 'required|string|max:255|unique:expense_categories,category_name,' . $id,
            'category_description' => 'nullable|string|max:1000'
        ]);

        $expenseCategory->update([
            'category_name' => $request->category_name,
            'category_description' => $request->category_description
        ]);

        toast('Expense Category Updated!', 'info');

        return redirect()->route('expense-categories.index');
    }


    public function destroy($id)
    {
        abort_if(Gate::denies('access_expense_categories'), 403);

        // Ambil data kategori secara manual menggunakan $id untuk menghindari 404 Model Binding di Linux
        $expenseCategory = ExpenseCategory::findOrFail($id);

        if ($expenseCategory->expenses()->isNotEmpty()) {
            return back()->withErrors('Can\'t delete beacuse there are expenses associated with this category.');
        }

        $expenseCategory->delete();

        toast('Expense Category Deleted!', 'warning');

        return redirect()->route('expense-categories.index');
    }
}
