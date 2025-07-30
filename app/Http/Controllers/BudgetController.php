<?php

namespace App\Http\Controllers;

use App\DataTables\BudgetsDataTable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Exp;
use app\Models\Budget;
use App\Models\Budget as ModelsBudget;
//import Http Request
use Illuminate\Http\RedirectResponse;
//return type View
use Illuminate\View\View;



class BudgetController extends Controller
{
    // public function index()
    // {
    //     return view('budget/index');
    //     //echo 'Hello World';
    // }

    public function index(BudgetsDataTable $dataTable)
    {
        //abort_if(Gate::denies('access_expenses'), 403);

        //return $dataTable->render('budget/index');
        return $dataTable->render('budget.index');
    }

    public function create()
    {
        return view('budget/create');
        //echo 'Hello World';
    }

    public function store(Request $request)
    {
        //validate form
        $request->validate([
            'date' => 'required|date',
            'amount' => 'required|numeric|max:2147483647'
        ]);


        //create post
        ModelsBudget::create([
            'date'     => $request->date,
            'amount'     => $request->amount,
            'details'   => $request->details
        ]);

        //redirect to index
        toast('Buadget Sales Created!', 'success');

        return redirect()->route('budget.create');
    }

    public function destroy($id): RedirectResponse
    {
        //abort_if(Gate::denies('delete_expenses'), 403);

        $budget = ModelsBudget::findOrFail($id);

        $budget->delete();

        toast('Budget Deleted!', 'warning');

        return redirect()->route('budget.index');
    }

    public function edit(string $id): View
    {
        //get post by ID
        $budget = ModelsBudget::findOrFail($id);

        //render view with post
        return view('budget.edit', compact('budget'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'date' => 'required|date',
            'amount' => 'required|numeric|max:2147483647'
        ]);

        //get budget by ID
        $budget = ModelsBudget::findOrFail($id);


        $budget->update([
            'date'     => $request->date,
            'amount'   => $request->amount,
            'details'   => $request->details
        ]);
        //redirect to index
        return redirect()->route('budget.index')->with(['success' => 'Data Berhasil Diubah!']);
    }
}
