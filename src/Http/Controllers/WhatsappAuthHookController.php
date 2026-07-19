<?php

namespace Steps\WsLoginHook\Http\Controllers;

use Illuminate\Http\Request;
use Steps\WsLoginHook\Events\OtpLoginStatusUpdated;
use Steps\WsLoginHook\Http\Requests\WsLoginHookLoginRequest;
use Steps\WsLoginHook\Services\WhatsappAuthHandler;
use Steps\WsLoginHook\Events\WsHookEvent;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Event;

class WhatsappAuthHookController extends Controller
{

    public function __construct(protected WhatsappAuthHandler $whatsappAuthHandler)
    {
    }

    public function wsWebhook(Request $request)
    {
        $parsed = $this->whatsappAuthHandler->handleWsWebhook($request);
        $otpRequest = $this->whatsappAuthHandler->verifyOtp($parsed['message']);
        
        if (! $otpRequest) {
            return response()->json(['status' => 'error', 'message' => 'Invalid OTP'], 422);
        }
        
        if ($otpRequest->is_expired) {
            broadcast(new OtpLoginStatusUpdated($otpRequest->serial_number, 'failed', __('OTP expired')));

            return response()->json(['status' => 'error', 'message' => 'OTP expired'], 422);
        }
        
        $handleMobileNumber = $this->whatsappAuthHandler->validatePhoneNumber($parsed['phone_number']);

        if (! $handleMobileNumber['success']) {
            broadcast(new OtpLoginStatusUpdated($otpRequest->serial_number, 'failed', __('Invalid phone number')));

            return response()->json(['status' => 'error', 'message' => 'Invalid phone number'], 422);
        }
        
        $otpRequest->update([
            'mobile' => $handleMobileNumber['phone_number'],
            'profile_name' => $parsed['profile_name'],
        ]);

        Event::dispatch(new WsHookEvent($otpRequest, $otpRequest->modelable));
        $message = $this->whatsappAuthHandler->getMessage($otpRequest->locale);
        $this->whatsappAuthHandler->sendWsMessage($message, $handleMobileNumber['phone_number']);

        broadcast(new OtpLoginStatusUpdated($otpRequest->serial_number, 'success'));

        return response()->json(['status' => 'success'], 200);
    }

    public function requestOtp(Request $request, $model)
    {
        $otp = $this->whatsappAuthHandler->generateOtp($request->serial_number, $model);
        return response()->json(['status' => 'success', 'data' => $otp], 200);
    }

    public function login(WsLoginHookLoginRequest $request)
    {
        $requestStatus = $this->whatsappAuthHandler->getRequestStatus($request->request_id, $request->serial_number);

        if (! $requestStatus) {
            return response()->json(['status' => 'error', 'message' => 'Request not found'], 422);
        }

        if ($requestStatus->status == 'pending') {
            return response()->json(['status' => 'error', 'message' => 'Request is pending'], 422);
        }

        if ($requestStatus->is_expired) {
            return response()->json(['status' => 'error', 'message' => 'OTP expired'], 422);
        }

        $handleMobileNumber = $this->whatsappAuthHandler->validatePhoneNumber($requestStatus->mobile);
        $handleMobileNumber['profile_name'] = $requestStatus->profile_name;
        $requestStatus->delete();

        return $requestStatus->model_type::loggedInResponse($handleMobileNumber);
    }
}
