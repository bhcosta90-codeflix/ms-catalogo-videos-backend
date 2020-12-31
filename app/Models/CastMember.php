<?php

namespace App\Models;

use App\Traits\Models\SerializeDateToIso8001;
use App\Traits\Models\UuidGenerate;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CastMember extends Model
{
    use HasFactory, Filterable, SoftDeletes, UuidGenerate, SerializeDateToIso8001;

    const TYPE_DIRECTOR = 1;
    const TYPE_ACTOR = 2;
    const TYPES_OPTIONS = [
        self::TYPE_DIRECTOR => 'Director',
        self::TYPE_ACTOR => 'Actor',
    ];

    protected $fillable = [
        'name',
        'type',
    ];

    protected $casts = [
        'type' => 'integer'
    ];
}
