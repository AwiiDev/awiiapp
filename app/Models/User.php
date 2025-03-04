<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Country;
use App\Models\Access\Role;
use App\Models\Admin\Driver;
use App\Models\Admin\Owner;
use App\Models\Request\Request;
use App\Models\Master\Developer;
use App\Models\Master\PocClient;
use App\Models\Traits\HasActive;
use App\Models\Admin\AdminDetail;
use App\Models\Admin\UserDetails;
use App\Models\Payment\UserWallet as UserWalletDetail;
use App\Models\LinkedSocialAccount;
use App\Models\Payment\DriverWallet;
use App\Base\Services\OTP\CanSendOTP;
use App\Models\Traits\DeleteOldFiles;
use App\Models\Traits\UserAccessTrait;
use Illuminate\Support\Facades\Storage;
use App\Models\Payment\UserWalletHistory;
use App\Models\Traits\HasActiveCompanyKey;
use App\Models\Traits\UserAccessScopeTrait;
use App\Base\Services\OTP\CanSendOTPContract;
use Nicolaslopezj\Searchable\SearchableTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Request\FavouriteLocation;
use App\Models\Payment\UserBankInfo;
use App\Models\Payment\WalletWithdrawalRequest;
use App\Models\Payment\RewardHistory;
use App\Models\Payment\RewardPoint;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\FoodDelivery\Stores;
use App\Models\Admin\ServiceLocation;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Request\RecentSearch;
use Illuminate\Notifications\Notifiable;
use App\Models\Payment\CardInfo;


class User extends Authenticatable implements CanSendOTPContract
{
    use CanSendOTP,
    DeleteOldFiles,
    HasActive,
    Notifiable,
    SoftDeletes,
    UserAccessScopeTrait,
    UserAccessTrait,
    SearchableTrait,
    HasActiveCompanyKey,HasApiTokens;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'username', 'email','gender', 'password', 'mobile', 'country', 'profile_picture', 'email_confirmed', 'mobile_confirmed', 'email_confirmation_token', 'active','fcm_token','login_by','apn_token','timezone','rating','rating_total','no_of_ratings','refferal_code','referred_by','social_nickname','social_id','social_token','social_token_secret','social_refresh_token','social_expires_in','social_avatar','social_avatar_original','social_provider','company_key','lang','current_lat','current_lng','ride_otp','stripe_customer_id','map_type',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'email_confirmation_token',
    ];

    /**
     * The attributes that have files that should be auto deleted on updating or deleting.
     *
     * @var array
     */
    public $deletableFiles = [
        'profile_picture',
    ];

    /**
     * The attributes that can be used for sorting with query string filtering.
     *
     * @var array
     */
    public $sortable = [
        'converted_created_at', 'id', 'name', 'username', 'email', 'mobile', 'profile_picture', 'last_login_at', 'created_at', 'updated_at','current_lat','current_lng'
    ];

    /**
     * The relationships that can be loaded with query string filtering includes.
     *
     * @var array
     */
    public $includes = [
        'roles', 'otp','requestDetail','recentSearchesList'
    ];

    /**
    * The accessors to append to the model's array form.
    *
    * @var array
    */
    protected $appends = [
        'country_name'
    ];

    /**
    * Get the Profile image full file path.
    *
    * @param string $value
    * @return string
    */
 
    public function getProfilePictureAttribute($value)
    {
        if (!$value) {
            // Check gender and return the appropriate default image
            if ($this->gender === 'female') {
                $default_image_path = config('base.default.female-user.profile_picture');

            } else {
                $default_image_path = config('base.default.male-user.profile_picture');

            }
            
            // Return the full URL to the default image
            return url('/') . $default_image_path;
        }
 
        // Return the stored profile picture URL
        // return Storage::disk(env('FILESYSTEM_DRIVER'))->url(file_path($this->uploadPath(), $value));
            $relativePath = file_path($this->uploadPath(), $value);
            // dd($relativePath);
            return url('storage/' . $relativePath);
    }
    
    /**
     * Override the "boot" method of the model.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        // Model event handlers
    }

    /**
     * Set the password using bcrypt hash if stored as plaintext.
     *
     * @param string $value
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = (password_get_info($value)['algo'] === 0) ? bcrypt($value) : $value;
    }

    /**
     * The roles associated with the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id');
    }

    /**
     * The OTP associated with the user's mobile number.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function otp()
    {
        return $this->hasOne(MobileOtp::class, 'mobile', 'mobile');
    }

   
    /**
     * The default file upload path.
     *
     * @return string|null
     */
    public function uploadPath()
    {
        return config('base.user.upload.profile-picture.path');
    }

    /**
     * The Staff associated with the user's id.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function admin()
    {
        return $this->hasOne(AdminDetail::class, 'user_id', 'id');
    }

    /**
     * The Bank info associated with the user's id.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function bankInfo()
    {
        return $this->hasOne(UserBankInfo::class, 'user_id', 'id');
    }

    /**
     * The Staff associated with the user's id.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function developer()
    {
        return $this->hasOne(Developer::class, 'user_id', 'id');
    }

    /**
    * The user wallet history associated with the user's id.
    *
    * @return \Illuminate\Database\Eloquent\Relations\hasOne
    */
    public function userWalletHistory()
    {
        return $this->hasMany(UserWalletHistory::class, 'user_id', 'id');
    }
    /**
    * The user cardinfo associated with the user's id.
    *
    * @return \Illuminate\Database\Eloquent\Relations\hasOne
    */
    public function userCards()
    {
        return $this->hasMany(CardInfo::class, 'user_id', 'id');
    }
    /**
    * The user wallet history associated with the user's id.
    *
    * @return \Illuminate\Database\Eloquent\Relations\hasOne
    */
    public function rewardHistory()
    {
        return $this->hasMany(RewardHistory::class, 'user_id', 'id');
    }
    public function rewardPoint()
    {
        return $this->hasOne(RewardPoint::class, 'user_id', 'id');
    }

    /**
    * The favouriteLocations associated with the user's id.
    *
    * @return \Illuminate\Database\Eloquent\Relations\hasOne
    */
    public function favouriteLocations()
    {
        return $this->hasMany(FavouriteLocation::class, 'user_id', 'id');
    }

    public function userWallet()
    {
        return $this->hasOne(UserWalletDetail::class, 'user_id', 'id');
    }
    public function driverWallet()
    {
        return $this->hasOne(DriverWallet::class, 'user_id', 'id');
    }

    public function withdrawalRequestsHistory()
    {
        return $this->hasMany(WalletWithdrawalRequest::class, 'user_id', 'id');
    }
    /**
     * The Driver associated with the user's id.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function driver()
    {
        return $this->hasOne(Driver::class, 'user_id', 'id')->withTrashed();
    }
    public function storeDetail()
    {
        return $this->hasOne(Stores::class, 'user_id', 'id');
    }

    public function accounts()
    {
        return $this->hasMany(LinkedSocialAccount::class, 'user_id', 'id');
    }
    public function requestDetail()
    {
        return $this->hasMany(Request::class, 'user_id', 'id');
    }

    /**
     * The Driver associated with the user's id.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function userDetails()
    {
        return $this->hasOne(UserDetails::class, 'user_id', 'id');
    }

    /**
    * Get formated and converted timezone of user's created at.
    *
    * @param string $value
    * @return string
    */
    public function getConvertedCreatedAtAttribute()
    {
        if ($this->created_at==null||!auth()->user()) {
            return null;
        }
        $timezone = auth()->user()->timezone?:env('SYSTEM_DEFAULT_TIMEZONE');
        return Carbon::parse($this->created_at)->setTimezone($timezone)->format('jS M h:i A');
    }
    /**
    * Get formated and converted timezone of user's created at.
    *
    * @param string $value
    * @return string
    */
    public function getConvertedUpdatedAtAttribute()
    {
        if ($this->updated_at==null||!auth()->user()) {
            return null;
        }
        $timezone = auth()->user()->timezone?:env('SYSTEM_DEFAULT_TIMEZONE');
        return Carbon::parse($this->updated_at)->setTimezone($timezone)->format('jS M h:i A');
    }

    /**
    * Specifies the user's FCM token
    *
    * @return string
    */
    public function routeNotificationForFcm()
    {
        return $this->fcm_token;
    }
    public function routeNotificationForApn()
    {
        return $this->apn_token;
    }



    protected $searchable = [
        'columns' => [
            'users.name' => 20,
            'users.email'=> 20,
            'users.mobile'=> 20,
        ],
    ];

    /**
    * The user that the country belongs to.
    * @tested
    *
    * @return \Illuminate\Database\Eloquent\Relations\belongsTo
    */
    public function countryDetail()
    {
        return $this->belongsTo(Country::class, 'country', 'id');
    }

    public function owner()
    {
        return $this->hasOne(Owner::class, 'user_id', 'id')->withTrashed();
    }
    public function getCountryNameAttribute() {
        return $this->countryDetail ? $this->countryDetail->name: null;
    }
    public function userNotification()
    {
        return $this->belongsTo(User::class,'id')->withTrashed();
    }

    public function recentSearchesList()
    {
        return $this->hasMany(RecentSearch::class, 'user_id', 'id');
    }

}
