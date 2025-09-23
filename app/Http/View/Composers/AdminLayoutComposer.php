<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class AdminLayoutComposer
{
    public function compose(View $view)
    {
        $logo = DB::table('logo_dan_favicon')->first();
        $view->with('logo', $logo);
    }
}
