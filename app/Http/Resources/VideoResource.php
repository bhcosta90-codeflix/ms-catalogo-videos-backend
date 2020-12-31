<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VideoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request) + [
                'url_file_thumb' => $this->url_file_thumb,
                'url_file_banner' => $this->url_file_banner,
                'url_file_trailer' => $this->url_file_trailer,
                'url_file_video' => $this->url_file_video,
                'categories' => CategoryResource::collection($this->categories),
                'genres' => GenreResource::collection($this->genres),
                'cast_members' => CastMemberResource::collection($this->castMembers),
            ];
    }
}
