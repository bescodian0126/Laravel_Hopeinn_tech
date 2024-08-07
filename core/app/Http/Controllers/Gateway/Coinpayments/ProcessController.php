<?php

namespace App\Http\Controllers\Gateway\Coinpayments;

use App\Constants\Status;
use App\Models\Deposit;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\Coinpayments\CoinPaymentHosted;
use App\Http\Controllers\Gateway\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProcessController extends Controller
{
    /*
     * CoinPaymentHosted Gateway
     */

    public static function process($deposit)
    {

        $coinPayAcc = json_decode($deposit->gatewayCurrency()->gateway_parameter);
        
        static $result = [];

        if ($deposit->btc_amo == 0 || $deposit->btc_wallet == "") {
            try {
                $cps = new CoinPaymentHosted();
            } catch (\Exception $e) {
                $send['error'] = true;
                $send['message'] = $e->getMessage();
                return json_encode($send);
            }

            $cps->Setup($coinPayAcc->private_key, $coinPayAcc->public_key);
            // print_r($cps); exit;
            $callbackUrl = route('ipn.'.$deposit->gateway->alias);

            $req = array(
                'amount' => $deposit->final_amo,
                'currency1' => 'USD',
                'currency2' => $deposit->method_currency,
                'custom' => $deposit->trx,
                'buyer_email' => auth()->user()->email,
                'ipn_url' => $callbackUrl,
            );

            $result = $cps->CreateTransaction($req);
            if ($result['error'] == 'ok') {
                $bcoin = sprintf('%.08f', $result['result']['amount']);
                $sendadd = $result['result']['address'];
                $deposit['btc_amo'] = $bcoin;
                $deposit['btc_wallet'] = $sendadd;
                $deposit['qr_code_img'] = $result['result']['qrcode_url'];
                $deposit->update();
                
            } else {
                $send['error'] = true;
                $send['message'] = $result['error'];
            }
        }

        $send['amount'] = $deposit->btc_amo;
        $send['sendto'] = $deposit->btc_wallet;
        // $send['img'] = cryptoQR($deposit->btc_wallet);
        $send['img'] = $deposit->qr_code_img;
        $send['currency'] = "$deposit->method_currency";
        $send['view'] = 'user.payment.crypto';
        return json_encode($send);
    }


    public function ipn(Request $request)
    {
        try {
            // Debugging log to check request data
            Log::info('IPN Request Received', $request->all());
    
            $track = $request->custom;
            $status = $request->status;
            $amount2 = floatval($request->amount2);
            $deposit = Deposit::where('trx', $track)->orderBy('id', 'DESC')->first();
    
            if ($status >= 100 || $status == 2) {
                $coinPayAcc = json_decode($deposit->gatewayCurrency()->gateway_parameter);
    
                // Debugging log to check details
                Log::info('Deposit and CoinPay details', [
                    'deposit_method_currency' => $deposit->method_currency,
                    'request_currency2' => $request->currency2,
                    'deposit_btc_amo' => $deposit->btc_amo,
                    'amount2' => $amount2,
                    'merchant_id' => $coinPayAcc->merchant_id,
                    'request_merchant' => $request->merchant,
                    'deposit_status' => $deposit->status,
                ]);
    
                if ($deposit->method_currency == $request->currency2 && $deposit->btc_amo <= $amount2 && $coinPayAcc->merchant_id == $request->merchant && $deposit->status == Status::PAYMENT_INITIATE) {
                    Log::info('Updating user data', ['deposit_id' => $deposit->id]);
                    PaymentController::userDataUpdate($deposit);
                }
            }
        } catch (\Exception $e) {
            // Log any exceptions
            Log::error('Error in IPN processing', ['exception' => $e->getMessage()]);
        }
    }

}
