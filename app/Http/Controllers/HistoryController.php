<?php

namespace App\Http\Controllers;

use App\Services\GlobalsService;
use App\Services\UniqueHistoryService;

class HistoryController extends Controller
{
    public function uniques()
    {
        abort_unless(UniqueHistoryService::isAvailable(), 404);

        return view('template::history.uniques');
    }

    public function globals()
    {
        abort_unless(GlobalsService::isAvailable(), 404);

        return view('template::history.globals');
    }
}
