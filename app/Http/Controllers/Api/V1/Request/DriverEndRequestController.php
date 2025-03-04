<?php

namespace App\Http\Controllers\Api\V1\Request;

use App\Jobs\NotifyViaMqtt;
use App\Models\Admin\Promo;
use App\Jobs\NotifyViaSocket;
use Kreait\Firebase\Contract\Database;
use Carbon\Carbon;
use App\Models\Admin\PromoUser;
use App\Base\Constants\Masters\UnitType;
use App\Base\Constants\Masters\PushEnums;
use App\Base\Constants\Masters\PaymentType;
use App\Base\Constants\Masters\WalletRemarks;
use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Requests\Request\DriverEndRequest;
use App\Jobs\Notifications\AndroidPushNotification;
use App\Transformers\Requests\TripRequestTransformer;
use App\Models\Admin\ZoneTypePackagePrice;
use Illuminate\Support\Facades\Log;
use App\Models\Request\RequestCancellationFee;
use App\Base\Constants\Setting\Settings;
use App\Jobs\Notifications\SendPushNotification;
use App\Models\Master\MailTemplate;
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Mail;
use App\Jobs\Mails\SendMailNotification;
use App\Jobs\Mails\SendInvoiceMailNotification;
use Illuminate\Http\Request;
use App\Models\Request\Request as RequestRequest;
use App\Models\Request\RequestBill;
use App\Models\Request\RequestStop; 
use App\Models\User;
use App\Helpers\Rides\RidePriceCalculationHelpers;
use App\Helpers\Rides\PaymentOptionCalculationHelper;
use App\Models\Admin\Incentive;
use App\Models\Payment\DriverIncentiveHistory;
use App\Models\Payment\DriverWallet;
use App\Models\Admin\Driver;
use App\Models\Admin\DriverLevelUp;
use App\Helpers\Rides\EndRequestHelper;
use App\Mail\UserInvoiceMail;
use App\Models\Admin\Setting;
use App\Models\Admin\InvoiceConfiguration;
use App\Models\ThirdPartySetting;
use App\Mail\DriverInvoiceMail;

/**
 * @group Driver-trips-apis
 *
 * APIs for Driver-trips apis
 */
class DriverEndRequestController extends BaseController
{
    use RidePriceCalculationHelpers,PaymentOptionCalculationHelper,EndRequestHelper;
   
    protected $database;
    
    public function __construct(Database $database)
    {
        $this->database = $database;
    }
    /**
    * Driver End Request
    * @bodyParam request_id uuid required id request
    * @bodyParam distance double required distance of request
    * @bodyParam drop_lat double required drop lattitude of request
    * @bodyParam drop_lng double required drop longitude of request
    * @bodyParam drop_address double required drop drop Address of request
    * @responseFile responses/requests/request_bill.json
    *
    */
    public function endRequest(DriverEndRequest $request)
    {   
        // Get Driver Detail
        $driver = auth()->user()->driver;

        // Get Request Detail
        $request_detail = $driver->requestDetail()->where('id', $request->request_id)->first();

        if (!$request_detail) {
            $this->throwAuthorizationException();
        }

        // Validate Trip request data
        if ($request_detail->is_completed) {

            $request_result = fractal($request_detail, new TripRequestTransformer)->parseIncludes('requestBill');
            return $this->respondSuccess($request_result, 'request_ended');
        }
        if ($request_detail->is_cancelled) {
            $this->throwCustomException('request cancelled');
        }


        // Collecting drop location detail & update to request_place table
        $firebase_request_detail = $this->database->getReference('requests/'.$request_detail->id)->getValue();

        $request_place_params = ['drop_lat'=>$request->drop_lat,'drop_lng'=>$request->drop_lng,'drop_address'=>$request->drop_address];

        if ($firebase_request_detail) {
            if(array_key_exists('lat_lng_array',$firebase_request_detail)){
                $locations = $firebase_request_detail['lat_lng_array'];
                $request_place_params['request_path'] = $locations;
            }
        }

        // Update Droped place details
        $request_detail->requestPlace->update($request_place_params);
        // Update Driver state as Available
        $request_detail->driverDetail->update(['available'=>true]);

         // Get currency code of Request
        $service_location = $request_detail->zoneType->zone->serviceLocation;

        $currency_code = $service_location->currency_code;

        $requested_currency_symbol = $service_location->currency_symbol;


        // Get the Price Details

        $zone_type = $request_detail->zoneType;

        $zone_type_price = $zone_type->zoneTypePrice()->first();

        // Calulate Distance & duration
        $distance = (double)$request->distance;

        $distance_and_duration = $this->calculateDistanceAndDuration($distance,$request_detail);

        $distance = $distance_and_duration['distance'];
        $duration = $distance_and_duration['duration'];


            $is_paid = false;

        $request_params = [
            'is_completed'=>true,
            'completed_at'=>date('Y-m-d H:i:s'),
            'total_distance'=>$distance,
            'total_time'=>$duration,
            'is_paid'=>$is_paid,
            ];

        if($request->poly_line !=null){

            $request_params['poly_line'] = $request->poly_line;

        }
        
        $request_detail->update($request_params);

        // Calulate Waiting Time
        $before_trip_start_waiting_time = $request->input('before_trip_start_waiting_time');
        $after_trip_start_waiting_time = $request->input('after_trip_start_waiting_time');

        $waiting_time = $this->calculateWaitingTime($before_trip_start_waiting_time,$after_trip_start_waiting_time,$zone_type_price);
        // Calculate Waiting Time Ends here

        // Get/Validate Coupon Detail
        $promo_detail =null;

        if ($request_detail->promo_id) {
            $user_id = $request_detail->userDetail->id;
            $service_location_id = $request_detail->service_location_id;
            $promo_detail = $this->validateAndGetPromoDetail($request_detail->promo_id,$user_id,$request_detail);
        }


        // Collect Request pickup & drop coords
        $pick_lat = $request_detail->pick_lat;
        $drop_lat = $request_detail->drop_lat;
        $pick_lng = $request_detail->pick_lng;
        $drop_lng = $request_detail->drop_lng;

        $timezone = $request_detail->serviceLocationDetail->timezone;

        $airport_surge_fee = 0;

        if ( $request_detail->is_airport)
        {
            $airport_surge_fee =  $zone_type->airport_surge;
        }

        // Calculate Bill of a Ride
        $calculated_bill = $this->calculateBillForARide($pick_lat,$pick_lng,$drop_lat,$drop_lng,$distance, $duration, $zone_type, $zone_type_price, $promo_detail,$timezone,null,$waiting_time,$request_detail,$driver,$airport_surge_fee);


         if($request_detail->is_rental && $request_detail->rental_package_id){

            $zone_type_price = ZoneTypePackagePrice::where('zone_type_id',$request_detail->zone_type_id)->where('package_type_id',$request_detail->rental_package_id)->first();

            $calculated_bill =  $this->calculateRentalRideFares($zone_type_price, $distance, $duration, $waiting_time, $promo_detail,$request_detail,$airport_surge_fee);

        }


        $calculated_bill['before_trip_start_waiting_time'] = $before_trip_start_waiting_time;
        $calculated_bill['after_trip_start_waiting_time'] = $after_trip_start_waiting_time;
        $calculated_bill['calculated_waiting_time'] = $waiting_time;
        $calculated_bill['waiting_charge_per_min'] = $zone_type_price->waiting_charge ?? 0;
        $calculated_bill['requested_currency_code'] = $currency_code;
        $calculated_bill['requested_currency_symbol'] = $requested_currency_symbol;

        // Store Bill detail
        $bill = $request_detail->requestBill()->create($calculated_bill);


        // Incentives & Driver Rewards/Level
        $incentive_feature = get_settings('show_incentive_feature_for_driver');
       
        if($incentive_feature==1)
        {
            if($driver->owner_id==null)
            {
                $this->incentives($request_detail);

            }

        }

        $driver_level_feature = get_settings('show_driver_level_feature');
       
        if($driver_level_feature==1 && !$driver->owner_id)
        {
            $this->levelUp($request_detail);

        }


        // Send push notification to the user
        $request_result = fractal($request_detail, new TripRequestTransformer)->parseIncludes(['requestBill','userDetail','driverDetail']);

        if ($request_detail->if_dispatch || $request_detail->user_id==null ) {
            goto end;
        }
        // Send Push notification to the user
        $user = $request_detail->userDetail;
        
        if($user){
            // $title = custom_trans('trip_completed_title',[],$user->lang);
            // $body = custom_trans('trip_completed_body',[],$user->lang);
            // dispatch(new SendPushNotification($user,$title,$body));
            $notification = \DB::table('notification_channels')
            ->where('topics', 'Invoice For End of the Ride User') // Match the correct topic
            ->first();

            if(!empty($user?->email)){
               
        
                // dd($notification);
        
                if ($notification && $notification->mail == 1) {

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
                            
                    $data = fractal($request_detail, new TripRequestTransformer)->parseIncludes(['userDetail', 'driverDetail', 'requestBill', 'rejectedDrivers'])->toArray(); 
                    $logo = Setting::where('name', 'logo')->first();
                    $invoice = ThirdPartySetting::where('module', 'mail_config')->pluck('value', 'name')->toArray();
                    $data['formatted_completed_at'] = isset($request_detail['completed_at']) 
                    ? Carbon::parse($request_detail['completed_at'])
                        ->setTimezone(env('SYSTEM_DEFAULT_TIMEZONE', 'Asia/Kolkata'))
                        ->format('M j, Y - h:i A') 
                    : null;
            
                    $notificationData = [
                    'email_subject' =>  $translation->email_subject ?? $notification->email_subject,
                    'mail_body' => str_replace('{name}', $user->name, $translation->mail_body ?? $notification->mail_body),
                    'banner_img' => $notification->banner_img,
                    'logo_img' => $notification->logo_img,
                    'button_name' => $translation->button_name ?? $notification->button_name,
                    'show_button' => $notification->show_button,
                    'show_img' => $notification->show_img,
                    'show_fbicon' => $notification->show_fbicon,
                    'show_instaicon' => $notification->show_instaicon,
                    'show_twittericon' => $notification->show_twittericon,
                    'show_linkedinicon' => $notification->show_linkedinicon,
                    'button_url' => $notification->button_url,
                    'footer' => json_decode($notification->footer, true),            
                    'footer_content' =>$translation->footer_content ?? $notification->footer_content,
                    'footer_copyrights' =>$translation->footer_copyrights ?? $notification->footer_copyrights,

                ];
            
                    // Send welcome email
                    // dd($user->email);
                    Mail::to($user->email)->send(new UserInvoiceMail($user, $notificationData, $data, $logo, $invoice ));
                }
            } 


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
        }

        
        $driver = $request_detail->driverDetail;


        if($driver && $driver->email){

            $notification = \DB::table('notification_channels')
                ->where('topics', 'Invoice For End of the Ride Driver') // Match the correct topic
                ->first();
        
                // dd($notification);
        
                if ($notification && $notification->mail == 1) {

                    $userLang = $driver->lang ?? 'en';
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
                            
                    $data = fractal($request_detail, new TripRequestTransformer)->parseIncludes(['userDetail', 'driverDetail', 'requestBill', 'rejectedDrivers'])->toArray(); 
                    $logo = Setting::where('name', 'logo')->first();
                    $invoice = ThirdPartySetting::where('module', 'mail_config')->pluck('value', 'name')->toArray();
                    $data['formatted_completed_at'] = isset($request_detail['completed_at']) 
                    ? Carbon::parse($request_detail['completed_at'])
                        ->setTimezone(env('SYSTEM_DEFAULT_TIMEZONE', 'Asia/Kolkata'))
                        ->format('M j, Y - h:i A') 
                    : null;
            
                    $notificationData = [
                    'email_subject' =>  $translation->email_subject ?? $notification->email_subject,
                    'mail_body' => str_replace('{name}', $driver->name, $translation->mail_body ?? $notification->mail_body),
                    'banner_img' => $notification->banner_img,
                    'logo_img' => $notification->logo_img,
                    'button_name' => $translation->button_name ?? $notification->button_name,
                    'show_button' => $notification->show_button,
                    'show_img' => $notification->show_img,
                    'show_fbicon' => $notification->show_fbicon,
                    'show_instaicon' => $notification->show_instaicon,
                    'show_twittericon' => $notification->show_twittericon,
                    'show_linkedinicon' => $notification->show_linkedinicon,
                    'button_url' => $notification->button_url,
                    'footer' => json_decode($notification->footer, true),            
                    'footer_content' =>$translation->footer_content ?? $notification->footer_content,
                    'footer_copyrights' =>$translation->footer_copyrights ?? $notification->footer_copyrights,

                ];
            
                    // Send welcome email
                    // dd($user->email);
                    Mail::to($driver->email)->send(new DriverInvoiceMail($driver, $notificationData, $data, $logo, $invoice ));
                }
        }

        end:
        
        return $this->respondSuccess($request_result, 'request_ended');
    }

    

    

    

    /**
    * Validate & Apply Promo code
    * @return \Illuminate\Http\JsonResponse
    *
    */
    public function validateAndGetPromoDetail($promo_code_id,$user_id,$request_detail)
    {
        $current_date = Carbon::today()->toDateTimeString();


        if($app_for=='taxi' || $app_for=='delivery')
        {      
            $expired = Promo::where('code', $promo_code)->where('service_location_id',$request_detail-> service_location_id)->where('to', '>', $current_date)->first();
        }else{
            $transport_type = request()->transport_type;
            $expired = Promo::where('code', $promo_code)->where('service_location_id',$service_location_id)->where(function($query)use($transport_type){
            $query->where('transport_type',$transport_type)->orWhere('transport_type','both');
            })->where('to', '>', $current_date)->where('active',true)->first();

        }
        if($expired)
        {

            if($expired->user_specific){
                $validate_promo_code = $expired->promoCodeUsers()->where('user_id',$request_detail->user_id)->first();
                if(!$validate_promo_code){
                    return null;
                }
            }
            $exceed_usage = PromoUser::where('promo_code_id', $expired->id)->where('user_id', $user_id)->count();

            if ($exceed_usage > $expired->uses_per_user) {
                return null;
            }
            else{
                return $expired;
            }
        }
        else{
            return null;
        }

    }

    /**
     * Payment Confirmation
     *
     * @response
     * {
     *     "success": true,
     *     "message": "success",
     * }
     *
     * */
    public function paymentConfirm(Request $request)
    {

       $driver = auth()->user()->driver;


        $request_detail = $driver->requestDetail()->where('id', $request->request_id)->first();

        // Throw an exception if the user is not authorised for this request
        if (!$request_detail) {
            $this->throwAuthorizationException();
        }

        if ($this->handlePayment($request_detail) ) {
            $request_detail->update([
                'is_paid'=>true,
            ]);
        }

        return $this->respondSuccess();

    }
    /**
     * Payment Method update
     *
     * @response
     * {
     *     "success": true,
     *     "message": "success",
     * }
     *
     * */
    public function paymentMethod(Request $request)
    {

       $driver = auth()->user()->driver;


        $request_detail = $driver->requestDetail()->where('id', $request->request_id)->first();

        // dd($user);
        // Throw an exception if the user is not authorised for this request
        if (!$request_detail) {
            $this->throwAuthorizationException();
        }
        $request_detail->update([
            'payment_opt'=>$request->payment_opt,
            'is_paid'=>true,

        ]);
        return $this->respondSuccess();
    }
    /**
     * Trip end for stop
     * 
     * @response
     * {
     *     "success": true,
     *     "message": "success",
     * }
     */
    public function tripEndBystop(Request $request)
    {
        Log::info("tripEndBystop");
        Log::info($request->all());

        $request_stops = RequestStop::where('id', $request->stop_id)->update(['completed_at' => now()]);


        Log::info($request_stops);

        return $this->respondSuccess();

    }

    /**
     * Calculate and allocate Incentives
     * 
     * @response
     * {
     *     "success": true,
     *     "message": "success",
     * }
     */
    public function incentives($request_detail)
    {
    Log::info("incentives");
     /*Driver*/

        $driver = Driver::whereId($request_detail->driver_id)->first();

        $driver_completed_rides = RequestRequest::where('driver_id', $driver->id)->where('is_completed', true)->count();
        $currentDate = Carbon::today();

        $driver_completed_rides_today = RequestRequest::whereDate('trip_start_time', $currentDate)->where('driver_id', $driver->id)->where('is_completed', true)->count();

        $driver_incentive_exists = Incentive::where('mode', 'daily')
            ->where('ride_count', '<=', $driver_completed_rides_today)
            ->pluck('ride_count')
            ->map(fn($count) => (int)$count)
            ->toArray();

        $driver_credited_incentive = DriverIncentiveHistory::where('driver_id', $driver->id)
            ->where('mode', 'daily')
            ->whereDate('date', $currentDate)
            ->pluck('ride_count')
            ->map(fn($count) => (int)$count)
            ->toArray();

        $daily_incentive = array_diff($driver_incentive_exists,$driver_credited_incentive);
     
        // Calculate the most recent completed Sunday
        $mostRecentSunday = $currentDate->copy()->startOfWeek()->subDay(); 
       
        // Query to get the completed rides count
        $driver_completed_rides_this_week = RequestRequest::where('driver_id', $driver->id)
            ->where('is_completed', true)
            ->whereDate('trip_start_time', '>=', $mostRecentSunday)
            ->whereDate('trip_start_time', '<=', $currentDate)
            ->count();
        
        $driver_weekly_incentive_exists = Incentive::where('mode', 'weekly')
            ->where('ride_count', '<=', $driver_completed_rides_this_week)
            ->pluck('ride_count')
            ->map(fn($count) => (int)$count)
            ->toArray();

        $driver_weekly_incentive_credited = DriverIncentiveHistory::where('driver_id', $driver->id)
            ->where('mode', 'weekly')
            ->whereDate('created_at', '>=', $mostRecentSunday)
            ->whereDate('created_at', '<=', $currentDate)
            ->pluck('ride_count')
            ->map(fn($count) => (int)$count)
            ->toArray();

        $weekly_incentive = array_diff($driver_weekly_incentive_exists,$driver_weekly_incentive_credited);

        $driver_wallet = DriverWallet::where('user_id', $driver->id)->first();
        
        if(count($daily_incentive)>0 || count($weekly_incentive)>0)
        {
            foreach ($daily_incentive as $ride_count) {
                $incentive = Incentive::where('mode', 'daily')->where('ride_count', $ride_count)->first();
            
                if ($incentive) {
                    DriverIncentiveHistory::create([
                        'driver_id' => $driver->id,
                        'amount' => $incentive->amount,
                        'mode' => 'daily',
                        'ride_count' => $ride_count,
                        'date' => $currentDate,
                    ]);
            
                    $driver_wallet->amount_added += $incentive->amount;
                    $driver_wallet->amount_balance += $incentive->amount;
                    $driver_wallet->save();
            
                    $driver->driverWalletHistory()->create([
                        'amount' => $incentive->amount,
                        'transaction_id' => str_random(6),
                        'remarks' => WalletRemarks::INCENTIVE_AMOUNT,
                        'is_credit' => true,
                    ]);
                }
            }
            
            foreach ($weekly_incentive as $ride_count) {
                $incentive = Incentive::where('mode', 'weekly')->where('ride_count', $ride_count)->first();
            
                if ($incentive) {
                    DriverIncentiveHistory::create([
                        'driver_id' => $driver->id,
                        'amount' => $incentive->amount,
                        'mode' => 'weekly',
                        'ride_count' => $ride_count,
                        'date' => $currentDate,
                    ]);
            
                    $driver_wallet->amount_added += $incentive->amount;
                    $driver_wallet->amount_balance += $incentive->amount;
                    $driver_wallet->save();
            
                    $driver->driverWalletHistory()->create([
                        'amount' => $incentive->amount,
                        'transaction_id' => str_random(6),
                        'remarks' => WalletRemarks::INCENTIVE_AMOUNT,
                        'is_credit' => true,
                    ]);
                }
            }
            // $title = custom_trans('daily_incentive_title');
            // $body = custom_trans('daily_incentive_notify_body');

            // dispatch(new SendPushNotification($driver,$title,$body));

            $notification = \DB::table('notification_channels')
                ->where('topics', 'Driver Daily Incentive') // Match the correct topic
                ->first();

            //    send push notification 
                if ($notification && $notification->push_notification == 1) {
                     // Determine the user's language or default to 'en'
                    $userLang = $driver->lang ?? 'en';
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
                    dispatch(new SendPushNotification($driver, $title, $body));
                }

        }

/*Incentive*/

           return $this->respondSuccess();


        

    }
    /**
     * Calculate and allocate Rewards
     * @return \Illuminate\Http\JsonResponse
     * 
     */
    public function fetchReward($driver_level,$driver,$request_detail) {

        $target_level = $driver_level->levelDetail;
        $bonus_ride_rewards = 0;
        $bonus_amount_rewards = 0;
        if(!$driver_level->ride_rewarded){
            if($target_level->is_min_ride_complete){
                $driver_completed_rides = RequestRequest::where('driver_id', $driver->id)->where('is_completed', true)->count();
                if($target_level->min_ride_count <= $driver_completed_rides){
                    $bonus_ride_rewards = $target_level->ride_points;
                    $driver_level->ride_rewarded = true;
                    $driver_level->save();
                    $this->rewardDriver($target_level->ride_points,$driver,WalletRemarks::DRIVER_LEVEL_UP_BONUS,$request_detail->id);
                }
            }else{
                $driver_level->ride_rewarded = true;
                $driver_level->save();
            }
        }
        if(!$driver_level->amount_rewarded){
            if($target_level->is_min_ride_amount_complete){
                $driverSpentAmount = RequestBill::whereHas('requestDetail', function ($query) use ($driver) {
                    $query->where('driver_id', $driver->id)
                          ->where('is_completed', 1);
                })->sum('driver_commision');
                if($target_level->min_ride_amount <= $driverSpentAmount){
                    $bonus_amount_rewards = $target_level->rideamount_points_points;
                    $driver_level->amount_rewarded = true;
                    $driver_level->save();
                    $this->rewardDriver($target_level->amount_points,$driver,WalletRemarks::DRIVER_LEVEL_UP_BONUS,$request_detail->id);
                }
            }else{
                $driver_level->amount_rewarded = true;
                $driver_level->save();
            }
        }
        return [
            'rewards'=>$bonus_amount_rewards + $bonus_ride_rewards,
            'bonus_ride_rewards'=>$bonus_ride_rewards,
            'bonus_amount_rewards'=>$bonus_amount_rewards,
        ];
    }
    /**
     * Reward Point rewards
     * 
     */
    public function rewardDriver($reward,$driver,$remarks,$request_id) {
        $driver_reward = $driver->loyaltyPoint;
        $driver_reward->points_added += $reward;
        $driver_reward->balance_reward_points += $reward;
        $driver_reward->save();

        // Add the history
        $driver->loyaltyHistory()->create([
            'reward_points'=>$reward,
            'request_id'=>$request_id,
            'remarks'=>$remarks,
            'is_credit'=>true,
        ]);
    }
    /**
     * Wallet Rewards
     * 
     */
    public function creditDriver($reward,$driver,$remarks,$credit = false) {
        $driver_wallet = DriverWallet::where('user_id', $driver->id)->first();
        
        $driver_wallet->amount_added += $reward;
        $driver_wallet->amount_balance += $reward;
        $driver_wallet->save();

        // Add the history
        $driver->driverWalletHistory()->create([
            'amount'=>$reward,
            'transaction_id'=>str_random(6),
            'remarks'=>$remarks,
            'is_credit'=>$credit,
        ]);
    }

    /**
     * Level Calculation
     * @return \Illuminate\Http\JsonResponse
     * 
     */
    public function levelUp($request_detail) {
        try {
            $driver = Driver::findOrFail($request_detail->driver_id);
            $driver_user = $driver->user;
    
            if (!$driver_user) {
                // Log error or handle missing user case
                return;
            }
    
            $driver_level = $driver->levelDetail ?? $this->initializeDriverLevel($driver);
            $calculatedRewards = $this->fetchReward($driver_level, $driver,$request_detail);
    
            if ($driver_level->amount_rewarded && $driver_level->ride_rewarded) {
                $this->promoteDriverToNextLevel($driver, $driver_level, $request_detail);
            }
        } catch (\Exception $e) {
            // Handle error, log, or notify
            Log::error('Level-up process failed for driver ID: ' . $driver->id . ' Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Level Calculation
     * @return \App\Models\Admin\DriverLevelUp
     * 
     */
    private function initializeDriverLevel($driver) {
        $first_level = DriverLevelUp::orderBy('level', 'asc')->first();
        $driver_level = $driver->levelHistory()->create(['level' => $first_level->level, 'level_id' => $first_level->id]);
        $driver->driver_level_up_id = $driver_level->id;
        $driver->save();
        return $driver_level;
    }
    
    /**
     * Level promotion
     * 
     */
    private function promoteDriverToNextLevel($driver, $driver_level, $request_detail) {
        $next_level = DriverLevelUp::where('level', $driver_level->level + 1)->first();
        // dd($next_level,$driver_level->level +1);
    
        if ($next_level) {
            $next_driver_level = $driver->levelHistory()->create(['level' => $next_level->level, 'level_id' => $next_level->id]);
            $driver->driver_level_up_id = $next_driver_level->id;
            $driver->save();
            if ($next_level->reward_type == 'reward-cash') {
                $this->creditDriver($next_level->reward, $driver, WalletRemarks::DRIVER_LEVEL_UP, true);
            } elseif ($next_level->reward_type == 'reward-point') {
                $this->rewardDriver($next_level->reward, $driver, WalletRemarks::DRIVER_LEVEL_UP, $request_detail->id);
            }
        }
    }
    
}
