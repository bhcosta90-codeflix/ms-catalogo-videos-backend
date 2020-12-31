<?php 

namespace App\ModelFilters;

class VideoFilter extends DefaultModelFilter
{
    protected $sortable = ['title', 'is_active', 'created_at'];

    public function search($search)
    {
        $this->where('title', 'LIKE', "%$search%");
    }
}
