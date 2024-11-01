<?php

use Illuminate\Database\Seeder;
use \App\Models\Unit;
use App\Services\UniqueLinkService;

class AddUniqueLinkForUnit extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $units = Unit::get();
        foreach ($units as $unit) {
            $linkData = [
                'model_id' => $unit->id,
                'model_type' => self::class,
                'link' => UniqueLinkService::build($unit)
            ];

            if($unit->link) $unit->link()->update($linkData);
            $unit->link()->create($linkData);
        }
    }
}
