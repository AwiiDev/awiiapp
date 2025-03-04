<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Web\BaseController;
use Illuminate\Http\Request;
use App\Models\User;
use App\Base\Constants\Masters\WalletRemarks;
use App\Models\Payment\UserWallet;
use App\Models\Payment\DriverWallet;
use App\Models\Payment\OwnerWallet;
use App\Models\Payment\OwnerWalletHistory;
use App\Models\Payment\UserWalletHistory;
use App\Models\Payment\DriverWalletHistory;
use App\Models\Admin\Subscription;
use App\Models\Admin\SubscriptionDetail;
use App\Base\Constants\Masters\PushEnums;
use App\Jobs\Notifications\SendPushNotification;
use Kreait\Firebase\Contract\Database;
use App\Base\Constants\Auth\Role;
use Carbon\Carbon;
use App\Models\Request\Request as RequestModel;

class MercadopagoController extends BaseController
{
    protected $database;


    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function mercadepago(Request $request)
    {


        $environment =  get_payment_settings('mercadopago_environment');

        if ($environment=="test") {
            $public_key =  get_payment_settings('mercadopago_test_public_key');
            $token =  get_payment_settings('mercadopago_test_access_token');
        }else{
            $public_key =  get_payment_settings('mercadopago_live_public_key');
            $token =  get_payment_settings('mercadopago_live_access_token');
        }

        $plan_id = request()->plan_id;
// dd($token);
        $user = User::find(request()->input('user_id'));

        // Extract parameters from the request
        $amount = $request->input('amount');
        $name = $user->name ?? 'misoftwares';
        $email = $user->email ?? 'support@misoftwares.com';
        $mobile = $user->mobile ?? '7871917871';
        $currency = $user->countryDetail->currency_code ?? "INR";

        $payment_for = $request->input('payment_for');
        $request_id = $request->input('request_id') ?? "test";

        if($payment_for == 'subscription'){
            $driver = $user->driver;
            if(!$driver){
                $this->throwAuthorizationException();
            }

            if($driver->is_subscribed){
                $this->throwCustomException('Driver already subscribed');
            }

            $vehicle_types = $driver->driverVehicleTypeDetail->pluck('vehicle_type');

            $plan = Subscription::active()->where('id',$plan_id)->whereIn('vehicle_type_id',$vehicle_types)->first();

            if(!$plan){
                $this->throwCustomException('Subscription is not Valid or Incorrect');
            }
        }

        // Ensure that the order ID is passed correctly to the view
        return view('mercadopago.checkout', compact('public_key', 'payment_for', 'request_id', 'user', 'amount', 'currency','token'))->render();
    }


    public function mercadopagoCheckout(Request $request){

        // dd($request->all());

        $exploded_reference = explode('----', $request->external_reference);
// dd($exploded_reference);
        $request_id = $exploded_reference[4];
        $web_booking_value = 0;
        $payment_for = $exploded_reference[2];
        $user_id = $exploded_reference[1];
        $user = User::find($user_id);
        $amount = $exploded_reference[3];

    if ($payment_for=="wallet") {

        $amount = $exploded_reference[3];

        if ($user->hasRole('user')) {
        $wallet_model = new UserWallet();
        $wallet_add_history_model = new UserWalletHistory();
        } elseif($user->hasRole('driver')) {
                    $wallet_model = new DriverWallet();
                    $wallet_add_history_model = new DriverWalletHistory();
                    $user_id = $user->driver->id;
        }else {
                    $wallet_model = new OwnerWallet();
                    $wallet_add_history_model = new OwnerWalletHistory();
                    $user_id = $user->owner->id;
        }

        $user_wallet = $wallet_model::firstOrCreate([
            'user_id'=>$user_id]);
        $user_wallet->amount_added += $amount;
        $user_wallet->amount_balance += $amount;
        $user_wallet->save();
        $user_wallet->fresh();

        $wallet_add_history_model::create([
            'user_id'=>$user_id,
            'amount'=>$amount,
            'transaction_id'=>$request->payment_id,
            'remarks'=>WalletRemarks::MONEY_DEPOSITED_TO_E_WALLET,
            'is_credit'=>true]);


                $pus_request_detail = json_encode($request->all());

                // $title = custom_trans('amount_credited_to_your_wallet_title',[],$user->lang);
                // $body = custom_trans('amount_credited_to_your_wallet_body',[],$user->lang);

                // dispatch(new NotifyViaMqtt('add_money_to_wallet_status'.$user_id, json_encode($socket_data), $user_id));

                // dispatch(new SendPushNotification($user,$title,$body));

                 $notification = \DB::table('notification_channels')
                            ->where('topics', 'User Wallet Amount') // Match the correct topic
                            ->first();

                        //    send push notification 
                            if ($notification && $notification->push_notification == 1) {
                                 // Determine the user's language or default to 'en'
                                $userLang = $user->lang ?? 'en';
                                // dd($userLang);
                
                                // Fetch the translation based on user language or fall back to 'en'
                                $translation = \DB::table('notification_channels_translations')
                                    ->where('notification_channel_id', $notification->id)
                                    ->where('locale', $userLang)
                                    ->first();
                
                                // If no translation exists, fetch the default language (English)
                                if (!$translation) {
                                    $translation = \DB::table('notification_channels_translations')
                                        ->where('notification_channel_id', $notification->id)
                                        ->where('locale', 'en')
                                        ->first();
                                }            
                                
                                $title =  $translation->push_title ?? $notification->push_title;
                                $body = strip_tags($translation->push_body ?? $notification->push_body);
                                dispatch(new SendPushNotification($user, $title, $body));
                            }

            } elseif ($payment_for == 'subscription') {
                $plan_id = $request->input('plan_id');
                $plan = Subscription::find($plan_id);

                $user = User::find($user_id);
                $params['transaction_id'] = str_random(6);
                $driver_wallet = $user->driver->DriverWallet;
                $driver_wallet->amount_spent += $amount;
                $driver_wallet->save();

                $user->driver->driverWalletHistory()->create([
                    'amount'=>$amount,
                    'transaction_id'=>str_random(6),
                    'remarks'=>WalletRemarks::SUBSCRIPTION_FEE,
                    'is_credit'=>false,
                ]);

                $driver = $user->driver;

                $expire_at = Carbon::parse(now())->addDay($plan->subscription_duration)->toDateString();
                $params = [
                    'driver_id' => $driver->id,
                    'subscription_id' => $plan_id,
                    'amount' => $amount,
                    'payment_opt' => 0,
                    'expired_at' => $expire_at,
                ];
                $params['transaction_id'] = str_random(6);
                $params['subscription_type'] = 1;
                $subscription = SubscriptionDetail::create($params);
                $driver->update([
                    'is_subscribed' => true,
                    'subscription_detail_id' => $subscription->id,
                ]);

                $result =  fractal($driver_wallet, new DriverWalletTransformer);

                // $title = custom_trans('subscription_title', [], $user->lang);
                // $body = custom_trans('subscription_body', [], $user->lang);
                

                // dispatch(new SendPushNotification($user,$title,$body));

                $notification = \DB::table('notification_channels')
                            ->where('topics', 'User Subscription') // Match the correct topic
                            ->first();

                        //    send push notification 
                            if ($notification && $notification->push_notification == 1) {
                                 // Determine the user's language or default to 'en'
                                $userLang = $user->lang ?? 'en';
                                // dd($userLang);
                
                                // Fetch the translation based on user language or fall back to 'en'
                                $translation = \DB::table('notification_channels_translations')
                                    ->where('notification_channel_id', $notification->id)
                                    ->where('locale', $userLang)
                                    ->first();
                
                                // If no translation exists, fetch the default language (English)
                                if (!$translation) {
                                    $translation = \DB::table('notification_channels_translations')
                                        ->where('notification_channel_id', $notification->id)
                                        ->where('locale', 'en')
                                        ->first();
                                }            
                                
                                $title =  $translation->push_title ?? $notification->push_title;
                                $body = strip_tags($translation->push_body ?? $notification->push_body);
                                dispatch(new SendPushNotification($user, $title, $body));
                            }
            }else{

                $request_id =  $exploded_reference[4];
                // Log::info($request_id);
                 $request_detail = RequestModel::where('id', $request_id)->first();

                
                $web_booking_value = $request_detail->web_booking;


                $request_detail->update(['is_paid' => true]);
                if(!$request_detail->is_parcel)
                {

                $driver_commission = $request_detail->requestBill->driver_commision;

                    $wallet_model = new DriverWallet();
                    $wallet_add_history_model = new DriverWalletHistory();
                    $user_id = $request_detail->driver_id;
                    /*wallet Modal*/
                    $user_wallet = $wallet_model::firstOrCreate([
                    'user_id'=>$user_id]);
                    $user_wallet->amount_added += $amount;
                    $user_wallet->amount_balance += $amount;
                    $user_wallet->save();
                    $user_wallet->fresh();
                    /*wallet history*/
                    $wallet_add_history_model::create([
                    'user_id'=>$user_id,
                    'amount'=>$amount,
                    'transaction_id'=>$request->PayerID,
                    'remarks'=>WalletRemarks::MONEY_DEPOSITED_TO_E_WALLET,
                    'is_credit'=>true]);


                    // $title = custom_trans('amount_credited_to_your_wallet_title');
                    // $body = custom_trans('amount_credited_to_your_wallet_body');

                    // dispatch(new SendPushNotification($request_detail->driverDetail->user,$title,$body));

                    $notification = \DB::table('notification_channels')
                            ->where('topics', 'User Wallet Amount') // Match the correct topic
                            ->first();

                        //    send push notification 
                            if ($notification && $notification->push_notification == 1) {
                                 // Determine the user's language or default to 'en'
                                $userLang = $request_detail->driverDetail->user->lang ?? 'en';
                                // dd($userLang);
                
                                // Fetch the translation based on user language or fall back to 'en'
                                $translation = \DB::table('notification_channels_translations')
                                    ->where('notification_channel_id', $notification->id)
                                    ->where('locale', $userLang)
                                    ->first();
                
                                // If no translation exists, fetch the default language (English)
                                if (!$translation) {
                                    $translation = \DB::table('notification_channels_translations')
                                        ->where('notification_channel_id', $notification->id)
                                        ->where('locale', 'en')
                                        ->first();
                                }            
                                
                                $title =  $translation->push_title ?? $notification->push_title;
                                $body = strip_tags($translation->push_body ?? $notification->push_body);
                                dispatch(new SendPushNotification($request_detail->driverDetail->user, $title, $body));
                            }
                 $this->database->getReference('requests/'.$request_detail->id)->update(['is_paid'=>1]);
                    }

            }


            return view('success',['success'],compact('web_booking_value','request_id'));


    }
}
