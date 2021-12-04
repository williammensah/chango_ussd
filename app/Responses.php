<?php

namespace App;

/**
 * Responses
 */
trait Responses
{
    public function response(string $menuContent, string $currentMenu = "", string $requestType = 'EXISTING'): array
    {
        return [
            'msisdn' => request()->msisdn,
            'menuContent' => $menuContent,
            'requestType' => $requestType,
            'sessionId' => request()->sessionId,
            'currentMenu' => $currentMenu ?? 'ROOT',
            // 'operator' => request()->operator,
            // 'userInput' => request()->userInput,
            // 'shortCode' => request()->shortCode,
        ];
    }
    public function endSession(string $menuContent = "", string $currentMenu = ""): array
    {
        return [
            'msisdn' => request()->msisdn,
            'requestType' => 'CLEANUP',
            'sessionId' => request()->sessionId,
            'currentMenu' => $currentMenu ?? 'ROOT',
            'menuContent' => $menuContent,
        ];
    }
}
