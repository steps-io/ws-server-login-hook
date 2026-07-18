<?php
namespace  Steps\WsLoginHook\Events;

class WsHookEvent extends Event
{
    public function __construct(public $otpRequest,public $user)
    {
        //
    }
}
