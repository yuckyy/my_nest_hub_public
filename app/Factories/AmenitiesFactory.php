<?php

namespace App\Factories;

use App\Models\Amenities;
use App\Models\AmenitiesStructure;
use Illuminate\Support\Facades\DB;

class AmenityObject {
    public $id;
    public $name;
    public $icon;
    public $group_type;
    public $value;

    public function __construct($id, $name, $icon, $group_type, $value, $unit_id) {
        $this->id = $id;
        $this->name = $name;
        $this->icon = $icon;
        $this->group_type = $group_type;
        $this->value = $value;
        $this->unit_id = $unit_id;
    }

    public function children() {
        return AmenitiesFactory::get($this->unit_id, $this->id);
    }
}

class AmenitiesFactory {
    public static function get($unit_id, $parent_id = null) {
        return array_map(
            function ($data) use ($unit_id) {
                return new AmenityObject(
                    $data->id,
                    $data->name,
                    $data->icon,
                    $data->group_type,
                    $data->value,
                    $unit_id
                );
            },
            DB::table('amenities_structures')
                ->where('parent', $parent_id)
                ->leftJoin('amenities', function ($join) use ($unit_id) {
                    $join
                        ->on('amenities.amenities_structure_id', '=', 'amenities_structures.id')
                        ->where('amenities.unit_id', $unit_id);
                })
                ->select(
                    'amenities_structures.id as id',
                    'amenities_structures.parent as parent',
                    'amenities_structures.name as name',
                    'amenities_structures.icon as icon',
                    'amenities_structures.group_type as group_type',
                    'amenities.value as value'
                )
                ->orderBy('sort', 'asc')
                ->get()
                ->toArray()
        );
    }

    public static function set($unit_id, $data) {
        DB::table('amenities')->where('unit_id', $unit_id)->delete();

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                foreach (
                    AmenitiesStructure::where('parent', $key)
                    ->whereIn('id', $value)
                    ->get() as $structure
                ) {
                    $amenity = new Amenities();
                    $amenity->unit_id = $unit_id;
                    $amenity->amenities_structure_id = $structure->id;
                    $amenity->value = 'checked';
                    $amenity->save();
                }

            } else {
                $temp = intval($value);

                if ($temp === 0) {
                    $amenity = new Amenities();
                    $amenity->unit_id = $unit_id;
                    $amenity->amenities_structure_id = $key;
                    $amenity->value = $value ?? '';
                    $amenity->save();
                } else {
                    if (
                        AmenitiesStructure::where(
                            [
                                'id' => $value,
                                'parent' => $key,
                            ]
                        )->count() > 0
                    ) {
                        $amenity = new Amenities();
                        $amenity->unit_id = $unit_id;
                        $amenity->amenities_structure_id = $value;
                        $amenity->value = 'checked';
                        $amenity->save();
                    } else {
                        $amenity = new Amenities();
                        $amenity->unit_id = $unit_id;
                        $amenity->amenities_structure_id = $key;
                        $amenity->value = $value ?? '';
                        $amenity->save();
                    }
                }
            }
        }
    }
}
