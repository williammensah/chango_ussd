<?php

namespace App\State;

use Cache;

class UserState
{
    protected $stateKey;

    public function __construct()
    {
        $this->stateKey = 'user.' . request()->sessionId . request()->msisdn;
    }
    public function store(array $input = [])
    {
        $data = Cache::has($this->stateKey) ? json_decode(Cache::get($this->stateKey), true) : [];
        $updatedData = json_encode(array_merge($data, $input));
        Cache::put($this->stateKey, $updatedData, 600);
        return $this;
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
