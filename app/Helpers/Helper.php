<?php
/*
Global accessed functions
*/

function notificationIcon($notification)
{
    if(isset($notification->data['priority_id'])){
        switch($notification->data['priority_id']){
            case 1 :
                $color = "info";
                break;
            case 2 :
                $color = "warning";
                break;
            case 3 :
                $color = "danger";
                break;
            default :
                $color = "secondary";
                break;
        }
    } else {
        $color = "secondary";
    }
    $icons = [
        'MAINTENANCE' => '<i class="fal fa-tools text-' . $color . '"></i>',
        'MAINTENANCE_RESOLVED' => '<i class="fal fa-tools text-success"></i>',
        'APPLICATION_CREATED' => '<i class="fal fa-file-signature text-info"></i>',
        'PAYMENT_OUTSTANDING' => '<i class="fal fa-dollar-sign text-danger"></i>',
        'PAYMENT_SOON' => '<i class="fal fa-dollar-sign text-info"></i>',
        'LINKED_FINANCE' => '<i class="fal fa-lightbulb-on text-success"></i>',
        'LEASE_CREATED' => '<i class="fal fa-file-signature text-success"></i>',
        'LEASE_ENDED' => '<i class="fal fa-file-signature text-danger"></i>',
        'LEASE_ABOUT_TO_END' => '<i class="fal fa-file-signature text-warning"></i>',
        'LEASE_CHANGED' => '<i class="fal fa-file-signature text-info"></i>',
        'BILL_ADDED' => '<i class="fal fa-file-invoice-dollar text-info"></i>',
        'UNIT_SHARED' => '<i class="fal fa-house text-success"></i>',
        'SCREENING' => '<i class="fal fa-shield-alt text-success"></i>',
        'CALENDAR_EVENT' => '<i class="fal fa-calendar-alt text-danger"></i>',
        'VERIFICATION_SUCCESS' => '<i class="fal fa-badge-check text-success"></i>',
        'VERIFICATION_DOCUMENT_FAILED' => '<i class="fal fa-exclamation-square text-danger"></i>',
        'VERIFICATION_SUSPENDED' => '<i class="fal fa-hand-paper text-danger"></i>',
        'TRANSACTION_ERROR' => '<i class="fal fa-exclamation-square text-danger"></i>',
    ];
    return $icons[$notification->data['type']];
}

function financeFormat($n){
    return number_format($n, 2, '.', ',');
}
function financeCurrencyFormat($n){
    return $n >= 0 ? "$" . number_format($n, 2, '.', ',') : "-$" . number_format(-$n, 2, '.', ',');
}

function sortableColumn($columnName, $databaseColumnName, $route, $params = []){
    $output = '<a class="text-dark sortLink" href="' . route($route, array_merge( ["column" => $databaseColumnName, "order" => Request::get('order') === 'asc' ? 'desc' : 'asc'] , $params ) ) . '">' . $columnName . " ";
    if (Request::get('column') === $databaseColumnName){
        $output .= Request::get('order') === 'asc' ? '<i class="text-secondary fas fa-sort-amount-up"></i>' : '<i class="text-secondary fas fa-sort-amount-down"></i>';
    } else {
        $output .= '<small><i class="text-muted fas fa-sort text-small"></i></small>';
    }
    $output .= '</a>';
    return $output;
}

function applicationsNavCounter(){
    $query = App\Models\Application::query();
    $query->leftJoin('applications_users', 'applications.id', '=', 'applications_users.application_id');
    $query->where('applications_users.user_id', Illuminate\Support\Facades\Auth::id());
    $query->where('applications_users.is_new','=','1');
    $count = $query->count();
    if($count){
        return '<span class="badge badge-dark">'.$count.'</span>';
    } else {
        return "";
    }
}

function maintenanceNavCounter(){
    $count = Illuminate\Support\Facades\Auth::user()->getNewMaintenanceRequestsCount();
    if($count){
        return '<span id="maintenanceRequestsCountInMenu" class="badge badge-dark">'.$count.'</span>';
    } else {
        return "";
    }
}

function planIcon($plan){
    $icons = [
        'Free Trial' => '<i class="fal fa-leaf"></i>',
        'Small' => '<i class="fal fa-home-heart"></i>',
        'Medium' => '<i class="fal fa-hotel"></i>',
        'Large' => '<i class="fal fa-city"></i>',
        'Unlimited' => '<i class="fal fa-flag-usa"></i>',
    ];
    return $icons[$plan->name] ?? "";
}

function financialTypeIcon($type){
    $types = [
        'card' => '<i class="fal fa-credit-card"></i>',
        'bank' => '<i class="fal fa-university"></i>',
        'stripe_account' => '<i class="fab fa-stripe"></i>',
        'paypal' => '<i class="fab fa-paypal"></i>',
        'dwolla_source' => '<i class="fa fa-envelope-open-dollar"></i>',
        'dwolla_target' => '<i class="fa fa-envelope-open-dollar"></i>',

        //'dwolla_source' => '<div style="font-weight:700;font-size:16px">ACH</div>',
        //'dwolla_target' => '<div style="font-weight:700;font-size:16px">ACH</div>',
    ];
    return $types[$type] ?? "";
}

function financialTypeIconClass($type){
    $types = [
        'card' => 'fa fa-credit-card',
        'bank' => 'fa fa-university',
        'stripe_account' => 'fab fa-stripe',
        'paypal' => 'fab fa-paypal',
        'dwolla_target' => 'fa fa-envelope-open-dollar',
        'dwolla_source' => 'fa fa-envelope-open-dollar',
    ];
    return $types[$type] ?? "";
}

function financialTypeIconClassSmall($type){
    $types = [
        'card' => 'fa fa-credit-card',
        'bank' => 'fa fa-university',
        'stripe_account' => 'fab fa-cc-stripe',
        'paypal' => 'fab fa-paypal',
        'dwolla_target' => 'fa fa-envelope-open-dollar',
        'dwolla_source' => 'fa fa-envelope-open-dollar',
    ];
    return $types[$type] ?? "";
}

function processingFee($amount, $sourceFinancialAccountType){
    switch ($sourceFinancialAccountType){
        case 'card':
            //Stripe CC Processing Fee = 2.9% + $0.30
            $total = ($amount + 0.3) / (1 - 0.029);
            $fee = $total - $amount;
            break;
        case 'bank':
            //Stripe ACH Direct Debit Processing Fee = 0.8% limited to $5
            $total = $amount / (1 - 0.008);
            $fee = $total - $amount;
            if($fee > 5) {
                $fee = 5;
            }
            break;
        case 'dwolla_source':
            //DWOLLA ACH Processing Fee = 0.5% min 5c max $5
            $total = $amount / (1 - 0.005);
            $fee = $total - $amount;
            if($fee > 5) {
                $fee = 5;
            }
            if($fee < 0.05) {
                $fee = 0.05;
            }
            break;
        case 'paypal':
            //PayPal Fee = 2.9% + $0.30
            $total = ($amount + 0.3) / (1 - 0.029);
            $fee = $total - $amount;
            break;
        default: $fee = 0;
    }

    return round($fee, 2);
}

function paypalFee($subtotal){
    return number_format(processingFee($subtotal, 'paypal'), 2, '.', '');
}
function paypalTotal($subtotal){
    return number_format($subtotal + processingFee($subtotal, 'paypal'), 2, '.', '');
}

function stripeCcFee($subtotal){
    return number_format(processingFee($subtotal, 'card'), 2, '.', '');
}
function stripeCcTotal($subtotal){
    return number_format($subtotal + processingFee($subtotal, 'card'), 2, '.', '');
}

function stripeAchDdFee($subtotal){
    return number_format(processingFee($subtotal, 'bank'), 2, '.', '');
}
function stripeAchDdTotal($subtotal){
    return number_format($subtotal + processingFee($subtotal, 'bank'), 2, '.', '');
}

function dwollaAchFee($subtotal){
    return number_format(processingFee($subtotal, 'dwolla_source'), 2, '.', '');
}
function dwollaAchTotal($subtotal){
    return number_format($subtotal + processingFee($subtotal, 'dwolla_source'), 2, '.', '');
}

function freeTrialParams($name){
    $param = [
        'max_units' => 10
    ];
    return $param[$name];
}

function transactionIdStringFilter($txId){
    $filter = [
        'https://api-sandbox.dwolla.com/transfers/',
        'https://api.dwolla.com/transfers/',
    ];
    return str_replace($filter, '', $txId);
}
function payMethodStringFilter($s){
    return str_replace('dwolla', 'Automatic Banking Payment', $s);
}