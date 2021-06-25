<?php

namespace App\Http\Controllers;

use App\Jobs\ImportUsersData;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function processFile()
    {
    	ImportUsersData::dispatch();

    	return 'success';
    }
}
