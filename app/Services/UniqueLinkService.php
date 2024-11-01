<?php


namespace App\Services;


use App\Services\Contracts\UniqueLinkServiceInterface;

class UniqueLinkService implements UniqueLinkServiceInterface
{
    public static function build($model) {
        return preg_replace(
            "/[^A-Za-z0-9|^\-]/",
            '',
            strtolower(
                str_replace(
                    " ",
                    "-",
                    (!empty($model->property) ? $model->property->full_address : "") . "-" . $model->id
                )
            )
        );
    }
}
