<?php

use App\Models\Role;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes(['verify' => true]);
Route::get('/easy-email-verify/{id}', 'Auth\VerificationController@easyVerify')->name('easy-email-verify');

//Route::get('/register', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::any('connect-stripe', ['as' => 'connect-stripe', 'uses' => 'User\FinanceController@connectStripe']);

Route::get('/register', function (\Illuminate\Http\Request $request) {
    $roles = Role::where('name', '<>', 'Admin')->get();
    if (!empty($request->get('redirect_link'))) session()->put('redirect_link', $request->get('redirect_link'));
    return view('auth.register', ['roles' => $roles]);
})->name('register');

Route::get('/registration/finish/{email}', 'User\FinishRegistrationController@view')->name('registration/finish');;
Route::post('/registration/finish', 'User\FinishRegistrationController@save')->name('registration/finish/save');

Route::domain('{unique_link}.' . env('APP_SHORT_URL', ''))->group(function () {
    Route::get('/', 'UniqueLinkController@view')->name('view/unique_link');
});
Route::get('/share/{unique_link}', 'UniqueLinkController@view')->name('view/local_unique_link');

Route::get('/', function (\Illuminate\Http\Request $request) {
    return view('auth.login', ['sessionExpired' => !empty($request->get('sessionExpired'))]);
})->middleware('guest')->name('/');

#GOOGLE LOGIN
Route::get('/auth/google', 'Auth\GoogleController@redirectToProvider')->name('/google_auth');
Route::get('/auth/google/callback', 'Auth\GoogleController@handleProviderCallback')->name('/google_auth_callback');

#FACEBOOK LOGIN
Route::get('auth/facebook', 'Auth\GoogleController@redirectToFacebook')->name('/facebook_auth');
Route::get('auth/facebook/callback', 'Auth\GoogleController@handleFacebookCallback')->name('/facebook_auth_callback');;

#PHP INFO
Route::get('/php', function () {
    return view('php_info');
})->name('/php');

# Public Unit Advert Page
Route::get('/unit-advert', function () {
    return view('unit_advert');
})->name('unit-advert');

Route::get('/unsubscribe', 'User\ProfileController@unsubscribe')->name('unsubscribe');
Route::get('/unsubscribe-complete', 'User\ProfileController@unsubscribeComplete')->name('unsubscribe-complete');

Route::group(['middleware' => ['auth']], function () {
    Route::get('/registration/membership', 'User\FinishRegistrationController@membership')->name('registration/membership');
    Route::get('/subscribe/{plan_id}', 'User\FinishRegistrationController@subscribe')->name('/subscribe')->middleware('OnlyLandlordOrPropertyManager');
    Route::post('apply-code', 'User\FinishRegistrationController@applyCode')->name('apply-code');

    Route::group(['prefix' => 'profile'], function () {
        Route::group(['prefix' => 'finance'], function () {
            Route::post('add-card-account', 'User\FinanceController@addCardAccount')->name('add-card-account');
        });
        Route::post('/create-subscription', 'User\MembershipController@createSubscription')->name('profile/create-subscription');
    });
});

Route::group(['middleware' => ['auth'/*, 'verified'*/]], function () {
    ################
    # DASHBOARD
    ################
    Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
    Route::get('/ajax-negative-invoices', 'DashboardController@ajaxLandlordGetNegativeInvoices')->name('ajax-negative-invoices');

    // Route::get('/dashboard/price', 'DashboardController@price')->name('dashboard/price');
    // Route::get('/dashboard/subscribe/{plan_id}', 'DashboardController@subscribe')->name('dashboard/subscribe');
    // Route::post('/dashboard/create-subscription', 'DashboardController@createSubscription')->name('dashboard/create-subscription');
    Route::get('/dashboard/request-feature', 'DashboardController@requestFeature')->name('dashboard/request-feature');
    Route::post('/dashboard/request-feature', 'DashboardController@requestFeatureSave')->name('dashboard/request-feature-save');


    ################
    #  MAINTENANCE
    ################

    Route::get('/maintenance/dashboard', function () {
        return view('maintenance');
    })->name('maintenance/dashboard');

    Route::post('/ajax_maintenance_details', 'Maintenance\IndexController@viewDetails')->name('ajax_maintenance_details');

    #############################
    # UPDATE/DELETE placeholders
    #############################

    Route::post('/update_field_sample', function () {
        return 1;//success
    })->name('update_field_sample');

    Route::post('/delete_field_sample', function () {
        return json_encode(1);//success
    })->name('delete_field_sample');

    ##############################################

    Route::group(['prefix' => 'profile'], function () {
        Route::get('/', 'User\ProfileController@edit')->name('profile');
        Route::post('/', 'User\ProfileController@save')->name('profile');

        Route::group(['prefix' => 'password'], function () {
            Route::get('/', 'User\PasswordController@edit')->name('profile/password');
            Route::post('/', 'User\PasswordController@save')->name('profile/password');
        });

        Route::group(['prefix' => 'photo'], function () {
            Route::get('/', 'User\ProfileController@photo')->name('profile/photo');
        });

        Route::get('/identity', 'User\IdentityController@index')->name('profile/identity')->middleware('OnlyLandlordOrPropertyManager');
        Route::post('/identity', 'User\IdentityController@save')->name('profile/identity');
        Route::post('/identity/retry', 'User\IdentityController@retry')->name('profile/identity/retry');
        Route::post('/identity/document', 'User\IdentityController@document')->name('profile/identity/document');
        Route::post('/identity/unverify', 'User\IdentityController@unverify')->name('profile/identity/unverify');
        Route::post('/identity/upload', 'User\IdentityController@documentUpload')->name('profile/identity/upload');
        Route::post('/identity/delete', 'User\IdentityController@documentDelete')->name('profile/identity/delete');
        Route::get('/identity/view-document/{id_hash}', 'User\IdentityController@documentView')->name('profile/identity/view-document');


        Route::group(['prefix' => 'finance'], function () {
            Route::get('/', 'User\FinanceController@index')->name('profile/finance');
            Route::post('add-checking-account', 'User\FinanceController@addCheckingAccount')->name('add-checking-account');
            Route::post('add-dwolla-account', 'User\FinanceController@addDwollaAchTenantAccount')->name('add-dwolla-account');
            // Route::post('add-card-account', 'User\FinanceController@addCardAccount')->name('add-card-account');
            Route::post('send-stripe-connect', 'User\FinanceController@sendStripeConnectRequest')->name('send-stripe-connect');
            Route::post('add-paypal-account', 'User\FinanceController@addPayPalAccount')->name('add-paypal-account');

            Route::post('add-dwolla-ach-landlord-account', 'User\FinanceController@addDwollaAchLandlordAccout')->name('add-dwolla-ach-landlord-account');

            Route::post('replace-finance-account', 'User\FinanceController@replaceFinanceAccount')->name('replace-finance-account');
            Route::post('link-units', 'User\FinanceController@linkUnits')->name('link-units');
            Route::post('get-linked-units', 'User\FinanceController@getLinkedUnits')->name('get-linked-units');
            Route::post('edit-finance-account', 'User\FinanceController@editFinanceAccount')->name('edit-finance-account');
            Route::post('update-finance-account', 'User\FinanceController@updateFinanceAccount')->name('update-finance-account');
            Route::post('remove-finance-account', 'User\FinanceController@removeFinanceAccount')->name('remove-finance-account');

            Route::post('plaid-link-token', 'User\FinanceController@getPlaidLinkToken')->name('plaid-link-token');
            Route::post('dwolla-iav-token', 'User\FinanceController@getDwollaIavToken')->name('dwolla-iav-token');
        });

        Route::get('/membership', 'User\MembershipController@index')->name('profile/membership')->middleware('OnlyLandlordOrPropertyManager');
        Route::get('/subscribe/{plan_id}', 'User\MembershipController@subscribe')->name('profile/subscribe')->middleware('OnlyLandlordOrPropertyManager');
        // Route::post('/create-subscription', 'User\MembershipController@createSubscription')->name('profile/create-subscription');
        Route::post('/cancel-subscription', 'User\MembershipController@cancelSubscription')->name('profile/cancel-subscription');

        Route::get('/addon/{addon_id}', 'User\MembershipController@addon')->name('profile/addon')->middleware('OnlyLandlordOrPropertyManager');
        Route::post('/addon-subscribe', 'User\MembershipController@addonSubscribe')->name('profile/addon-subscribe')->middleware('OnlyLandlordOrPropertyManager');
        Route::post('/addon-cancel', 'User\MembershipController@addonCancel')->name('profile/addon-cancel')->middleware('OnlyLandlordOrPropertyManager');

        Route::get('/profile/email-preferences', 'User\ProfileController@emailPreferences')->name('profile/email-preferences');
        Route::post('/profile/email-preferences', 'User\ProfileController@emailPreferencesSave')->name('profile/email-preferences');

    });

    Route::group(['prefix' => '/leases'], function ($unit) {
        Route::post('/', 'LeaseController@editSave', ['unit' => $unit])->name('leases/edit-save');

        Route::get('/add', 'LeaseController@add', ['unit' => $unit])->name('leases/add')->middleware('OnlyLandlordOrPropertyManager');
        Route::post('/add', 'LeaseController@addSave', ['unit' => $unit])->name('leases/add-save')->middleware('OnlyLandlordOrPropertyManager');
        Route::get('/add/skip', 'LeaseController@skip', ['unit' => $unit])->name('leases/add-save/skip')->middleware('OnlyLandlordOrPropertyManager');
        Route::get('/add/back', 'LeaseController@back', ['unit' => $unit])->name('leases/add-save/back')->middleware('OnlyLandlordOrPropertyManager');

        Route::post('/end', 'LeaseController@close', ['unit' => $unit])->name('leases/end')->middleware('OnlyLandlordOrPropertyManager');
        Route::post('/resend-email', 'LeaseController@resendEmail', ['unit' => $unit])->name('leases/resend-email')->middleware('OnlyLandlordOrPropertyManager');
        Route::post('/ajax-get-property-unit', 'LeaseController@ajaxGetPropertyUnit')->name('leases/ajax-get-property-unit');

        Route::post('/document-upload', 'LeaseController@documentUpload')->name('leases/document-upload')->middleware('OnlyLandlordOrPropertyManager');
        Route::post('/document-delete', 'LeaseController@documentDelete')->name('leases/document-delete');
        Route::post('/move-in-out-upload', 'LeaseController@moveInOutUpload')->name('leases/move-in-out-upload');
    });

    Route::group(['prefix' => 'properties', 'middleware' => ['OnlyLandlordOrPropertyManager']], function () {
        Route::get('/', 'Properties\IndexController@index')->name('properties');

        Route::group(['prefix' => 'add'], function () {
            Route::get('/', 'Properties\IndexController@add')->name('properties/add');
            Route::post('/', 'Properties\IndexController@addSave')->name('properties/add-save');
        });

        Route::group(['prefix' => 'application'], function ($unit) {
//            Route::get('/', 'Properties\ApplicationsController@index', ['unit' => $unit])->name('properties/units/applications')->middleware('EnsureUserIsOwnerOfUnit');
//            Route::post('invite-tenant-validate', 'Properties\ApplicationsController@postInviteValidate')->name('properties/units/applications/invite-tenant-validate/post');
//            Route::post('invite-tenant', 'Properties\ApplicationsController@postInvite')->name('properties/units/applications/invite-tenant/post');

            Route::get('/', 'Applications\IndexController@createapplications')->name('properties/add-from-list');
            Route::post('/', 'Applications\IndexController@storeapplications')->name('properties/add-from-list-save');
            Route::post('add/validation', 'Applications\IndexController@validation')->name('properties/add-save/validation');
            Route::get('{id}/ajax-edit-pets', 'Applications\IndexController@ajaxEditPets')->name('/ajax-edit-pets');
        });

        Route::group(['prefix' => 'documents'], function () {
            Route::get('/{id}', 'Properties\DocumentsController@index')->name('properties/documents');
            Route::post('/document-delete', 'Properties\DocumentsController@documentDelete')->name('properties/document-delete');
            Route::post('/document-upload', 'Properties\DocumentsController@documentUpload')->name('properties/document-upload')->middleware('OnlyLandlordOrPropertyManager');
//            Route::get('/category-filter', 'Properties\DocumentsController@categoryFilter')->name('properties/category-filter');
//            Route::post('/{id}', 'Properties\IndexController@editSave')->name('properties/edit-save');
//            Route::post('/details/{id}', 'Properties\IndexController@editDetailsSave')->name('properties/edit-details-save');
        });

        Route::group(['prefix' => 'edit'], function () {
            Route::get('/{id}', 'Properties\IndexController@edit')->name('properties/edit');
            Route::post('/{id}', 'Properties\IndexController@editSave')->name('properties/edit-save');
            Route::post('/details/{id}', 'Properties\IndexController@editDetailsSave')->name('properties/edit-details-save');
        });
        Route::group(['prefix' => 'indexedit'], function () {
            Route::get('/{id}', 'Properties\IndexController@indexedit')->name('properties/indexedit');
            Route::post('/{id}', 'Properties\IndexController@indexeditSave')->name('properties/indexedit-save');
            Route::post('/details/{id}', 'Properties\IndexController@indexeditDetailsSave')->name('properties/indexedit');
        });
        Route::group(['prefix' => 'operations'], function () {
            Route::get('/{id}', 'Properties\IndexController@operations')->name('properties/operations');
            Route::post('/{id}', 'Properties\IndexController@operationsSave')->name('properties/operations-save');
        });
        Route::post('/ajax_property_unarchive', 'Properties\IndexController@unarchive')->name('ajax_property_unarchive');
        Route::post('/ajax_unit_unarchive', 'Properties\UnitsController@unarchive')->name('ajax_unit_unarchive');

        Route::post('/duplicate-unit', 'Properties\IndexController@duplicateUnit')->name('properties/copy');

        Route::group(['prefix' => 'units'], function () {
            Route::group(['prefix' => 'edit'], function () {
                Route::get('/{id}', 'Properties\UnitsController@edit')->name('properties/units/edit')->middleware('EnsureUserIsOwnerOfUnit');
                Route::post('/{id}', 'Properties\UnitsController@editSave')->name('properties/units/edit-save')->middleware('EnsureUserIsOwnerOfUnit');
            });
            Route::group(['prefix' => 'media'], function () {
                Route::get('/{id}', 'Properties\UnitsController@media')->name('properties/units/media')->middleware('EnsureUserIsOwnerOfUnit');
            });
            Route::group(['prefix' => '/{unit}/tenants'], function () {
                Route::get('/', 'Properties\UnitsController@tenants')->name('properties/units/tenants')->middleware('EnsureUserIsOwnerOfUnit');
            });
            Route::group(['prefix' => 'operations'], function () {
                //Route::get('/{id}', 'Properties\UnitsController@operations')->name('properties/units/operations');
                Route::post('/{id}', 'Properties\UnitsController@operationsSave')->name('properties/units/operations-save');
            });

            Route::group(['prefix' => '/{unit}/leases'], function ($unit) {
                Route::get('/', 'Properties\Units\LeaseController@index', ['unit' => $unit])->name('properties/units/leases')->middleware('EnsureUserIsOwnerOfLease');
            });

            ### Maintenance

            Route::group(['prefix' => '/{unit}/maintenance'], function ($unit) {
                Route::get('/', 'Properties\MaintenanceController@dashboard', ['unit' => $unit])->name('properties/units/maintenance');
                Route::get('list-view', 'Properties\MaintenanceController@listView', ['unit' => $unit])->name('properties/units/maintenance/list-view');
                Route::post('/ajax_autocomplete', 'Properties\MaintenanceController@autocomplete', ['unit' => $unit])->name('properties/units/maintenance/ajax_autocomplete');
                Route::post('/ajax_maintenance_details', 'Properties\MaintenanceController@viewDetails', ['unit' => $unit])->name('properties/units/maintenance/ajax_maintenance_details');
                Route::post('/ajax_maintenance_add', 'Properties\MaintenanceController@add', ['unit' => $unit])->name('properties/units/maintenance/ajax_maintenance_add');
                Route::post('/ajax_maintenance_add_message', 'Properties\MaintenanceController@addMessage', ['unit' => $unit])->name('properties/units/maintenance/ajax_maintenance_add_message');
                Route::post('/ajax_maintenance_delete', 'Properties\MaintenanceController@delete', ['unit' => $unit])->name('properties/units/maintenance/ajax_maintenance_delete');
                Route::post('/ajax_maintenance_archive', 'Properties\MaintenanceController@archive', ['unit' => $unit])->name('properties/units/maintenance/ajax_maintenance_archive');
                Route::post('/ajax_maintenance_unarchive', 'Properties\MaintenanceController@unarchive', ['unit' => $unit])->name('properties/units/maintenance/ajax_maintenance_unarchive');
                Route::post('/ajax_maintenance_notify', 'Properties\MaintenanceController@notify', ['unit' => $unit])->name('properties/units/maintenance/ajax_maintenance_notify');
                Route::post('/ajax_update_maintenance_status', 'Properties\MaintenanceController@updateStatus', ['unit' => $unit])->name('properties/units/maintenance/ajax_update_maintenance_status');
//
//                Route::post('/sort', function () {
//                    return Response::json(array('status' => 'Success'));
//                },['unit' => $unit])->name('maintenance/sort');
//
                Route::post('/document-upload', 'Properties\MaintenanceController@documentUpload', ['unit' => $unit])->name('properties/units/maintenance/document-upload');
                Route::post('/document-delete', 'Properties\MaintenanceController@documentDelete', ['unit' => $unit])->name('properties/units/maintenance/document-delete');
                Route::post('/draft-delete', 'Properties\MaintenanceController@draftDelete', ['unit' => $unit])->name('properties/units/maintenance/draft-delete');

            });

            ### Application

            Route::group(['prefix' => '/{unit}/applications'], function ($unit) {
                Route::get('/', 'Properties\ApplicationsController@index', ['unit' => $unit])->name('properties/units/applications')->middleware('EnsureUserIsOwnerOfUnit');
                Route::post('invite-tenant-validate', 'Properties\ApplicationsController@postInviteValidate')->name('properties/units/applications/invite-tenant-validate/post');
                Route::post('invite-tenant', 'Properties\ApplicationsController@postInvite')->name('properties/units/applications/invite-tenant/post');
            });

            Route::group(['prefix' => '{unit}/share'], function ($unit) {
                Route::get('', 'Properties\UnitsController@share')->name('properties/units/share')->middleware('EnsureUserIsOwnerOfUnit');
                Route::post('', 'Properties\UnitsController@postShare')->name('properties/units/share/post');
                Route::post('amenities-save', 'Properties\UnitsController@editAmenitiesSave', ['unit' => $unit])->name('properties/units/share/amenities-save')->middleware('EnsureUserIsOwnerOfUnit');
                Route::post('terms-save', 'Properties\UnitsController@editTermsSave', ['unit' => $unit])->name('properties/units/share/terms-save')->middleware('EnsureUserIsOwnerOfUnit');
            });

            ### Payments

            Route::group(['prefix' => '/{unit}/payments'], function ($unit) {
                Route::get('/', 'Properties\Units\PaymentsController@index', ['unit' => $unit])->name('properties/units/payments')->middleware('EnsureUserIsOwnerOfUnit');

                Route::post('change-finance-account', 'Properties\Units\PaymentsController@changeFinanceAccount', ['unit' => $unit])->name('properties/units/payments/change-finance-account')->middleware('EnsureUserIsOwnerOfUnit');
            });

            //Route::post('remove-bill', 'Properties\Units\PaymentsController@removeBill')->name('remove-bill')->middleware('EnsureUserIsOwnerOfBill');
            Route::post('add-bill', 'Properties\Units\PaymentsController@addBill')->name('add-bill')->middleware('EnsureUserIsOwnerOfLease');
            //Route::post('view-payments', 'Properties\Units\PaymentsController@viewPayments')->name('view-payments')->middleware('EnsureUserIsOwnerOfInvoice');
            Route::post('edit-payments', 'Properties\Units\PaymentsController@editPayments')->name('edit-payments')->middleware('EnsureUserIsOwnerOfInvoice');
            Route::post('add-payment', 'Properties\Units\PaymentsController@addPayment')->name('add-payment')->middleware('EnsureUserIsOwnerOfInvoice');
            Route::post('mark-as-paid', 'Properties\Units\PaymentsController@markAsPaid')->name('mark-as-paid'); //security check moved to the controller
            Route::post('remove-invoice', 'Properties\Units\PaymentsController@removeInvoice')->name('remove-invoice')->middleware('EnsureUserIsOwnerOfInvoice');

            Route::group(['prefix' => '/{unit_id}/expenses'], function ($unit) {
                Route::get('/', 'Properties\Units\ExpensesController@index', ['unit_id' => $unit])->name('properties/units/expenses')->middleware('EnsureUserIsOwnerOfUnit');
            });
        });
        Route::group(['prefix' => '/{property_id}/expenses'], function ($property) {
            Route::get('/', 'Properties\ExpensesController@index', ['property_id' => $property])->name('properties/expenses');//->middleware('EnsureUserIsOwnerOfProperty');

        });
    });

    Route::group(['prefix' => 'expenses'], function () {
        Route::get('/', 'ExpensesController@index')->name('expenses');
        Route::get('/ajax-expenses', 'ExpensesController@ajaxGetExpenses')->name('ajax-expenses');

        Route::post('add', 'ExpensesController@addExpense')->name('add-expense');
        Route::post('view', 'ExpensesController@viewExpense')->name('view-expense');
        //Route::post('edit', 'ExpensesController@editExpense')->name('edit-expense');
        Route::post('remove', 'ExpensesController@removeExpense')->name('remove-expense');
    });

    Route::post('ajax-view-payments', 'Properties\Units\PaymentsController@viewPayments')->name('view-payments');

    Route::group(['prefix' => 'applications', 'as' => 'applications'], function () {
        Route::get('', 'Applications\IndexController@index')->name('');
        Route::get('filter', 'Applications\IndexController@index')->name('/filter');
        Route::get('add', 'Applications\IndexController@create')->name('/add');
        Route::post('add', 'Applications\IndexController@store')->name('/add-save');
        Route::post('add/validation', 'Applications\IndexController@validation')->name('/add-save/validation');
        Route::get('{id}', 'Applications\IndexController@show')->name('/view');
        Route::get('{id}/view-edit', 'Applications\IndexController@viewEdit')->name('/view-edit');
        Route::get('{id}/ajax-edit-employment-and-incomes', 'Applications\IndexController@ajaxEditEmploymentAndIncomes')->name('/ajax-edit-employment-and-incomes');
        Route::get('{id}/ajax-edit-additional-incomes', 'Applications\IndexController@ajaxEditAdditionalIncomes')->name('/ajax-edit-additional-incomes');
        Route::get('{id}/ajax-edit-residence-histories', 'Applications\IndexController@ajaxEditResidenceHistories')->name('/ajax-edit-residence-histories');
        Route::get('{id}/ajax-edit-references', 'Applications\IndexController@ajaxEditReferences')->name('/ajax-edit-references');
        Route::get('{id}/ajax-edit-pets', 'Applications\IndexController@ajaxEditPets')->name('/ajax-edit-pets');
        Route::get('{id}/ajax-edit-additional-info', 'Applications\IndexController@ajaxEditAdditionalInfo')->name('/ajax-edit-additional-info');
        Route::get('{id}/ajax-edit-notes', 'Applications\IndexController@ajaxEditNotes')->name('/ajax-edit-notes');
        Route::get('{id}/ajax-edit-internal-notes', 'Applications\IndexController@ajaxEditInternalNotes')->name('/ajax-edit-internal-notes');
        Route::post('{id}/ajax-edit-save', 'Applications\IndexController@ajaxEditSave')->name('/ajax-edit-save');
        Route::post('{id}/edit-save', 'Applications\IndexController@editSave')->name('/edit-save');
        Route::delete('delete', 'Applications\IndexController@delete')->name('/delete');
        Route::get('{id}/share', 'Applications\IndexController@share')->name('/share');
        Route::post('{id}/share', 'Applications\IndexController@postShare')->name('/share/post');
        Route::post('invite-tenant-validate', 'Applications\IndexController@postInviteValidate')->name('/invite-tenant-validate/post');
        Route::post('invite-tenant', 'Applications\IndexController@postInvite')->name('/invite-tenant/post');

        Route::post('/document-upload', 'Applications\IndexController@documentUpload')->name('/document-upload');
        Route::post('/document-delete', 'Applications\IndexController@documentDelete')->name('/document-delete');
    });
    Route::post('/ajax_application_unarchive', 'Applications\IndexController@unarchive')->name('ajax_application_unarchive');

    Route::group(['prefix' => 'maintenance'], function () {

        Route::get('service-pro', 'Maintenance\ServicePro\ServiceProController@index')->name('maintenance/service-pro');
        Route::group(['prefix' => 'service-pro'], function ($id) {
            Route::post('add', 'Maintenance\ServicePro\ServiceProController@store')->name('service-pro/add');
            Route::get('{id}/delete', 'Maintenance\ServicePro\ServiceProController@delete', ['id' => $id])->name('service-pro/delete');
            Route::get('{id}/view-edit', 'Maintenance\ServicePro\ServiceProController@view', ['id' => $id])->name('/view-edit');
        });
        Route::get('/', 'Maintenance\IndexController@dashboard')->name('maintenance');
        Route::get('list-view', 'Maintenance\IndexController@listView')->name('maintenance/list-view');

        Route::post('/ajax_autocomplete', 'Maintenance\IndexController@autocomplete')->name('ajax_autocomplete');

        Route::post('/ajax_maintenance_details', 'Maintenance\IndexController@viewDetails')->name('ajax_maintenance_details');
        Route::post('/ajax_maintenance_add', 'Maintenance\IndexController@add')->name('ajax_maintenance_add');
        Route::post('/ajax_maintenance_add_message', 'Maintenance\IndexController@addMessage')->name('ajax_maintenance_add_message');
        Route::post('/ajax_maintenance_delete', 'Maintenance\IndexController@delete')->name('ajax_maintenance_delete');
        Route::post('/ajax_maintenance_archive', 'Maintenance\IndexController@archive')->name('ajax_maintenance_archive');
        Route::post('/ajax_maintenance_unarchive', 'Maintenance\IndexController@unarchive')->name('ajax_maintenance_unarchive');
        Route::post('/ajax_maintenance_notify', 'Maintenance\IndexController@notify')->name('ajax_maintenance_notify');
        Route::post('/ajax_update_maintenance_status', 'Maintenance\IndexController@updateStatus')->name('ajax_update_maintenance_status');

        Route::post('/sort', function () {
            return Response::json(array('status' => 'Success'));
        })->name('maintenance/sort');

        Route::post('/document-upload', 'Maintenance\IndexController@documentUpload')->name('maintenance/document-upload');
        Route::post('/document-delete', 'Maintenance\IndexController@documentDelete')->name('maintenance/document-delete');
        Route::post('/draft-delete', 'Maintenance\IndexController@draftDelete')->name('maintenance/draft-delete');

    });

    Route::group(['prefix' => 'payments'], function () {
        Route::get('/', 'Payments\IndexController@index')->name('payments');
        Route::get('/ajax-invoices', 'Payments\IndexController@ajaxTenantGetInvoices')->name('ajax-invoices');

        Route::get('/invoices', 'Payments\IndexController@invoices')->name('payments/invoices');
        Route::post('/ajax-details', 'Payments\IndexController@ajaxDetails')->name('payments/ajax-details');
    });

    Route::group(['prefix' => 'notifications'], function () {
        Route::post('/', 'Notifications\IndexController@index')->name('notifications');
        Route::post('/mark-as-read', 'Notifications\IndexController@markAsReadAction')->name('notifications/mark-as-read');
    });

});
Route::get('application-register-apply', 'Applications\IndexController@registerApply')->name('application-register-apply');
Route::post('application-register-apply', 'Applications\IndexController@registerApplySave')->name('application-register-apply-save');

Route::group(['middleware' => ['auth'/*, 'verified'*/]], function () {

    Route::group(['as' => 'tenant'], function () {
        Route::group(['prefix' => 'leases', 'as' => '/leases'], function () {
            Route::get('', 'Tenant\Leases\IndexController@index')->name('');
        });
    });

    Route::get('pay-invoice', 'Tenant\PaymentsController@payInvoice')->name('pay-invoice');
    Route::get('recurring-setup', 'Tenant\PaymentsController@recurringSetup')->name('recurring-setup');
    Route::post('process-payment', 'Tenant\PaymentsController@processPayment')->name('process-payment');
    Route::post('recurring-payment', 'Tenant\PaymentsController@recurringPayment')->name('recurring-payment');
    Route::post('recurring-stop', 'Tenant\PaymentsController@recurringStop')->name('recurring-stop');
    Route::get('tenant-paypal-return', 'Tenant\PaymentsController@paypalReturn')->name('tenant-paypal-return');
});
Route::post('tenant-paypal-notify', 'Tenant\PaymentsController@paypalNotify')->name('tenant-paypal-notify');

#############
# FILES
#############
Route::group(['prefix' => 'files', 'middleware' => ['auth'/*, 'verified'*/]], function () {
    Route::group(['prefix' => 'profile'], function () {
        Route::post('/upload', 'FilesController@profilePhotoUpload')->name('files/profile/upload');
        Route::post('/delete', 'FilesController@profilePhotoDelete')->name('files/profile/delete');
    });
    Route::group(['prefix' => 'property'], function () {
        Route::group(['prefix' => 'image'], function () {
            Route::post('/upload', 'FilesController@propertyImageUpload')->name('files/property/image/upload');
            Route::post('/delete', 'FilesController@propertyImageDelete')->name('files/property/image/delete');
        });
        Route::group(['prefix' => 'gallery'], function () {
            Route::post('/upload', 'FilesController@propertyGalleryUpload')->name('files/property/gallery/upload');
            Route::post('/delete', 'FilesController@propertyGalleryDelete')->name('files/property/gallery/delete');
            Route::post('/sort', 'FilesController@propertyGallerySort')->name('files/property/gallery/sort');
        });

        Route::group(['prefix' => 'unit'], function () {
            Route::group(['prefix' => 'image'], function () {
                Route::post('/upload', 'FilesController@unitImageUpload')->name('files/property/unit/image/upload');
                Route::post('/delete', 'FilesController@unitImageDelete')->name('files/property/unit/image/delete');
            });
            Route::group(['prefix' => 'gallery'], function () {
                Route::post('/upload', 'FilesController@unitGalleryUpload')->name('files/property/unit/gallery/upload');
                Route::post('/delete', 'FilesController@unitGalleryDelete')->name('files/property/unit/gallery/delete');
                Route::post('/sort', 'FilesController@unitGallerySort')->name('files/property/unit/gallery/sort');
            });
        });
    });
});


Route::group(['prefix' => '/', 'middleware' => ['auth'/*, 'verified'*/]], function () {
    Route::group(['prefix' => 'properties'], function () {
        Route::get('/{property_id}/units/{unit_id}/tenants', 'Properties\UnitsController@editUnitTenants')->name('editUnitTenants');
    });
});


Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'AdminProperties']], function () {
    Route::get('/users', 'Admin\AdminController@users')->name('users');
    Route::post('/login-as-user', 'Admin\AdminController@loginAsUser')->name('login-as-user');
    Route::get('/delete-user', 'Admin\AdminController@deleteUser')->name('delete-user');
    Route::post('/delete-user-submit', 'Admin\AdminController@deleteUserSubmit')->name('delete-user-submit');

    Route::get('/coupons', 'Admin\CouponController@index')->name('coupons');
    Route::get('/coupons/create', 'Admin\CouponController@add')->name('coupons.create');
    Route::get('/coupons/edit/{id}', 'Admin\CouponController@edit')->name('coupons.edit');
    Route::post('/coupons/save', 'Admin\CouponController@save')->name('coupons.save');
    Route::get('/coupons/delete/{coupon}', 'Admin\CouponController@delete')->name('coupons.delete');
    Route::post('/ajax-get-users', 'Admin\CouponController@ajaxGetUsers')->name('ajax-get-users');

    Route::get('/products', 'Admin\ProductController@index')->name('products');
    Route::get('/products/edit/{id}', 'Admin\ProductController@edit')->name('products.edit');
    Route::post('/products/save', 'Admin\ProductController@save')->name('products.save');

    Route::get('/addons', 'Admin\AddonsController@index')->name('admin/addons');
    Route::get('/addons/edit/{id}', 'Admin\AddonsController@edit')->name('admin/addons.edit');
    Route::post('/addons/save', 'Admin\AddonsController@save')->name('admin/addons.save');

    Route::post('/save-subscription-option', 'Admin\ProductController@saveOption')->name('save-subscription-option');
});


Route::get('/home', 'HomeController@index')->name('home');

Route::get('/reports', 'DashboardController@reports')->name('reports');

Route::get('/taz', 'HomeController@taz')->name('taz');


//Route::get('/dwolla', 'DwollaController@dwola')->name('dwolla');
Route::post('/dwolla/webhook', 'DwollaController@dwollaWebhook')->name('dwolla/webhook');
//    Route::post('/dwola/webhook', 'DwollaController@dwollaWebhook')->name('dwola/webhook');
Route::get('/dwolla/landlord/create', 'DwollaController@dwollaLandlordCreate')->name('/dwolla/landlord/create');
Route::get('/dwolla/tenant/create', 'DwollaController@dwollaTenantCreate')->name('/dwolla/tenant/create');
Route::get('/dwolla/transfer', 'DwollaController@dwollaTransfer')->name('/dwolla/transfer');
Route::get('/dwolla/webhook/update', 'DwollaController@dwollaWebhookUpdate')->name('dwolla/webhook/update');


Route::get('/api/plans', 'ApiController@plans')->name('plans');

Route::get('/test/test', function () {
});

//addons
Route::group(['prefix' => 'addon', 'middleware' => ['auth'/*, 'verified'*/, 'OnlyLandlordOrPropertyManager']], function () {
    Route::get('/screening/adv', 'Addon\ScreeningController@adv')->name('addon/screening/adv');
    Route::get('/screening/{id}', 'Addon\ScreeningController@index')->name('addon/screening');
    Route::get('/screening/api-test', 'Addon\ScreeningController@test')->name('addon/screening/api-test');
    Route::post('/screening/send', 'Addon\ScreeningController@send')->name('addon/screening/send');
});
//Route::get('/screening/callback-08295', 'Addon\ScreeningController@callback')->name('addon/screening/callback');
Route::post('/screening/callback-08295', 'Addon\ScreeningController@callback')->name('addon/screening/callback');
Route::get('/screening/callback-test', 'Addon\ScreeningController@callbackTest')->name('addon/screening/callback-test');

Route::post('/user-settings/set', 'UserSettingsController@ajaxSet')->name('user-settings/set');

Route::get('fullcalendar', 'CalendarController@index')->name('fullcalendar');
Route::get('fullcalendar-events', 'CalendarController@events')->name('fullcalendar-events');
Route::post('fullcalendar-details', 'CalendarController@details')->name('fullcalendar-details');
Route::post('fullcalendar-post', 'CalendarController@post')->name('fullcalendar-post');

//help
Route::group(['prefix' => 'help', 'middleware' => ['auth'/*, 'verified'*/]], function () {
    Route::group(['prefix' => 'landlord'], function () {

        Route::get('/get-started', function () {
            return view('help/landlord/get-started');
        })->name('help/landlord/get-started');

        Route::get('/finance', function () {
            return view('help/landlord/finance');
        })->name('help/landlord/finance');

        Route::get('/maintenance', function () {
            return view('help/landlord/maintenance');
        })->name('help/landlord/maintenance');

    });

    Route::group(['prefix' => 'tenant'], function () {

        Route::get('/get-started', function () {
            return view('help/tenant/get-started');
        })->name('help/tenant/get-started');

        Route::get('/tenant', function () {
            return view('help/tenant/finance');
        })->name('help/tenant/finance');

        Route::get('/maintenance', function () {
            return view('help/tenant/maintenance');
        })->name('help/tenant/maintenance');
    });

});


