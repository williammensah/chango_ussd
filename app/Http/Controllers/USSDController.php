<?php

namespace App\Http\Controllers;

use Cache;
use App\MenuContent;

abstract class USSDController extends Controller
{
    use MenuContent;
    protected $menuName;
    public function index()
    { }
    public function setNextScreen($nextScreen, $session)
    {
        return Cache::put(request()->sessionId, json_encode(array_merge($session, ['next_screen' => $nextScreen])));
    }
}
