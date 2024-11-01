<?php

use Illuminate\Database\Seeder;
use App\Models\ExpenseType;

class ExpenseTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('expense_types')->insert(['id' => '1', 'name' => 'Cleaning', 'pid' => '43']);
        DB::table('expense_types')->insert(['id' => '2', 'name' => 'Electrical','pid' => '43']);
        DB::table('expense_types')->insert(['id' => '3', 'name' => 'Floors','pid' => '43']);
        DB::table('expense_types')->insert(['id' => '4', 'name' => 'Foundation','pid' => '43']);
        DB::table('expense_types')->insert(['id' => '5', 'name' => 'Garbage','pid' => '43']);
        DB::table('expense_types')->insert(['id' => '6', 'name' => 'Homeowner Association Fees','pid' => '42']);
        DB::table('expense_types')->insert(['id' => '7', 'name' => 'HVAC','pid' => '43']);
        DB::table('expense_types')->insert(['id' => '8', 'name' => 'Insurance','pid' => '42']);
        DB::table('expense_types')->insert(['id' => '9', 'name' => 'Landscaping','pid' => '43']);
        DB::table('expense_types')->insert(['id' => '10', 'name' => 'Legal','pid' => '43']);

        DB::table('expense_types')->insert(['id' => '11', 'name' => 'Mortgage','pid' => '42']);
        DB::table('expense_types')->insert(['id' => '12', 'name' => 'Natural Gas/Heating','pid' => '43']);
        DB::table('expense_types')->insert(['id' => '13', 'name' => 'Painting','pid' => '43']);
        DB::table('expense_types')->insert(['id' => '14', 'name' => 'Plumbing','pid' => '43']);
//        DB::table('expense_types')->insert(['id' => '15', 'name' => 'Property Management Costs']);
        DB::table('expense_types')->insert(['id' => '16', 'name' => 'Property Taxes','pid' => '42']);
        DB::table('expense_types')->insert(['id' => '17', 'name' => 'Roof','pid' => '43']);
        DB::table('expense_types')->insert(['id' => '18', 'name' => 'Sewer line','pid' => '43']);
        DB::table('expense_types')->insert(['id' => '19', 'name' => 'Siding','pid' => '43']);
        DB::table('expense_types')->insert(['id' => '20', 'name' => 'Water heater','pid' => '43']);
        DB::table('expense_types')->insert(['id' => '21', 'name' => 'Water line','pid' => '43']);
        DB::table('expense_types')->insert(['id' => '22', 'name' => 'Water/Sewer','pid' => '43']);
        DB::table('expense_types')->insert(['id' => '23', 'name' => 'Loans','pid' => '42']);
        DB::table('expense_types')->insert(['id' => '24', 'name' => 'Management Fees','pid' => '42']);
        DB::table('expense_types')->insert(['id' => '25', 'name' => 'Legal & Professional','pid' => '42']);
        DB::table('expense_types')->insert(['id' => '26', 'name' => 'Marketing','pid' => '42']);
        DB::table('expense_types')->insert(['id' => '27', 'name' => 'Appliances','pid' => '43']);
        DB::table('expense_types')->insert(['id' => '28', 'name' => 'Doors','pid' => '43']);
        DB::table('expense_types')->insert(['id' => '29', 'name' => 'Pest Control','pid' => '43']);
        DB::table('expense_types')->insert(['id' => '30', 'name' => 'Pool','pid' => '43']);
        DB::table('expense_types')->insert(['id' => '31', 'name' => 'Parking','pid' => '43']);
        DB::table('expense_types')->insert(['id' => '32', 'name' => 'Windows','pid' => '43']);
        DB::table('expense_types')->insert(['id' => '33', 'name' => 'Water','pid' => '44']);
        DB::table('expense_types')->insert(['id' => '34', 'name' => 'Gas','pid' => '44']);
        DB::table('expense_types')->insert(['id' => '35', 'name' => 'Electric','pid' => '44']);
        DB::table('expense_types')->insert(['id' => '36', 'name' => 'Phone','pid' => '44']);
        DB::table('expense_types')->insert(['id' => '37', 'name' => 'Internet','pid' => '44']);
        DB::table('expense_types')->insert(['id' => '38', 'name' => 'Parking','pid' => '44']);
        DB::table('expense_types')->insert(['id' => '39', 'name' => 'Other','pid' => '42']);
        DB::table('expense_types')->insert(['id' => '40', 'name' => 'Other','pid' => '43']);
        DB::table('expense_types')->insert(['id' => '41', 'name' => 'Other','pid' => '44']);


        DB::table('expense_types')->insert(['id' => '42', 'name' => 'Administrative']);
        DB::table('expense_types')->insert(['id' => '43', 'name' => 'Maintenance & Repairs']);
        DB::table('expense_types')->insert(['id' => '44', 'name' => 'Utilities']);
//        $e = [
//            'Cleaning',
//            'Electrical',
//            'Floors',
//            'Foundation',
//            'Garbage',
//            'Homeowner Association Fees',
//            'HVAC',
//            'Insurance',
//            'Landscaping',
//            'Legal',
//            'Mortgage',
//            'Natural Gas/Heating',
//            'Painting',
//            'Plumbing',
//            'Property Management Costs',
//            'Property Taxes',
//            'Roof',
//            'Sewer line',
//            'Siding',
//            'Water heater',
//            'Water line',
//            'Water/Sewer',
//        ];
//
//        $b = [
//            'Administrative',
//            'Maintenance & Repairs',
//            'Utilities',
//        ];

//        DB::table('expense_types')->insert(['id' => '30', 'name' => 'Administrative']);
//        DB::table('expense_types')->insert(['id' => '31', 'name' => 'Maintenance & Repairs']);
//        DB::table('expense_types')->insert(['id' => '32', 'name' => 'Utilities']);

//        foreach($e as $expenseName){
//            $expenseType = new ExpenseType();
//            $expenseType->name = $expenseName;
//            $expenseType->save();
//        }
//        foreach($b as $expensePid){
//            $expenseType = new ExpenseType();
//            $expenseType->name = $expenseName;
////            $expenseType->pid = $expensePid;
//            $expenseType->save();
//        }

    }
}
