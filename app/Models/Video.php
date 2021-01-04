<?php

namespace App\Models;

use App\ModelFilters\VideoFilter;
use App\Traits\Models\SerializeDateToIso8001;
use App\Traits\Models\UploadFile;
use App\Traits\Models\UuidGenerate;
use Chelout\RelationshipEvents\Concerns\HasBelongsToManyEvents;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class Video extends Model
{
    use HasFactory, Filterable, SoftDeletes, UuidGenerate, UploadFile, SerializeDateToIso8001, HasBelongsToManyEvents;

    const THUMB_FILE_MAX_SIZE = 1024 * 5; #5MB
    const BANNER_FILE_MAX_SIZE = 1024 * 10; #10MB
    const TRAILLER_FILE_MAX_SIZE = 1024 * 1024 * 1; #1GB
    const VIDEO_FILE_MAX_SIZE = 1024 * 1024 * 50; #50GB

    const RATING_LIST = ['L', '10', '12', '14', '16', '18'];

    protected $observables = [
        'belongsToManyAttached',
        'belongsToManyDetached',
    ];

    protected $fillable = [
        'title',
        'description',
        'year_launched',
        'opened',
        'rating',
        'duration',
        'file_thumb',
        'file_banner',
        'file_trailer',
        'file_video',
    ];

    protected $casts = [
        "year_launched" => "integer",
        "opened" => "boolean",
        "duration" => "integer",
    ];

    public static function create(array $attributes = [])
    {
        $files = (new self)->extractFiles($attributes);

        try {
            DB::beginTransaction();
            /** @var Video */
            $obj = static::query()->create($attributes);

            if ($obj) {
                $obj->handleRelations($attributes);
                $obj->uploadFiles($files);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($obj)) {
                $obj->removeFiles($files);
            }
            throw $e;
        }

        return $obj;
    }

    /**
     * @param array $attributes
     * @param array $options
     *
     * @return bool
     */
    public function update(array $attributes = [], array $options = []): bool
    {
        $files = self::extractFiles($attributes);

        try {
            DB::beginTransaction();
            $saved = parent::update($attributes, $options);
            $this->handleRelations($attributes);

            if ($saved) {
                $this->uploadFiles($files);
            }

            DB::commit();

            if($saved && count($files)){
                $this->removeOldFiles();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($saved)) {
                $this->removeFiles($files);
            }
            throw $e;
        }

        return (bool) $saved;
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class)->withTrashed();
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class)->withTrashed();
    }

    public function castMembers()
    {
        return $this->belongsToMany(CastMember::class)->withTrashed();
    }

    /**
     * @param array $attributes
     *
     * @return void
     */
    public function handleRelations(array $attributes = []): void
    {
        if (isset($attributes['categories'])) {
            $this->categories()->sync($attributes['categories']);
        }

        if (isset($attributes['genres'])) {
            $this->genres()->sync($attributes['genres']);
        }

        if (isset($attributes['cast_members'])) {
            $this->castMembers()->sync($attributes['cast_members']);
        }
    }

    public function getUrlFileThumbAttribute()
    {
        return $this->getFileUrl($this->file_thumb);
    }

    public function getUrlFileBannerAttribute()
    {
        return $this->getFileUrl($this->file_banner);
    }

    public function getUrlFileTrailerAttribute()
    {
        return $this->getFileUrl($this->file_trailer);
    }

    public function getUrlFileVideoAttribute()
    {
        return $this->getFileUrl($this->file_video);
    }

    public function modelFilter()
    {
        return $this->provideFilter(VideoFilter::class);
    }

    public function getFieldFiles(): array
    {
        return [
            'file_thumb',
            'file_banner',
            'file_trailer',
            'file_video',
        ];
    }

    /**
     * @param array $attributes
     *
     * @return array
     */
    public function extractFiles(array &$attributes = []): array
    {
        $files = [];

        foreach ($this->getFieldFiles() as $file) {
            if (isset($attributes[$file]) && $attributes[$file] instanceof UploadedFile) {
                $files[] = $attributes[$file];
                $attributes[$file] = $attributes[$file]->hashName();
            }
        }

        return $files;
    }

    protected function uploadDir()
    {
        return $this->id;
    }
}
