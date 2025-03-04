<?php

namespace App\Http\Controllers\Api\V1\Auth\Registration;

use App\Models\User;
use App\Models\Country;
use App\Models\Admin\Driver;
use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Database;
use App\Base\Constants\Auth\Role;
use Illuminate\Support\Facades\DB;
use App\Events\Auth\UserRegistered;
use Illuminate\Support\Facades\Log;
use App\Models\Admin\ServiceLocation;
use App\Base\Constants\Masters\WalletRemarks;
use App\Http\Controllers\Api\V1\BaseController;
use App\Jobs\Notifications\AndroidPushNotification;
use App\Base\Services\OTP\Handler\OTPHandlerContract;
use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Requests\Auth\Registration\DriverRegistrationRequest;
use App\Jobs\Notifications\Auth\Registration\UserRegistrationNotification;
use App\Base\Services\ImageUploader\ImageUploaderContract;
use App\Jobs\Notifications\SendPushNotification;
use App\Models\Master\MailTemplate;
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Mail;
use App\Jobs\Mails\SendMailNotification;
use App\Models\Admin\DriverVehicleType;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin\Owner;
/**
 * @group SignUp-And-Otp-Validation
 *
 * APIs for Driver Register
 */
class DriverSignupController extends LoginController
{
    protected $user;
    protected $driver;
    protected $otpHandler;
    protected $country;
    protected $database;
    protected $imageUploader;



    public function __construct(User $user, Driver $driver, Country $country, OTPHandlerContract $otpHandler, Database $database,ImageUploaderContract $imageUploader)
    {
        $this->user = $user;
        $this->driver = $driver;
        $this->otpHandler = $otpHandler;
        $this->country = $country;
        $this->database = $database;
        $this->imageUploader = $imageUploader;
    }

    /**
    * Register the driver and send welcome email.
    * @bodyParam name string required name of the driver
    * @bodyParam company_key string optional company key of demo
    * @bodyParam mobile integer required mobile of driver
    * @bodyParam email email required email of the driver
    * @bodyParam device_token string required device_token of the driver
    * @bodyParam terms_condition boolean required it should be 0 or 1
    * @bodyParam service_location_id uuid required service location of the driver. it can be listed from service location list apis
     * @bodyParam refferal_code string optional refferal_code of the another driver
    * @bodyParam login_by tinyInt required from which device the driver registered
    * @bodyParam is_company_driver tinyInt required value can be 0 or 1.
    * @bodyParam vehicle_type uuid required vehicle types. listed by types api
    * @bodyParam car_make string required car make of the driver
    * @bodyParam car_model string required car model of the driver
    * @bodyParam custom_make string required car make of the driver
    * @bodyParam custom_model string required car model of the driver
    * @bodyParam car_number string required car number of the driver
    * @bodyParam car_color string required car color of the driver
    *
    * @param \App\Http\Requests\Auth\Registration\UserRegistrationRequest $request
    * @return \Illuminate\Http\JsonResponse
    * @responseFile responses/auth/register.json
    */

    public function register(DriverRegistrationRequest $request)
    {

        $created_params = $request->only(['name','mobile','email','country','gender']);

        $mobile = $request->mobile;


        $country_code = $this->country->where('dial_code', $request->input('country'))->exists();

        if (!$country_code) {
            $this->throwCustomException('unable to find country');
        }
        $country_id = $this->country->where('dial_code', $request->input('country'))->pluck('id')->first();

        $created_params['country'] = $country_id;

        $profile_picture = null;

        if ($uploadedFile = $this->getValidatedUpload('profile_picture', $request)) {
            $profile_picture = $this->imageUploader->file($uploadedFile)
                ->saveProfilePicture();
        }
       

        if ($request->has('email_confirmed') == true)
        {
            $data['email_confirmed'] = true;
        }
        $data = [
            'name' => $request->input('name'),
            'gender' => $request->input('gender'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
            'mobile' => $mobile,
            'mobile_confirmed' => true,
            'fcm_token'=>$request->input('device_token'),
            'login_by'=>$request->input('login_by'),
            'country'=>$country_id,
            'profile_picture'=>$profile_picture,
            'refferal_code'=>str_random(6),
            'lang'=>$request->input('lang')
        ];
        
        $user = $this->user->create($data);
        
        $created_params['mobile'] = $mobile;

        $created_params['uuid'] = driver_uuid();
        $created_params['active'] = false; //@TODO need to remove in future
        

        $driver = $user->driver()->create($created_params); 

        // Create Empty Wallet to the driver
        $driver_wallet = $driver->driverWallet()->create(['amount_added'=>0]);
        $driver->user->rewardPoint()->create(['amount']);

        $user->attachRole(Role::DRIVER);
        
        if(($user->email_confirmed) == true){

    /*mail Template*/
        $user_name = $user->name;
        $mail_template = MailTemplate::where('mail_type', 'welcome_mail_driver')->first();

        $description = $mail_template->description;

        $description = str_replace('$user_name', $user_name, $description);

        $mail_template->description = $description;

        $mail_template = $mail_template->description;

        $user_mail = $user->email;

        //dispatch(new SendMailNotification($mail_template, $user_mail));

    /*mail Template*/
    }


    $url = route('approveddriver.viewProfile', ['driver' => $driver->id]);


    $uuid = uniqid();
    
    $notificationData = [
        'id' => $uuid, // Use the same UUID for both Firebase and MySQL
        'body' => "New Driver Registered",
        'title' => "New Driver Registered",
        'read' => false,
        'updated_at' => round(microtime(true) * 1000),
        'url' => $url
    ];
    
    // Insert into Firebase
    $this->database->getReference('admin-notification/' . $uuid)
                ->set($notificationData);

        event(new UserRegistered($user));
      
        return $this->authenticateAndRespond($user, $request, $needsToken=true);
    }

    /**
    * Validate Mobile-For-Driver
    * @bodyParam mobile integer required mobile of driver
     * @response {
     * "success":true,
     * "message":"mobile_validated",
     * }
    *
    */
    public function validateDriverMobile(Request $request)
    {
        $mobile = $request->mobile;


        $validate_exists_mobile = $this->user->belongsTorole(Role::DRIVER)->where('mobile', $mobile)->exists();

        if($request->has('role') && $request->role=='driver'){

            $validate_exists_mobile = $this->user->belongsTorole(Role::DRIVER)->where('mobile', $mobile)->exists();

        }
        if($request->has('role') && $request->role=='owner'){

             $validate_exists_mobile = $this->user->belongsTorole(Role::OWNER)->where('mobile', $mobile)->exists();
        }


        if($request->has('email')){

        $validate_exists_email = $this->user->belongsTorole(Role::DRIVER)->where('email', $request->email)->exists();

        if($request->has('role')&& $request->role=='driver'){

            $validate_exists_email = $this->user->belongsTorole(Role::DRIVER)->where('email', $request->email)->exists();
        }
        if($request->has('role')&& $request->role=='owner'){

            $validate_exists_email = $this->user->belongsTorole(Role::OWNER)->where('email', $request->email)->exists();
        }

        if ($validate_exists_email) {
            $this->throwCustomException('Provided email has already been taken');
        }

        }

        if ($validate_exists_mobile) {
            $this->throwCustomException('Provided mobile has already been taken');
        }

        return $this->respondSuccess(null, 'mobile_validated');
    }

    /**
    * Validate Mobile-For-Driver-For-Login
    * @bodyParam mobile integer required mobile of driver
     * @response {
     * "success":true,
     * "message":"mobile_exists",
     * }
     */
   public function validateDriverMobileForLogin(Request $request)
    {

if ($request->has('mobile') && $request->has('email')) {
    $mobile = $request->mobile;
    $email = $request->email;

    $existsMobile = $this->user->belongsTorole($request->role)->where('mobile', $mobile)->exists();
    $existsEmail = $this->user->belongsTorole($request->role)->where('email', $email)->exists();

    if ($existsMobile && $existsEmail) {
        // Both mobile and email exist
        return $this->respondSuccess(null, 'mobile_and_email_exist');
    } elseif ($existsMobile) {
        // Only mobile exists
        return $this->respondSuccess(null, 'mobile_exists');
    } elseif ($existsEmail) {
        // Only email exists
        return $this->respondSuccess(null, 'email_exists');
    } else {
        // Neither mobile nor email exist
        return response()->json(['success' => false, 'message' => 'email_does_not_exist', 'enabled_module' => get_settings('enable_modules_for_applications')]);
    }
}



      if ($request->has('mobile'))
        {

        $mobile = $request->mobile;

        $validate_exists_mobile = $this->user->belongsTorole(Role::DRIVER)->where('mobile', $mobile)->exists();

        if($request->has('role') && $request->role=='driver'){

        $validate_exists_mobile = $this->user->belongsTorole(Role::DRIVER)->where('mobile', $mobile)->exists();

        }
        if($request->has('role') && $request->role=='owner'){

            $validate_exists_mobile = $this->user->belongsTorole(Role::OWNER)->where('mobile', $mobile)->exists();
        }

        if ($validate_exists_mobile) {
            return $this->respondSuccess(null, 'mobile_exists');
        }

                return response()->json(['success'=>false,'message'=>'mobile_does_not_exists','enabled_module'=>get_settings('enable_modules_for_applications')]);

        // return $this->respondFailed('mobile_does_not_exists');
       }
      if ($request->has('email'))
        {

        $email = $request->email;

        $validate_exists_email = $this->user->belongsTorole(Role::DRIVER)->where('email', $email)->exists();

        if($request->has('role') && $request->role=='driver'){

        $validate_exists_email = $this->user->belongsTorole(Role::DRIVER)->where('email', $email)->exists();

        }
        if($request->has('role') && $request->role=='owner'){

            $validate_exists_email = $this->user->belongsTorole(Role::OWNER)->where('email', $email)->exists();
        }

        if ($validate_exists_email) {
            return $this->respondSuccess(null, 'email_exists');
        }

        return response()->json(['success'=>false,'message'=>'email_does_not_exists','enabled_module'=>get_settings('enable_modules_for_applications')]);
        // return $this->respondFailed('email_does_not_exists');
      }


    }



    /**
    * Add Commission to the referred driver
    *
    */
    public function addCommissionToRefferedUser($reffered_user)
    {
        $driver_wallet = $reffered_user->driverWallet;
        $referral_commision = get_settings('referral_commision_for_driver')?:0;

        $driver_wallet->amount_added += $referral_commision;
        $driver_wallet->amount_balance += $referral_commision;
        $driver_wallet->save();

        // Add the history
        $reffered_user->driverWalletHistory()->create([
            'amount'=>$referral_commision,
            'transaction_id'=>str_random(6),
            'remarks'=>WalletRemarks::REFERRAL_commission,
            'refferal_code'=>$reffered_user->refferal_code,
            'is_credit'=>true]);

        // Notify user
        // $title = custom_trans('referral_earnings_notify_title');
        // $body = custom_trans('referral_earnings_notify_body');

        // $title = custom_trans('referral_earnings_notify_title',[],$reffered_user->user->lang);
        // $body = custom_trans('referral_earnings_notify_body',[],$reffered_user->user->lang);


        // dispatch(new SendPushNotification($reffered_user->user,$title,$body));


         $notification = \DB::table('notification_channels')
                    ->where('topics', 'Driver Referral') // Match the correct topic
                    ->first();

        //   send push notification 
            if ($notification && $notification->push_notification == 1) {
                 // Determine the user's language or default to 'en'
                $userLang = $reffered_user->user->lang ?? 'en';
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
                dispatch(new SendPushNotification($reffered_user->user, $title, $body));
            }

    }


    /**
     * Owner Register
     * @bodyParam name string required name of the owner
     * @bodyParam company_name string required name of the the company
     * @bodyParam address string required address of the the company
     * @bodyParam city string required city of the the company
     * @bodyParam tax_number string required tax_number of the the company
     * @bodyParam country string required country dial code of the the company
     * @bodyParam postal_code string required postal_code of the the company
     * @bodyParam mobile integer required mobile of owner
     * @bodyParam email email required email of the owner
     * @bodyParam device_token string required device_token of the owner
     * @bodyParam service_location_id uuid required service location of the owner. it can be listed from service location list apis
     * @bodyParam login_by tinyInt required from which device the owner registered
     * @return \Illuminate\Http\JsonResponse
     * @responseFile responses/auth/register.json
     *
     * */
    public function ownerRegister(Request $request){

        $request->validate([
            'company_name' => 'sometimes|required|unique:owners,company_name,NULL,id,deleted_at,NULL',
            'name' => 'sometimes|required',
            'address'=>'sometimes|required|min:10',
            'postal_code'=>'sometimes|required|numeric|max:8',
            'city'=>'sometimes|required',
            'service_location_id' => 'sometimes|required',
            'tax_number' => 'sometimes|required',
            'device_token'=>'required',
            'login_by'=>'required|in:android,ios',
            'country' =>'required|exists:countries,dial_code',
        ]);


         $created_params = $request->only(['service_location_id','company_name','mobile','email','address','postal_code','city','tax_number']);

         $created_params['owner_name'] = $request->name;
         $created_params['name'] = $request->name;

        if($request->has('transport_type'))
        {
         $created_params['transport_type'] = $request->transport_type;
            
        }
         $created_params['approve'] = false;

        if ($request->input('service_location_id')) {
            $timezone = ServiceLocation::where('id', $request->input('service_location_id'))->pluck('timezone')->first();
        } else {
            $timezone = env('SYSTEM_DEFAULT_TIMEZONE');
        }

        $country_id = $this->country->where('dial_code', $request->input('country'))->pluck('id')->first();

        $profile_picture = null;

        if ($uploadedFile = $this->getValidatedUpload('profile', $request)) {
            $profile_picture = $this->imageUploader->file($uploadedFile)
                ->saveProfilePicture();
        }

        $mobile = $request->mobile;


        if ($request->has('email_confirmed') == true)
        {
            $data['email_confirmed'] = true;
        }
        $password = $request->has('password') ? Hash::make($request->input('password')) : null;
        $data = [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'gender' => $request->input('gender'),
            'mobile' => $mobile,
            'password' => $password,
            'mobile_confirmed' => true,
            'fcm_token'=>$request->input('device_token'),
            'login_by'=>$request->input('login_by'),
            'timezone'=>$timezone,
            'country'=>$country_id,
            'profile_picture'=>$profile_picture,
            'refferal_code'=>str_random(6),
            'lang'=>$request->input('lang'),
        ];

        DB::beginTransaction();
        try {

        $user = $this->user->create($data);

        $user->attachRole(Role::OWNER);


        $owner = $user->owner()->create($created_params);

        $owner_wallet = $owner->ownerWalletDetail()->create(['amount_added'=>0]);


        $this->database->getReference('owners/owner_'.$owner->id)->set(['id'=>$owner->id,'active'=>1,'updated_at'=> Database::SERVER_TIMESTAMP]);


        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            Log::error('Error while Registering a owner account. Input params : ' . json_encode($request->all()));
            return $this->respondBadRequest('Unknown error occurred. Please try again later or contact us if it continues.');
        }
        DB::commit();
        return $this->authenticateAndRespond($user, $request, $needsToken=true);

    }
    /**
     * Update password for Driver or Owner
     * @bodyParam email string optional User email
     * @bodyParam mobile string optional User mobile
     * @bodyParam password string required New password to be set
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @response {"success":true,"message":"password_updated_successfuly"}
     */
    public function updatePassword(Request $request)
    {

if($request->has('role') && $request->role=='driver'){
/*Email*/
        if($request->has('email')) {
            $driver = Driver::where('email', $request->email)->first();

            if($driver) {
                $password = $request->password;

                // Hash the password
                $hashedPassword = Hash::make($password);

                // Update the password
                $driver->user->update(['password' => $hashedPassword]);

            return response()->json(['success'=>true,'message'=>'success','message'=>'password_updated_successfuly']);

            }
         }
/*mobile*/
        if($request->has('mobile')) {
            $driver = Driver::where('mobile', $request->mobile)->first();

            if($driver) {
                $password = $request->password;

                // Hash the password
                $hashedPassword = Hash::make($password);

                // Update the password
                $driver->user->update(['password' => $hashedPassword]);

             return response()->json(['success'=>true,'message'=>'success','message'=>'password_updated_successfuly']);

            }
          }
/*mobile Ends*/
  }else{

/*Owner*/
/*Email*/
        if($request->has('email')) {
            $owner = Owner::where('email', $request->email)->first();

            if($owner) {
                $password = $request->password;

                // Hash the password
                $hashedPassword = Hash::make($password);

                // Update the password
                $owner->user->update(['password' => $hashedPassword]);

            return response()->json(['success'=>true,'message'=>'success','message'=>'password_updated_successfuly']);

            }
         }
/*mobile*/
        if($request->has('mobile')) {
            $owner = Owner::where('mobile', $request->mobile)->first();

            if($owner) {
                $password = $request->password;

                // Hash the password
                $hashedPassword = Hash::make($password);

                // Update the password
                $owner->user->update(['password' => $hashedPassword]);

             return response()->json(['success'=>true,'message'=>'success','message'=>'password_updated_successfuly']);

            }
          }
/*mobile Ends*/

    }
  }//function ends

}
