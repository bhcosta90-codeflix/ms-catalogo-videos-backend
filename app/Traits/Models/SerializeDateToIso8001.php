<?php


namespace App\Traits\Models;

trait SerializeDateToIso8001
{
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format(\DateTime::ISO8601);
    }

}
