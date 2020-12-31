<?php

namespace App\Models;

use App\ModelFilters\CastMemberFilter;
use App\Traits\Models\SerializeDateToIso8001;
use App\Traits\Models\UuidGenerate;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, Filterable, SoftDeletes, UuidGenerate, SerializeDateToIso8001;

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function modelFilter()
    {
        return $this->provideFilter(CastMemberFilter::class);
    }
}
