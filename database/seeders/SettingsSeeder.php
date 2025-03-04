<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;
use App\Base\Constants\Setting\Settings as SettingSlug;
use App\Base\Constants\Setting\SettingCategory;
use App\Base\Constants\Setting\SettingSubGroup;
use App\Base\Constants\Setting\SettingValueType;

class SettingsSeeder extends Seeder
{
    /**
     * List of all the settings_from_seeder to be created along with their details.
     *
     * @var array
     */
    protected $settings_from_seeder = [

        SettingSlug::SERVICE_TAX_FOR_FOOD => [
            'category'=>SettingCategory::TAX_SETTINGS,
            'value' => 30,
            'field' => SettingValueType::TEXT,
            'option_value' => null,
            'group_name' => null,
        ],


        SettingSlug::TRIP_DISPTACH_TYPE => [
            'category'=>SettingCategory::TRIP_SETTINGS,
            'value' => 1,
            'field' => SettingValueType::SELECT,
            'option_value' => '{"one-by-one":1,"to-all-drivers":0}',
            'group_name' => null,
        ],        
        SettingSlug::MINIMUM_WALLET_AMOUNT_FOR_TRANSFER => [
            'category'=>SettingCategory::WALLET,
            'value' => 500,
            'field' => SettingValueType::TEXT,
            'option_value' => null,
            'group_name' => null,
        ],
         SettingSlug::DRIVER_SEARCH_RADIUS => [
            'category'=>SettingCategory::TRIP_SETTINGS,
            'value' => 3,
            'field' => SettingValueType::TEXT,
            'option_value' => null,
            'group_name' => null,
        ],
        SettingSlug::ROUND_THE_BILL_VALUE => [
            'category'=>SettingCategory::TRIP_SETTINGS,
            'value' => 1,
             'field' => SettingValueType::SELECT,
             'option_value' => '{"yes":1,"no":0}',
             'group_name' => null,
        ],

         SettingSlug::ENABLE_LUGGAGE_PREFERENCE_FOR_USER => [
            'category'=>SettingCategory::CUSTOMIZATION_SETTINGS,
            'value' => '1',
            'field' => SettingValueType::SELECT,
            'option_value' => '{"yes":1,"no":0}',
            'group_name' => null,
        ],                
        SettingSlug::MAXIMUM_TIME_FOR_FIND_DRIVERS_FOR_BIDDING_RIDE => [
            'category'=>SettingCategory::TRIP_SETTINGS,
            'value' => 5,
            'field' => SettingValueType::TEXT,
            'option_value' => null,
            'group_name' => null,
        ],
        SettingSlug::MAXIMUM_TIME_FOR_ACCEPT_REJECT_BIDDING_RIDE => [
            'category'=>SettingCategory::TRIP_SETTINGS,
            'value' => 5,
            'field' => SettingValueType::TEXT,
            'option_value' => null,
            'group_name' => null,
        ],

         SettingSlug::ENABLE_SHIPMENT_LOAD_FEATURE => [
            'category'=>SettingCategory::CUSTOMIZATION_SETTINGS,
            'value' => '1',
            'field' => SettingValueType::SELECT,
            'option_value' => '{"yes":1,"no":0}',
            'group_name' => null,
        ],
        SettingSlug::ENABLE_SHIPMENT_UNLOAD_FEATURE => [
            'category'=>SettingCategory::CUSTOMIZATION_SETTINGS,
            'value' => '1',
            'field' => SettingValueType::SELECT,
            'option_value' => '{"yes":1,"no":0}',
            'group_name' => null,
        ],
        SettingSlug::ENABLE_DIGITAL_SIGNATURE => [
            'category'=>SettingCategory::CUSTOMIZATION_SETTINGS,
            'value' => '1',
            'field' => SettingValueType::SELECT,
            'option_value' => '{"yes":1,"no":0}',
            'group_name' => null,
        ],
         SettingSlug::USER_CAN_MAKE_A_RIDE_AFTER_X_MINIUTES => [
            'category'=>SettingCategory::TRIP_SETTINGS,
            'value' => 30,
            'field' => SettingValueType::TEXT,
            'option_value' => null,
            'group_name' => null,
        ],
         SettingSlug::USER_CAN_CANCEL_A_ORDER_IN_X_SECONDS => [
            'category'=>SettingCategory::TRIP_SETTINGS,
            'value' => 60,
            'field' => SettingValueType::TEXT,
            'option_value' => null,
            'group_name' => null,
        ],        
         SettingSlug::MINIMUM_TIME_FOR_SEARCH_DRIVERS_FOR_SCHEDULE_RIDE => [
            'category'=>SettingCategory::TRIP_SETTINGS,
            'value' => 30,
            'field' => SettingValueType::TEXT,
            'option_value' => null,
            'group_name' => null,
        ],
        SettingSlug::MINIMUM_TIME_FOR_TRIP_START_DRIVERS_FOR_SCHEDULE_RIDE => [
           'category'=>SettingCategory::TRIP_SETTINGS,
           'value' => 30,
           'field' => SettingValueType::TEXT,
           'option_value' => null,
           'group_name' => null,
       ],
        SettingSlug::MAXIMUM_TIME_FOR_FIND_DRIVERS_FOR_REGULAR_RIDE => [
            'category'=>SettingCategory::TRIP_SETTINGS,
            'value' => 5,
            'field' => SettingValueType::TEXT,
            'option_value' => null,
            'group_name' => null,
        ],
        SettingSlug::TRIP_ACCEPT_REJECT_DURATION_FOR_DRIVER => [
            'category'=>SettingCategory::TRIP_SETTINGS,
            'value' => 30,
            'field' => SettingValueType::TEXT,
            'option_value' => null,
            'group_name' => null,
        ],
        SettingSlug::HOW_MANY_TIMES_A_DRIVER_TIMES_A_DRIVER_CAN_ENABLE_THE_MY_ROUTE_BOOKING_PER_DAY => [
            'category'=>SettingCategory::TRIP_SETTINGS,
            'value' => 1,
            'field' => SettingValueType::TEXT,
            'option_value' => null,
            'group_name' => null,
        ],   
        SettingSlug::ENABLE_MY_ROUTE_BOOKING_FEATURE => [
            'category'=>SettingCategory::CUSTOMIZATION_SETTINGS,
            'value' => 1,
             'field' => SettingValueType::SELECT,
             'option_value' => '{"yes":1,"no":0}',
             'group_name' => null,
        ],
        SettingSlug::ENABLE_WEB_BOOKING_FEATURE => [
            'category'=>SettingCategory::CUSTOMIZATION_SETTINGS,
            'value' => 1,
             'field' => SettingValueType::SELECT,
             'option_value' => '{"yes":1,"no":0}',
             'group_name' => null,
        ],
        SettingSlug::ENABLE_SUB_VEHICLE_FEATURE => [
            'category'=>SettingCategory::CUSTOMIZATION_SETTINGS,
            'value' => 1,
             'field' => SettingValueType::SELECT,
             'option_value' => '{"yes":1,"no":0}',
             'group_name' => null,
        ],
        SettingSlug::MAP_TYPE => [
            'category'=>SettingCategory::MAP_SETTINGS,
            'value' => 'open_street_map',
            'field' => SettingValueType::SELECT,
            'option_value' => '{"google_map":"google","open_street_map":"open_street"}',
            'group_name' => null,
        ],
        SettingSlug::ENABLE_VASE_MAP => [
            'category'=>SettingCategory::CUSTOMIZATION_SETTINGS,
            'value' => '1',
            'field' => SettingValueType::SELECT,
            'option_value' => '{"yes":1,"no":0}',
            'group_name' => null,
        ],
    //General category settings
        SettingSlug::ENABLE_MODULES_FOR_APPLICATIONS => [
            'category'=>SettingCategory::CUSTOMIZATION_SETTINGS,
             'value' => "both",
             'field' => SettingValueType::SELECT,
             'option_value' => '{"taxi":"taxi","delivery":"delivery","both":"both","food_delivery":"food_delivery"}',
             'group_name' => null,
        ],  
        SettingSlug::LOGO => [
            'category'=>SettingCategory::GENERAL,
            'value' => 'rest.png',
            'field' => SettingValueType::FILE,
            'option_value' => null,
            'group_name' => null,
        ],
        SettingSlug::FAVICON => [
            'category'=>SettingCategory::GENERAL,
            'value' => 'favicon.png',
            'field' => SettingValueType::FILE,
            'option_value' => null,
            'group_name' => null,
        ],
        SettingSlug::LOGINBG => [
            'category'=>SettingCategory::GENERAL,
            'value' => 'workspace.jpg',
            'field' => SettingValueType::FILE,
            'option_value' => null,
            'group_name' => null,
        ],
        SettingSlug::OWNER_LOGINBG => [
            'category'=>SettingCategory::GENERAL,
            'value' => null,
            'field' => SettingValueType::FILE,
            'option_value' => null,
            'group_name' => null,
        ],
        SettingSlug::NAV_COLOR => [
            'category'=>SettingCategory::GENERAL,
            'value' => '#0ab39c',
            'field' => SettingValueType::TEXT,
            'option_value' => null,
            'group_name' => null,
        ],
        SettingSlug::SIDEBAR_COLOR => [
            'category'=>SettingCategory::GENERAL,
            'value' => '#405189',
            'field' => SettingValueType::TEXT,
            'option_value' => null,
            'group_name' => null,
        ],
        SettingSlug::SIDEBARTXT_COLOR => [
            'category'=>SettingCategory::GENERAL,
            'value' => '#ffffff',
            'field' => SettingValueType::TEXT,
            'option_value' => null,
            'group_name' => null,
        ],
        SettingSlug::FOOTER_CONTENT1 => [
            'category'=>SettingCategory::GENERAL,
            'value' => '2024 © Misoftwares.',
            'field' => SettingValueType::TEXT,
            'option_value' => null,
            'group_name' => null,
        ],
        SettingSlug::FOOTER_CONTENT2 => [
            'category'=>SettingCategory::GENERAL,
            'value' => 'Design & Develop by Misoftwares',
            'field' => SettingValueType::TEXT,
            'option_value' => null,
            'group_name' => null,
        ],
        SettingSlug::APP_NAME => [
            'category'=>SettingCategory::GENERAL,
            'value' => 'Tagxi',
            'field' => SettingValueType::TEXT,
            'option_value' => null,
            'group_name' => null,
        ],
        SettingSlug::FACEBOOK => [
            'category'=>SettingCategory::GENERAL,
            'value' => 'https://facebook.com',
            'field' => SettingValueType::TEXT,
            'option_value' => null,
            'group_name' => null,
        ],
        SettingSlug::INSTAGRAM => [
            'category'=>SettingCategory::GENERAL,
            'value' => 'https://instagram.com',
            'field' => SettingValueType::TEXT,
            'option_value' => null,
            'group_name' => null,
        ],
        SettingSlug::LINKEDIN => [
            'category'=>SettingCategory::GENERAL,
            'value' => 'https://linkedin.com',
            'field' => SettingValueType::TEXT,
            'option_value' => null,
            'group_name' => null,
        ],
        SettingSlug::TWITTER => [
            'category'=>SettingCategory::GENERAL,
            'value' => 'https://twitter.com',
            'field' => SettingValueType::TEXT,
            'option_value' => null,
            'group_name' => null,
        ],
        SettingSlug::CURRENCY => [
            'category'=>SettingCategory::GENERAL,
            'value' => 'INR',
            'field' => SettingValueType::TEXT,
            'option_value' => null,
            'group_name' => null,
        ],
        SettingSlug::CURRENCY_SYMBOL => [
            'category'=>SettingCategory::GENERAL,
            'value' => '₹',
            'field' => SettingValueType::TEXT,
            'option_value' => null,
            'group_name' => null,
        ],
        SettingSlug::DEFAULT_CURRENCY_CODE_FOR_MOBILE_APP => [
            'category'=>SettingCategory::CUSTOMIZATION_SETTINGS,
            'value' => 'INR',
            'field' => SettingValueType::TEXT,
            'option_value' => null,
            'group_name' => null,
        ],        
        SettingSlug::DEFAULT_COUNTRY_CODE_FOR_MOBILE_APP => [
            'category'=>SettingCategory::CUSTOMIZATION_SETTINGS,
            'value' => 'IN',
            'field' => SettingValueType::TEXT,
            'option_value' => null,
            'group_name' => null,
        ],

        SettingSlug::CONTACT_US_MOBILE1 => [
            'category'=>SettingCategory::GENERAL,
            'value' => '0000000000',
            'field' => SettingValueType::TEXT,
            'option_value' => null,
            'group_name' => null,
        ],
        SettingSlug::CONTACT_US_MOBILE2 => [
            'category'=>SettingCategory::GENERAL,
            'value' => '0000000000',
            'field' => SettingValueType::TEXT,
            'option_value' => null,
            'group_name' => null,
        ],
        SettingSlug::CONTACT_US_LINK => [
            'category'=>SettingCategory::GENERAL,
            'value' => 'https://tagxi-landing.ondemandappz.com/',
            'field' => SettingValueType::TEXT,
            'option_value' => null,
            'group_name' => null,
        ],
        SettingSlug::DEFAULT_LAT => [
            'category'=>SettingCategory::GENERAL,
            'value' => 11.21215,
            'field' => SettingValueType::TEXT,
            'option_value' => null,
            'group_name' => null,
        ],
        SettingSlug::DEFAULT_LONG => [
            'category'=>SettingCategory::GENERAL,
            'value' => 76.54545,
            'field' => SettingValueType::TEXT,
            'option_value' => null,
            'group_name' => null,
        ],
        SettingSlug::ENABLE_COUNTRY_RESTRICT_ON_MAP => [
            'category'=>SettingCategory::CUSTOMIZATION_SETTINGS,
            'value' => '0',
            'field' => SettingValueType::SELECT,
            'option_value' => '{"yes":1,"no":0}',
            'group_name' => null,
        ],   
        SettingSlug::SHOW_WALLET_FEATURE_ON_MOBILE_APP => [
            'category'=>SettingCategory::CUSTOMIZATION_SETTINGS,
             'value' => 1,
             'field' => SettingValueType::SELECT,
             'option_value' => '{"yes":1,"no":0}',
             'group_name' => null,
        ],
        SettingSlug::SHOW_BANK_INFO_FEATURE_ON_MOBILE_APP => [
            'category'=>SettingCategory::CUSTOMIZATION_SETTINGS,
             'value' => 1,
             'field' => SettingValueType::SELECT,
             'option_value' => '{"yes":1,"no":0}',
             'group_name' => null,
        ],
        SettingSlug::BIDDING_LOW_PERCENTAGE => [
            'category'=>SettingCategory::TRIP_SETTINGS,
            'value' => 50,
            'field' => SettingValueType::TEXT,
            'option_value' => 0,
            'group_name' => null,
        ], 
        SettingSlug::BIDDING_HIGH_PERCENTAGE => [
            'category'=>SettingCategory::TRIP_SETTINGS,
            'value' => 10,
            'field' => SettingValueType::TEXT,
            'option_value' => 0,
            'group_name' => null,
        ],    
        SettingSlug::BIDDING_AMOUNT_INCREASE_OR_DECREASE => [
            'category'=>SettingCategory::TRIP_SETTINGS,
            'value' => 10,
            'field' => SettingValueType::TEXT,
            'option_value' => 0,
            'group_name' => null,
        ],
        SettingSlug::USER_BIDDING_LOW_PERCENTAGE => [
            'category'=>SettingCategory::TRIP_SETTINGS,
            'value' => 50,
            'field' => SettingValueType::TEXT,
            'option_value' => 0,
            'group_name' => null,
        ], 
        SettingSlug::USER_BIDDING_HIGH_PERCENTAGE => [
            'category'=>SettingCategory::TRIP_SETTINGS,
            'value' => 10,
            'field' => SettingValueType::TEXT,
            'option_value' => 0,
            'group_name' => null,
        ],    
        SettingSlug::USER_BIDDING_AMOUNT_INCREASE_OR_DECREASE => [
            'category'=>SettingCategory::TRIP_SETTINGS,
            'value' => 10,
            'field' => SettingValueType::TEXT,
            'option_value' => 0,
            'group_name' => null,
        ],                
        SettingSlug::SHOW_WALLET_FEATURE_ON_MOBILE_APP_DRIVER => [
            'category'=>SettingCategory::CUSTOMIZATION_SETTINGS,
             'value' => 1,
             'field' => SettingValueType::SELECT,
             'option_value' => '{"yes":1,"no":0}',
             'group_name' => null,
        ],    
        SettingSlug::SHOW_INSTATNT_RIDE_FEATURE_ON_MOBILE_APP => [
            'category'=>SettingCategory::CUSTOMIZATION_SETTINGS,
             'value' => 1,
             'field' => SettingValueType::SELECT,
             'option_value' => '{"yes":1,"no":0}',
             'group_name' => null,
        ],   
        SettingSlug::SHOW_OUTSTATION_RIDE_FEATURE => [
            'category'=>SettingCategory::CUSTOMIZATION_SETTINGS,
             'value' => 0,
             'field' => SettingValueType::SELECT,
             'option_value' => '{"yes":1,"no":0}',
             'group_name' => null,
        ],
        SettingSlug::SHOW_DELIVERY_OUTSTATION_RIDE_FEATURE => [
            'category'=>SettingCategory::CUSTOMIZATION_SETTINGS,
             'value' => 0,
             'field' => SettingValueType::SELECT,
             'option_value' => '{"yes":1,"no":0}',
             'group_name' => null,
        ],   

        SettingSlug::SHOW_OWNER_MODULE_ON_MOBILE_APP => [
            'category'=>SettingCategory::CUSTOMIZATION_SETTINGS,
             'value' => 1,
             'field' => SettingValueType::SELECT,
             'option_value' => '{"yes":1,"no":0}',
             'group_name' => null,
        ], 
        SettingSlug::SHOW_WALLET_MONEY_TRANSFER_FEAUTRE_ON_MOBILE_APP_FOR_OWNER => [
            'category'=>SettingCategory::CUSTOMIZATION_SETTINGS,
             'value' => 1,
             'field' => SettingValueType::SELECT,
             'option_value' => '{"yes":1,"no":0}',
             'group_name' => null,
        ],
        SettingSlug::SHOW_WALLET_FEATURE_ON_MOBILE_APP_OWNER => [
            'category'=>SettingCategory::CUSTOMIZATION_SETTINGS,
             'value' => 1,
             'field' => SettingValueType::SELECT,
             'option_value' => '{"yes":1,"no":0}',
             'group_name' => null,
        ],
        SettingSlug::SHOW_WALLET_MONEY_TRANSFER_FEAUTRE_ON_MOBILE_APP => [
            'category'=>SettingCategory::CUSTOMIZATION_SETTINGS,
             'value' => 1,
             'field' => SettingValueType::SELECT,
             'option_value' => '{"yes":1,"no":0}',
             'group_name' => null,
        ], 
        SettingSlug::SHOW_WALLET_MONEY_TRANSFER_FEAUTRE_ON_MOBILE_APP_FOR_DRIVER => [
            'category'=>SettingCategory::CUSTOMIZATION_SETTINGS,
             'value' => 1,
             'field' => SettingValueType::SELECT,
             'option_value' => '{"yes":1,"no":0}',
             'group_name' => null,
        ], 
        SettingSlug::ENABLE_PET_PREFERENCE_FOR_USER => [
            'category'=>SettingCategory::CUSTOMIZATION_SETTINGS,
             'value' => 0,
             'field' => SettingValueType::SELECT,
             'option_value' => '{"yes":1,"no":0}',
             'group_name' => null,
        ], 
        SettingSlug::ENABLE_DOCUMENT_AUTO_APPROVAL => [
            'category'=>SettingCategory::CUSTOMIZATION_SETTINGS,
             'value' => 0,
             'field' => SettingValueType::SELECT,
             'option_value' => '{"yes":1,"no":0}',
             'group_name' => null,
        ], 
      SettingSlug::SHOW_EMAIL_OTP_FEAUTRE_ON_MOBILE_APP => [
            'category'=>SettingCategory::CUSTOMIZATION_SETTINGS,
             'value' => 1,
             'field' => SettingValueType::SELECT,
             'option_value' => '{"yes":1,"no":0}',
             'group_name' => null,
        ],  
        SettingSlug::RESTERAUNT_PAYOUT_TYPE => [
            'category'=>SettingCategory::GENERAL,
             'value' => "daily",
             'field' => SettingValueType::SELECT,
             'option_value' => '{"daily":"daily","weekly":"weekly","monthly":"monthly"}',
             'group_name' => null,
        ],         
        SettingSlug::DRIVER_WALLET_MINIMUM_AMOUNT_TO_GET_ORDER => [
            'category'=>SettingCategory::WALLET,
            'value' => -10000,
            'field' => SettingValueType::TEXT,
            'option_value' => null,
            'group_name' => null,
        ],
        SettingSlug::OWNER_WALLET_MINIMUM_AMOUNT_TO_GET_ORDER => [
            'category'=>SettingCategory::WALLET,
            'value' => -10000,
            'field' => SettingValueType::TEXT,
            'option_value' => null,
            'group_name' => null,
        ],
        SettingSlug::REFERRAL_commission_FOR_USER => [
            'category'=>SettingCategory::REFERRAL,
            'value' => 30,
            'field' => SettingValueType::TEXT,
            'option_value' => null,
            'group_name' => null,
        ],
          SettingSlug::REFERRAL_commission_FOR_DRIVER => [
            'category'=>SettingCategory::REFERRAL,
            'value' => 30,
            'field' => SettingValueType::TEXT,
            'option_value' => null,
            'group_name' => null,
        ],
        SettingSlug::GOOGLE_MAP_KEY => [
            'category'=>SettingCategory::MAP_SETTINGS,
            'value' => 'Your google map key',
            'field' => SettingValueType::PASSWORD,
            'option_value' => null,
            'group_name' => null,
        ], 
        // SettingSlug::GOOGLE_TRANSLATION_API_KEY => [
        //     'category'=>SettingCategory::MAP_SETTINGS,
        //     'value' => 'AIzaSyA-n6_B74MUzv2walwhMHqyfWqH92J6Nno',
        //     'field' => SettingValueType::PASSWORD,
        //     'option_value' => null,
        //     'group_name' => null,
        // ], 
        // SettingSlug::GOOGLE_CLOUD_PROJECT_ID_FOR_TRANSLATION => [
        //     'category'=>SettingCategory::MAP_SETTINGS,
        //     'value' => 'arre-argentina',
        //     'field' => SettingValueType::PASSWORD,
        //     'option_value' => null,
        //     'group_name' => null,
        // ], 
        // SettingSlug::GOOGLE_MAP_KEY_FOR_DISTANCE_MATRIX => [
        //     'category'=>SettingCategory::MAP_SETTINGS,
        //     'value' => 'AIzaSyBeVRs1icwooRpk7ErjCEQCwu0OQowVt9I',
        //     'field' => SettingValueType::PASSWORD,
        //     'option_value' => null,
        //     'group_name' => null,
        // ], 
        // SettingSlug::GOOGLE_SHEET_ID => [
        //     'category'=>SettingCategory::MAP_SETTINGS,
        //     'value' => '1sOIs6oiLv-xrc3cNq2rvOv9ItXoux2MZJE_gdnBT7co',
        //     'field' => SettingValueType::TEXT,
        //     'option_value' => null,
        //     'group_name' => null,
        // ],
        SettingSlug::SHOW_RENTAL_RIDE_FEATURE => [
            'category'=>SettingCategory::CUSTOMIZATION_SETTINGS,
            'value' => '1',
            'field' => SettingValueType::SELECT,
            'option_value' => '{"yes":1,"no":0}',
            'group_name' => null,
        ],
        SettingSlug::SHOW_DELIVERY_RENTAL_RIDE_FEATURE => [
            'category'=>SettingCategory::CUSTOMIZATION_SETTINGS,
            'value' => '1',
            'field' => SettingValueType::SELECT,
            'option_value' => '{"yes":1,"no":0}',
            'group_name' => null,
        ],  
        SettingSlug::SHOW_TAXI_RENTAL_RIDE_FEATURE => [
            'category'=>SettingCategory::CUSTOMIZATION_SETTINGS,
            'value' => '1',
            'field' => SettingValueType::SELECT,
            'option_value' => '{"yes":1,"no":0}',
            'group_name' => null,
        ], 
        SettingSlug::SHOW_CARD_PAYMENT_FEATURE => [
            'category'=>SettingCategory::CUSTOMIZATION_SETTINGS,
            'value' => '1',
            'field' => SettingValueType::SELECT,
            'option_value' => '{"yes":1,"no":0}',
            'group_name' => null,
        ],                     
         SettingSlug::SHOW_RIDE_OTP_FEATURE => [
            'category'=>SettingCategory::CUSTOMIZATION_SETTINGS,
            'value' => '1',
            'field' => SettingValueType::SELECT,
            'option_value' => '{"yes":1,"no":0}',
            'group_name' => null,
        ],
         SettingSlug::SHOW_RIDE_LATER_FEATURE => [
            'category'=>SettingCategory::CUSTOMIZATION_SETTINGS,
            'value' => '1',
            'field' => SettingValueType::SELECT,
            'option_value' => '{"yes":1,"no":0}',
            'group_name' => null,
        ],
         SettingSlug::SHOW_RIDE_WITHOUT_DESTINATION => [
            'category'=>SettingCategory::CUSTOMIZATION_SETTINGS,
            'value' => '1',
            'field' => SettingValueType::SELECT,
            'option_value' => '{"yes":1,"no":0}',
            'group_name' => null,
        ],
        SettingSlug::SHOW_INCENTIVE_FEATURE_FOR_DRIVER => [
            'category'=>SettingCategory::CUSTOMIZATION_SETTINGS,
            'value' => '1',
            'field' => SettingValueType::SELECT,
            'option_value' => '{"yes":1,"no":0}',
            'group_name' => null,
        ],        
      /*Mailer Name*/
        SettingSlug::MAIL_MAILER => [
            'category'=>SettingCategory::MAIL_CONFIGURATION,
            'value' => 'smtp',
            'field' => SettingValueType::TEXT,
            'group_name' => null,
        ],
        SettingSlug::MAIL_HOST => [
            'category'=>SettingCategory::MAIL_CONFIGURATION,
            'value' => 'smtp.gmail.com',
            'field' => SettingValueType::TEXT,
            'group_name' => null,
        ],
        SettingSlug::MAIL_PORT => [
            'category'=>SettingCategory::MAIL_CONFIGURATION,
            'value' => '587',
            'field' => SettingValueType::TEXT,
            'group_name' => null,
        ],
        SettingSlug::MAIL_USERNAME => [
            'category'=>SettingCategory::MAIL_CONFIGURATION,
            'value' => 'misoftwares2021@gmail.com',
            'field' => SettingValueType::PASSWORD,
            'group_name' => null,
        ],
        SettingSlug::MAIL_PASSWORD => [
            'category'=>SettingCategory::MAIL_CONFIGURATION,
            'value' => 'vnlzidnvidfninvdvlnjdfv',
            'field' => SettingValueType::PASSWORD,
            'group_name' => null,
        ],
        SettingSlug::MAIL_ENCRYPTION => [
            'category'=>SettingCategory::MAIL_CONFIGURATION,
            'value' => 'tls',
            'field' => SettingValueType::TEXT,
            'group_name' => null,
        ],    
        SettingSlug::MAIL_FROM_ADDRESS => [
            'category'=>SettingCategory::MAIL_CONFIGURATION,
            'value' => 'misoftwares2021@gmail.com',
            'field' => SettingValueType::TEXT,
            'group_name' => null,
        ], 
         SettingSlug::MAIL_FROM_NAME => [
            'category'=>SettingCategory::MAIL_CONFIGURATION,
            'value' => 'misoftwares',
            'field' => SettingValueType::TEXT,
            'group_name' => null,
        ],
        SettingSlug::ENABLE_NOT_FOUND => [
            'category'=>SettingCategory::GENERAL,
            'value' => '1',
            'field' => SettingValueType::SELECT,
            'option_value' => '{"enabled":1,"disabled":0}',
            'group_name' => null,
       ],
       SettingSlug::ADMIN_LOGIN => [
        'category'=>SettingCategory::GENERAL,
        'value' => 'admin',
        'field' => SettingValueType::TEXT,
        'option_value' => null,
        'group_name' => null,
        ],
        SettingSlug::OWNER_LOGIN => [
            'category'=>SettingCategory::GENERAL,
            'value' => 'owner-login',
            'field' => SettingValueType::TEXT,
            'option_value' => null,
            'group_name' => null,
        ],
        SettingSlug::USER_LOGIN => [
            'category'=>SettingCategory::GENERAL,
            'value' => 'user-login',
            'field' => SettingValueType::TEXT,
            'option_value' => null,
            'group_name' => null,
        ],
        SettingSlug::SHOW_DRIVER_LEVEL_FEATURE => [
            'category'=>SettingCategory::GENERAL,
            'value' => '0',
            'field' => SettingValueType::SELECT,
            'option_value' => '{"yes":1,"no":0}',
            'group_name' => null,
        ],
        SettingSlug::DRIVER_REGISTER_MODULE => [
            'category'=>SettingCategory::GENERAL,
            'value' => 'commission',
            'field' => SettingValueType::SELECT,
            'option_value' => '{"commission":"commission","subscription":"subscription","both":"both"}',
            'group_name' => null,
        ],
        SettingSlug::REWARD_POINT_VALUE => [
            'category'=>SettingCategory::GENERAL,
            'value' => '1',
            'field' => SettingValueType::TEXT,
            'option_value' => null,
            'group_name' => null,
        ],
        SettingSlug::MINIMUM_REWARD_POINT => [
            'category'=>SettingCategory::GENERAL,
            'value' => '1',
            'field' => SettingValueType::TEXT,
            'option_value' => null,
            'group_name' => null,
        ],
        SettingSlug::ENABLE_PEAK_ZONE_FEATURE => [
            'category'=>SettingCategory::PEAK_ZONE_SETTINGS,
            'value' => 1,
            'field' => SettingValueType::TEXT,
            'option_value' => null,
            'group_name' => null,
        ],
        SettingSlug::PEAK_ZONE_RIDE_COUNT => [
            'category'=>SettingCategory::PEAK_ZONE_SETTINGS,
            'value' => 5,
            'field' => SettingValueType::TEXT,
            'option_value' => null,
            'group_name' => null,
        ],
        SettingSlug::DISTANCE_PRICE_PERCENTAGE => [
            'category'=>SettingCategory::PEAK_ZONE_SETTINGS,
            'value' => 20,
            'field' => SettingValueType::TEXT,
            'option_value' => null,
            'group_name' => null,
        ],
        SettingSlug::PEAK_ZONE_RADIUS => [
            'category'=>SettingCategory::PEAK_ZONE_SETTINGS,
            'value' => 10,
            'field' => SettingValueType::TEXT,
            'option_value' => null,
            'group_name' => null,
        ],
        SettingSlug::PEAK_ZONE_DURATION => [
            'category'=>SettingCategory::PEAK_ZONE_SETTINGS,
            'value' => 15,
            'field' => SettingValueType::TEXT,
            'option_value' => null,
            'group_name' => null,
        ],
        SettingSlug::PEAK_ZONE_HISTORY_DURATION => [
            'category'=>SettingCategory::PEAK_ZONE_SETTINGS,
            'value' => 15,
            'field' => SettingValueType::TEXT,
            'option_value' => null,
            'group_name' => null,
        ],
        SettingSlug::LANDING_HEADER_BG_COLOR => [
            'category'=>SettingCategory::GENERAL,
            'value' => '#ffffff',
            'field' => SettingValueType::TEXT,
            'option_value' => null,
            'group_name' => null,
        ],
        SettingSlug::LANDING_HEADER_TEXT_COLOR => [
            'category'=>SettingCategory::GENERAL,
            'value' => '#212529',
            'field' => SettingValueType::TEXT,
            'option_value' => null,
            'group_name' => null,
        ],
        SettingSlug::LANDING_HEADER_ACTIVE_TEXT_COLOR => [
            'category'=>SettingCategory::GENERAL,
            'value' => '#0ab39c',
            'field' => SettingValueType::TEXT,
            'option_value' => null,
            'group_name' => null,
        ],
        SettingSlug::LANDING_FOOTER_BG_COLOR => [
            'category'=>SettingCategory::GENERAL,
            'value' => '#000000',
            'field' => SettingValueType::TEXT,
            'option_value' => null,
            'group_name' => null,
        ],
        SettingSlug::LANDING_FOOTER_TEXT_COLOR => [
            'category'=>SettingCategory::GENERAL,
            'value' => '#f1ffff',
            'field' => SettingValueType::TEXT,
            'option_value' => null,
            'group_name' => null,
        ],
        SettingSlug::ENABLE_LANDING_SITE => [
            'category'=>SettingCategory::CUSTOMIZATION_SETTINGS,
            'value' => 1,
             'field' => SettingValueType::SELECT,
             'option_value' => '{"yes":1,"no":0}',
             'group_name' => null,
        ],
    ];


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $settingDB = Setting::all();

        foreach ($this->settings_from_seeder as $setting_slug=>$setting) {
            $categoryFound = $settingDB->first(function ($one) use ($setting_slug) {
                return $one->name === $setting_slug;
            });

            $created_params = [
                        'name' => $setting_slug
                    ];

            $to_create_setting_data = array_merge($created_params, $setting);

            if (!$categoryFound) {
                Setting::create($to_create_setting_data);
            }
        }
    }
}
