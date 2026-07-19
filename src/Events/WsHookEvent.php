<?php
namespace Steps\WsLoginHook\Events;


class WsHookEvent
{
    public function __construct(public $otpRequest, public $user)
    {
        //
    }
}
