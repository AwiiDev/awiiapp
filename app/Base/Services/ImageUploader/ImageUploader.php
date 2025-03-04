<?php

namespace App\Base\Services\ImageUploader;

use App\Base\Services\Hash\HashGeneratorContract;
use App\Base\Constants\Setting\Settings;
use Exception;
use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;



class ImageUploader implements ImageUploaderContract
{


    protected $encoder;

    /**
     * The HashGenerator instance.
     *
     * @var \App\Base\Services\Hash\HashGeneratorContract
     */
    protected $hashGenerator;

    /**
     * The config array.
     *
     * @var array
     */
    protected $config;

    /**
     * Image encoding format.
     *
     * @var string
     */
    protected $format;

    /**
     * Image encoding quality.
     *
     * @var int
     */
    protected $quality = 100;

    /**
     * New image width.
     *
     * @var int|null
     */
    protected $resizeWidth = null;

    /**
     * New image height.
     *
     * @var int|null
     */
    protected $resizeHeight = null;

    /**
     * The uploaded file.
     *
     * @var \Illuminate\Http\UploadedFile|null
     */
    protected $file = null;

    /**
     * ImageUploader constructor.
     *
     * @param \App\Base\Services\Hash\HashGeneratorContract $hashGenerator
     * @param \Illuminate\Config\Repository $config
     */
    public function __construct(HashGeneratorContract $hashGenerator, ConfigRepository $config)
    {
        $this->hashGenerator = $hashGenerator;
        $this->config = $config['base'];
        $this->format = $this->config('uploads.image.encode');
    }

    /**
     * Save the user profile picture.
     *
     * @return string Returns the saved filename
     */
    public function saveProfilePicture()
    {
        $this->validateFile();

        $config = $this->config('user.upload.profile-picture');

        // $this->setDefaultResize(data_get($config, 'image.store_resolution'));

        // $image = $this->encodeImage();

        // $filename = $this->hashGenerator->extension($this->format)->make();

        // $filePath = file_path(data_get($config, 'path'), $filename);

        // Storage::put($filePath, $image);
        $image = $this->file;
        $file_format = $image->getClientOriginalExtension();
        $filename = $this->hashGenerator->extension($file_format)->make();
        $filePath = data_get($config, 'path'); 
        $path = Storage::disk('local')->putFileAs($filePath,$image, $filename);
        return $filename;
    }

    public function saveOwnerDocument($ownerId)
    {
        $this->validateFile();
    
        $config = $this->config('owner.upload.documents');

        $image = $this->file;
        $file_format = $image->getClientOriginalExtension();
        $filename = $this->hashGenerator->extension($file_format)->make();
        $filePath = file_path(data_get($config, 'path') ,$ownerId); 
        $path = Storage::disk('local')->putFileAs($filePath,$image, $filename);
        return $filename;
    
        // $image = $this->file;
        // $file_format = $image->getClientOriginalExtension();
        // $filename = $this->hashGenerator->extension($file_format)->make();
    
        // $filePath = file_path(data_get($config, 'path'), $ownerId);
    
        // Storage::makeDirectory($filePath);
    
        // $path = Storage::putFileAs($filePath, $image, $filename);

        // return $filename;
    }

    /**
     * Save the user profile picture.
     *
     * @return string Returns the saved filename
     */
    public function saveDriverDocument($driver_id)
    {
        $this->validateFile();
    
        $config = $this->config('driver.upload.documents');
    
        $image = $this->file;
        $file_format = $image->getClientOriginalExtension();
        $filename = $this->hashGenerator->extension($file_format)->make();
    
        // Create a file path that includes the driver ID
        $filePath = file_path(data_get($config, 'path'), $driver_id);
    
        // Ensure the directory exists
        Storage::makeDirectory($filePath);
    
        // Store the file in the specified path with the generated filename
        $path = Storage::putFileAs($filePath, $image, $filename);
    
        return $filename;
    }
    
    /**
     * Save the user profile picture.
     *
     * @return string Returns the saved filename
     */
    public function saveFleetDocument($fleet_id)
    {
        $this->validateFile();
    
        $config = $this->config('fleets.upload.images');
    
        $image = $this->file;
        $file_format = $image->getClientOriginalExtension();
        $filename = $this->hashGenerator->extension($file_format)->make();

        $filePath = file_path(data_get($config, 'path'), $fleet_id);
    
        Storage::makeDirectory($filePath);
    
        $path = Storage::putFileAs($filePath, $image, $filename);

        return $filename;
    }

     /**
     * Save the request delivery proof documents.
     *
     * @return string Returns the saved filename
     */
    public function saveRequestDeliveryProof()
    {
        $this->validateFile();

        $config = $this->config('request.upload.delivery-proof');

        $image = $this->file;
        $file_format = $image->getClientOriginalExtension();
        $filename = $this->hashGenerator->extension($file_format)->make();
        $filePath = file_path(data_get($config, 'path'),'');
        $path = Storage::putFileAs($filePath, $image, $filename);

        return $filename;
    }

    /**
     * Save the Driver LevelUp image
     *
     * @return string Returns the saved filename
     */
    public function saveDriverLevelImage()
    {
        $this->validateFile();
    
        $config = $this->config('driver.upload.levels');
    
        $image = $this->file;
        $file_format = $image->getClientOriginalExtension();
        $filename = $this->hashGenerator->extension($file_format)->make();
    
        $filePath = data_get($config, 'path');
    
        Storage::makeDirectory($filePath);
    
        $path = Storage::putFileAs($filePath, $image, $filename);
    
        return $filename;
    }

    /**
     * Save the user profile picture.
     *
     * @return string Returns the saved filename
     */
    public function savePushImage()
    {
        $this->validateFile();

        $config = $this->config('pushnotification.upload.images');

        $image = $this->file;
        $file_format = $image->getClientOriginalExtension();
        $filename = $this->hashGenerator->extension($file_format)->make();
        $filePath = file_path(data_get($config, 'path'),'');
        $path = Storage::putFileAs($filePath, $image, $filename);

        return $filename;
    }

    /**
     * Save the CountryFlag Image.
     *
     * @return string Returns the saved filename
     */
    public function saveCountryFlagImage()
    {
        $this->validateFile();

        $config = $this->config('country.upload.flag');

        // dd($config);

        $this->setDefaultResize(data_get($config, 'image.store_resolution'));

        $image = $this->encodeImage();

        $filename = $this->hashGenerator->extension($this->format)->make();

        $filePath = file_path(data_get($config, 'path'), $filename);

        // dd($filePath);

        Storage::disk('public')->put($filePath, $image);

        // Storage::put($filePath, $image);

        return $filename;
    }
    /**
     * Save the user profile picture.
     *
     * @return string Returns the saved filename
     */
    public function saveSystemAdminLogo()
    {
        $this->validateFile();
        $config = $this->config('system-admin.upload.logo');

        $image = $this->file;
        $file_format = $image->getClientOriginalExtension();
        $filename = $this->hashGenerator->extension($file_format)->make();
        $filePath = data_get($config, 'path'); 
        $path = Storage::disk('local')->putFileAs($filePath, $image, $filename);

        return $filename;
    }

     /**
     * Save the Firebase Setting Json File
     *
     * @return string Returns the array data
     */
    public function saveSystemFirebaseJson()
    {
        $this->validateFile();
        $image = $this->file;
        $this->validateFileTYpe($image);
        $path = $image->path();
        $jsonContent = file_get_contents($path);
        $jsonData = json_decode($jsonContent, true);
        $result = $this->CheckJsonData($jsonData);
        $fileName = "firebase.json";
        if($result['status'])
        {
            $filePath = storage_path('push-configurations/'.$fileName.'');

            if (File::exists($filePath)) {
                 File::delete($filePath);
            }
            $image->move(storage_path('push-configurations'), $fileName);
            $result['file_name'] = $fileName;
        }

        return $result;

    }


    public function saveVehicleTypeImage()
    {
        $this->validateFile(); // Validate the file if needed
    
        $config = $this->config('types.upload.images');
        $image = $this->file; // Assuming $this->file is your uploaded file
        $fileFormat = $image->getClientOriginalExtension();
        $filename = $this->hashGenerator->extension($fileFormat)->make(); // Generate a unique filename
        $filePath = data_get($config, 'path'); // Get the storage path from config
        
        // Store the file in the 'public' disk under the specified path
        $path = Storage::disk('local')->putFileAs($filePath, $image, $filename);
    
        return $filename; // Return the relative path (including filename) for storage in your database
    }
    /**
     * Save the VehicleType Image.
     *
     * @return string Returns the saved filename
     */
    public function saveCompanyImage($admin_id)
    {
        $this->validateFile();

        $config = $this->config('company.upload.images');

        $image = $this->file;
        $file_format = $image->getClientOriginalExtension();
        $filename = $this->hashGenerator->extension($file_format)->make();
        $filePath = file_path(data_get($config, 'path'),'');
        $path = Storage::putFileAs($filePath, $image, $filename);


        return $filename;
    }

    /**
     * Set the uploaded image file to manipulate.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return $this
     */
    public function file(UploadedFile $file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Set the new image size values.
     *
     * @param int|null $width
     * @param int|null $height
     * @return $this
     */
    public function resize($width = null, $height = null)
    {
        $this->resizeWidth = $width;
        $this->resizeHeight = $height;

        return $this;
    }

    /**
     * Set the encoding quality.
     * Value can be between 10 - 100.
     *
     * @param int $quality
     * @return $this
     */
    public function quality($quality)
    {
        $this->quality = $quality;

        return $this;
    }

    /**
     * Set the image encoding format.
     *
     * @param string $format
     * @return $this
     */
    public function format($format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * Get the config value.
     *
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     */
    protected function config($key, $default = null)
    {
        return data_get($this->config, $key, $default);
    }

    /**
     * Check if the file is set.
     *
     * @throws Exception
     */
    protected function validateFile()
    {
        if (is_null($this->file)) {
            throw new Exception('Image uploader: No file provided.');
        }
    }

       /**
     * Check if the file is Json.
     *
     * @throws Exception
     */
    protected function validateFileType($file)
    {
        $extension = $file->getClientOriginalExtension();
        if ($extension != "json") {
            throw new Exception('Please upload the Firebase Settings File as Json Format');
        }
    }

          /**
     * get the json data and check if the data exists
     *
     * @throws Exception
     */
    public function CheckJsonData($json_data)
    {

         if(is_array($json_data))
         {
            If(array_key_exists('type',$json_data) && array_key_exists('project_id',$json_data))
            {

                if($json_data['type'] == "service_account")
                {
                     $response_array = array("status"=>true,"json_data"=>$json_data);
                }
                else
                {
                     $response_array = array("status"=>false,"message"=>"Type Should be Service account in the given Firebase JSON File");
                }
            }
            else{
                 $response_array = array("status"=>false,"message"=>"Type or Project id is missed in the given Json File");
            }
         }
         else{
             $response_array = array("status"=>false,"message"=>"Json File format is wrong in Firebase Setting");

         }


         return $response_array;

    }

    /**
     * Set the default resize values for encoding.
     *
     * @param int $width
     * @param int|null $height
     * @param bool $force
     */
    protected function setDefaultResize($width, $height = null, $force = false)
    {
        if ((is_null($this->resizeWidth) && is_null($this->resizeHeight) || $force)) {
            $this->resizeWidth = $width;
            $this->resizeHeight = $height;
        }
    }

    /**
     * Encode the uploaded image and get the resource handle.
     *
     * @param bool $autoScale
     * @return \Intervention\Image\Image
     */
    protected function encodeImage($autoScale = true)
    {
        return $this->encoder
            ->resize($this->resizeWidth, $this->resizeHeight)
            ->quality($this->quality)
            ->format($this->format)
            ->encode($this->file, $autoScale);
    }

    /**
     * Save the Fleet registration certificate
     *
     * @return string Returns the saved filename
     */
    public function saveFleetRegistrationCertificateImage()
    {
        $this->validateFile();

        $config = $this->config('fleets.upload.images');

        $image = $this->file;
        $file_format = $image->getClientOriginalExtension();
        $filename = $this->hashGenerator->extension($file_format)->make();
        $filePath = file_path(data_get($config, 'path'),'');
        $path = Storage::putFileAs($filePath, $image, $filename);


        return $filename;
    }


    /**
     * Save the Fleet vehicle back side image
     *
     * @return string Returns the saved filename
     */
    public function saveFleetBackSideImage()
    {
        $this->validateFile();

        $config = $this->config('fleets.upload.images');

        $image = $this->file;
        $file_format = $image->getClientOriginalExtension();
        $filename = $this->hashGenerator->extension($file_format)->make();
        $filePath = file_path(data_get($config, 'path'),'');
        $path = Storage::putFileAs($filePath, $image, $filename);

        return $filename;
    }
    /**
     * Save the user profile picture.
     *
     * @return string Returns the saved filename
     */
    public function saveBannerImage()
    {
        $this->validateFile();

        $config = $this->config('bannerimage.upload.images');

        $image = $this->file;
        $file_format = $image->getClientOriginalExtension();
        $filename = $this->hashGenerator->extension($file_format)->make();
        $filePath = data_get($config, 'path'); 
        $path = Storage::disk('local')->putFileAs($filePath, $image, $filename);

        return $filename;
    }


    public function OnboardingImage()
    {
        $this->validateFile();

        $config = $this->config('onboarding.upload');

        $image = $this->file;
        $file_format = $image->getClientOriginalExtension();
        $filename = $this->hashGenerator->extension($file_format)->make();
        $filePath = file_path(data_get($config, 'path'),'');
        $path = Storage::disk('local')->putFileAs($filePath, $image, $filename);

        return $filename;
    }

    public function saveLandingHomeImage()
    {

        $this->validateFile(); // Validate the file if needed
    
        $config = $this->config('website.upload.images');
        $image = $this->file; // Assuming $this->file is your uploaded file
        $fileFormat = $image->getClientOriginalExtension();
        $filename = $this->hashGenerator->extension($fileFormat)->make(); // Generate a unique filename
        $filePath = data_get($config, 'path'); // Get the storage path from config
        
        // Store the file in the 'public' disk under the specified path
        $path = Storage::disk('local')->putFileAs($filePath, $image, $filename);
    
        return $filename; // Return the relative path (including filename) for storage in your database
    }
    
    public function saveLandingHeaderImage()
    {

        $this->validateFile(); // Validate the file if needed
    
        $config = $this->config('website.upload.images');
        $image = $this->file; // Assuming $this->file is your uploaded file
        $fileFormat = $image->getClientOriginalExtension();
        $filename = $this->hashGenerator->extension($fileFormat)->make(); // Generate a unique filename
        $filePath = data_get($config, 'path'); // Get the storage path from config
        
        // Store the file in the 'public' disk under the specified path
        $path = Storage::disk('local')->putFileAs($filePath, $image, $filename);
    
        return $filename; // Return the relative path (including filename) for storage in your database
    }

    public function saveLandingDriverImage()
    {

        $this->validateFile(); // Validate the file if needed
    
        $config = $this->config('website.upload.images');
        $image = $this->file; // Assuming $this->file is your uploaded file
        $fileFormat = $image->getClientOriginalExtension();
        $filename = $this->hashGenerator->extension($fileFormat)->make(); // Generate a unique filename
        $filePath = data_get($config, 'path'); // Get the storage path from config
        
        // Store the file in the 'public' disk under the specified path
        $path = Storage::disk('local')->putFileAs($filePath, $image, $filename);
    
        return $filename; // Return the relative path (including filename) for storage in your database
    }

    public function saveLandingAboutusImage()
    {

        $this->validateFile(); // Validate the file if needed
    
        $config = $this->config('website.upload.images');
        $image = $this->file; // Assuming $this->file is your uploaded file
        $fileFormat = $image->getClientOriginalExtension();
        $filename = $this->hashGenerator->extension($fileFormat)->make(); // Generate a unique filename
        $filePath = data_get($config, 'path'); // Get the storage path from config
        
        // Store the file in the 'public' disk under the specified path
        $path = Storage::disk('local')->putFileAs($filePath, $image, $filename);
    
        return $filename; // Return the relative path (including filename) for storage in your database
    }

    public function saveLandingUserImage()
    {

        $this->validateFile(); // Validate the file if needed
    
        $config = $this->config('website.upload.images');
        $image = $this->file; // Assuming $this->file is your uploaded file
        $fileFormat = $image->getClientOriginalExtension();
        $filename = $this->hashGenerator->extension($fileFormat)->make(); // Generate a unique filename
        $filePath = data_get($config, 'path'); // Get the storage path from config
        
        // Store the file in the 'public' disk under the specified path
        $path = Storage::disk('local')->putFileAs($filePath, $image, $filename);
    
        return $filename; // Return the relative path (including filename) for storage in your database
    }

    public function saveInvoiceLogoImage()
    {

        $this->validateFile(); // Validate the file if needed
    
        $config = $this->config('invoice.upload');
        $image = $this->file; // Assuming $this->file is your uploaded file
        $fileFormat = $image->getClientOriginalExtension();
        $filename = $this->hashGenerator->extension($fileFormat)->make(); // Generate a unique filename
        $filePath = data_get($config, 'path'); // Get the storage path from config
        
        // Store the file in the 'public' disk under the specified path
        $path = Storage::disk('local')->putFileAs($filePath, $image, $filename);
    
        return $filename; // Return the relative path (including filename) for storage in your database
    }


    public function saveEmailTemplateImage()
    {

        $this->validateFile(); // Validate the file if needed
    
        $config = $this->config('website.upload.images');
        $image = $this->file; // Assuming $this->file is your uploaded file
        $fileFormat = $image->getClientOriginalExtension();
        $filename = $this->hashGenerator->extension($fileFormat)->make(); // Generate a unique filename
        $filePath = data_get($config, 'path'); // Get the storage path from config
        
        // Store the file in the 'public' disk under the specified path
        $path = Storage::disk('local')->putFileAs($filePath, $image, $filename);
    
        return $filename; // Return the relative path (including filename) for storage in your database
    }


}
