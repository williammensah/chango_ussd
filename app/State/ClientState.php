<?php

namespace App\State;

use Cache;

class ClientState
{
    protected $stateKey;

    public function __construct()
    {
        $this->stateKey = request()->sessionId . request()->msisdn;
    }
    /**
     * @input : currentMenu, requestData
     * The state is set for the next request
     */
    public function setState(string $currentMenu, array $requestData = [], $flow = '')
    {
        $state = ['current_menu' => $currentMenu, 'data' => $requestData, 'flow' => $flow];
        
        Cache::put($this->stateKey, json_encode($state), 600);
        return $this;
    }

    public function getState()
    {
        return json_decode(Cache::get($this->stateKey), true);
    }

    public function clearState()
    {
        return Cache::forget($this->stateKey);
    }
}
