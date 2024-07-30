<?php

//namespace App\Helpers;

use Illuminate\Database\Eloquent\ModelNotFoundException;

if (!function_exists('getResponse')) {
    function getResponse($data, $token, $message, $status): array
    {
        $responseResults = [
            'data' => $data,
            'token' => $token,
            'message' => $message,
            'status' => $status,
        ];
        return $responseResults;
    }
}

if (!function_exists('getResponseIfValidationFailed')) {
    function getResponseIfValidationFailed($data, $token, $message, $status): array
    {
        $responseResults = [
            'errors' => $data,
            'token' => $token,
            'message' => $message,
            'status' => $status,
        ];
        return $responseResults;
    }
}

function getJsonArray($value)
{
    $array = explode(',', $value);
    $tagsArray = [];

    foreach ($array as $index => $tag) {
        $tagsArray[$index + 1] = $tag;
    }

    return json_encode($tagsArray, JSON_UNESCAPED_SLASHES);
}

function _userCannot(string|array ...$permissions): bool
{
    $permissions = Arr::flatten($permissions);

    return !Auth::user()->can($permissions);
}
function _permissionErrorMessage(): string
{
    return __('You don`t have permission to perform this task.');
}
