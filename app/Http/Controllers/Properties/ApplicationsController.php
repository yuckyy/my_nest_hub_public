<?php

namespace App\Http\Controllers\Properties;

use App\Http\Requests\AddApplicationRequest;
use App\Models\Unit;
use App\Notifications\LandlordInviteTenant;
use App\Repositories\Contracts\ApplicationsRepositoryInterface;
use App\Repositories\Contracts\PropertiesRepositoryInterface;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class ApplicationsController extends Controller
{
    private $ar;
    private $pr;

    public function __construct(ApplicationsRepositoryInterface $ar, PropertiesRepositoryInterface $pr) {
        $this->ar = $ar;
        $this->pr = $pr;
    }

    public function validation(Request $request) {
        $rules = [
            //
            'firstname' => ['required', 'string'],
            'lastname' => ['required', 'string'],
            'dob' => ['required', 'date', 'date_format:Y-m-d'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'string'],
            'employmentAndlIncomes.*.employment' => ['required', 'string'],
            'employmentAndlIncomes.*.income' => ['required', 'numeric'],
            'residenceHistories.*.start_date' => ['required','date', 'date_format:Y-m-d'],
            'residenceHistories.*.address' => ['required', 'string'],
            'residenceHistories.*.city' => ['required', 'string'],
            'residenceHistories.*.state_id' => ['required', 'integer'],
            'incomes.*.description' => ['required', 'string'],
            'incomes.*.amount' => ['required', 'numeric'],
            'references.*.name' => ['required', 'string'],
            'references.*.email' => ['required', 'email'],
            'references.*.phone' => ['required', 'string'],
            'pets.*.pets_type_id' => ['required', 'integer'],
//            'pets.*.description' => ['required', 'string'],
            'unit_id' => ['required', 'integer'],
            'start_date' => ['required', 'date', 'date_format:Y-m-d'],
            'end_date' => ['sometimes', 'date', 'date_format:Y-m-d', 'nullable'],
        ];

        $attributes =  [
            'employmentAndlIncomes.*.employment' => 'employment and incomes employment',
            'employmentAndlIncomes.*.income' => 'employment and incomes income',
            'residenceHistories.*.start_date' => 'residence history start date',
            'residenceHistories.*.address' => 'residence history address',
            'residenceHistories.*.city' => 'residence history city',
            'residenceHistories.*.state_id' => 'residence history state',
            'incomes.*.description' =>  'incomes description',
            'incomes.*.amount' => 'incomes amount',
            'references.*.name' => 'references name',
            'references.*.email' => 'references email',
            'references.*.phone' => 'references phone',
            'pets.*.pets_type_id' => 'pets type',
            'pets.*.description' =>  'pets description',
            'start_date' => 'start date',
            'unit_id' => 'unit',
            'end_date' => 'end date'
        ];

        $this->validate($request, $rules, [], $attributes);

    }

    public function index(Request $request, int $unit) {
        $u = Unit::find($unit);
        $applicationsData = $this->ar->get($request->all() + ['unit_id' => $unit]);
        $applicationsDataCounter = $this->ar->getWithoutPaginate($request->all() + ['unit_id' => $unit]);
        $applications = $applicationsData['applications'];
        $applicationsCounter = $applicationsDataCounter['applications'];
        $applicationsCount = $applicationsCounter->count();
        $applicationsCountWithoutFilter = $applicationsData['applicationsCountWithoutFilter'];
        $properties = !empty(Auth::id()) ? $this->pr->getByColumn('user_id', Auth::id()) :
            $this->pr->get($request->all());

        return view(
            'properties.units.applications.index',
            array_merge(
                compact('applications', 'unit', 'applicationsCountWithoutFilter'),
                [
                    'applicationsCount' => $applicationsCount,
                    'unit' => $u,
                    'properties' => $properties,
                ]
            )
        );
    }


    public function create() {
        return view('applications.add');
    }

    public function store(AddApplicationRequest $request) {
        $application = $this->ar->save($request->all());
        return redirect()->route('applications')->withFlashSuccess('Application successfully created!');
    }

    public function show(int $id) {
        $application = $this->ar->getById($id);
        return view('applications.view', compact('application'));
    }
    public function edit(array $data, int $id) {

    }
    public function update() {

    }
    public function destroy(int $id) {
        $application = $this->ar->delete($id);
        return redirect()->route('applications')->withFlashSuccess("Application $application->id successfully deleted!");

    }

    public function delete(Request $request)
    {
        $application = $this->ar->destroy((int)$request->id);
        if (!$application) return response()->json('error', 404);
        return response()->json('error', 200);
    }

    public function postInviteValidate(Request $request, $unit) {
        $request->validate(
            [
                'email' => 'required|email',
            ]
        );
    }

    public function postInvite(Request $request, $unit) {
        $request->validate(
            [
                'email' => 'required|email',
            ]
        );
        $landlord = Auth::user();
        $unitObj = Unit::find($unit);
        Notification::route('mail', $request['email'])->notify(new LandlordInviteTenant($landlord, $unitObj, $request['email']));

        return redirect()->route('properties/units/applications', ['unit' => $unit])
            ->with('success','The tenant had been invited to submit an application.')
            ->with('whatsnext',"Our system delivered to your potential tenant an email with instructions on how to submit an application. Once the tenant submits an application, our system will send you an email notification and you will have the ability to preview the tenants' application.");
    }
}
