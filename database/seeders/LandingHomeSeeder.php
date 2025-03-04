<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin\LandingHome;
use DB;
use Illuminate\Support\Str;

class LandingHomeSeeder extends Seeder
{
     
    public function run()
    { 
        $home = LandingHome::first();

        if($home){
            goto end;

        }
        
        \DB::table('landing_homes')->insert(array (
            0 => 
            array (
            'id' => Str::uuid(),
            'hero_title' => 'It’s time to change your ride experience! Download the Tagxi app Today!',
            'hero_user_link_android' => 'https://misoftwares.in/',
            'hero_user_link_apple' => 'https://misoftwares.in/',
            'hero_driver_link_android' => 'https://misoftwares.in/',
            'hero_driver_link_apple' => 'https://misoftwares.in/',
            'feature_heading' => 'Advantage of using our Apps',
            'feature_para' => 'Tagxi app can provide a superior transportation experience for passengers and drivers alike, fostering a more efficient and user-friendly ecosystem',
            'feature_sub_heading_1' => 'Tap a button, get a ride',
            'feature_sub_para_1' => 'Choose your ride and set your location. Youll see your drivers picture and vehicle details, and can track their arrival on the map.',
            'feature_sub_heading_2' => 'Always on, always available',
            'feature_sub_para_2' => 'No phone calls to make, no pick-ups to schedule. With 24/7 availability, request a ride any time of day, any day of the year.',
            'feature_sub_heading_3' => 'Ride and Pay',
            'feature_sub_para_3' => 'Our taxi service is available at an unbeatable price. We offer the best taxi service deals with professional drivers and support you can always rely on. Our taxi is safe, modern as well as easy on your wallet.',
            'feature_sub_heading_4' => 'You rate, we listen',
            'feature_sub_para_4' => 'Rate your driver and provide anonymous feedback about your trip. Your input helps us make every ride a 5-star experience.',
            'box_img_1' => '1.png',
            'box_para_1' => 'On-demand rides for in-demand people',
            'box_img_2' => '2.jpg',
            'box_para_2' => 'Make your work commute or business trip more environmentally friendly and cost effective.',
            'box_img_3' => '3.jpg',
            'box_para_3' => 'Safe and easy rides Througout India',
            'about_title_1' => 'ABOUT',
            'about_title_2' => 'The Company',
            'about_img' => 'company.png',
            'about_para' => 'Mobility Intelligence Softwares is a blooming Indian start-up company with our baby-steps, since 2021 and is headquartered in the town of Coimbatore, India. It employs 20 skilled professionals. Our team of Industrial experts identify and deliver solution driving real business outcomes. We help your Businesses and Organizations delivering Digital Innovation, Product Innovation and Modernisation at business speed.',
            'about_lists' => 'Dedicated Team Members,Awesome Services,Customer Support,Quality Assurance',
            'service_heading_1' => 'DIGITAL SERVICES',
            'service_heading_2' => 'A complete solution for your Taxi Service.',
            'service_para' => 'Tagxi takes pride, delivering on-demand dispatch solution.',
            'services' => 'Data Protection,Customer Support,Quality Assurance,Awesome Services',
            'service_img' => 'service.png',
            'drive_heading' => 'Why Drive with Tagxi!',
            'drive_title_1' => 'About Us',
            'drive_para_1' => 'Tagxi is a rideshare platform facilitating peer to peer ridesharing by means of connecting passengers who are in need of rides from drivers with available cars to get from point A to point B with the press of a button. tyt is a clever 4 letter word that sounds like "easy" a fantastic connotation of effortless ease and accessibility to get you to your destination. Tagxi welcomes applicants year-round - summer, winter, fall, spring, and holiday work seekers.',
            'drive_title_2' => 'Our Mission',
            'drive_para_2' => 'It’s our goal to create a flexible working environment that is inclusive and reflects the diversity of the cities we serve—where everyone can be their authentic self, and where that authenticity is celebrated as a strength. By creating an environment where people from every background can thrive, we’ll make tyt a better company—for our drivers and our customers.',
            'drive_title_3' => 'Driver Commitment',
            'drive_para_3' => 'We promise to provide the technology and the support needed to empower you to be your own boss, deciding on when and how often you drive. Let us take you places. Through our software, we take the guesswork and hassle out of securing your fare. We will always seek to apply technological advancements to the current process in order that the driver is fully equipped to operate in the given climate.',
            'service_area_img' => 'locations.png',
            'service_area_title' => 'Service Locations',
            'service_area_para' => 'We cover all major cities and surrounding areas in India. For every destination in life, we will connect you to a reliable driver in minutes. Let us take you there',
            'locale' => 'en',
            'language' => 'English',
            
        ),
    ));


end:
    }
}