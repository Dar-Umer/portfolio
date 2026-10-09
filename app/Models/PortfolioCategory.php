<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PortfolioCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
    ];

    public function projects(): HasMany
    {
        return $this->hasMany(PortfolioProject::class);
    }
}
