<?php

namespace App\Http\Controllers\Properties;

use App\Models\Expenses;
use App\Models\Payment;
use App\Models\Property;
use App\Models\PropertyType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\Array_;
use Storage;

class ExpensesController extends Controller
{
    public function index($property_id, Request $request) {

        $property = Property::find($property_id);
        if (!$property) {
            abort(404);
        }
        $user = Auth::user();
        $units = $property->units;

        $sql = '';//for testing
        $chart = [];
        for($m = 11; $m >= 0; $m--){
            //$currentMonth = Carbon::now()->startOfMonth();
            $start = Carbon::now()->startOfMonth()->subMonth($m)->startOfMonth();
            $end = Carbon::now()->startOfMonth()->subMonth($m)->endOfMonth();

            $expenses_unit = Expenses::join('units', 'units.id', '=', 'expenses.unit_id')
                ->where('units.property_id',$property->id)
                ->where('expense_date', '>=', $start->format('Y-m-d'))
                ->where('expense_date', '<=', $end->format('Y-m-d'))
                ->sum('amount');
            $expenses_property = Expenses::where('property_id',$property->id)
                ->where('expense_date', '>=', $start->format('Y-m-d'))
                ->where('expense_date', '<=', $end->format('Y-m-d'))
                ->sum('amount');
            $expenses = $expenses_unit + $expenses_property;

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
            $income = Payment::whereIn('invoice_id',$invoices)->sum('amount');

            $chart[$m] = [];
            $chart[$m]['month'] = $start->format('M Y');
            $chart[$m]['start'] = $start->format('Y-m-d');
            $chart[$m]['end'] = $end->format('Y-m-d');
            $chart[$m]['expenses'] = $expenses;
            $chart[$m]['income'] = $income;
            $chart[$m]['profit'] = $income - $expenses;
        }

        $query = DB::table('expenses');
        $query->leftJoin('units', 'units.id', '=', 'expenses.unit_id');
        $query->where('units.property_id', '=', $property_id);
        $query->orWhere('expenses.property_id', '=', $property_id);
        $query->select(
            'expenses.*',
            DB::raw('IFNULL(units.name, "-") AS unit_name')
        );
        $expensesCount = $query->count();
//        $exCategory = DB::table('expense_types');
//        $exCategory ->where('pid', '=', null);
//        $exCategory ->where('id', '=', '43');
//        $exCategory ->get();
        $exCategory = DB::table('expense_types')
            ->where('pid', '=', null)
            ->get();
        $exSubCategory = DB::table('expense_types')
            ->where('pid', '<>', null)
            ->get();
        $allExCategory = Array();
        $allExSubCategory = Array();
        foreach($exCategory as  $value){
            array_push($allExCategory, $value);
        }
        foreach($exSubCategory as  $value){
            array_push($allExSubCategory, $value);
        }

//        var_dump($allExSubCategory);
//        die;
        return view(
            'properties.expenses',
            [
                'types' => PropertyType::all(),
                'user' => $user,
                'property' => $property,
                'units' => $units,
                'chart' => $chart,
                'sql' => $sql,
                'expensesCount' => $expensesCount,
                'allcategory' => $allExCategory,
                'allsubcategory' => $allExSubCategory
            ]
        );
    }

}
