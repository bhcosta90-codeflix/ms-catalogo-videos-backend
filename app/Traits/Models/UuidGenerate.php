<?php

namespace App\Traits\Models;

use Illuminate\Support\Str;

trait UuidGenerate
{
    protected static function bootUuidGenerate()
    {
        static::creating(function ($model) {
            if (!$model->getKey()) {
                $field = $model->getKeyName();

                if (method_exists($model, 'getFieldUuid')) {
                    $field = $model->getFieldUuid();
                }

                if (empty($model->{$field})) {
                    $model->{$field} = (string) Str::uuid();
                }
            }
        });
    }

    /**
     * @return bool
     */
    private function verify(): bool
    {
        $field = $this->getKeyName();
        if (method_exists($this, 'getFieldUuid')) {
            $field = $this->getFieldUuid();
        }
        return (bool) $field == $this->getKeyName();
    }

    public function getIncrementing()
    {
        return !$this->verify();
    }

    public function getKeyType()
    {
        return $this->verify() ? 'string' : 'integer';
    }
}
