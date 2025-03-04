<?php

namespace App\Http\Controllers;

use App\Base\Filters\Master\CommonMasterFilter;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use App\Http\Controllers\Web\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Admin\LandingContact;
use App\Models\Admin\LandingEmail;
use App\Models\Admin\LandingHeader;
use Illuminate\Support\Facades\Validator;
use App\Base\Services\ImageUploader\ImageUploaderContract;
use DB;
use Auth;
use Session;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use App\Base\Services\ImageUploader\ImageUploader;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\Mailer\LandingContactEmail;


class LandingContactController extends BaseController
{
    protected $imageUploader;

    protected $landingContact;

 

    public function __construct(LandingContact $landingContact, ImageUploaderContract $imageUploader)
    {
        $this->landingContact = $landingContact;
        $this->imageUploader = $imageUploader;
    }

    public function index()
    {
        return Inertia::render('pages/landingsite/contact/index');
    }


    // List of Vehicle Type
    public function list(QueryFilterContract $queryFilter, Request $request)
    {
        $query = LandingContact::query();
// dd("ssss");
        $results = $queryFilter->builder($query)->paginate();

        return response()->json([
            'results' => $results->items(),
            'paginator' => $results,
        ]);
    }
    public function create()
    {

        return Inertia::render('pages/landingsite/contact/create',['app_for'=>env('APP_FOR'),]);
    }
    public function store(Request $request)
    {
         // Validate the incoming request
         $request->validate([
            'hero_title' => 'required',
            'contact_heading' => 'required',
            'contact_para' => 'required',
            'contact_address_title' => 'required',
            'contact_address' => 'required',
            'contact_phone_title' => 'required',
            'contact_phone' => 'required',
            'contact_mail_title' => 'required',
            'contact_mail' => 'required',
            'contact_web_title' => 'required',
            'contact_web' => 'required',
            'form_name' => 'required',
            'form_mail' => 'required',
            'form_subject' => 'required',
            'form_message' => 'required',
            'form_btn' => 'required',
            'locale' => 'required',
            'language' => 'required',
        ]);

        $created_params = $request->all();

        LandingContact::create($created_params);

        // Optionally, return a response
        return response()->json([
            'successMessage' => 'Landing Contact Content created successfully.'
        ], 201);
    }

    public function edit($id)
    {

        $landingContact = LandingContact::find($id);
        return Inertia::render(
            'pages/landingsite/contact/create',
            ['landingContact' => $landingContact,'app_for'=>env('APP_FOR'),]
        );
    }
    public function update(Request $request, LandingContact $landingContact)
    {

         // Validate the incoming request
         $request->validate([
            'hero_title' => 'required',
            'contact_heading' => 'required',
            'contact_para' => 'required',
            'contact_address_title' => 'required',
            'contact_address' => 'required',
            'contact_phone_title' => 'required',
            'contact_phone' => 'required',
            'contact_mail_title' => 'required',
            'contact_mail' => 'required',
            'contact_web_title' => 'required',
            'contact_web' => 'required',
            'form_name' => 'required',
            'form_mail' => 'required',
            'form_subject' => 'required',
            'form_message' => 'required',
            'form_btn' => 'required',
            'locale' => 'required',
            'language' => 'required',
        ]);
        
        $updated_params = $request->all();

        $landingContact->update($updated_params);

        // Optionally, return a response
        return response()->json([
            'successMessage' => 'Contact Content updated successfully.',
            'landingContact' => $landingContact,
        ], 201);

    }
    public function destroy(LandingContact $landingContact)
    {
        $landingContact->delete();

        return response()->json([
            'successMessage' => 'Contact  deleted successfully',
        ]);
    }  

    public function contactpage(Request $request)
    {
        $selectedLocale = $request->input('locale', session('selectedLocale', 'en')); // default to 'en'
        session(['selectedLocale' => $selectedLocale]); // store the selected locale in the session
        $landingContact = LandingContact::where('locale', $selectedLocale)->first();
        $landingHeader = LandingHeader::where('locale', $selectedLocale)->first();

        return Inertia::render('pages/landing/contact', [
            'landingContact' => $landingContact,
            'landingHeader' => $landingHeader,
            'locales' => $this->getLocales(),
        ]);
    }
    
    private function getLocales()
    {
        return LandingHeader::pluck('locale', 'id');
    }





public function contact_message(Request $request)
{
    // Validate the incoming request
    $request->validate([
        'name' => 'required',
        'mail' => 'required',
        'subject' => 'required',
        'comments' => 'required',
        // 'recaptchaResponse' => 'required',
        'recaptchaResponse' => $request->has('recaptchaResponse') ? 'required' : 'nullable',
    ]);
    // $recaptchaResponse = $request->recaptchaResponse;
    // $secret_Key = config('services.recaptcha.secret');
    // // dd( $secret_Key);

    // // Make a POST request to Google's reCAPTCHA API
    // $recaptcharesponse = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
    //     'secret'   => $secret_Key,
    //     'response' => $recaptchaResponse,
    // ]);

    // Validate reCAPTCHA if present
    if ($request->has('recaptchaResponse')) {
        $recaptchaResponse = $request->recaptchaResponse;
        $secret_Key = config('services.recaptcha.secret');

        $recaptchaVerification = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret'   => $secret_Key,
            'response' => $recaptchaResponse,
        ])->json();

        if (empty($recaptchaVerification['success'])) {
            return response()->json(['error' => 'reCAPTCHA validation failed.'], 422);
        }
    }



    // Decode the response body to get JSON data
    // $responseData = $recaptcharesponse->json();
    

    // Check if the reCAPTCHA verification was successful
    // if (!$responseData['success']) {
    //     return response()->json(['error' => 'reCAPTCHA validation failed.'], 422);
    // }

    // return response()->json(['success' => 'reCAPTCHA validated successfully.']);

    // Create a new message
    $created_params = $request->only(['name', 'mail', 'subject', 'comments']);
    LandingEmail::create($created_params);

    // Optionally, send the email
    Mail::to( env('MAIL_FROM_ADDRESS'))->send(new LandingContactEmail($created_params));

    // Return a response
    return response()->json([
        'successMessage' => 'Message created successfully.',
        'created_params' => $created_params,
    ], 201);
}

}