<?php

namespace App\Http\Controllers\Properties\Units;

use App\Repositories\Contracts\ApplicationsRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Unit;

use Illuminate\Support\Facades\DB;
use Storage;

class ExpensesController extends Controller
{
    private $ar;
    public function __construct(ApplicationsRepositoryInterface $ar) {
        $this->ar = $ar;
    }
    public function index($unit_id, Request $request) {
        $unit = Unit::find($unit_id);

        $unitID = ['unit_id'=>$unit_id];
        $applicationsData = $this->ar->getWithoutPaginate($request->all() + ['unit_id' => $unitID]);
        $applicationsCount = $applicationsData['applications']->count();

        if (!$unit) {
            abort(404);
        }

        $user = Auth::user();

        $query = DB::table('expenses');
        $query->where('expenses.unit_id', '=', $unit_id);
        $expensesCount = $query->count();

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

        return view(
            'properties.units.expenses.index',
            [
                'applicationsCount' => $applicationsCount,
                'user' => $user,
                'unit' => $unit,
                'expensesCount' => $expensesCount,
                'allcategory' => $allExCategory,
                'allsubcategory' => $allExSubCategory
            ]
        );
    }

}
