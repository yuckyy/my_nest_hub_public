<?php

namespace App\Http\Controllers\Api\Applications;

use App\Http\Requests\AddApplicationRequest;
use App\Repositories\Contracts\ApplicationsRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class IndexController extends Controller
{
    //
//    public function index($page = 1) {
//        return view(
//            'applications.index',
//            [
//                'propertiesExist' => Auth::user()->properties()->count() > 0,
//                'page' => $page,
//            ]
//        );
//    }

    private $ar;

    public function __construct(ApplicationsRepositoryInterface $ar) {
        $this->ar = $ar;
    }

    public function destroy(int $id) {
        $application = $this->ar->delete($id);
        if (!$application) return false;
        return true;
    }
}
