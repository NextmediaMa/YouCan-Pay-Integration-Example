<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use YouCan\Pay\YouCanPay;

class PayController extends Controller
{
    private YouCanPay $youCanPay;

    public function __construct()
    {
        YouCanPay::setIsSandboxMode(true);

        $this->youCanPay = YouCanPay::instance()->useKeys(config('ycpay.private_key'), config('ycpay.public_key'));
    }

    public function widget(Request $request)
    {
        $orderId = uniqid();

        $request->session()->put('orderId', $orderId);

        $token = $this->youCanPay->token->create($orderId, "2000", "USD", $request->ip()); //20 USD

        return view('integrations.widget', [
            'token' => $token->getId()
        ]);
    }

    public function standalone(Request $request)
    {
        $orderId = uniqid();

        $request->session()->put('orderId', $orderId);

        $token = $this->youCanPay->token->create(
            $orderId,
            "2000",
            "USD",
            $request->ip(),
            route('callback', ['success' => true]),
            route('callback', ['success' => false])
        ); //20 USD

        return view('integrations.standalone', [
            'paymentUrl' => $token->getPaymentURL()
        ]);
    }

    public function callback(Request $request)
    {
        if ($request->query('success')) {
            if ($this->validatePayment($request->query('transaction_id'))) {
                return 'Payment was successful.';
            }

            return 'Payment could not be verified.';
        }

        return sprintf("Error: %s", $request->query('message'));
    }

    public function verify(Request $request)
    {
        $transactionId = $request->get('transactionId');

        if ($this->validatePayment($transactionId)) {
            return 'Payment was successful.';
        }

        return 'Payment could not be verified.';
    }

    private function validatePayment(string $transactionId): bool
    {
        $orderId = $this->youCanPay->transaction->get($transactionId)?->getOrderId();

        if (session()->get("orderId") === $orderId) {
            return true;
        }

        return false;
    }
}
