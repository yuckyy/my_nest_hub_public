<?php

namespace App\Http\Controllers;

use App\Models\ExpenseType;
use App\Models\File;
use App\Models\Payment;
use App\Models\Property;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Unit;
use App\Models\Lease;
use App\Models\Invoice;
use App\Models\Expenses;
use Illuminate\Support\Facades\DB;
use Storage;

class ExpensesController extends Controller
{
    public function index(Request $request) {
        $user = Auth::user();
        $properties = $user->properties;

        $totalIncome = 0;
        $totalIncome12 = 0;
        $totalExpenses = 0;
        $totalExpenses12 = 0;
        foreach($properties as $property){
            $totalIncome += $property->totalIncome();
            $totalIncome12 += $property->totalIncome12();
            $totalExpenses += $property->totalExpenses();
            $totalExpenses12 += $property->totalExpenses12();
        }

        $sql = '';//for testing
        $chart = [];
        for($m = 11; $m >= 0; $m--){
            //$currentMonth = Carbon::now()->startOfMonth();
            $start = Carbon::now()->startOfMonth()->subMonth($m)->startOfMonth();
            $end = Carbon::now()->startOfMonth()->subMonth($m)->endOfMonth();

            $expenses = 0;
            $income = 0;
            foreach($properties as $property){
                $expenses_unit = Expenses::join('units', 'units.id', '=', 'expenses.unit_id')
                    ->where('units.property_id',$property->id)
                    ->where('expense_date', '>=', $start->format('Y-m-d'))
                    ->where('expense_date', '<=', $end->format('Y-m-d'))
                    ->sum('amount');
                $expenses_property = Expenses::where('property_id',$property->id)
                    ->where('expense_date', '>=', $start->format('Y-m-d'))
                    ->where('expense_date', '<=', $end->format('Y-m-d'))
                    ->sum('amount');
                $expenses += $expenses_unit + $expenses_property;

                $query = DB::table('invoices');
                $query->join('leases', 'leases.id', '=', 'invoices.base_id');
                $query->join('units', 'units.id', '=', 'leases.unit_id');
                $query->join('properties', 'properties.id', '=', 'units.property_id');
                $query->where('invoices.is_lease_pay', '=', 1);
                $query->where('properties.id',$property->id);
                $query->where('due_date', '>=', $start->format('Y-m-d'));
                $query->where('due_date', '<=', $end->format('Y-m-d'));
                $sql = str_replace_array('?', $query->getBindings(), $query->toSql());
                $invoices = $query->pluck('invoices.id');
                $income += Payment::whereIn('invoice_id',$invoices)->sum('amount');
            }

            $chart[$m] = [];
            $chart[$m]['month'] = $start->format('M Y');
            $chart[$m]['start'] = $start->format('Y-m-d');
            $chart[$m]['end'] = $end->format('Y-m-d');
            $chart[$m]['expenses'] = $expenses;
            $chart[$m]['income'] = $income;
            $chart[$m]['profit'] = $income - $expenses;
        }

        return view(
            'expenses.index',
            [
                'user' => $user,
                'properties' => $properties,
                'totalIncome' => $totalIncome,
                'totalIncome12' => $totalIncome12,
                'totalExpenses' => $totalExpenses,
                'totalExpenses12' => $totalExpenses12,

                'chart' => $chart,
                'sql' => $sql,
            ]
        );
    }

    public function ajaxGetExpenses(Request $request)
    {
        if(!empty($request->unit_id)){
            // Unit Page
            $query = DB::table('expenses');
            $query->where('expenses.unit_id', '=', $request->unit_id);

            $isPropertyPage = false;
        } else {
            // Property Page
            if(!empty($request->parent)){
                if($request->parent == 'property'){
                    // Property based expenses
                    $query = DB::table('expenses');
                    $query->where('expenses.property_id', '=', $request->property_id);
                    $query->select(
                        'expenses.*',
                        DB::raw('"-" AS unit_name')
                    );
                } else {
                    // Unit based expenses
                    $query = DB::table('expenses');
                    $query->join('units', 'units.id', '=', 'expenses.unit_id');
                    $query->where('units.property_id', '=', $request->property_id);
                    $query->select(
                        'expenses.*',
                        DB::raw('units.name AS unit_name')
                    );
                }
            } else {
                // Unit based && property based expenses
                $query = DB::table('expenses');
                $query->leftJoin('units', 'units.id', '=', 'expenses.unit_id');
                $query->where('units.property_id', '=', $request->property_id);
                $query->orWhere('expenses.property_id', '=', $request->property_id);
                $query->select(
                    'expenses.*',
                    DB::raw('IFNULL(units.name, "-") AS unit_name')
                );
            }

            $isPropertyPage = true;
        }

        //easy filter
        /*if (!empty($request['paid'])) {
            if ($request['paid'] == 'paid'){
                $query->whereRaw('IFNULL(p.paid, 0) - invoices.amount = 0');
            } else {
                $query->whereRaw('IFNULL(p.paid, 0) - invoices.amount < 0');
            }
        }*/

        //advanced filter
        if (!empty($request['expense_date'])) {
            $query->where('expenses.expense_date', '=', $request['expense_date']);
        }
        if (!empty($request['name'])) {
            $query->where('expenses.name', 'like', '%' . $request['name'] . '%');
        }
        if (!empty($request['amount'])) {
            $query->whereRaw('expenses.amount = ?', [$request['amount']]);
        }
        if (!empty($request['monthly'])) {
            $query->whereRaw('expenses.monthly = ?', [$request['monthly'] == 'Yes']);
        }
        if (!empty($request['unit_id'])) {
            $query->whereRaw('expenses.unit_id = ?', [$request['unit_id']]);
        }

        //Sorting
        $availableColumns = [
            'expense_date',
            'name',
            'amount',
            'monthly',
            'unit_id',
            'unit_name',
        ];
        $column = $request->get('column');
        if (in_array($column, $availableColumns)) {
            if ($request->get('order') === 'asc') {
                $query->orderBy($column, 'asc');
            } else {
                $query->orderBy($column, 'desc');
            }
        }

        $expenses = $query->paginate(10);

        return view(
            'expenses.ajax-expenses',
            [
                'expenses' => $expenses,
                'isPropertyPage' => $isPropertyPage,
            ]
        );
    }

    public function addExpense(Request $request)
    {
        if(!empty($request->unit_id)){
            $unit = Unit::find($request->unit_id);
            if( empty($unit) || ($unit->property->user->id != Auth::user()->id) ){
                abort(404);
            }
        } else if(!empty($request->property_id)){
            $property = Property::find($request->property_id);
            if( empty($property) || ($property->user->id != Auth::user()->id) ){
                abort(404);
            }
        }

        $rules = [
            'expense_type' => 'required|string',
            'expense_amount' => 'required|string',
            'expense_date' => 'required|string',
        ];

        if($request->expense_type == '_new') {
            $rules['expense_name'] = 'required|string';
        }

        $request->validate($rules);

        //add expense type
        if($request->expense_type == '_new') {
            $expenseType = ExpenseType::where(function ($query) {
                $query->where("user_id", Auth::user()->id)
                    ->orWhereNull("user_id");
            })->where(function ($query) use ($request) {
                $query->where('name', $request->expense_name);
            })->first();
            if($expenseType){
                $expenseName = $expenseType->name;
            } else {
                $expenseType = new ExpenseType();
                $expenseType->name = $request->expense_name;
                $expenseType->pid = $request->pid;
                $expenseType->user_id = Auth::user()->id;
                $expenseType->save();
                $expenseName = $expenseType->name;
            }
        } else {
            $expenseType = ExpenseType::find($request->expense_type);
            $expenseName = $expenseType->name;
        }

        //add expense
        if($request->monthly){
            if($request->no_end_date) {
                //start & end date exists - generate invoices
                $start = Carbon::parse($request->expense_date);
                $end = Carbon::now();
                $diff = $start->diffInMonths($end) + 1;
                if(($diff > 0) && ($diff < 120)){
                    $date = $start;
                    for($i = 1; $i <= $diff; $i++){
                        $expenses = new Expenses();
                        $expenses->name = $expenseName;
                        $expenses->unit_id = $request->unit_id ?? null;
                        $expenses->property_id = $request->property_id ?? null;
                        $expenses->amount = str_replace(",", "", $request->expense_amount);
                        $expenses->expense_date = $date->format('Y-m-d');
                        $expenses->notes = $request->notes ?? null;
                        $expenses->monthly = false;
                        $expenses->created_with = 'batch';
                        $expenses->save();

                        if ($request->hasFile('expense_file')) {
                            if(empty($document)) {
                                [, , $filename] = preg_split('/\//', $request->file('expense_file')->store('public/expenses'));
                                $document = new File();
                                $document->filename = $filename;
                                $document->save();
                            }
                            $expenses->file_id = $document->id;
                            $expenses->save();
                        }

                        $date = $date->addMonthNoOverflow();
                    }
                }

                if(!empty($expenses)){
                    $expenses->monthly = true;
                    $expenses->save();
                }
                /*
                $expenses = new Expenses();
                $expenses->name = ($request->expense_type == "_new") ? $request->expense_name : $request->expense_type;
                $expenses->unit_id = $request->unit_id ?? null;
                $expenses->property_id = $request->property_id ?? null;
                $expenses->amount = str_replace(",", "", $request->expense_amount);
                $expenses->expense_date = $request->expense_date;
                $expenses->notes = $request->notes ?? null;

                $expenses->monthly = true;
                $expenses->created_with = 'individually';

                $expenses->save();
                */
            } else {
                //start & end date exists - generate invoices
                $start = Carbon::parse($request->expense_date);
                $end = Carbon::parse($request->end_date);
                $diff = $start->diffInMonths($end) + 1;
                if(($diff > 0) && ($diff < 120)){
                    $date = $start;
                    for($i = 1; $i <= $diff; $i++){
                        $expenses = new Expenses();
                        $expenses->name = $expenseName;
                        $expenses->unit_id = $request->unit_id ?? null;
                        $expenses->property_id = $request->property_id ?? null;
                        $expenses->amount = str_replace(",", "", $request->expense_amount);
                        $expenses->expense_date = $date->format('Y-m-d');
                        $expenses->notes = $request->notes ?? null;
                        $expenses->monthly = false;
                        $expenses->created_with = 'batch';
                        $expenses->save();

                        if ($request->hasFile('expense_file')) {
                            if(empty($document)) {
                                [, , $filename] = preg_split('/\//', $request->file('expense_file')->store('public/expenses'));
                                $document = new File();
                                $document->filename = $filename;
                                $document->save();
                            }
                            $expenses->file_id = $document->id;
                            $expenses->save();
                        }

                        $date = $date->addMonthNoOverflow();
                    }
                }
            }
        } else {
            $expenses = new Expenses();
            $expenses->name = $expenseName;
            $expenses->unit_id = $request->unit_id ?? null;
            $expenses->property_id = $request->property_id ?? null;
            $expenses->amount = str_replace(",", "", $request->expense_amount);
            $expenses->expense_date = $request->expense_date;
            $expenses->notes = $request->notes ?? null;

            $expenses->monthly = false;
            $expenses->created_with = 'individually';

            $expenses->save();

            if ($request->hasFile('expense_file')) {
                [, , $filename] = preg_split('/\//', $request->file('expense_file')->store('public/expenses'));
                $document = new File();
                $document->filename = $filename;
                $document->save();
                $expenses->file_id = $document->id;
                $expenses->save();
            }

        }

        if(!empty($request->unit_id)){
            return redirect()->route('properties/units/expenses', ['unit' => $request->unit_id])
                ->with('success','Expenses has been successfully added.');
        }
        return redirect()->route('properties/expenses', ['unit' => $request->property_id])
            ->with('success','Expenses has been successfully added.');
    }

    public function viewExpense(Request $request)
    {
        $expense = Expenses::find($request->id);
        if( empty($expense) ){
            abort(404);
        }

        if($expense->unit_id) {
            $unit = Unit::find($expense->unit_id);
            if (empty($unit) || ($unit->property->user->id != Auth::user()->id)) {
                abort(404);
            }
        }
        if($expense->property_id) {
            $property = Property::find($expense->property_id);
            if (empty($property) || ($property->user->id != Auth::user()->id)) {
                abort(404);
            }
        }

        return response()->json([
            'view' => view('expenses.view-expenses-modal',compact('expense'))->render()
        ],200);
    }

    /*
    public function editExpense(Request $request)
    {
        $invoice = Invoice::find($request->id);

        return response()->json([
            'view' => view('includes.units.add-payments-modal',compact('invoice'))->render()
        ],200);
    }
    */

    public function removeExpense(Request $request)
    {
        $expense = Expenses::find($request->expense_id);
        if( empty($expense) ){
            abort(404);
        }

        if($expense->unit_id) {
            $unit = Unit::find($expense->unit_id);
            if (empty($unit) || ($unit->property->user->id != Auth::user()->id)) {
                abort(404);
            }
        }
        if($expense->property_id) {
            $property = Property::find($expense->property_id);
            if (empty($property) || ($property->user->id != Auth::user()->id)) {
                abort(404);
            }
        }

        if($expense->file_id){
            $document = $expense->file;
            if(Expenses::where('file_id', $expense->file_id)->count() == 1){
                Storage::delete('public/expenses/' . $document->filename);
                $document->delete();
            }
        }

        $expense->delete();
        return back()->with('success','Expenses has been deleted.');
    }
}
