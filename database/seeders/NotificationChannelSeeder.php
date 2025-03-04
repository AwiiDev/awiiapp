<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin\NotificationChannel;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NotificationChannelSeeder extends Seeder
{
    
    public function run()
    { 
        $notification = NotificationChannel::first();
        // $userName = User::latest()->value('name') ?? 'User'; 

        if (!$notification) {
            // Seed the notification channels
            \DB::table('notification_channels')->insert([
                [
                    'id' => Str::uuid(),
                    'role' =>'user',
                    'topics' => 'New Customer Registration',
                    'topics_content' => 'Choose how Customer will get notified about Sent notification on customer registration',
                    'push_notification' => 1,
                    'mail' => 1,
                    'sms' => 1,
                    'email_subject' => 'Registration Mail',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '<p>Hello {name}</p>
                                    <p>Thank you for signing up with us, your trusted taxi app. Your registration was successful, and we are excited to have you on board.</p>
                                    <p>Your Account Details</p>
                                    <p>Email: {email}</p>
                                    <p>Mobile Number: {mobile}</p>
                                    <p>We are now ready to help you with your transportation needs! To get started, simply click the button below to log in to your account:</p> 
                                    <p>Best regards, </p>         
                                    <p>MI Softwares</p>',
                    'button_name'=>'Log in',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'show_img' => 0,
                    'banner_img' => 'profile-bg.jpg',                    
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'Register successfully',
                    'push_body' => 'Register successfully',
                    
                ],
                [
                    'id' => Str::uuid(),
                    'role' =>'user',
                    'topics' => 'User Ride Later',
                    'topics_content' => 'Choose how Customer will get notified about Sent notification on ride later',
                    'push_notification' => 1,
                    'mail' => 1,
                    'sms' => 1,
                    'email_subject' => 'Driver Assigned For Ride',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>Your Ride Later Trip is Confirmed</p>
                                    <p>Thank You for Riding with us</p>
                                    <p>Your "ride later" trip has been successfully scheduled.</p>
                                    <p>Best regards, </p>         
                                    <p>MI Softwares</p>',
                    'button_name'=>'View Details',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg',                     
                    'show_img' => 0,                   
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'New Trip Requested 😊️',
                    'push_body' => 'New Trip Requested, you can accept or Reject the request',
                    
                ],
                [
                    'id' => Str::uuid(),
                    'role' =>'user',
                    'topics' => 'Invoice For End of the Ride User',
                    'topics_content' => 'Choose how Customer will get notified about Sent notification on invoice for the end of the ride',
                    'push_notification' => 1,
                    'mail' => 1,
                    'sms' => 1,
                    'email_subject' => 'Invoice For Ride',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>Thank You for Riding with us</p>
                                    <p><strong>Here is the summary of your recent trip: <strong></p>',
                    'button_name'=>'Reset Password',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg',                      
                    'show_img' => 0,                 
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'Driver Ended the trip',
                    'push_body' => 'Driver finished the ride, Please help us by rating the driver',
                    
                ],
                [
                    'id' => Str::uuid(),
                    'role' =>'user',
                    'topics' => 'User Referral',
                    'topics_content' => 'Choose how Customer will get notified about Sent notification on referral code',
                    'push_notification' => 1,
                    'mail' => 1,
                    'sms' => 1,
                    'email_subject' => 'Referral Code User',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>Thanks you being a valued Customer! We are excited to offer you a referral code that you can share with your friends,
                                    family, or colleagues.</p>
                                    <p>When they use this referral code, they will receive a discount on their first ride, and you will earn rewards as well.</p>
                                    <p>Share this code with others, and start earning rewards today! The more you refer, the more you can earn!</p>
                                    <p>To use the referral code, simply share it with the person you refer,
                                     and they can enter it during the booking process on our app.</p>
                                     <p>Best regards, </p>         
                                    <p>MI Softwares</p>',

                    'button_name'=>'Share',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg', 
                    'show_img' => 0,                   
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'You have Earned with your Referral code 😊️',
                    'push_body' => 'We are happy to inform you that you have earned money with your referral code',
                    
                ],
                [
                    'id' => Str::uuid(),
                    'role' =>'user',
                    'topics' => 'User Wallet Amount',
                    'topics_content' => 'Choose how Customer will get notified about Sent notification on wallet amount Adjusted',
                    'push_notification' => 1,
                    'mail' => 1,
                    'sms' => 1,
                    'email_subject' => 'Wallet Amount Adjusted',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>We are happy to inform you that an amount has been successfully Adjusted to your wallet.</p> 
                                    <p><strong>Transaction Details</strong></p>
                                    <p><strong>Transaction Id:</strong> {transaction_id}</p>
                                    <p><strong>Amount:</strong> {currency}{amount}</p>
                                    <p><strong>Current Balance:</strong>{currency}{current_balance}</p>
                                    <p>Best regards, </p>         
                                    <p>MI Softwares</p>',
                    'button_name'=>'View Details',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg',
                    'show_img' => 0,                    
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'Amount Added Succesfully',
                    'push_body' => 'Amount Credited to Your Wallet Succesfully',
                    
                ],
                [
                    'id' => Str::uuid(),
                    'role' =>'user',
                    'topics' => 'User Amount Transfer',
                    'topics_content' => 'Choose how Customer will get notified about Sent notification on amount transfer',
                    'push_notification' => 1,
                    'mail' => 1,
                    'sms' => 1,
                    'email_subject' => 'Wallet Amount Transfer',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>We are writing to confirm that you have successfully transferred an amount from your wallet.</p>
                                    <p><strong>Transaction Details</strong></p>
                                    <p><strong>Transaction Id:</strong> {transaction_id}</p>
                                    <p><strong>Amount:</strong> {currency}{amount}</p>
                                    <p><strong>Current Balance:</strong>{currency}{current_balance}</p>
                                    <p>If you did not initiate this transfer, please contact our support team immediately.</p>
                                    <p>Thank you for using our services!</p>
                                    <p>Best regards, </p>         
                                    <p>MI Softwares</p>',
                    'button_name'=>'View Details',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg',    
                    'show_img' => 0,                
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'You Have Received Money',
                    'push_body' => 'You Have Received Money From',
                    
                ],
                [
                    'id' => Str::uuid(),
                    'role' =>'driver',
                    'topics' => 'Driver Referral',
                    'topics_content' => 'Choose how Driver will get notified about Sent notification on referral code',
                    'push_notification' => 1,
                    'mail' => 1,
                    'sms' => 1,
                    'email_subject' => 'Referral Code Driver',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>Thanks you being a valued Driver! We are excited to offer you a referral code that you can share with your friends,
                                    family, or colleagues.</p>
                                    <p>When they use this referral code, they will receive a discount on their first ride, and you will earn rewards as well.</p>
                                    <p>Share this code with others, and start earning rewards today! The more you refer, the more you can earn!</p>
                                    <p>To use the referral code, simply share it with the person you refer,
                                     and they can enter it during the booking process on our app.</p>
                                     <p>Best regards, </p>         
                                    <p>MI Softwares</p>',

                    'button_name'=>'Share',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg', 
                    'show_img' => 0,                   
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'You have Earned with your Referral code 😊️',
                    'push_body' => 'We are happy to inform you that you have earned money with your referral code',
                    
                ],

                [
                    'id' => Str::uuid(),
                    'role' =>'driver',
                    'topics' => 'Driver Document Expired',
                    'topics_content' => 'Choose how Driver will get notified about Sent notification on Document Expired',
                    'push_notification' => 1,
                    'mail' => 0,
                    'sms' => 0,
                    'email_subject' => 'Document Expired',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>Document Expired, Kindly Update your documents</p>
                                     <p>Best regards, </p>         
                                    <p>MI Softwares</p>',

                    'button_name'=>'Share',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg', 
                    'show_img' => 0,                   
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'Document Expires',
                    'push_body' => 'Document Expired',
                    
                ],
                [
                    'id' => Str::uuid(),
                    'role' =>'driver',
                    'topics' => 'Driver Ride Remainder',
                    'topics_content' => 'Choose how Driver will get notified about Sent notification on Ride Remainder',
                    'push_notification' => 1,
                    'mail' => 0,
                    'sms' => 0,
                    'email_subject' => 'Ride Remainder',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>To get a Ride, Open the app</p>
                                     <p>Best regards, </p>         
                                    <p>MI Softwares</p>',

                    'button_name'=>'Share',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg', 
                    'show_img' => 0,                   
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'Gentle Reminder 😊️',
                    'push_body' => 'Please open the App to get ride requests',
                    
                ],
                [
                    'id' => Str::uuid(),
                    'role' =>'driver',
                    'topics' => 'Driver Account Approval',
                    'topics_content' => 'Choose how Driver will get notified about Sent notification on account approval',
                    'push_notification' => 1,
                    'mail' => 1,
                    'sms' => 1,
                    'email_subject' => 'Account Approval',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Congratulations,{name} </p>
                                    <p>We are  to inform you that your driver account  has been successfully approved. You are now ready to start accepting ride requests and earning..</p>   
                                    <p>Please log in to your account using the credentials provided during registration. If you encounter any issues, feel free to reach out to our support team.</p>        
                                    <p>Best regards, </p>         
                                    <p>MI Softwares</p>',
                    'button_name' => 'Button',
                    'button_url' => 'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg', 
                    'show_img' => 0,                   
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]), 
                    'push_title' => 'Account Approved 😃️',
                    'push_body' => 'Your profile verified and approved',
                ],
                [
                    'id' => Str::uuid(),
                    'role' =>'driver',
                    'topics' => 'Driver Account Disapproval',
                    'topics_content' => 'Choose how Driver will get notified about Sent notification on account disapproval',
                    'push_notification' => 1,
                    'mail' => 1,
                    'sms' => 1,
                    'email_subject' => 'Account Disapproval',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p> We regret to inform you that your application to become a driver with our taxi service has not been approved at this time.</p>   
                                    <p>If you have any questions or need further clarification, feel free to contact our support team.</p>        
                                    <p>Best regards, </p>         
                                    <p>MI Softwares</p>',
                    'button_name' => 'Button',
                    'button_url' => 'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg', 
                    'show_img' => 0,                   
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'Account Declined 🙁️',
                    'push_body' => 'Your Account declined due to some reason. please contact our admin', 
                ],
                [
                    'id' => Str::uuid(),
                    'role' =>'driver',
                    'topics' => 'Driver Wallet Amount',
                    'topics_content' => 'Choose how Driver will get notified about Sent notification on wallet amount Adjusted',
                    'push_notification' => 1,
                    'mail' => 1,
                    'sms' => 1,
                    'email_subject' => 'Wallet Amount Adjusted',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>We are happy to inform you that an amount has been successfully Adjusted to your wallet.</p>  
                                    <p><strong>Transaction Details</strong></p>
                                    <p><strong>Transaction Id:</strong> {transaction_id}</p>
                                    <p><strong>Amount:</strong> {currency}{amount}</p>
                                    <p><strong>Current Balance:</strong>{currency}{current_balance}</p>
                                    <p>Best regards, </p>         
                                    <p>MI Softwares</p>',
                    'button_name'=>'View Details',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg', 
                    'show_img' => 0,                   
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'Amount Added Succesfully',
                    'push_body' => 'Amount Credited to Your Wallet Succesfully', 
                    
                ],
                [
                    'id' => Str::uuid(),
                    'role' =>'driver',
                    'topics' => 'Driver Amount Transfer',
                    'topics_content' => 'Choose how Driver will get notified about Sent notification on amount transfer',
                    'push_notification' => 1,
                    'mail' => 1,
                    'sms' => 1,
                    'email_subject' => 'Amount Transfer',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>We are writing to confirm that you have successfully transferred an amount from your wallet.</p>                                    
                                    <p><strong>Transaction Details</strong></p>
                                    <p><strong>Transaction Id:</strong> {transaction_id}</p>
                                    <p><strong>Amount:</strong> {currency}{amount}</p>
                                    <p><strong>Current Balance:</strong>{currency}{current_balance}</p>
                                    <p>If you did not initiate this transfer, please contact our support team immediately.</p>
                                    <p>Thank you for using our services!</p>',
                    'button_name'=>'View Details',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg',        
                    'show_img' => 0,            
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'You Have Received Money',
                    'push_body' => 'You Have Received Money From',
                    
                ],
                [
                    'id' => Str::uuid(),
                    'role' =>'driver',
                    'topics' => 'Driver Withdrawal Request Approval',
                    'topics_content' => 'Choose how Driver will get notified about Sent notification on request approval',
                    'push_notification' => 1,
                    'mail' => 1,
                    'sms' => 1,
                    'email_subject' => 'Withdrawal Request Approval',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>  Your withdrawal request has been approved. Here are the details:.</p>                                    
                                    <p><strong>Transaction Details</strong></p>
                                    <p><strong>Transaction Id:</strong> {transaction_id}</p>
                                    <p><strong>Amount:</strong> {currency}{amount}</p>
                                    <p><strong>Current Balance:</strong>{currency}{current_balance}</p>
                                    <p>If you have any issues with payment, kindly reply to this email or send an email to support@gmail.com</p>
                                    <p>Thank you for using our services!</p>',
                    'button_name'=>'View Details',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg', 
                    'show_img' => 0,                   
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'Payment Credited 😃️',
                    'push_body' => 'Your Payment Credited To Your Given Account',
                    
                ],
                [
                    'id' => Str::uuid(),
                    'role' =>'driver',
                    'topics' => 'Driver Withdrawal Request Decline',
                    'topics_content' => 'Choose how Driver will get notified about Sent notification on request decline',
                    'push_notification' => 1,
                    'mail' => 1,
                    'sms' => 1,
                    'email_subject' => 'Withdrawal Request Decline',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>Unfortunately, your withdrawal request has been declined.</p>   
                                    <p>If you have any issues with payment, kindly reply to this email or send an email to support@gmail.com</p>
                                    <p>Thank you for using our services!</p>',
                    'button_name'=>'View Details',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg',   
                    'show_img' => 0,                 
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'Payment Declained ',
                    'push_body' => 'Your Payment Declained',
                    
                ],
                [
                    'id' => Str::uuid(),
                    'role' =>'driver',
                    'topics' => 'Invoice For End of the Ride Driver',
                    'topics_content' => 'Choose how Driver will get notified about Sent notification on invoice for the end of the ride',
                    'push_notification' => 0,
                    'mail' => 1,
                    'sms' => 1,
                    'email_subject' => 'Invoice For Ride',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>Thank You for Riding with us</p>
                                    <p><strong>Here is the summary of your recent trip: <strong></p>',
                    'button_name'=>'Reset Password',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg',                      
                    'show_img' => 0,                 
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'Invoice Downloaded successfully',
                    'push_body' => 'Invoice Downloaded successfully',
                    
                ],
                [
                    'id' => Str::uuid(),
                    'role' =>'user',
                    'topics' => 'Trip Cancelled',
                    'topics_content' => 'Choose how Driver will get notified about Sent notification on Trip Cancelled',
                    'push_notification' => 1,
                    'mail' => 0,
                    'sms' => 0,
                    'email_subject' => 'Trip Cancelled',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>Trip Cancelled</p>',
                    'button_name'=>'Reset Password',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg',                      
                    'show_img' => 0,                 
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'Trip Cancelled By Customer 🙁️',
                    'push_body' => 'The customer cancelled the ride,please wait for another ride',
                    
                ],

                [
                    'id' => Str::uuid(),
                    'role' =>'driver',
                    'topics' => 'Trip Cancelled By Driver',
                    'topics_content' => 'Choose how Driver will get notified about Sent notification on Trip Cancelled',
                    'push_notification' => 1,
                    'mail' => 0,
                    'sms' => 0,
                    'email_subject' => 'Trip Cancelled',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>Trip Cancelled</p>',
                    'button_name'=>'Reset Password',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg',                      
                    'show_img' => 0,                 
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'Trip Cancelled By Driver 🙁️',
                    'push_body' => 'The Driver cancelled the ride,please wait for another ride',
                    
                ],
                [
                    'id' => Str::uuid(),
                    'role' =>'driver',
                    'topics' => 'Driver Daily Incentive',
                    'topics_content' => 'Choose how Driver will get notified about Sent notification on Daily Incentive',
                    'push_notification' => 1,
                    'mail' => 0,
                    'sms' => 0,
                    'email_subject' => 'Daily Incentive',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>Daily Incentive</p>',
                    'button_name'=>'Reset Password',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg',                      
                    'show_img' => 0,                 
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'Daily Incentive',
                    'push_body' => 'Daily Incentive Credited To Your Wallet',
                    
                ],
                [
                    'id' => Str::uuid(),
                    'role' =>'driver',
                    'topics' => 'Fleet Approved',
                    'topics_content' => 'Choose how Driver will get notified about Sent notification on Fleet Approved',
                    'push_notification' => 1,
                    'mail' => 0,
                    'sms' => 0,
                    'email_subject' => 'Fleet Approved',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>Trip Cancelled</p>',
                    'button_name'=>'Reset Password',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg',                      
                    'show_img' => 0,                 
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'Fleet Got Approved',
                    'push_body' => 'Fleet Got Approved, Now you can assign driver for your fleet',
                    
                ],
                [
                    'id' => Str::uuid(),
                    'role' =>'driver',
                    'topics' => 'Fleet Decline',
                    'topics_content' => 'Choose how Driver will get notified about Sent notification on Fleet Decline',
                    'push_notification' => 1,
                    'mail' => 0,
                    'sms' => 0,
                    'email_subject' => 'Fleet Decline',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>Fleet Decline</p>',
                    'button_name'=>'Reset Password',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg',                      
                    'show_img' => 0,                 
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'Fleet Got Declined by Admin',
                    'push_body' => 'Fleet Got Declined by Admin, Please Contact Admin',
                    
                ],
                [
                    'id' => Str::uuid(),
                    'role' =>'driver',
                    'topics' => 'Fleet Account Removed',
                    'topics_content' => 'Choose how Driver will get notified about Sent notification on Fleet Account Removed',
                    'push_notification' => 1,
                    'mail' => 0,
                    'sms' => 0,
                    'email_subject' => 'Fleet Account Removed',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>Fleet Account Removed</p>',
                    'button_name'=>'Reset Password',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg',                      
                    'show_img' => 0,                 
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'Fleet Removed From Your Account',
                    'push_body' => 'Fleet Removed From Your Account, Please Wait For Assigning Fleet',
                    
                ],
                [
                    'id' => Str::uuid(),
                    'role' =>'driver',
                    'topics' => ' New Fleet Assigned',
                    'topics_content' => 'Choose how Driver will get notified about Sent notification on New Fleet Assigned',
                    'push_notification' => 1,
                    'mail' => 0,
                    'sms' => 0,
                    'email_subject' => 'New Fleet Assigned',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>New Fleet Assigned</p>',
                    'button_name'=>'Reset Password',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg',                      
                    'show_img' => 0,                 
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'New Fleet Assigned For you',
                    'push_body' => 'New Fleet assigned for you',
                    
                ],
                [
                    'id' => Str::uuid(),
                    'role' =>'user',
                    'topics' => 'User Transfer Credit Points',
                    'topics_content' => 'Choose how Customer will get notified about Sent notification on Transfer Credit Points',
                    'push_notification' => 1,
                    'mail' => 0,
                    'sms' => 0,
                    'email_subject' => 'Transfer Credit Points',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>We are writing to confirm that you have successfully transferred an amount from your wallet.</p>
                                    <p><strong>Transaction Details</strong></p>
                                    <p><strong>Transaction Id:</strong> {transaction_id}</p>
                                    <p><strong>Amount:</strong> {currency}{amount}</p>
                                    <p><strong>Current Balance:</strong>{currency}{current_balance}</p>
                                    <p>If you did not initiate this transfer, please contact our support team immediately.</p>
                                    <p>Thank you for using our services!</p>
                                    <p>Best regards, </p>         
                                    <p>MI Softwares</p>',
                    'button_name'=>'View Details',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg',    
                    'show_img' => 0,                
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'Reward Points Converted 😃️',
                    'push_body' => 'Your Reward Points Credited To Your Account',
                    
                ],
                [
                    'id' => Str::uuid(),
                    'role' =>'user',
                    'topics' => 'User Transaction Failed',
                    'topics_content' => 'Choose how Customer will get notified about Sent notification on Transaction Failed',
                    'push_notification' => 1,
                    'mail' => 0,
                    'sms' => 0,
                    'email_subject' => 'Transaction Failed',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>We are writing to confirm that you have successfully transferred an amount from your wallet.</p>
                                    <p><strong>Transaction Details</strong></p>
                                    <p><strong>Transaction Id:</strong> {transaction_id}</p>
                                    <p><strong>Amount:</strong> {currency}{amount}</p>
                                    <p><strong>Current Balance:</strong>{currency}{current_balance}</p>
                                    <p>If you did not initiate this transfer, please contact our support team immediately.</p>
                                    <p>Thank you for using our services!</p>
                                    <p>Best regards, </p>         
                                    <p>MI Softwares</p>',
                    'button_name'=>'View Details',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg',    
                    'show_img' => 0,                
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'Transaction Failed',
                    'push_body' => 'Transaction Failed',
                    
                ],
                [
                    'id' => Str::uuid(),
                    'role' =>'driver',
                    'topics' => 'Driver Payment Received ',
                    'topics_content' => 'Choose how Driver will get notified about Sent notification on Payment Received',
                    'push_notification' => 1,
                    'mail' => 0,
                    'sms' => 0,
                    'email_subject' => 'Payment Received',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>We are writing to confirm that you have successfully transferred an amount from your wallet.</p>
                                    <p><strong>Transaction Details</strong></p>
                                    <p><strong>Transaction Id:</strong> {transaction_id}</p>
                                    <p><strong>Amount:</strong> {currency}{amount}</p>
                                    <p><strong>Current Balance:</strong>{currency}{current_balance}</p>
                                    <p>If you did not initiate this transfer, please contact our support team immediately.</p>
                                    <p>Thank you for using our services!</p>
                                    <p>Best regards, </p>         
                                    <p>MI Softwares</p>',
                    'button_name'=>'View Details',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg',    
                    'show_img' => 0,                
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'Payment Received',
                    'push_body' => 'Payment Received from customer',
                    
                ],
                [
                    'id' => Str::uuid(),
                    'role' =>'driver',
                    'topics' => 'Driver Ride Confirmation ',
                    'topics_content' => 'Choose how Driver will get notified about Sent notification on Ride Confirmation',
                    'push_notification' => 1,
                    'mail' => 0,
                    'sms' => 0,
                    'email_subject' => 'Ride Confirmed By Customer',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>We are writing to confirm that you have successfully transferred an amount from your wallet.</p>
                                    <p><strong>Transaction Details</strong></p>
                                    <p><strong>Transaction Id:</strong> {transaction_id}</p>
                                    <p><strong>Amount:</strong> {currency}{amount}</p>
                                    <p><strong>Current Balance:</strong>{currency}{current_balance}</p>
                                    <p>If you did not initiate this transfer, please contact our support team immediately.</p>
                                    <p>Thank you for using our services!</p>
                                    <p>Best regards, </p>         
                                    <p>MI Softwares</p>',
                    'button_name'=>'View Details',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg',    
                    'show_img' => 0,                
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'Ride Confirmed By Customer',
                    'push_body' => 'Ride Confirmed By Customer, Please Reach the customer pickup location on time',
                    
                ],
                [
                    'id' => Str::uuid(),
                    'role' =>'driver',
                    'topics' => 'Driver Arrived',
                    'topics_content' => 'Choose how Driver will get notified about Sent notification on Driver Arrived',
                    'push_notification' => 1,
                    'mail' => 0,
                    'sms' => 0,
                    'email_subject' => 'Driver Arrived',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>We are writing to confirm that you have successfully transferred an amount from your wallet.</p>
                                    <p><strong>Transaction Details</strong></p>
                                    <p><strong>Transaction Id:</strong> {transaction_id}</p>
                                    <p><strong>Amount:</strong> {currency}{amount}</p>
                                    <p><strong>Current Balance:</strong>{currency}{current_balance}</p>
                                    <p>If you did not initiate this transfer, please contact our support team immediately.</p>
                                    <p>Thank you for using our services!</p>
                                    <p>Best regards, </p>         
                                    <p>MI Softwares</p>',
                    'button_name'=>'View Details',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg',    
                    'show_img' => 0,                
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'Driver Arrived 😊️',
                    'push_body' => 'The Driver arrived to pick you up',
                    
                ],
                [
                    'id' => Str::uuid(),
                    'role' =>'user',
                    'topics' => 'Driver On the way to pickup',
                    'topics_content' => 'Choose how Driver will get notified about Sent notification on Driver On the way to pickup',
                    'push_notification' => 1,
                    'mail' => 0,
                    'sms' => 0,
                    'email_subject' => 'Driver On the way to pickup',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>We are writing to confirm that you have successfully transferred an amount from your wallet.</p>
                                    <p><strong>Transaction Details</strong></p>
                                    <p><strong>Transaction Id:</strong> {transaction_id}</p>
                                    <p><strong>Amount:</strong> {currency}{amount}</p>
                                    <p><strong>Current Balance:</strong>{currency}{current_balance}</p>
                                    <p>If you did not initiate this transfer, please contact our support team immediately.</p>
                                    <p>Thank you for using our services!</p>
                                    <p>Best regards, </p>         
                                    <p>MI Softwares</p>',
                    'button_name'=>'View Details',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg',    
                    'show_img' => 0,                
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'Driver Is On The Way To Pickup',
                    'push_body' => 'Driver Is On The Way To Pickup',
                    
                ],
                [
                    'id' => Str::uuid(),
                    'role' =>'user',
                    'topics' => 'User Trip Started ',
                    'topics_content' => 'Choose how Customer will get notified about Sent notification on Trip Started',
                    'push_notification' => 1,
                    'mail' => 0,
                    'sms' => 0,
                    'email_subject' => 'Trip Started',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>We are writing to confirm that you have successfully transferred an amount from your wallet.</p>
                                    <p><strong>Transaction Details</strong></p>
                                    <p><strong>Transaction Id:</strong> {transaction_id}</p>
                                    <p><strong>Amount:</strong> {currency}{amount}</p>
                                    <p><strong>Current Balance:</strong>{currency}{current_balance}</p>
                                    <p>If you did not initiate this transfer, please contact our support team immediately.</p>
                                    <p>Thank you for using our services!</p>
                                    <p>Best regards, </p>         
                                    <p>MI Softwares</p>',
                    'button_name'=>'View Details',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg',    
                    'show_img' => 0,                
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'Trip Started',
                    'push_body' => 'Trip started towards the drop location',
                    
                ],
                [
                    'id' => Str::uuid(),
                    'role' =>'user',
                    'topics' => 'User Trip Request Accepted ',
                    'topics_content' => 'Choose how Customer will get notified about Sent notification on Trip Request Accepted',
                    'push_notification' => 1,
                    'mail' => 0,
                    'sms' => 0,
                    'email_subject' => 'Trip Request Accepted',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>We are writing to confirm that you have successfully transferred an amount from your wallet.</p>
                                    <p><strong>Transaction Details</strong></p>
                                    <p><strong>Transaction Id:</strong> {transaction_id}</p>
                                    <p><strong>Amount:</strong> {currency}{amount}</p>
                                    <p><strong>Current Balance:</strong>{currency}{current_balance}</p>
                                    <p>If you did not initiate this transfer, please contact our support team immediately.</p>
                                    <p>Thank you for using our services!</p>
                                    <p>Best regards, </p>         
                                    <p>MI Softwares</p>',
                    'button_name'=>'View Details',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg',    
                    'show_img' => 0,                
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'Trip Request Accepted',
                    'push_body' => 'The Driver is coming to pick you',
                    
                ],
                [
                    'id' => Str::uuid(),
                    'role' =>'user',
                    'topics' => 'Driver not Found',
                    'topics_content' => 'Choose how Customer will get notified about Sent notification on Driver not Found',
                    'push_notification' => 1,
                    'mail' => 0,
                    'sms' => 0,
                    'email_subject' => 'Driver not Found',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>We are writing to confirm that you have successfully transferred an amount from your wallet.</p>
                                    <p><strong>Transaction Details</strong></p>
                                    <p><strong>Transaction Id:</strong> {transaction_id}</p>
                                    <p><strong>Amount:</strong> {currency}{amount}</p>
                                    <p><strong>Current Balance:</strong>{currency}{current_balance}</p>
                                    <p>If you did not initiate this transfer, please contact our support team immediately.</p>
                                    <p>Thank you for using our services!</p>
                                    <p>Best regards, </p>         
                                    <p>MI Softwares</p>',
                    'button_name'=>'View Details',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg',    
                    'show_img' => 0,                
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'No Driver Found Around You 🙁️',
                    'push_body' => 'Sorry plese try again after some times,there is no driver available for your ride now',
                    
                ],
                [
                    'id' => Str::uuid(),
                    'role' =>'Driver',
                    'topics' => 'Driver Subscription',
                    'topics_content' => 'Choose how Driver will get notified about Sent notification on Driver Subscription Successfully',
                    'push_notification' => 1,
                    'mail' => 0,
                    'sms' => 0,
                    'email_subject' => 'Subscribed Succesfully',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>We are writing to confirm that you have successfully transferred an amount from your wallet.</p>
                                    <p><strong>Transaction Details</strong></p>
                                    <p><strong>Transaction Id:</strong> {transaction_id}</p>
                                    <p><strong>Amount:</strong> {currency}{amount}</p>
                                    <p><strong>Current Balance:</strong>{currency}{current_balance}</p>
                                    <p>If you did not initiate this transfer, please contact our support team immediately.</p>
                                    <p>Thank you for using our services!</p>
                                    <p>Best regards, </p>         
                                    <p>MI Softwares</p>',
                    'button_name'=>'View Details',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg',    
                    'show_img' => 0,                
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'Subscribed Succesfully',
                    'push_body' => 'You have subscribed successfully',
                    
                ],

            ]);
        }

    //     // Insert translations
        $notificationChannels = \DB::table('notification_channels')->get();

                    foreach ($notificationChannels as $channelData) 
            {
                // Check if the notification channel already exists based on a unique attribute (like 'email_subject')
                $notificationChannel = NotificationChannel::where('topics', $channelData->topics)->first();

                if ($notificationChannel) {
                    // Update existing notification channel
                    // $notificationChannel->update($channelData);
                    $notificationChannel->update((array) $channelData);

                    // Delete old translations for the notification channel
                    $notificationChannel->notificationChannelTranslationWords()->delete();
                } else {
                    // Create new notification channel if it doesn't exist
                    // $notificationChannel = NotificationChannel::create($channelData);
                    $notificationChannel = NotificationChannel::create((array) $channelData);
                }

                // Prepare translation data for the 'en' locale
                $translationData = [
                    'email_subject' => $channelData->email_subject,
                    'mail_body' => $channelData->mail_body,
                    'button_name' => $channelData->button_name,
                    'footer_content' => $channelData->footer_content,
                    'footer_copyrights' => $channelData->footer_copyrights,
                    'push_title' => $channelData->push_title,
                    'push_body' => $channelData->push_body,
                    'locale' => 'en',
                    'notification_channel_id' => $notificationChannel->id,
                ];

                // Store the translations as objects for 'en' locale
                $translations_data['en'] = new \stdClass();
                $translations_data['en']->locale = 'en';
                $translations_data['en']->email_subject = $channelData->email_subject;
                $translations_data['en']->mail_body = $channelData->mail_body;
                $translations_data['en']->button_name = $channelData->button_name;
                $translations_data['en']->footer_content = $channelData->footer_content;
                $translations_data['en']->footer_copyrights = $channelData->footer_copyrights;
                $translations_data['en']->push_title = $channelData->push_title;
                $translations_data['en']->push_body = $channelData->push_body;

                // Insert the translation data into the related translation table
                $notificationChannel->notificationChannelTranslationWords()->create($translationData);

                // Store the translation dataset in JSON format for the notification channel
                $notificationChannel->translation_dataset = json_encode($translations_data);
                
                // Save the updated notification channel with its translations
                $notificationChannel->save();
            }

    }
}


