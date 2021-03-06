<?php

namespace App\Models;

use App\ModelFilters\GenreFilter;
use App\Traits\Models\SerializeDateToIso8001;
use App\Traits\Models\UuidGenerate;
use Chelout\RelationshipEvents\Concerns\HasBelongsToManyEvents;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Genre extends Model
{
    use HasFactory, Filterable, SoftDeletes, UuidGenerate, SerializeDateToIso8001, HasBelongsToManyEvents;

    protected $fillable = [
        'name',
        'is_active'
    ];

    protected $observables = [
        'belongsToManyAttached',
        'belongsToManyDetached',
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
