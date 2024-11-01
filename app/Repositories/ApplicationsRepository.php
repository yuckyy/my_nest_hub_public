<?php


namespace App\Repositories;


use App\Models\Application;
use App\Repositories\Contracts\ApplicationsRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class ApplicationsRepository implements ApplicationsRepositoryInterface
{
    private function filter(array $data = []) {
        $query = Application::query();

        $query->withTrashed();

        $query->leftJoin('applications_users', 'applications.id', '=', 'applications_users.application_id');

        if (!empty(Auth::id())){
            $query->where(function($q6) {
                $q6->where('applications.user_id', Auth::id());
                $q6->orWhere('applications_users.user_id', Auth::id());
            });
        }

        if (!empty($data['column']) && !empty($data['value']))
            $query->where($data['column'], $data['value']);

        if (!empty($data['unit_id'])) $query->where('unit_id', $data['unit_id']);

        $applicationsCountWithoutFilter = $query->count();

        if (!empty($data['q_properties'])) {
            $propertyId = $data['q_properties'];
            $query->whereHas('unit', function($q1) use ($propertyId) {
                $q1->whereHas('property', function($q2) use ($propertyId) {
                    $q2->where('properties.id', $propertyId);
                });
            });

            $ids = $query->pluck('applications.id')->toArray();
        }

        if (!empty($data['q_search'])) {
            if (!empty($ids)) $query->whereIn('applications.id', $ids);
            $search = explode(" ", $data['q_search']);
            if (count($search) == 2) {
                $query->where('firstname','like', '%' . reset($search) . '%')
                    ->orWhere('lastname','like', '%' . end($search) . '%');
            } else{
                $query->where('firstname','like', '%' . reset($search) . '%')
                    ->orWhere('lastname','like', '%' . reset($search) . '%');
            }
        }

        if (!empty($data['q_applications'])) {
            $status = $data['q_applications'];

            if ($status === 'Deleted') {
                $query->whereNotNull('deleted_at');
            } else if ($status === 'Approved') {
                $query->whereNull('deleted_at')->whereHas('leases');
            } else if ($status === 'Pending') {
                $query->whereNull('deleted_at')->whereDoesntHave('leases');
            }
        } else {
            $query->whereNull('deleted_at');
        }

        //TODO revise this repository interface and remove it some day. move it to the controller
        if (\Request::has('archived')) {
            if(Auth::user()->isLandlord() || Auth::user()->isPropManager()){
                $query->where('applications.archived',1);
            }
        } else {
            if(Auth::user()->isLandlord() || Auth::user()->isPropManager()){
                $query->where('applications.archived',0);
            }
        }

        return [
            'applicationsCountWithoutFilter' => $applicationsCountWithoutFilter,
            'query' => $query
        ];
    }


    public function get(array $data = []) {
        $filterData = $this->filter($data);

        return [
            'applicationsCountWithoutFilter' => $filterData['applicationsCountWithoutFilter'],
            'applications' => $filterData['query']->select(['*','applications.id as id','applications.user_id as user_id'])->orderby("applications_users.applied_at","DESC")->orderby("applications.created_at","DESC")->paginate(20)
        ];
    }
    public function getWithoutPaginate(array $data = []) {
        $filterData = $this->filter($data);

        return [
            'applicationsCountWithoutFilter' => $filterData['applicationsCountWithoutFilter'],
            'applications' => $filterData['query']->select(['*','applications.id as id','applications.user_id as user_id'])->orderby("applications_users.applied_at","DESC")->orderby("applications.created_at","DESC")->paginate(100000000)
        ];
    }

    public function getAll() {
        return Application::all();
    }

    public function getById(int $id) {
        $application = Application::find($id);
        //$application->new = 0;
        //$application->save();
        return $application;
    }

    public function getAllByColumn(string $column, $value) {
       return Application::where($column, $value)->get();
    }

    public function getByColumn(string $column, $value, array $data = []) {
        return $this->filter($data + ['column' => $column, 'value' => $value])['query']->paginate(config('app.per_page'));
    }

    public function save(array $data) {
        $data['firstname'] = ucfirst(strtolower($data['firstname']));
        $data['lastname'] = ucfirst(strtolower($data['lastname']));

        $application = Application::create($data + ['user_id' => Auth::id()]);

        if (!empty($data['incomes']))
            foreach ($data['incomes'] as $incomeData){
                $incomeData['amount'] = (float) str_replace(",","",$incomeData['amount']);
                $application->incomes()->create($incomeData);
            }

        if (!empty($data['amenties']))
            foreach ($data['amenties'] as $amentyData)
                $application->amenties()->create($amentyData);

        if (!empty($data['pets']))
            foreach ($data['pets'] as $petData)
                $application->pets()->create($petData);

        if (!empty($data['employmentAndlIncomes']))
            foreach ($data['employmentAndlIncomes'] as $employmentAndlIncomeData){
                $employmentAndlIncomeData['income'] = (float) str_replace(",","",$employmentAndlIncomeData['income']);
                $employmentAndlIncomeData['employment'] = ucfirst(strtolower($employmentAndlIncomeData['employment']));
                $application->employmentAndlIncomes()->create($employmentAndlIncomeData);
            }


        if (!empty($data['references']))
            foreach ($data['references'] as $referenceData) {
                $referenceData['name'] = ucfirst(strtolower($referenceData['name']));
                $application->references()->create($referenceData);
            }

        if (!empty($data['residenceHistories']))
            foreach ($data['residenceHistories'] as $residenceHistoryData) {
                $residenceHistoryData['address'] = ucfirst(strtolower($residenceHistoryData['address']));
                $residenceHistoryData['city'] = ucfirst(strtolower($residenceHistoryData['city']));
                $application->residenceHistories()->create($residenceHistoryData);
            }

        return $application;
    }

    public function update(array $data, int $id) {
        $application = Application::findOrFail($id)->update($data);
        return $application->save();
    }

    public function updateColumn(string $column, string $value, int $id) {
        $application = Application::findOrFail($id);
        if (!empty($application->$column)) $application->$column = $value;
        return $application->save();
    }

    //public function destroy($id) {
    //    return Application::findOrFail($id)->delete();
    //}
}
