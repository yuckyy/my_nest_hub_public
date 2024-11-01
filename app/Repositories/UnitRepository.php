<?php


namespace App\Repositories;


use App\Models\Unit;
use App\Repositories\Contracts\UnitRepositoryInterface;

class UnitRepository implements UnitRepositoryInterface
{

    public function getById(int $id) {
        return Unit::find($id);
    }

    public function getByColumn(string $column, string $value) {
        return Unit::where($column, $value)->get();
    }

    public function getByUniqueLink(string $link) {
        return Unit::whereHas('link', function($q) use ($link) {
            $q->where('link', $link);
        })->first();
    }
}
