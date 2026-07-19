<?php
namespace Steps\WsLoginHook\Contracts;

use Illuminate\Http\JsonResponse;

interface WsLoginHook
{
    public static function loggedInResponse($phoneData): JsonResponse;
}
