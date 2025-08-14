<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AirpayService;

class AirpayController extends Controller
{
    protected $airpayService;

    public function __construct(AirpayService $airpayService)
    {
        $this->airpayService = $airpayService;
    }

    public function showPaymentForm()
    {
        return view('airpay.payment-form');
    }

    public function processPayment(Request $request)
    {
        $request->validate([
            'buyerEmail' => 'required|email|max:50',
            'buyerPhone' => 'required|regex:/^[0-9\-\s]{8,15}$/',
            'buyerFirstName' => 'required|regex:/^[a-zA-Z\s\d]{1,50}$/',
            'buyerLastName' => 'required|regex:/^[a-zA-Z\s\d]{1,50}$/',
            'buyerAddress' => 'required|max:255',
            'amount' => 'required|regex:/^\d{1,6}\.\d{2}$/',
            'orderid' => 'required|alpha_num|max:20'
        ]);

        try {
            $redirectData = $this->airpayService->initiatePayment($request->all());
            return view('airpay.redirect', compact('redirectData'));
        } catch (\Exception $e) {
            return view('airpay.payment-form', ['errors' => [$e->getMessage()]]);
        }
    }

    public function handleResponse(Request $request)
    {
        try {
            $response = $this->airpayService->handlePaymentResponse($request->all());
            return view('airpay.response', compact('response'));
        } catch (\Exception $e) {
            return view('airpay.response', ['error' => $e->getMessage()]);
        }
    }
}