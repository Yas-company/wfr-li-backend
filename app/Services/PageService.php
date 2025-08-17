<?php

namespace App\Services;

use App\Models\Page;

class PageService
{
    public function getPageBySlug($slug)
    {
        return Page::where('slug', $slug)->isActive()->firstOrFail();
    }
}