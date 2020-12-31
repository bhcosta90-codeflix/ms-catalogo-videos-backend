<?php

namespace App\Models;

use App\ModelFilters\GenreFilter;
use App\Traits\Models\UuidGenerate;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Genre extends Model
{
    use HasFactory, Filterable, SoftDeletes, UuidGenerate;

    protected $fillable = [
        'name',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function modelFilter()
    {
        return $this->provideFilter(GenreFilter::class);
    }

}
