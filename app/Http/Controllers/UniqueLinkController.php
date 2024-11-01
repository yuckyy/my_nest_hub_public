<?php

namespace App\Http\Controllers;

use App\Factories\AmenitiesFactory;
use App\Models\UniqueLink;
use App\Repositories\Contracts\UniqueLinkRepositoryInterface;
use Illuminate\Http\Request;

class UniqueLinkController extends Controller
{
    //
    private $ulr;

    public function __construct(UniqueLinkRepositoryInterface $ulr){
        $this->ulr = $ulr;
    }

    public function view(string $link) {
        $model = $this->ulr->getByColumn('link', $link);
        if (empty($model)) abort(404);

        $modelType = $model->model_type;

        $modelName = explode("\\", $modelType);
        $modelName = mb_strtolower(end($modelName));

        if ($modelName == 'unit') {
            $unit = $model->model;
            $structures = AmenitiesFactory::get($unit->id);
            return view('public.' . $modelName . '.view', compact('unit', 'structures'));
        }
        abort(404);
    }
}
