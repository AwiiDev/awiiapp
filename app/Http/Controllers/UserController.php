<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Transformers\CountryTransformer;
use App\Models\User;
use App\Base\Services\ImageUploader\ImageUploaderContract;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Api\V1\BaseController;
use Kreait\Firebase\Database;
use App\Base\Constants\Masters\WalletRemarks;
use App\Models\Payment\UserWalletHistory;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use App\Base\Filters\Admin\UserFilter;
use Illuminate\Support\Facades\Storage;
use App\Base\Filters\Master\CommonMasterFilter;
use Carbon\Carbon;
use App\Base\Constants\Auth\Role;
use Illuminate\Support\Facades\Hash;
use App\Mail\UserRegistationMail;
use Illuminate\Support\Facades\Mail;
use App\Mail\WalletAddAmountMail;
use App\Transformers\Payment\UserWalletHistoryTransformer;

class UserController extends BaseController
{

    protected $imageUploader;
    protected $user;

    public function __construct(ImageUploaderContract $imageUploader, User $user)
    {
        $this->imageUploader = $imageUploader;
        $this->user = $user;
    }

    public function index() 
    {
        $app_for = env("APP_FOR");

        if (access()->hasRole('owner')) {
            return redirect()->route('approvedFleetdriver.Index');
        }
        return Inertia::render('pages/user/index',['app_for'=>$app_for]);
    }
    // List of User
    public function list(QueryFilterContract $queryFilter, Request $request)
    {
        $query = User::belongsTorole(Role::USER)->orderBy('created_at','DESC');
        // dd($query->get());

        $results = $queryFilter->builder($query)->customFilter(new UserFilter)->paginate();
// dd($results);
        return response()->json([
            'results' => $results->items(),
            'paginator' => $results,
        ]);
    }
    public function create()
     {
        $query = Country::active()->get();

        $countries = fractal($query, new CountryTransformer);

        $result = json_decode($countries->toJson(),true);
        
        $default_country = Country::active()->where('code',get_settings('default_country_code_for_mobile_app'))->first();

        // dd($default_country->flag);

        $default_dial_code = $default_country->dial_code;
        $default_country_id = $default_country->id;
        $app_for = env("APP_FOR");
        $default_flag = $default_country->flag;
        return Inertia::render('pages/user/create', ['countries'=>$result['data'],'default_dial_code'=>$default_dial_code,'app_for'=>$app_for,'user'=>null,'default_flag'=>$default_flag,'default_country_id'=>$default_country_id]);
    }
    public function store(Request $request)
    {

        // dd($request->has('profile_picture'));
         // Validate the incoming request
         $created_params =   $request->validate([
            'name' => 'required',
            'country'=>'required',
            'mobile'=>'required|mobile_number|min:8',
            'email' => 'required',
            'gender' => 'required',
            // 'profile_picture' => 'required',
        ]);
        
        $created_params['password'] = bcrypt($request->input('password'));

        $created_params['active'] = true;

        
        // if ($uploadedFile = $this->getValidatedUpload('profile_picture', $request)) {
        //     $created_params['profile_picture'] = $this->imageUploader->file($uploadedFile)
        //         ->saveProfilePicture();
        // }

        if ($uploadedFile = $request->file('profile_picture')) {
            $created_params['profile_picture'] = $this->imageUploader->file($uploadedFile)
                ->saveProfilePicture();
        }

        // Create a new User
        $user = User::create($created_params);
        // dd($user);
        $user->userWallet()->create(['amount_added'=>0]);

        $user->attachRole('user');


        // Fetch the notification channel
        $notification = \DB::table('notification_channels')
        ->where('topics', 'New Customer Registration')
        ->first();

        if ($notification && $notification->mail == 1) {
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

        // Prepare notification data
        $notificationData = [
            'email_subject' => $translation->email_subject ?? $notification->email_subject,
            'mail_body' => str_replace(
                ['{name}', '{email}', '{mobile}'],
                [$user->name, $user->email, $user->mobile],
                $translation->mail_body ?? $notification->mail_body
            ),
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
            'footer_content' => $translation->footer_content ?? $notification->footer_content,
            'footer_copyrights' => $translation->footer_copyrights ?? $notification->footer_copyrights,
        ];

        // Send the email
        Mail::to($user->email)->send(new UserRegistationMail($user, $notificationData));
        }

        // Optionally, return a response
        return response()->json([
            'successMessage' => 'user created successfully.',
            'user' => $user,
        ], 201);
    }

    public function edit($id)
    {

        $user = User::find($id);
// dd($user);

        $query = Country::active()->get();

        $countries = fractal($query, new CountryTransformer);

        $result = json_decode($countries->toJson(),true);
        
        $default_country = $user->countryDetail()->first();

        // dd($user->profile_picture);

        $default_dial_code = $user->countryDetail->dial_code;
        $default_country_id = $user->countryDetail->id;
        $default_flag = $user->countryDetail->flag;

        $app_for = env("APP_FOR");

        return Inertia::render(
            'pages/user/create', ['countries'=>$result['data'],'default_dial_code'=>$default_dial_code,'user'=>$user,'app_for'=>$app_for,'default_flag'=>$default_flag,'default_country_id'=>$default_country_id]
        );
    }

    public function update(Request $request, User $user)
    {
    // dd($request->all());

        // Validate the incoming request
            $updated_params =  $request->validate([
                'name' => 'required',
                'country'=>'required',
                'mobile'=>'required|mobile_number|min:8',
                'email' => 'required',
                'gender' => 'required',
                // 'profile_picture' => 'required',
            ]);


            // if($request->hasFile('profile_picture')){
            //     if ($user->icon) {
            //         Storage::delete('public/' . $user->profile_picture);
            //     }
            //     // if ($uploadedFile = $this->getValidatedUpload('profile_picture', $request)) {
            //     //     $updated_params['profile_picture'] = $this->imageUploader->file($uploadedFile)
            //     //         ->saveProfilePicture();
            //     // }

            //     if ($uploadedFile = $request->file('profile_picture')) {
            //         $updated_params['profile_picture'] = $this->imageUploader->file($uploadedFile)
            //             ->saveProfilePicture();
            //     }
            // }
            if ($uploadedFile = $request->file('profile_picture')) {
                $updated_params['profile_picture'] = $this->imageUploader->file($uploadedFile)
                    ->saveProfilePicture();
            }

            $user->update($updated_params);

            // Optionally, return a response
            return response()->json([
                'successMessage' => 'User updated successfully.',
                'user' => $user,
            ], 201);

        }

        public function editPassword($id)
        {
            // You don't need to fetch other user information here, just the password fields
            $user = User::find($id);
            
            // Return the edit password page
            return Inertia::render(
                'pages/user/edit', ['user' => $user]
            );
        }

        public function updatePasswords(Request $request, User $user)
        {
            // Validate the password and confirmation
            $updated_params = $request->validate([
                'password' => 'required|min:8',  // Confirmed is for password_confirmation
                'confirm_password' => 'required|same:password',
            ]);

            // Update the password
            $user->update([
                'password' => bcrypt($updated_params['password']),
            ]);
            // $user->update($updated_params);
// dd($updated_params);
            return response()->json([
                'successMessage' => 'Password updated successfully.',
                'user' => $user,
            ], 201);
        }

        public function checkMobileExists($mobile, $userId = null)
        {
            $query = User::where('mobile', $mobile);
            if ($userId !== null) {
                $query->where('id', '!=', $userId);
            }
            $userExists = $query->exists();
            return response()->json(['exists' => $userExists]);
        }

        public function checkEmailExists($email, $userId = null)
        {
            $query = User::where('email', $email);
            if ($userId !== null) {
                $query->where('id', '!=', $userId);
            }
            $userExists = $query->exists();
            return response()->json(['exists' => $userExists]);
        }
        public function updateStatus(Request $request)
        {
            // dd($request->all());
            User::where('id', $request->id)->update(['active'=> $request->status]);

            return response()->json([
                'successMessage' => 'User status updated successfully',
            ]);


        }

    public function destroy(User $user)
    {
        if($user->is_deleted!=null)
        {
            $user->update(['is_deleted_at'=>Carbon::now()]);

        }else{
            $user->delete();

        }

        return response()->json([
            'successMessage' => 'User deleted successfully',
        ]);
    }   

    public function viewProfile(User $user) 
    {
        $currency = $user->countryDetail()->pluck('currency_symbol');

        $user_date = $user->getConvertedCreatedAtAttribute();

        $user_wallet = $user->userWallet;


        $completed_request = $user->requestDetail()->where('is_completed', true)->count();

        $cancelled_request = $user->requestDetail()->where('is_cancelled', true)->count();

        $on_going = $user->requestDetail()->where('is_cancelled', false)->where('is_completed', false)->count();



        // dd($user->getConvertedCreatedAtAttribute());

        return Inertia::render('pages/user/view_profile', ['user'=>$user,
        'user_date'=>$user_date, 
        'currency'=>$currency,
        'user_wallet'=>$user_wallet,
        'completed_request'=>$completed_request, 
        'cancelled_request'=>$cancelled_request, 
        'on_going'=>$on_going, 
    ]);
    }
    // walletHistoryList
    public function walletHistoryList( User $user)
    {

        // dd($user);
        $results = $user->userWalletHistory()->orderBy('created_at', 'desc')->paginate();
        $items = fractal($results, new UserWalletHistoryTransformer)->toArray();
        return response()->json([
            'results' => $items['data'],
            'paginator' => $results,
        ]);
    }

    public function walletAddAmount(Request $request, User $user)
    {
        $request->validate([
            'amount' => 'required|numeric',
            'operation' => 'required|in:add,subtract'
        ]);
        $currency = $user->countryDetail()->pluck('currency_symbol')->first();

        $user_wallet = $user->userWallet;


        $amount = $request->input('amount');
        $operation = $request->input('operation');
        $transaction_id = str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);

        if ($operation === 'subtract' && $user_wallet->amount_balance < $amount) {
            return response()->json(['message' => 'Insufficient funds'], 400);
        }


        if ($operation === 'add') {
            $user_wallet->amount_added += $amount;
            $user_wallet->amount_balance += $amount;
            $is_credit = true;
            $remarks = WalletRemarks::MONEY_DEPOSITED_TO_E_WALLET_FROM_ADMIN;
        } else {
            $user_wallet->amount_balance -= $amount;
            $user_wallet->amount_spent += $amount;
            $is_credit = false;
            $remarks = WalletRemarks::WITHDRAWN_FROM_WALLET;
        }

        $user_wallet->save();

        UserWalletHistory::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'transaction_id' => $transaction_id,
            'remarks' => $remarks,
            'is_credit' => $is_credit,
        ]);

        $notification = \DB::table('notification_channels')
        ->where('topics', 'User Wallet Amount') // Match the correct topic
        ->first();

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
    
        $notificationData = [
            'email_subject' =>  $translation->email_subject ?? $notification->email_subject,
            'mail_body' => str_replace(['{name}', '{transaction_id}','{currency}', '{amount}', '{current_balance}'],
                          [$user->name, $transaction_id,$currency, $amount, $user_wallet->amount_balance,], 
                          $translation->mail_body ?? $notification->mail_body),
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
            'footer_content' => $translation->footer_content ?? $notification->footer_content,
            'footer_copyrights' => $translation->footer_copyrights ?? $notification->footer_copyrights,
        ];
    
            // Send welcome email
            // dd($user->email);
            Mail::to($user->email)->send(new WalletAddAmountMail($user, $notificationData));
        }

        return response()->json(['message' => 'Amount adjusted successfully', 'transaction_id' => $transaction_id], 200);
    }
// deletedUser
    public function deletedUser()
    {
        return Inertia::render('pages/user/deletedIndex',['app_for'=>env("APP_FOR")]);

    }
    public function deletedList(QueryFilterContract $queryFilter, Request $request)
    {
        $query = User::belongsToRole('user')->where('is_deleted_at', '!=', null);
        // dd($query->get());

        $results = $queryFilter->builder($query)->customFilter(new CommonMasterFilter)->paginate();

        return response()->json([
            'results' => $results->items(),
            'paginator' => $results,
        ]);
    }
    public function profileEdit() 
    {
        $user = auth()->user();
    
        return Inertia::render('pages/profile-edit', [
            'auth' => ['user' => $user]
        ]);
    }
    
    public function updateProfile(Request $request)
    {
        // dd($request->all());
        if(env('APP_FOR') == 'demo') {
            return response()->json(['message' => "You are not authorized"],403);
        }

        $updated_params = $request->validate([
                            'name' => 'required',
                            'mobile' => 'required',
                            'email' => 'required',
                        ]);
        

        if ($uploadedFile = $request->file('profile_picture')) {
            $updated_params['profile_picture'] = $this->imageUploader->file($uploadedFile)
                ->saveProfilePicture();
        }
// dd($updated_params);
        $user = auth()->user()->update($updated_params);
            
        return response()->json(['message' => 'Password Updated successfully'], 200);


    }
    public function updatePassword(Request $request)
    {
        if(env('APP_FOR') == 'demo') {
            return response()->json(['message' => "You are not authorized"],403);
        }
        $password = Hash::make($request->password);

        $user = auth()->user()->update(['password'=>$password]);
            
        return response()->json(['message' => 'Password Updated successfully'], 200);

    }
    public function requestList(User $user)
    {
        // dd($user);
        $requests = $user->requestDetail()->paginate();

        return response()->json([
            'requests' => $requests->items(),
            'paginator' => $requests,
        ]);
    }

}
