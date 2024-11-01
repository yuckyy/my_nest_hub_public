<?php


namespace App\Repositories;


use App\Models\Property;
use App\Repositories\Contracts\PropertiesRepositoryInterface;

class PropertiesRepository implements PropertiesRepositoryInterface
{

    public function get(array $data = []) {
        return Property::paginate(config('app.per_page'));
    }

    public function getAll() {
        return Property::all();
    }

    public function getById(int $id) {
        return Property::find($id);
    }

    public function getByColumn(string $column, string $value) {
        return Property::where($column, $value)->get();
    }

    public function update(array $data, int $id) {
        $property = Property::findOrFail($id)->update($data);
        return $property->save();
    }

    public function updateColumn(string $column, string $value, int $id) {
        $property = Property::findOrFail($id);
        if (!empty($property->$column)) $property->$column = $value;
        return $property->save();
    }

    public function destroy($id) {
        return Property::findOrFail($id)->delete();
    }
}
