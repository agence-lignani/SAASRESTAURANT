<?php

namespace App\Filament\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToCurrentRestaurant
{
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('restaurant_id', app('filament.restaurant')->id);
    }
}
