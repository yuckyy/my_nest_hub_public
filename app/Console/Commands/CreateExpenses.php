<?php

namespace App\Console\Commands;

use App\Models\Expenses;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CreateExpenses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:expenses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create monthly expenses';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $now = Carbon::now();

        //for debugging
        //$now = Carbon::parse('2021-05-31');

        if( $now->format('Y-m-d') === $now->copy()->endOfMonth()->format('Y-m-d') ){
            if($now->format('d') == '31'){
                $daysToCheck = [31];
            }
            if($now->format('d') == '30'){
                $daysToCheck = [30,31];
            }
            if($now->format('d') == '29'){
                $daysToCheck = [29,30,31];
            }
            if($now->format('d') == '28'){
                $daysToCheck = [28,29,30,31];
            }
        } else {
            $daysToCheck = [ intval($now->format('d')) ];
        }

        $expenses = Expenses::where('monthly','1')
            ->whereRaw('DAY(expense_date) in ('.implode(',',$daysToCheck).')')
            ->whereRaw('MONTH(expense_date) != ' . intval($now->format('m')))
            ->get();

        foreach ($expenses as $expense){
            $today = Carbon::parse($now->format('Y-m-d'));
            $i = 1;
            do {
                if($i > 100){
                    break;
                }

                $newExpense = new Expenses();
                $newExpense->name = $expense->name;
                $newExpense->unit_id = $expense->unit_id;
                $newExpense->property_id = $expense->property_id;
                $newExpense->amount = $expense->amount;
                $newExpense->notes = $expense->notes;
                $newExpense->file_id = $expense->file_id;;

                $newExpense->expense_date = Carbon::parse($expense->expense_date)->addMonthNoOverflow($i)->format('Y-m-d');

                $newExpense->monthly = false;
                $newExpense->created_with = 'cron';

                $newExpense->save();

                //echo "\n" . $newExpense->name . " - " . $newExpense->expense_date . "\n";
                $i++;
            } while ( Carbon::parse($newExpense->expense_date) < Carbon::parse($today) );

            $expense->monthly = false;
            $expense->save();

            $newExpense->monthly = true;
            $newExpense->save();
        }

        \Log::info("Cron: CreateExpenses");
    }

}
