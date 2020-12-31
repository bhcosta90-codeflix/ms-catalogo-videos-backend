<?php

namespace App\Traits\Models;

use Arr;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Storage;

trait UploadFile
{
    public $oldFiles = [];

    public static function bootUploadFile()
    {
        static::updating(function(Model $obj){
            $fildsUpdated = array_keys($obj->getDirty());
            $filesUpdated = array_intersect($fildsUpdated, $obj->getFieldFiles());
            $filesFiltered = Arr::where($filesUpdated, function($fileField) use($obj){
                return $obj->getOriginal($fileField);
            });
            $obj->oldFiles = array_map(function($fileField) use($obj) {
                return $obj->getOriginal($fileField);
            }, $filesFiltered);
        });

        static::deleted(function(Model $obj){
            $files = [];
            foreach($obj->getFieldFiles() as $field) {
                if(!empty($obj->$field) && Storage::exists($obj->$field)){
                    $files[] = $obj->$field;
                }
            }
            $obj->removeFiles($files);
        });
    }

    public abstract function getFieldFiles(): array;

    protected abstract function uploadDir();

    public function removeOldFiles()
    {
        $this->removeFiles($this->oldFiles);
    }

    /**
     * @param UploadedFile[] $files
     *
     * @return void
     */
    public function uploadFiles(array $files): void
    {
        foreach($files as $file)
        {
            $this->uploadFile($file);
        }
    }

    /**
     * @param UploadedFile $file
     *
     * @return void
     */
    public function uploadFile(UploadedFile $file): void
    {
        $file->store($this->uploadDir());
    }

    /**
     * @param string[]|UploadedFile[] $files
     *
     * @return void
     */
    public function removeFiles(array $files): void
    {
        foreach($files as $file)
        {
            $this->removeFile($file);
        }
    }

    /**
     * @param string|UploadedFile $file
     *
     * @return void
     */
    public function removeFile($file): void
    {
        $filename = $file instanceof UploadedFile ? $file->hashName() : $file;
        Storage::delete("{$this->uploadDir()}/{$filename}");
        try {
            $arrayFilename = explode('.', $filename);
            array_pop($arrayFilename);
            $nameDirectory = implode('.', $arrayFilename);
            Storage::deleteDirectory("converter/{$this->uploadDir()}/{$nameDirectory}");
        } catch(\Exception $e) {

        }
    }

    /**
     * @param string $filename
     *
     * @return string|null
     */
    public function relativeFilePath(?string $filename): ?string
    {
        if($filename){
            return "{$this->uploadDir()}/{$filename}";
        }

        return null;
    }

    /**
     * @param string $filename
     *
     * @return string|null
     */
    public function getFileUrl(?string $filename): ?string
    {
        if($filename){
            $link = Storage::url($this->relativeFilePath($filename));
            return $link;
        }

        return null;
    }
}
