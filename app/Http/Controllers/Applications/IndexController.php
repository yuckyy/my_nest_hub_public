<?php

namespace App\Http\Controllers\Applications;

use App\Http\Requests\AddApplicationRequest;
use App\Http\Requests\Tenant\ShareApplicationRequest;
use App\Models\Application;
use App\Models\ApplicationDocument;
use App\Models\EmploymentAndIncome;
use App\Models\AdditionalIncome;
use App\Models\ResidenceHistory;
use App\Models\References;
use App\Models\Pets;
use App\Models\Role;
use App\Models\State;
use App\Models\PetsTypes;
use App\Models\Unit;
use App\Models\Property;
use App\Models\User;
use App\Notifications\LandlordInviteTenant;
use App\Notifications\LandlordNewApplicationReceived;
use App\Notifications\TenantSharedApplication;
use App\Repositories\Contracts\ApplicationsRepositoryInterface;
use App\Repositories\Contracts\PropertiesRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\PropertiesRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class IndexController extends Controller
{

    private $ar;
    private $pr;
    private $ur;

    public function __construct(ApplicationsRepositoryInterface $ar, PropertiesRepositoryInterface $pr, UserRepositoryInterface $ur) {
        $this->ar = $ar;
        $this->pr = $pr;
        $this->ur = $ur;
    }

    public function validation(Request $request) {
//        return $request->all();
        $rules = [
            //
            'firstname' => ['required', 'string'],
            'lastname' => ['required', 'string'],
            'dob' => ['required', 'date', 'date_format:Y-m-d'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'string'],
            'employmentAndlIncomes.*.employment' => ['required', 'string'],
            'employmentAndlIncomes.*.income' => ['required', 'string'],
            'residenceHistories.*.start_date' => ['required','date', 'date_format:Y-m-d'],
            'residenceHistories.*.end_date' => ['date', 'date_format:Y-m-d',  'greater_than_field:start_date', 'nullable'],
            'residenceHistories.*.address' => ['required', 'string'],
            'residenceHistories.*.city' => ['required', 'string'],
            'residenceHistories.*.state_id' => ['required', 'integer'],
            'incomes.*.description' => ['required', 'string'],
            'incomes.*.amount' => ['required', 'string'],
            'references.*.name' => ['required', 'string'],
            'references.*.email' => ['required', 'email'],
            'references.*.phone' => ['required', 'string'],
            'pets.*.pets_type_id' => ['required', 'integer'],
//            'pets.*.description' => ['required', 'string'],
            'unit_id' => ['required', 'integer'],
            'start_date' => ['required', 'date', 'date_format:Y-m-d'],
            'end_date' => ['date', 'date_format:Y-m-d',  'greater_than_field:start_date', 'nullable'],
        ];


        $messages = [
            'incomes.*.amount.regex' => 'This value must be no more than 8 digits before the dot',
            'employmentAndlIncomes.*.income.regex' => 'This value must be no more than 8 digits before the dot',
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

        $this->validate($request, $rules, $messages, $attributes);

    }

    public function index(Request $request, $unit = null) {
        $applicationsData = $this->ar->get($request->all());
        $applications = $applicationsData['applications'];
        $applicationsArchive = $applications->where('archived',1)->count();
        $applicationsCountWithoutFilter = $applicationsData['applicationsCountWithoutFilter'];
        $properties = Property::leftJoin('units', 'properties.id', '=', 'units.property_id')
            ->leftJoin('applications', 'units.id', '=', 'applications.unit_id')
            ->where('properties.user_id', Auth::id())
            ->whereNotNull('applications.id')
            //->whereNull('applications.deleted_at')
            ->selectRaw('properties.*')
            ->distinct()
            ->get();

        if (\Request::has('archived')) {
            if(Auth::user()->isLandlord() || Auth::user()->isPropManager()){
                return view('applications.archive', compact('applications', 'properties', 'unit', 'applicationsCountWithoutFilter'));
            } else {
                return view('applications.index', compact('applications', 'properties', 'unit', 'applicationsCountWithoutFilter', 'applicationsArchive'));
            }
        } else {
            return view('applications.index', compact('applications', 'properties', 'unit', 'applicationsCountWithoutFilter', 'applicationsArchive'));
        }
    }


    public function create(Request $request) {
        $properties = !empty(Auth::id()) ? $this->pr->getByColumn('user_id', Auth::id()) :
            $this->pr->get($request->all());

        if(!(Auth::user()->isLandlord() || Auth::user()->isPropManager())){
            $tenant = Auth::user();
        } else {
            $tenant = null;
        }
// var_dump($properties);
// die;
        //delete found not connected documents
        $documents = ApplicationDocument::where(['user_id' => Auth::user()->id])->whereNull('application_id')->get();
        foreach($documents as $document){
            Storage::delete('public/' . $document->filepath);
            if(!empty($document->thumbnailpath)){
                Storage::delete('public/' . $document->thumbnailpath);
            }
            $document->delete();
        }

        return view('applications.add', compact('properties','tenant'));
    }

    public function createapplications(Request $request) {
        $unit = Unit::find($request->unit_id);
        $properties = !empty(Auth::id()) ? $this->pr->getByColumn('user_id', Auth::id()) :
            $this->pr->get($request->all());

        if(!(Auth::user()->isLandlord() || Auth::user()->isPropManager())){
            $tenant = Auth::user();
        } else {
            $tenant = null;
        }
//        $unit =
// var_dump($properties);
// die;
        //delete found not connected documents
        $documents = ApplicationDocument::where(['user_id' => Auth::user()->id])->whereNull('application_id')->get();
        // var_dump($documents);
        // die;
        foreach($documents as $document){
            Storage::delete('public/' . $document->filepath);
            if(!empty($document->thumbnailpath)){
                Storage::delete('public/' . $document->thumbnailpath);
            }
            $document->delete();
        }
//var_dump( $unit['id']);
//        $petsTypes = $unit->with(['petsTypes' => PetsTypes::get(), 'states' => State::get(), 'units' => Unit::get()]);
        return view('applications.add-from-list', compact('properties','tenant','unit'));
    }


    public function store(AddApplicationRequest $request) {
        $application = $this->ar->save($request->all());

        $document_ids = $request->get('document_ids', []);
        foreach ($document_ids as $document_id) {
            $document = ApplicationDocument::find($document_id);
            $document->application_id = $application->id;
            $document->save();
        }

        if(!(Auth::user()->isLandlord() || Auth::user()->isPropManager())){
            if(!empty($application->unit_id)) {
                $tenant = Auth::user();
                $unit = Unit::find($application->unit_id);
                $landlord = $unit->property->user;
                $landlord->notify(new LandlordNewApplicationReceived($tenant, $landlord, $application));
                $landlord->sharedApplications()->attach($application->id);
            }

            return redirect()->route('applications')
                ->with('success','Application has been created.')
                ->with('whatsnext','Your application is ready to be shared with your potential landlord. On the left-hand side of the menu, click on “Applications” and then click on the “Share Application” icon. Watch this video to see how to share an application.')
                ->with('gif', url('/').'/images/help/tenant-how-to-share-application-with-landlord.gif');
        }
        $unit = Unit::find($application->unit_id);
        return redirect()->route('properties/units/applications' , compact('unit'))->with('success','Application has been created.');
    }

    public function storeapplications(AddApplicationRequest $request) {
        $application = $this->ar->save($request->all());

        $document_ids = $request->get('document_ids', []);
        foreach ($document_ids as $document_id) {
            $document = ApplicationDocument::find($document_id);
            $document->application_id = $application->id;
            $document->save();
        }

        if(!(Auth::user()->isLandlord() || Auth::user()->isPropManager())){
            if(!empty($application->unit_id)) {
                $tenant = Auth::user();
                $unit = Unit::find($application->unit_id);
                $landlord = $unit->property->user;
                $landlord->notify(new LandlordNewApplicationReceived($tenant, $landlord, $application));
                $landlord->sharedApplications()->attach($application->id);
            }

            return redirect()->route('applications')
                ->with('success','Application has been created.')
                ->with('whatsnext','Your application is ready to be shared with your potential landlord. On the left-hand side of the menu, click on “Applications” and then click on the “Share Application” icon. Watch this video to see how to share an application.')
                ->with('gif', url('/').'/images/help/tenant-how-to-share-application-with-landlord.gif');
        }
        return redirect()->route('applications')->with('success','Application has been created.');
    }


    public function show(int $id) {
        $application = Application::find($id);

        if (!$application) {
            return response()->view('errors.' . '404-app', [], 404);
        }
        $shared = Application::leftJoin('applications_users', 'applications.id', '=', 'applications_users.application_id')
            ->where('applications.id', $id)
            ->where('applications_users.user_id', Auth::user()->id)->count();
        $owner = Application::where('user_id', Auth::user()->id)
            ->where('applications.id', $id)
            ->count();
        if (!$shared && !$owner) {
            return response()->view('errors.' . '404-app', [], 404);
        }

        DB::update('UPDATE `applications_users` set `is_new` = FALSE WHERE `application_id` = ? AND `user_id` = ?', [$id,Auth::user()->id]);

        $documents = ApplicationDocument::where('application_id',$application->id)->get();

        return view('applications.view', compact('application', 'documents'));
    }

    public function viewEdit(int $id) {
        $application = Application::find($id);

        if (!$application) {
            return response()->view('errors.' . '404-app', [], 404);
        }
        $owner = Application::where('user_id', Auth::user()->id)
            ->where('applications.id', $id)
            ->count();
        if (!$owner) {
            return response()->view('errors.' . '404-app', [], 404);
        }

        DB::update('UPDATE `applications_users` set `is_new` = FALSE WHERE `application_id` = ? AND `user_id` = ?', [$id,Auth::user()->id]);

        $properties = Property::where(['user_id' => Auth::user()->id])->get();
        if(!empty($application->unit_id)) {
            $currUnit = Unit::find($application->unit_id);
            $currProperty = !empty($currUnit) ? $currUnit->property : null;
        } else {
            $currUnit = null;
            $currProperty = null;
        }

        $documents = ApplicationDocument::where('user_id', Auth::user()->id)->where('application_id',$application->id)->get();

        return view('applications.edit', compact('application','properties','currUnit','currProperty','documents'));
    }

    public function editSave(int $id, Request $request) {
        $application = Application::find($id);

        if (!$application) {
            abort(404);
        }
        $owner = Application::where('user_id', Auth::user()->id)
            ->where('applications.id', $id)
            ->count();
        if (!$owner) {
            abort(404);
        }

        if (!empty($request['additionalInfo'])) {
            $request->merge(['smoke' => request()->exists('smoke')]);
            $request->merge(['evicted_or_unlawful' => request()->exists('evicted_or_unlawful')]);
            $request->merge(['felony_or_misdemeanor' => request()->exists('felony_or_misdemeanor')]);
            $request->merge(['refuse_to_pay_rent' => request()->exists('refuse_to_pay_rent')]);
        }
        if (!empty($request['updateNotes'])) {
            $request->merge(['notes' => request()->get('notes') ? request()->get('notes') : ""]);
        }
        if (!empty($request['updateInternalNotes'])) {
            $request->merge(['internal_notes' => request()->get('internal_notes') ? request()->get('internal_notes') : ""]);
        }

        $application->update($request->all());

        if (!empty($request['employmentAndIncomesUpdate'])) {
            DB::table('employment_and_incomes')
                ->where('application_id', $id)
                ->delete();
            if(is_array($request['employmentAndIncomes'])) {
                foreach ($request['employmentAndIncomes'] as $data) {
                    if(isset($data['employment']) && isset($data['income'])) {
                        $m = new EmploymentAndIncome();
                        $m->application_id = $id;
                        $m->employment = $data['employment'];
                        $m->income = (float)str_replace(",", "", $data['income'] ?? 0.00);
                        $m->save();
                    }
                }
            }
        }

        if (!empty($request['additionalIncomesUpdate'])) {
            DB::table('additional_incomes')
                ->where('application_id', $id)
                ->delete();
            if(is_array($request['additionalIncomes'])) {
                foreach ($request['additionalIncomes'] as $data) {
                    if(isset($data['description']) && isset($data['amount'])) {
                        $m = new AdditionalIncome();
                        $m->application_id = $id;
                        $m->description = $data['description'];
                        $m->amount = (float)str_replace(",", "", $data['amount'] ?? 0.00);
                        $m->save();
                    }
                }
            }
        }

        if (!empty($request['residenceHistoriesUpdate'])) {
            DB::table('residence_histories')
                ->where('application_id', $id)
                ->delete();
            if(is_array($request['residenceHistories'])) {
                foreach ($request['residenceHistories'] as $data) {
                    if(isset($data['state_id'])) {
                        ResidenceHistory::create($data + ['application_id' => $id]);
                    }
                }
            }
        }

        if (!empty($request['referencesUpdate'])) {
            DB::table('references')
                ->where('application_id', $id)
                ->delete();
            if(is_array($request['references'])) {
                foreach ($request['references'] as $data) {
                    if(isset($data['name']) || isset($data['email']) || isset($data['phone'])) {
                        References::create($data + ['application_id' => $id]);
                    }
                }
            }
        }

        if (!empty($request['petsUpdate'])) {
            DB::table('pets')
                ->where('application_id', $id)
                ->delete();
            if(is_array($request['pets'])) {
                foreach ($request['pets'] as $data) {
                    if(isset($data['pets_type_id']) && isset($data['description'])) {
                        Pets::create($data + ['application_id' => $id]);
                    }
                }
            }
        }

        if($request->get('return')){
            return redirect()->route($request->get('return'), ['id' => $id])->with('success','Application has been updated.');
        }
        return redirect()->route('applications/view-edit', ['id' => $id])->with('success','Application has been updated.');
    }

    public function ajaxEditEmploymentAndIncomes(int $id){
        $application = Application::find($id);
        $employmentAndIncomes = $application->employmentAndlIncomes()->get();
        return view('applications.ajax-edit-employment-and-incomes', compact('application','employmentAndIncomes'));
    }

    public function ajaxEditAdditionalIncomes(int $id){
        $application = Application::find($id);
        $additionalIncomes = $application->additionalIncomes()->get();
        return view('applications.ajax-edit-additional-incomes', compact('application','additionalIncomes'));
    }

    public function ajaxEditResidenceHistories(int $id){
        $application = Application::find($id);
        $residenceHistories = $application->residenceHistories()->get();
        $states = State::get();
        return view('applications.ajax-edit-residence-histories', compact('application','residenceHistories','states'));
    }

    public function ajaxEditReferences(int $id){
        $application = Application::find($id);
        $references = $application->references()->get();
        return view('applications.ajax-edit-references', compact('application','references'));
    }

    public function ajaxEditPets(int $id){
        $application = Application::find($id);
        $pets = $application->pets()->get();
        $petsTypes = PetsTypes::get();
        return view('applications.ajax-edit-pets', compact('application','pets','petsTypes'));
    }

    public function ajaxEditNotes(int $id){
        $application = Application::find($id);
        return view('applications.ajax-edit-notes', compact('application'));
    }

    public function ajaxEditInternalNotes(int $id){
        $application = Application::find($id);
        return view('applications.ajax-edit-internal-notes', compact('application'));
    }

    public function ajaxEditAdditionalInfo(int $id){
        $application = Application::find($id);
        return view('applications.ajax-edit-additional-info', compact('application'));
    }

    public function share(int $id, Request $request) {
        DB::update('UPDATE `applications_users` set `is_new` = FALSE WHERE `application_id` = ? AND `user_id` = ?', [$id,Auth::user()->id]);

        $user = Auth::user();
        $application = Application::find($id);
        return view('applications.share', compact('application', 'user'));
    }

    public function postShare(int $id, ShareApplicationRequest $request) {
        $this->validate($request, ['email' => ['required', 'email']]);
        $application = Application::find($id);
        //$this->ur->shareLandlordApplication($request->all(), $application);

        $sharedUser = Auth::user();
        $landlord = User::where('email', $request['email'])->whereHas('roles', function ($q) {
            $q->where('name', 'Landlord');
        })->first();
        if ($landlord) {
            $landlord->notify(new LandlordNewApplicationReceived($sharedUser, $landlord, $application));
            $landlord->sharedApplications()->syncWithoutDetaching([$application->id]);
        } else {
            $tenant = User::where('email',$request['email'])->first();
            if(!empty($tenant)){
                $tenant->notify(new TenantSharedApplication($sharedUser, $application, $request['email']));
            } else {
                \Illuminate\Support\Facades\Notification::route('mail', $request['email'])->notify(new TenantSharedApplication($sharedUser, $application, $request['email']));
            }
        }

        return redirect()->route('applications')
            ->with('success','Thank you for sharing your application! Our system delivered your application to the landlord.')
            ->with('whatsnext','Landlord will review your application. He has a choice to reject or approve it. You can share your application with other landlords.');
    }

    public function edit(array $data, int $id) {

    }
    public function update() {

    }
    //public function destroy(int $id) {
    //    $application = $this->ar->delete($id);
    //    return redirect()->route('applications')->with('success',"Application $application->id has been deleted!");
    //
    //}

    public function delete(Request $request)
    {
        $application = Application::findOrFail((int)$request->id);
        if (!$application) return response()->json('error', 404);
        if ($application->user_id == Auth::user()->id){
            $application->delete();
            DB::statement('DELETE FROM `applications_users` WHERE `application_id` = ?', [ (int)$request->id ]);
            return response()->json('success', 200);
        }
        DB::statement('DELETE FROM `applications_users` WHERE `application_id` = ? AND `user_id` = ?', [ (int)$request->id, Auth::user()->id ]);
        return response()->json('success', 200);
    }

    public function unarchive(Request $request) {

        $application = Application::where('id', $request->get('record_id'))->first();
        if (!$application) {
            abort(404);
        }
        $property = $application->unit->property;
        if(Auth::user()->id != $property->user_id){
            abort(404);
        }

        $application->archived = 0;
        $application->save();

        $result = [
            "result" => "success",
        ];
        return json_encode($result);
    }

    public function postInviteValidate(Request $request) {
        $request->validate(
            [
                'email' => 'required|email',
            ]
        );
    }

    public function postInvite(Request $request) {
        $request->validate(
            [
                'email' => 'required|email',
            ]
        );
        $landlord = Auth::user();
        Notification::route('mail', $request['email'])->notify(new LandlordInviteTenant($landlord, null, $request['email']));

        return redirect()->route('applications')
            ->with('success','The tenant had been invited to submit an application.')
            ->with('whatsnext',"Our system delivered to your potential tenant an email with instructions on how to submit an application. Once the tenant submits an application, our system will send you an email notification and you will have the ability to preview the tenants' application.");
    }

    public function registerApply(Request $request) {
        if(empty($request->email)){
            return redirect()->route('register');
        }

        if(!empty(Auth::id())){
            $user = Auth::user();
            if($request->email != $user->email){
                Auth::logout();
                return redirect()->route('login');
            }
        } else {
            $user = User::where('email',$request->email)->first();
            if(!empty($user)){
                return redirect()->route('login')->with('error','An account with your email already exists. Please login first.');
            }
        }
        if(!empty($request->unit_id)){
            $unit = Unit::find($request->unit_id);
            $landlord = $unit->property->user;
        }
        if(!empty($request->landlord_id) && empty($landlord)){
            $landlord = User::find($request->landlord_id);
        }
        if(empty($landlord)){
            return redirect()->route('register');
        }
        if(empty($unit)){
            $properties = $this->pr->getByColumn('user_id', $landlord->id);
        } else {
            $properties = Property::where('id',$unit->property_id)->get();
        }

        if(empty($properties)){
            return redirect()->route('register');
        }

        $petsTypes = PetsTypes::get();

        return view(
            'applications.register-apply',
            array_merge(
                [
                    'unit' => $unit ?? null,
                    'properties' => $properties,
                    'email' => $request->email,
                    'petsTypes' => $petsTypes,
                    'landlord' => $landlord,
                    'user' => $user ?? null,
                ]
            )
        );

    }

    public function registerApplySave(AddApplicationRequest $request) {

        $landlord = User::find($request->landlord_id);

        $firstname = ucfirst(strtolower($request->firstname));
        $lastname = ucfirst(strtolower($request->lastname));

        if(!empty(Auth::id())){
            $user = Auth::user();
        } else {
            //create user
            $user = User::create([
                'name' => $firstname,
                'lastname' => $lastname,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $role = Role::where('name', 'Tenant')->first();
            $user->roles()->attach($role);
            $user->save();
            $user->markEmailAsVerified();
            Auth::login($user);
        }

        //create application
        $data = $request->all();
        $data['user_id'] = $user->id;
        $application = Application::create($data);

        if (!empty($request['incomes']))
            foreach ($request['incomes'] as $incomeData){
                $incomeData['amount'] = (float) str_replace(",","",$incomeData['amount']);
                $application->incomes()->create($incomeData);
            }

        if (!empty($request['amenties']))
            foreach ($request['amenties'] as $amentyData)
                $application->amenties()->create($amentyData);

        if (!empty($request['pets']))
            foreach ($request['pets'] as $petData)
                $application->pets()->create($petData);

        if (!empty($request['employmentAndlIncomes']))
            foreach ($request['employmentAndlIncomes'] as $employmentAndlIncomeData){
                $employmentAndlIncomeData['income'] = (float) str_replace(",","",$employmentAndlIncomeData['income']);
                $employmentAndlIncomeData['employment'] = ucfirst(strtolower($employmentAndlIncomeData['employment']));
                $application->employmentAndlIncomes()->create($employmentAndlIncomeData);
            }


        if (!empty($request['references']))
            foreach ($request['references'] as $referenceData) {
                $referenceData['name'] = ucfirst(strtolower($referenceData['name']));
                $application->references()->create($referenceData);
            }

        if (!empty($request['residenceHistories']))
            foreach ($request['residenceHistories'] as $residenceHistoryData) {
                $residenceHistoryData['address'] = ucfirst(strtolower($residenceHistoryData['address']));
                $residenceHistoryData['city'] = ucfirst(strtolower($residenceHistoryData['city']));
                $application->residenceHistories()->create($residenceHistoryData);
            }


        //Notify landlord
        if(!empty($application->unit_id)) {
            $unit = Unit::find($application->unit_id);
            $landlord = $unit->property->user;
        }
        $landlord->notify(new LandlordNewApplicationReceived($user, $landlord, $application));
        $landlord->sharedApplications()->attach($application->id);


        /*
        return redirect()->route('applications')
            ->with('success','Application has been created.')
            ->with('whatsnext','Your application is ready to be shared with your potential landlord. On the left-hand side of the menu, click on “Applications” and then click on the “Share Application” icon. Watch this video to see how to share an application.')
            ->with('gif', url('/').'/images/help/tenant-how-to-share-application-with-landlord.gif');
            */

        return redirect()->route('applications')->with('success','Application has been created.');
    }



    public function documentUpload(Request $request)
    {
        $user = Auth::user();

        $allowed_extensions = ['doc', 'docx', 'pdf', 'txt', 'jpg', 'jpeg', 'png', 'gif', 'xls', 'xlsx', 'csv'];

        if (!$request->has('documents')) {
            return response()->json(['error' => 'No files found for upload.']);
        }

        $document_files = $request->file('documents', []);
        $target_file_path_output = [];

        foreach ($document_files as $document_file) {
            if(in_array(strtolower($document_file->getClientOriginalExtension()), $allowed_extensions)){

                $filePath = 'public/applications/' . $user->id;
                [, $filepath] = preg_split('/\//', $document_file->store($filePath), 2);
                $document = new ApplicationDocument();
                $document->user_id = $user->id;
                $document->application_id = empty($request->application_id) ? null : $request->application_id;
                $document->filepath = $filepath;
                $document->name = $document_file->getClientOriginalName();
                $document->extension = $document_file->getClientOriginalExtension();
                $document->mime = $document_file->getMimeType();
                $document->save();

                $target_file_path_output[] = [
                    'url' => url('storage/' . $filepath),
                    'name' => $document->name,
                    'icon' => $document->icon(),
                    'id' => $document->id
                ];

            } else {
                $target_file_path_output[] = [
                    'error' => 'File type not allowed',
                    'name' => $document_file->getClientOriginalName(),
                    'icon' => '<i class="fal fa-file"></i>',
                    'id' => '0'
                ];
            }

        }

        $output = ['uploaded' => $target_file_path_output];
        return response()->json($output);
    }

    public function documentDelete(Request $request)
    {
        $user = Auth::user();
        $document = ApplicationDocument::where(['id' => $request->document_id, 'user_id' => $user->id ])->first();

        if(!empty($document)) {
            $document_id = $document->id;

            Storage::delete('public/' . $document->filepath);
            if(!empty($document->thumbnailpath)){
                Storage::delete('public/' . $document->thumbnailpath);
            }
            $document->delete();

            $output = ['success' => 'Processed', 'document_id' => $document_id];
            return response()->json($output);
        }
    }

}
