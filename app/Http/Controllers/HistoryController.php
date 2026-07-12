<?php

namespace App\Http\Controllers;

use App\Models\Setting;

class HistoryController extends Controller
{
    public function uniques()
    {
        abort_unless(
            config('silkpanel.version') === 'isro' && (bool) Setting::get('history_unique_enabled', true),
            404
        );

        return view('template::history.uniques');
    }

    public function globals()
    {
        abort_unless(
            config('silkpanel.version') === 'isro' && (bool) Setting::get('history_global_enabled', true),
            404
        );

        return view('template::history.globals');
    }
}
