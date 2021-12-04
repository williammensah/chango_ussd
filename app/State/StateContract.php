<?php

namespace App\State;

use Cache;

class StateContract
{
    protected $stateKey;
    public function setState(string $currentMenu, array $requestData = [], $flow = '')
    {
        $state = ['current_menu' => $currentMenu, 'data' => $requestData, 'flow' => $flow];
        Cache::put($this->stateKey, json_encode($state), 600);
    }
    public function getState()
    {
        return json_decode(Cache::get($this->stateKey), true);
    }
    public function clearState()
    {
        Cache::forget($this->stateKey);
    }
}
