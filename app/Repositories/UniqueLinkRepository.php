<?php


namespace App\Repositories;


use App\Models\UniqueLink;
use App\Repositories\Contracts\UniqueLinkRepositoryInterface;

class UniqueLinkRepository implements UniqueLinkRepositoryInterface
{

    public function getById(int $id) {
        return UniqueLink::find($id);
    }

    public function getByColumn(string $column, string $value) {
        return UniqueLink::where($column, $value)->first();
    }
}
