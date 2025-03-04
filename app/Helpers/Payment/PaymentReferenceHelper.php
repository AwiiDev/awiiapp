<?php

namespace App\Helpers\Payment;

use Kreait\Firebase\Contract\Database;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Payment\UserWallet;
use App\Models\Payment\DriverWallet;
use App\Base\Constants\Masters\WalletRemarks;
use App\Base\Constants\Masters\PushEnums;
use App\Models\Payment\OwnerWallet;
use App\Models\Payment\OwnerWalletHistory;
use App\Transformers\Payment\OwnerWalletTransformer;
use App\Models\Request\Request as RequestModel;
use App\Jobs\Notifications\SendPushNotification;
use App\Models\Payment\UserWalletHistory;
use App\Models\Payment\DriverWalletHistory;

trait PaymentReferenceHelper
{   

    public function __construct(Database $database)
    {
        
        $this->database = $database;

    }

    /**
     * Generate Payment Reference
     * 
     * 
     * */

    protected function generatePaymentReference($user_id,$request_for)
    {
        
        $current_timestamp = Carbon::now()->timestamp;
        
        $reference = $request_for.'--'.$current_timestamp.'--'.$user_id;

        return $reference;
    }


   /**
    * Add money to wallet
    * 
    * */
   protected function addMoneyToWallet($user,$requested_amount,$transaction_id)
   {
        
        $user_id = $user->id;

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
        $user_wallet->amount_added += $requested_amount;
        $user_wallet->amount_balance += $requested_amount;
        $user_wallet->save();
        $user_wallet->fresh();

        $wallet_add_history_model::create([
            'user_id'=>$user_id,
            'amount'=>$requested_amount,
            'transaction_id'=>$transaction_id,
            'remarks'=>WalletRemarks::MONEY_DEPOSITED_TO_E_WALLET,
            'is_credit'=>true]);

                
            // $title = trans('push_notifications.amount_credited_to_your_wallet_title',[],$user->lang);
            //     $body = trans('push_notifications.amount_credited_to_your_wallet_body',[],$user->lang);

            //     dispatch(new SendPushNotification($user,$title,$body));

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

        // End
        return $this->respondSuccess(null,'money_added_successfully');



   }

    /**
     * Make Payment At end of the ride
     * 
     * */
    protected function makePaymentForRide($request_id,$transaction_id){

        $request_detail = RequestModel::find($request_id); 
        
        $driver = $request_detail->driverDetail;    

        //  Update payement status
        $request_detail->is_paid = 1;

        $request_detail->save();

        $driver_commision = $request_detail->requestBill->driver_commision;

        $user_wallet = DriverWallet::firstOrCreate([
            'user_id'=>$driver->id]);

        $user_wallet->amount_added += $driver_commision;
        $user_wallet->amount_balance += $driver_commision;
        $user_wallet->save();
        $user_wallet->fresh();

        DriverWalletHistory::create([
            'user_id'=>$driver->id,
            'amount'=>$driver_commision,
            'transaction_id'=>$transaction_id,
            'remarks'=>WalletRemarks::TRIP_COMMISSION_FOR_DRIVER,
            'is_credit'=>true]);

        $this->database->getReference('requests/'.$request_detail->id)->update(['is_paid'=>1,'updated_at'=> Database::SERVER_TIMESTAMP]);

        // $title = trans('push_notifications.payment_completed_by_user_title',[],$driver->user->lang);
        // $body = trans('push_notifications.payment_completed_by_user_body',[],$driver->user->lang);

        // dispatch(new SendPushNotification($driver->user,$title,$body));

        $notification = \DB::table('notification_channels')
                ->where('topics', 'Driver Wallet Amount') // Match the correct topic
                ->first();

            //    send push notification 
                if ($notification && $notification->push_notification == 1) {
                     // Determine the user's language or default to 'en'
                    $userLang = $driver->user->lang ?? 'en';
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
                    dispatch(new SendPushNotification($driver->user, $title, $body));
                }

        return $this->respondSuccess(null,'ride_payment_success');

    }


    
}
