<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use App\Base\Services\ImageUploader\ImageUploaderContract;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use App\Models\User;
use App\Models\Admin\Owner;
use App\Models\Admin\OwnerNeededDocument;
use App\Models\Admin\OwnerDocument;
use App\Base\Filters\Admin\OwnerFilter;
use App\Base\Constants\Masters\DriverDocumentStatus;
use App\Base\Constants\Auth\Role;
use App\Models\Admin\ServiceLocation;
use Illuminate\Http\Request;
use App\Http\Controllers\Web\BaseController;
use Kreait\Firebase\Contract\Database;
use App\Models\Country;
use App\Transformers\CountryTransformer;
use Log;
use DB;
use Carbon\Carbon;
use App\Base\Constants\Masters\WalletRemarks;
use App\Models\Method;
use App\Jobs\Notifications\SendPushNotification;
use App\Transformers\Payment\WalletWithdrawalRequestsTransformer;
use App\Models\Payment\WalletWithdrawalRequest;
use  App\Models\Payment\OwnerWallet;

class ManageOwnerController extends BaseController
{

    /**
    * ImageUploader instance.
    *
    * @var ImageUploaderContract
    */
    protected $imageUploader;
    protected $owner;
    protected $database;

    /**
     * DriverDocumentController constructor.
     *
     * @param ImageUploaderContract $imageUploader
     */
    public function __construct(ImageUploaderContract $imageUploader, owner $owner,Database $database)
    {
        $this->imageUploader = $imageUploader;
        $this->owner = $owner;
        $this->database = $database;
    }
    public function index() {
        $location = ServiceLocation::active()->get();
        return Inertia::render('pages/manage_owners/index',['serviceLocations' => $location,'app_for'=>env("APP_FOR"),]);
    }

    public function list(QueryFilterContract $queryFilter, Request $request)
    {
        $query = Owner::query()->orderBy('created_at','DESC');
        $results = $queryFilter->builder($query)->customFilter(new OwnerFilter)->paginate();

        return response()->json([
            'results' => $results->items(),
            'paginator' => $results,
        ]);
    }

    public function checkMobileExists(Request $request)
    {
        $query = Owner::where('mobile', $request->mobile);
        if ($request->owner_id !== null) {
            $query->where('id', '!=', $request->owner_id);
        }
        $driverExists = $query->exists();
        return response()->json(['exists' => $driverExists]);
    }

    public function checkEmailExists(Request $request)
    {
        $query = Owner::where('email', $request->email);
        if ($request->owner_id !== null) {
            $query->where('id', '!=', $request->owner_id);
        }
        $driverExists = $query->exists();
        return response()->json(['exists' => $driverExists]);
    }
    public function create()
    {
        $location = ServiceLocation::active()->get();

        $query = Country::active()->get();

        $countries = fractal($query, new CountryTransformer);

        $result = json_decode($countries->toJson(),true);
        
        $default_country = Country::active()->where('code',get_settings('default_country_code_for_mobile_app'))->first();


        $default_dial_code = $default_country->dial_code;
        $default_country_id = $default_country->id;
        $default_flag = $default_country->flag;
        return Inertia::render('pages/manage_owners/create',[
            'countries'=>$result['data'],
            'default_dial_code'=>$default_dial_code,
            'serviceLocation' => $location,
            'default_flag'=>$default_flag,
            'default_country_id'=>$default_country_id]);
    }
    public function store(Request $request) {
        // dd($request->all());
        $validated = $request->validate([
            'company_name' => 'required',
            'name' => 'required',
            'country' => 'required',
            'email' => 'required',
            'mobile'=>'required|mobile_number|min:8',
            'password' => 'required',
            'service_location_id' => 'required',
            'transport_type' => 'required',
        ]);
        $validated['password'] = bcrypt($request->input('password'));
        $validated['first_name'] = bcrypt($request->input('name'));
        $token = str_random(40);
        $validated['email_confirmation_token'] = bcrypt($token);
        $user_params = $request->only([
            'mobile','email'
        ]);
        $user_params['name'] = $validated['name'];
        $user_params['country'] = $validated['country'];
        $user_params['password'] = bcrypt($request->input('password'));
        $user = User::create($user_params);
        $user->attachRole(Role::OWNER);

        
        $owner = $user->owner()->create($validated);

        $owner_wallet = $owner->ownerWalletDetail()->create(['amount_added'=>0]);
        return response()->json([
            'results' => 'Owner Created Successfully',
        ],201);
    }

    public function edit(Owner $owner) 
    {
        // dd($owner);
        $location = ServiceLocation::active()->get();

        $query = Country::active()->get();

        $countries = fractal($query, new CountryTransformer);

        $result = json_decode($countries->toJson(),true);
        
        $default_country = Country::where('id',$owner->user->country)->first();


        $default_dial_code = $default_country->dial_code;
        $default_country_id = $default_country->id;
        $default_flag = $default_country->flag;

        return Inertia::render('pages/manage_owners/create',['serviceLocation' => $location,
        'default_dial_code'=>$default_dial_code,
        'countries'=>$result['data'],
        'default_flag'=>$default_flag,
        'default_country_id'=>$default_country_id,'owner'=>$owner,'app_for'=>env('APP_FOR')]);
    }
    public function update(Owner $owner, Request $request) 
    {
        $validated = $request->validate([
            'company_name' => 'required',
            'name' => 'required',
            'country' => 'required',
            'email' => 'required',
            'mobile'=>'required|mobile_number|min:8',
            'password' => 'sometimes',
            'service_location_id' => 'required',
            'transport_type' => 'required',
        ]);
        if($request->input('password')){
            $validated['password'] = bcrypt($request->input('password'));
        }
        $token = str_random(40);
        $validated['email_confirmation_token'] = bcrypt($token);
        $user_params = $request->only([
            'mobile','email'
        ]);
        $user_params['name'] = $validated['name'];
        $user_params['country'] = $validated['country'];
        if($request->input('password')){
            $user_params['password'] = $validated['password'];
        }
        $owner->user->update($user_params);
        $owner->update($validated);
        return response()->json([
            'successMessage' => 'Owner Updated Successfully',
        ],201);
    }

    public function editPassword(Owner $owner)
        {
            $location = ServiceLocation::active()->get();

        $query = Country::active()->get();

        $countries = fractal($query, new CountryTransformer);

        $result = json_decode($countries->toJson(),true);
        
        $default_country = Country::where('id',$owner->user->country)->first();


        $default_dial_code = $default_country->dial_code;
        $default_country_id = $default_country->id;
        $default_flag = $default_country->flag;

        return Inertia::render('pages/manage_owners/edit',['serviceLocation' => $location,
        'default_dial_code'=>$default_dial_code,
        'countries'=>$result['data'],
        'default_flag'=>$default_flag,
        'default_country_id'=>$default_country_id,'owner'=>$owner,'app_for'=>env('APP_FOR')]);
        }

        public function updatePasswords(Owner $owner, Request $request)
        {
            // Validate the password and confirmation
            $updated_params = $request->validate([
                'password' => 'required|min:8',  // Confirmed is for password_confirmation
                'confirm_password' => 'required|same:password',
            ]);

            if($request->input('password')){
                $validated['password'] = bcrypt($request->input('password'));
            }
            if($request->input('password')){
                $user_params['password'] = $validated['password'];
            }
            $owner->user->update($user_params);
            $owner->update($validated);
        }

    public function delete(Owner $owner) {
        $owner->delete();
        $owner->user->delete();
        return response()->json([
            'successMessage' => 'Owner Deleted Successfully',
            'serviceLocations' =>ServiceLocation::active()->get(),
        ],201);
    }
    public function document(Owner $owner) 
    {

        // Fetch uploaded documents
        $ownerDocuments = $owner->ownerDocument ?: collect(); // Default to empty collection if null
        $ownerDocuments = $ownerDocuments->keyBy('document_id'); // Key by document_id for easy lookup
    
        // Fetch required documents
        $ownerNeededDocuments = OwnerNeededDocument::where('active', true)->get();
    
        // Merge data
        $documents = $ownerNeededDocuments->map(function ($doc) use ($ownerDocuments) {
            $uploadedDoc = $ownerDocuments->get($doc->id);
            return [
                'id' => $doc->id,
                'name' => $doc->name,
                'doc_type' => $doc->doc_type,
                'has_identify_number' => $doc->has_identify_number,
                'has_expiry_date' => $doc->has_expiry_date,
                'active' => $doc->active,
                'identify_number_locale_key' => $doc->identify_number_locale_key,
                'uploaded' => $uploadedDoc ? true : false,
                'expiry_date' => $uploadedDoc->expiry_date ?? null,
                'identify_number' => $uploadedDoc->identify_number ?? null,
                'document_status' => $uploadedDoc->document_status ?? null,
                'comment' => $uploadedDoc->comment ?? null,
                'image' => $uploadedDoc->image ?? null,
                'back_image' => $uploadedDoc->back_image ?? null,
            ];
        });

        // dd($documents);
    
        return Inertia::render('pages/manage_owners/document', [
            'documents' => $documents,
            'ownerId' => $owner->id,
        ]);

    }
    public function documentUpload(OwnerNeededDocument $document, Owner $ownerId)
    {
        $uploaded = $ownerId->ownerDocument()->where('document_id', $document->id)->first();

// dd($document);
    return Inertia::render('pages/manage_owners/document_upload',['ownerId'=>$ownerId,
    'uploaded'=>$uploaded, 'document'=>$document,]);

    }
    public function documentUploadStore(Request $request, OwnerNeededDocument $document, Owner $ownerId,)
    {

        // dd($request->all());
        $created_params = $request->only(['identify_number']);

        $created_params['owner_id'] = $ownerId->id;
        $created_params['document_id'] = $document->id;

        $created_params['expiry_date'] = null;


        if($request->expiry_date!=null)
        {
            $expiry_date = Carbon::parse($request->expiry_date)->toDateTimeString();

            $created_params['expiry_date'] = $expiry_date;
        }


        if ($uploadedFile = $request->file('image')) {
            $created_params['image'] = $this->imageUploader->file($uploadedFile)
                ->saveOwnerDocument($ownerId->id);
        }

        if ($uploadedFile = $request->file('back_image')) {
            $created_params['back_image'] = $this->imageUploader->file($uploadedFile)
                ->saveOwnerDocument($ownerId->id);
        }
        // dd($created_params);

        // Check if document exists
        $owner_documents = OwnerDocument::where('owner_id', $ownerId->id)->where('document_id', $document->id)->first();

        if ($owner_documents) {
            $created_params['document_status'] = DriverDocumentStatus::REUPLOADED_AND_WAITING_FOR_APPROVAL;
            OwnerDocument::where('owner_id', $ownerId->id)->where('document_id', $document->id)->update($created_params);
        } else {
            $created_params['document_status'] = DriverDocumentStatus::UPLOADED_AND_WAITING_FOR_APPROVAL;
            OwnerDocument::create($created_params);
        }


        // Optionally, return a response
        return response()->json([
            'successMessage' => 'Owner Document uploaded successfully.',
                'ownerId'=>$ownerId,
                'document'=>$document
                ], 201);

    }
    public function approvOwnerDocument($documentId,$ownerId,$status)
    {
        $owner = Owner::find($ownerId);

        $ownerDoc = OwnerDocument::where('owner_id', $ownerId)->where('document_id', $documentId)->first();

        if (!$ownerDoc) {
            return response()->json([
                'status' => 'failure',
                'message' => 'Document not found for the given driver.'
            ], 404); // Return a 404 status code for better semantics
        }

        $ownerDoc->update(['document_status' => $status]);


        $documentStatuses = $owner->ownerDocument->pluck('document_status');
        if($status==1)
        {
       
            $allDocumentsApproved = $documentStatuses->every(function ($value) {
                return $value == 1;
            });
            // dd($allDocumentsApproved);
            if ($allDocumentsApproved)
            {
                $owner->update(['approve'=>1]);
    
                $this->database->getReference('owners/owner_' . $owner->id)
                ->update(['approve' => 1, 'updated_at' => Database::SERVER_TIMESTAMP]);
        
                // $title = custom_trans('driver_approved', [], $owner->user->lang);
                // $body = custom_trans('driver_approved_body', [], $owner->user->lang);
            
                // dispatch(new SendPushNotification($owner->user, $title, $body));

                 $notification = \DB::table('notification_channels')
                ->where('topics', 'Driver Account Approval') // Match the correct topic
                ->first();

            //    send push notification 
                if ($notification && $notification->push_notification == 1) {
                     // Determine the user's language or default to 'en'
                    $userLang = $owner->user->lang ?? 'en';
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
                    dispatch(new SendPushNotification($owner->user, $title, $body));
                }
                return redirect()->route('manageowners.index');
           }
    
        }else{
            $allDocumentsDisapproved = $documentStatuses->every(function ($value) {
                return $value == 5;
            });
    
            if ($allDocumentsDisapproved){
                $owner->update(['approve'=>0]);
        
                $this->database->getReference('owners/owner_' . $owner->id)
                ->update(['approve' => 0, 'updated_at' => Database::SERVER_TIMESTAMP]);
        

                // $title = custom_trans('driver_declined_title', [], $owner->user->lang);
                // $body = custom_trans('driver_declined_body', [], $owner->user->lang);
            
                // dispatch(new SendPushNotification($owner->user, $title, $body)); 
                
                $notification = \DB::table('notification_channels')
                ->where('topics', 'Account Disapproval') // Match the correct topic
                ->first();

            //    send push notification 
                if ($notification && $notification->push_notification == 1) {
                     // Determine the user's language or default to 'en'
                    $userLang = $owner->user->lang ?? 'en';
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
                    dispatch(new SendPushNotification($owner->user, $title, $body));
                }
                return redirect()->route('manageowners.index');
            }
        }


    
        return response()->json([
            'status' => 'success',
            'message' => 'Owner document approved successfully.'
        ]);


// dd($owner);

    }
    public function updateAndApprove(Owner $ownerId)
    {
        $documentStatuses = $ownerId->ownerDocument->pluck('document_status');

         // Handle the case where no document statuses exist
         if ($documentStatuses->isEmpty()) {           
            return response()->json(['message' => 'No documents found. Update not performed.']);
        }
       
        $allDocumentsApproved = $documentStatuses->every(function ($value) {
            return $value == 1;
        });
        // dd($allDocumentsApproved);
        if ($allDocumentsApproved)
        {
            $ownerId->update(['approve'=>1]);

    
            $this->database->getReference('owners/owner_' . $ownerId->id)
            ->update(['approve' => 1, 'updated_at' => Database::SERVER_TIMESTAMP]);
    

        // $title = custom_trans('driver_approved', [], $ownerId->user->lang);
        // $body = custom_trans('driver_approved_body', [], $ownerId->user->lang);
    
        // dispatch(new SendPushNotification($ownerId->user, $title, $body));

        $notification = \DB::table('notification_channels')
                ->where('topics', 'Driver Account Approval') // Match the correct topic
                ->first();

            //    send push notification 
                if ($notification && $notification->push_notification == 1) {
                     // Determine the user's language or default to 'en'
                    $userLang = $ownerId->user->lang ?? 'en';
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
                    dispatch(new SendPushNotification($ownerId->user, $title, $body));
                }


            return response()->json([
                'successMessage' => 'Owner  Approved successfully',
            ]);

        }else{
            // dd("Else ");

            return response()->json([
                'failureMessage' => 'Please Upload All Documents',
            ]);

        }
// dd($ownerId);

    }
    public function ownerPaymentHistory() {
        return Inertia::render('pages/manage_owners/owner_payment_history');
    }
//withdrwal request List
    public function WithdrawalRequestOwnersIndex()
    {
        return Inertia::render('pages/withdrawal_request_owners/index',['app_for'=>env("APP_FOR"),]);
    }

    public function WithdrawalRequestOwnersViewDetails(Owner $owner)
    {
        $walletBalance = $owner->ownerWalletDetail ? $owner->ownerWalletDetail->amount_balance : 0;
// dd($owner->ownerWalletDetail);
        $bankDetails = [
            'account_holder_name' => $owner->name,
        ];

        $methods = Method::with('fields')->get(); // Fetch all methods with their fields
        $bankInfos = $owner->bankInfoDetail;
// dd($bankInfos);
        $formattedBankInfos = $methods->map(function ($method) use ($bankInfos) {
            $fields = $method->fields->map(function ($field) use ($bankInfos) {
                $info = $bankInfos->firstWhere('field_id', $field->id);

                return [
                    'field_name' => $field->input_field_name,
                    'value' => $info->value ?? null,
                ];
            });

            return [
                'method_name' => $method->method_name,
                'fields' => $fields,
            ];
        });

        // dd($formattedBankInfos);

        return Inertia::render('pages/withdrawal_request_owners/view_in_detail', [
            'app_for' => env("APP_FOR"),
            'walletBalance' => $walletBalance,
            'bankDetails' => $bankDetails,
            'owner_id' => $owner->id,
            'formattedBankInfos' => $formattedBankInfos,
        ]);
    }

    public function WithdrawalRequestOwnersList(QueryFilterContract $queryFilter)
    {


        $query = WalletWithdrawalRequest::whereHas('ownerDetail.user',function($query){
            $query->companyKey();
            })->orderBy('created_at','desc')->with('ownerDetail');


        $results =  $queryFilter->builder($query)->customFilter(new OwnerFilter())->paginate();
        $items = fractal($results->items(), new WalletWithdrawalRequestsTransformer)->toArray();
        $results->setCollection(collect($items['data']));
        // dd($results);

        return response()->json([
            'results' => $results->items(),
            'paginator' => $results,
        ]);  
    }   
    //WithdrawalRequestAmount 
    public function WithdrawalRequestAmount(QueryFilterContract $queryFilter, Owner $owner_id)
    {
        // Debugging driver_id for confirmation
        //dd($driver_id);

        $query = WalletWithdrawalRequest::whereHas('ownerDetail.user', function($query) {
            $query->companyKey();
        })
        ->where('owner_id', $owner_id->id) // Filter by driver_id
        ->orderBy('created_at', 'desc')
        ->with('ownerDetail');

        $results = $queryFilter->builder($query)->customFilter(new OwnerFilter())->paginate();
        $items = fractal($results->items(), new WalletWithdrawalRequestsTransformer)->toArray();
        $results->setCollection(collect($items['data']));

        return response()->json([
            'results' => $results->items(),
            'paginator' => $results,
        ]);
    }
    public function updatePaymentStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:wallet_withdrawal_requests,id',
            'status' => 'required|in:approved,declined',
        ]);

        $wallet_withdrawal_request = WalletWithdrawalRequest::findOrFail($request->id);

        if ($request->status === 'approved') {
            // Handle approval logic
            $owner_wallet = OwnerWallet::firstOrCreate(['user_id' => $wallet_withdrawal_request->owner_id]);
            $owner_wallet->amount_spent += $wallet_withdrawal_request->requested_amount;
            $owner_wallet->amount_balance -= $wallet_withdrawal_request->requested_amount;
            $owner_wallet->save();

            $wallet_withdrawal_request->ownerDetail->ownerWalletHistoryDetail()->create([
                'amount' => $wallet_withdrawal_request->requested_amount,
                'transaction_id' => str_random(6),
                'remarks' => WalletRemarks::WITHDRAWN_FROM_WALLET,
                'is_credit' => false,
            ]);

            $wallet_withdrawal_request->status = 1; // Approved

            $user = $owner_wallet->owner->user;
            // $title = custom_trans('payment_credited',[],$user->lang);
            // $body = custom_trans('payment_credited_body',[],$user->lang);
            // $push_data = ['notification_enum'=>"payment_credited"];
        
            // dispatch(new SendPushNotification($user, $title, $body,$push_data));

            $notification = \DB::table('notification_channels')
                ->where('topics', 'Driver Withdrawal Request Approval') // Match the correct topic
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
            $push_data = ['notification_enum'=>"payment_credited"];
                    dispatch(new SendPushNotification($user, $title, $body, $push_data));
                }

        } elseif ($request->status === 'declined') {
            $wallet_withdrawal_request->status = 2; // Declined

            $owner_wallet = OwnerWallet::firstOrCreate(['user_id' => $wallet_withdrawal_request->owner_id]);


            $user = $owner_wallet->driver->user;
            // $title = custom_trans('payment_declained',[],$user->lang);
            // $body = custom_trans('payment_declained_body',[],$user->lang);
            // $push_data = ['notification_enum'=>"payment_declained"];
        
            // dispatch(new SendPushNotification($user, $title, $body,$push_data));

            $notification = \DB::table('notification_channels')
                ->where('topics', 'Driver Withdrawal Request Decline') // Match the correct topic
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
                     $push_data = ['notification_enum'=>"payment_declained"];
                    dispatch(new SendPushNotification($user, $title, $body,$push_data));
                }

        }

        $wallet_withdrawal_request->payment_status = $request->status;
        $wallet_withdrawal_request->save();

        return response()->json([
            'successMessage' => 'Owner payment status updated successfully.',
        ]);
    }
}
