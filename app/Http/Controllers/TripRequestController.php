<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use App\Models\Request\Request as RequestModel;
use App\Base\Filters\Admin\RequestFilter;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Admin\Zone;
use App\Models\Admin\VehicleType;
use App\Models\Admin\Driver;
use App\Models\ThirdPartySetting;
use Kreait\Firebase\Contract\Database;
use App\Jobs\Notifications\SendPushNotification;
use App\Transformers\Requests\TripRequestTransformer;
use Dompdf\Dompdf;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Admin\Setting;
use App\Models\Admin\InvoiceConfiguration;
use Illuminate\Support\Facades\Mail;
use App\Mail\RideLaterMail;

class TripRequestController extends Controller
{
    protected $database;

    protected $requestmodel;

    public function __construct(Database $database,RequestModel $requestmodel)
    {
        $this->database = $database;
        $this->requestmodel = $requestmodel;
    }
    public function ridesRequest() {
        $ongoing = RequestModel::with('userDetail','driverDetail')
            ->where('is_cancelled', false)
            ->where('is_completed', false)
            ->orderBy('created_at','DESC')->pluck('id');
        $settings = ThirdPartySetting::where('module', 'firebase')->pluck('value', 'name')->toArray();

        $firebaseConfig = (object) [
            'apiKey' => $settings['firebase_api_key'],
            'authDomain' => $settings['firebase_auth_domain'],
            'databaseURL' => $settings['firebase_database_url'],
            'projectId' => $settings['firebase_project_id'],
            'storageBucket' => $settings['firebase_storage_bucket'],
            'messagingSenderId' => $settings['firebase_messaging_sender_id'],
            'appId' => $settings['firebase_app_id'],
            'measurementId' => $settings['firebase_measurement_id'],
        ];
        return Inertia::render('pages/rides_request/index',[
            'zones' => Zone::active()->get(),
            'types' => VehicleType::active()->get(),
            'ongoing_rides' => $ongoing,
            'firebaseConfig' => $firebaseConfig,
            'enable_outstation' => get_settings('show_outstation_ride_feature') || get_settings('show_delivery_outstation_ride_feature'),
        ]);
    }

    public function viewDetails(RequestModel $requestmodel) {
        $settings = ThirdPartySetting::where('module', 'firebase')->pluck('value', 'name')->toArray();
        $onsearch = $requestmodel->on_search;
        $requestmodel = fractal($requestmodel, new TripRequestTransformer)->parseIncludes(['userDetail','driverDetail','requestBill','rejectedDrivers'])->toArray();
        $requestmodel['data']['onSearch'] = $onsearch;
        $firebaseConfig = (object) [
            'apiKey' => $settings['firebase_api_key'],
            'authDomain' => $settings['firebase_auth_domain'],
            'databaseURL' => $settings['firebase_database_url'],
            'projectId' => $settings['firebase_project_id'],
            'storageBucket' => $settings['firebase_storage_bucket'],
            'messagingSenderId' => $settings['firebase_messaging_sender_id'],
            'appId' => $settings['firebase_app_id'],
            'measurementId' => $settings['firebase_measurement_id'],
        ];
        if(get_map_settings('map_type') == "open_street_map"){
            return Inertia::render('pages/rides_request/open-view',
                [
                    'request' => $requestmodel['data'],
                    'service_location'=>null,
                    'app_for' => env('APP_FOR'),
                    'pick_icon'=>asset('image/map/pickup.png'),
                    'drop_icon'=>asset('image/map/drop.png'),
                    'firebaseConfig'=>$firebaseConfig,
                ]);
        }
        $googleMapKey = get_map_settings('google_map_key'); // Retrieve the Google Map API key
        return Inertia::render('pages/rides_request/view',
            [
                'request' => $requestmodel['data'],
                'service_location'=>null,
                'app_for' => env('APP_FOR'),
                'pick_icon'=>asset('image/map/pickup.png'),
                'drop_icon'=>asset('image/map/drop.png'),
                'googleMapKey'=>$googleMapKey,
                'firebaseConfig'=>$firebaseConfig,
            ]
        );
    }
    
    public function list(QueryFilterContract $queryFilter, Request $request)
    {
        $query = RequestModel::where('requests.transport_type', 'taxi')
            ->with('userDetail', 'driverDetail')
            ->orderBy('created_at', 'DESC');
    
        if (auth()->user()->hasRole('owner')) {
            // Retrieve the specific owner associated with the authenticated user
            $owner = auth()->user()->owner;
            $query->where('owner_id', $owner->id)->orWhere('booked_by', auth()->user()->id);
        }
    
        $results = $queryFilter->builder($query)
            ->customFilter(new RequestFilter)
            ->paginate();
    
        // Convert each item to array to include appends
        $items = fractal($results->items(), new TripRequestTransformer)->toArray();    
    
        // Explicitly convert results to array for appended attributes
        $results->setCollection(collect($results->items())->map(fn($item) => $item->toArray()));
    
        return response()->json([
            'results' => $results->items(),
            'paginator' => $results,
        ]);
    }
    
    public function ongoingRidesRequest()
    {
        $ongoing = RequestModel::with('userDetail','driverDetail')
            ->where('is_cancelled', false)
            ->where('is_completed', false)
            ->orderBy('created_at','DESC')->get();
        $settings = ThirdPartySetting::where('module', 'firebase')->pluck('value', 'name')->toArray();

        $firebaseConfig = (object) [
            'apiKey' => $settings['firebase_api_key'],
            'authDomain' => $settings['firebase_auth_domain'],
            'databaseURL' => $settings['firebase_database_url'],
            'projectId' => $settings['firebase_project_id'],
            'storageBucket' => $settings['firebase_storage_bucket'],
            'messagingSenderId' => $settings['firebase_messaging_sender_id'],
            'appId' => $settings['firebase_app_id'],
            'measurementId' => $settings['firebase_measurement_id'],
        ];
        return Inertia::render('pages/ongoing_rides/index',[
            'zones' => Zone::active()->get(),
            'types' => VehicleType::active()->get(),
            'ongoing_rides' => $ongoing,
            'firebaseConfig' => $firebaseConfig,
        ]);
    }
    public function ongoingRideDetail(RequestModel $request)
    {
        $items = fractal($request, new TripRequestTransformer)->toArray();
        return response()->json([
            'result' => $items['data'],
            'current_time' => get_converted_time(now(),$request->timezone),
        ]);
    }
    public function assignView(RequestModel $request)
    {
        if($request->is_cancelled || $request->driver_id){
            return redirect('rides-request/view/'.$request->id);
        }
        $firebaseSettings = [
            'firebase_api_key' => get_firebase_settings('firebase_api_key'),
            'firebase_auth_domain' => get_firebase_settings('firebase_auth_domain'),
            'firebase_database_url' => get_firebase_settings('firebase_database_url'),
            'firebase_project_id' => get_firebase_settings('firebase_project_id'),
            'firebase_storage_bucket' => get_firebase_settings('firebase_storage_bucket'),
            'firebase_messaging_sender_id' => get_firebase_settings('firebase_messaging_sender_id'),
            'firebase_app_id' => get_firebase_settings('firebase_app_id'),
            'firebase_measurement_id' => get_firebase_settings('firebase_measurement_id'),
        ];
        $item = fractal($request, new TripRequestTransformer)->parseIncludes(['userDetail','driverDetail'])->toJson();
        $request = json_decode($item);
        if(get_map_settings('map_type') == 'open_street_map'){
            return Inertia::render('pages/ongoing_rides/assign-open',[
                'result' => $request->data,
                'app_for' => env('APP_FOR'),
                'firebaseSettings' => $firebaseSettings,
            ]);
        }
        $map_key = get_map_settings('google_map_key');
        return Inertia::render('pages/ongoing_rides/assign',[
            'map_key' => $map_key,
            'app_for' => env('APP_FOR'),
            'result' => $request->data,
            'firebaseSettings' => $firebaseSettings,
        ]);
    }
    public function assignDriver(RequestModel $requestmodel,Request $request) {
        $assigned = $requestmodel->is_cancelled || $requestmodel->is_completed || $requestmodel->driver_id || $requestmodel->requestMeta()->exists();
        if($assigned) {
            return response()->json(['status'=>false,'message'=>'Cannot Assign Request']);
        }
        $request->validate([
            'driver_id'  => 'required' 
        ]);
        $driver = Driver::find($request->driver_id);
        
        if(!$driver) {
            return response()->json(['status'=>false,'message'=>'Cannot Assign Driver']);
        }
        $selected_drivers["user_id"] = $requestmodel->user_id;
        $selected_drivers["driver_id"] = $driver->id;
        $selected_drivers["active"] = 1;
        $selected_drivers["assign_method"] = 1;
        $selected_drivers["created_at"] = date('Y-m-d H:i:s');
        $selected_drivers["updated_at"] = date('Y-m-d H:i:s');

        $requestmodel->requestMeta()->create($selected_drivers);
        $this->database->getReference('request-meta/'.$requestmodel->id)
                    ->set([
                            'driver_id'=>$driver->id,
                            'request_id'=>$requestmodel->id,
                            'user_id'=>$requestmodel->user_id,
                            'active'=>1,
                            'transport_type'=>"taxi",
                            'updated_at'=> Database::SERVER_TIMESTAMP
                        ]);
        $requestmodel->update(['assign_method'=>1, 'accepted_ride_fare'=>$requestmodel->offerred_ride_fare,'is_bid_ride'=>false]);


        $notifable_driver = $driver->user;

        // $title = custom_trans('new_request_title',[],$notifable_driver->lang);
        // $body = custom_trans('new_request_body',[],$notifable_driver->lang);
        // $push_data = ['title' => $title,'message' => $body,'push_type'=>'meta-request'];

        // dispatch(new SendPushNotification($notifable_driver,$title,$body,$push_data));

        $user = $requestmodel->userDetail;
        if ($requestmodel->is_later) { 
        $notification = \DB::table('notification_channels')
            ->where('topics', 'User Ride Later') // Match the correct topic
            ->first();
    
            // dd($notification);
    
            if ($notification && $notification->mail == 1) {

                $userLang = $notifable_driver->lang ?? 'en';
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
                Mail::to($user->email)->send(new RideLaterMail($user, $notificationData));
            }
        }


        $notification = \DB::table('notification_channels')
            ->where('topics', 'User Ride Later') // Match the correct topic
            ->first();

        //   send push notification 
                if ($notification && $notification->push_notification == 1) {
                     // Determine the user's language or default to 'en'
                    $userLang = $notifable_driver->lang ?? 'en';
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
        $push_data = ['title' => $title,'message' => $body,'push_type'=>'meta-request'];
                    dispatch(new SendPushNotification($notifable_driver,$title,$body,$push_data));
                }
        return response()->json(['status'=>true,'message'=>'Assigned Successfully']);
    }
    public function driverFind(Driver $driver,Request $request)
    {
        $request = RequestModel::find($request->request_id);
        return response()->json([
            'successMessage' => 'Driver Found successfully',
            'driver' => $driver,
            'current_time' => get_converted_time(now(),$request->timezone),
        ]);
    }
    public function cancelRide(RequestModel $requestmodel) {
        $update_parms['is_cancelled'] = true;
        $update_parms['cancelled_at'] = date('Y-m-d H:i:s');
        $update_parms['cancel_method'] = 0;
        if($requestmodel->driver_id){
            Driver::where('id',$requestmodel->driver_id)->update(['available'=>true]);
        }
        if($requestmodel->driverDetail)
        {
            $notifiable_driver = $requestmodel->driverDetail->user;
            // $title = custom_trans('trip_cancelled_by_user_title',[],$notifiable_driver->lang);
            // $body = custom_trans('trip_cancelled_by_user_body',[],$notifiable_driver->lang);
            // dispatch(new SendPushNotification($notifiable_driver,$title,$body));

            $notification = \DB::table('notification_channels')
                ->where('topics', 'Trip Cancelled') // Match the correct topic
                ->first();

            //    send push notification 
                if ($notification && $notification->push_notification == 1) {
                     // Determine the user's language or default to 'en'
                    $userLang = $notifiable_driver->lang ?? 'en';
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
                    dispatch(new SendPushNotification($notifiable_driver, $title, $body));
                }
        }
        $this->database->getReference('requests/' . $requestmodel->id)->update(['is_cancelled' => true, 'cancelled_by_user' => true]);
        $this->database->getReference('requests/' . $requestmodel->id)->remove();
        $this->database->getReference('SOS/' . $requestmodel->id)->remove();
        $this->database->getReference('request-meta/' . $requestmodel->id)->remove();
        $requestmodel->update($update_parms);
        $requestmodel->requestMeta()->delete();

        return response()->json([
            'successMessage' => 'Trip Cancelled successfully',
            'request' => $requestmodel,
        ]);
    }
    public function sosDetail(RequestModel $request)
    {
        if($request->is_cancelled || $request->is_completed) {
            return response()->json(['message'=>'Invalid SOS'],422);
        }
        $result = json_decode(fractal($request, new TripRequestTransformer)->parseIncludes(['userDetail','driverDetail'])->toJson());
        return response()->json([
            'successMessage' => 'Ride Found successfully',
            'request' => $result->data,
            'current_time' => get_converted_time(now(),$request->timezone),
        ]);
    }
    public function trackRequest(RequestModel $request)
    {
        $settings = ThirdPartySetting::where('module', 'firebase')->pluck('value', 'name')->toArray();
        $onsearch = $request->on_search;
        $requestmodel = fractal($request, new TripRequestTransformer)->parseIncludes(['userDetail','driverDetail','requestBill','rejectedDrivers'])->toArray();
        $requestmodel['data']['onSearch'] = $onsearch;

        $firebaseConfig = (object) [
            'apiKey' => $settings['firebase_api_key'],
            'authDomain' => $settings['firebase_auth_domain'],
            'databaseURL' => $settings['firebase_database_url'],
            'projectId' => $settings['firebase_project_id'],
            'storageBucket' => $settings['firebase_storage_bucket'],
            'messagingSenderId' => $settings['firebase_messaging_sender_id'],
            'appId' => $settings['firebase_app_id'],
            'measurementId' => $settings['firebase_measurement_id'],
        ];

        if(get_map_settings('map_type') == "open_street_map"){
            return Inertia::render('pages/rides_request/open-track-request',
                [
                    'request' => $requestmodel['data'],
                    'service_location'=>null,
                    'app_for' => env('APP_FOR'),
                    'pick_icon'=>asset('image/map/pickup.png'),
                    'drop_icon'=>asset('image/map/drop.png'),
                    'firebaseConfig'=>$firebaseConfig,
                ]);
        }
        
        $googleMapKey = get_map_settings('google_map_key');

        return Inertia::render('pages/rides_request/track-request',
            [
                'request' => $requestmodel['data'],
                'service_location'=>null,
                'app_for' => env('APP_FOR'),
                'pick_icon'=>asset('image/map/pickup.png'),
                'drop_icon'=>asset('image/map/drop.png'),
                'googleMapKey'=>$googleMapKey,
                'firebaseConfig'=>$firebaseConfig,
            ]
        );
    }

    // public function downloadInvoice(RequestModel $requestmodel, Request $request)
    // {
    //     $requestmodel = fractal($requestmodel, new TripRequestTransformer)->parseIncludes(['userDetail','driverDetail','requestBill','rejectedDrivers'])->toArray();
    //     try {
    //         if ($request->invoice_type === "user") {
    //             // Prepare data for user invoice
    //             $data = $requestmodel;
    // // dd($data);
    //             // Generate PDF for user invoice
    //             $pdf = Pdf::loadView('emails.invoice', compact('data'));
    
    //             // Return the PDF as a download
    //             return $pdf->download('user_invoice.pdf');
    //         } elseif ($request->invoice_type === "driver") {
    //             // Prepare data for driver invoice
    //             $data = $requestmodel;
    
    //             // Generate PDF for driver invoice
    //             $pdf = Pdf::loadView('emails.driver_invoice', compact('data'));
    
    //             // Return the PDF as a download
    //             return $pdf->download('driver_invoice.pdf');
    //         }
    
    //         // Handle invalid invoice type
    //         return response()->json(['error' => 'Invalid invoice type'], 400);
    //     } catch (\Exception $e) {
    //         // Handle exceptions
    //         return response()->json(['error' => 'Failed to generate invoice: ' . $e->getMessage()], 500);
    //     }
    // }

    public function downloadInvoice(RequestModel $requestmodel, Request $request)
{
    $requestmodel = fractal($requestmodel, new TripRequestTransformer)->parseIncludes(['userDetail', 'driverDetail', 'requestBill', 'rejectedDrivers'])->toArray();
    $logo = Setting::where('name', 'logo')->first();
    $invoice_configuration = ThirdPartySetting::where('module', 'mail_config')->pluck('value', 'name')->toArray();
    try {
        if ($request->invoice_type === "user") {
            $data = $requestmodel;
            $img = $logo;
            $invoice = $invoice_configuration;
            // dd($invoice);
             // Format completed_at for the view
             $data['formatted_completed_at'] = isset($requestmodel['completed_at']) 
             ? Carbon::parse($requestmodel['completed_at'])
                 ->setTimezone(env('SYSTEM_DEFAULT_TIMEZONE', 'Asia/Kolkata'))
                 ->format('M j, Y - h:i A') 
             : null;

            // Return user invoice Blade view
            return view('emails.invoice', compact('data','logo','invoice'));
        } elseif ($request->invoice_type === "driver") {
            $data = $requestmodel;
            $img = $logo;
            $invoice = $invoice_configuration;

            // Return driver invoice Blade view
            return view('emails.driver_invoice', compact('data','logo','invoice'));
        }

        // Handle invalid invoice type
        return response()->json(['error' => 'Invalid invoice type'], 400);
    } catch (\Exception $e) {
        // Handle exceptions
        return response()->json(['error' => 'Failed to generate invoice: ' . $e->getMessage()], 500);
    }
}
}
