<div>
    <table class="body-wrap">
                <tbody><tr>
                    <td></td>
                    <td class="container" width="600">
                        <div class="content">
                            <table class="main" width="100%" cellpadding="0" cellspacing="0" itemprop="action" itemscope="" itemtype="http://schema.org/ConfirmAction" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; border-radius: 3px; margin: 0; border: none;">
                                <tbody><tr>
                                    <td class="content-wrap">
                                        <meta itemprop="name" content="Confirm Email" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                        <table width="100%" cellpadding="0" cellspacing="0" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                            <tbody><tr style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                <td class="content-block" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;" valign="top">
                                                    <div style="margin-bottom: 15px;" >
                                                        <img src="{{ asset('storage/uploads/website/images/' . $notificationData['logo_img']) }}" alt="logo-img" height="23" >
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td  class="content-block" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 20px; line-height: 1.5; font-weight: 500; vertical-align: top; margin: 0; padding: 0 0 10px;" valign="top">
                                                    {{$notificationData['email_subject']}}
                                                </td>
                                            </tr> 
                                            <tr>
                                                {!! $notificationData['mail_body'] !!}
                                            </tr>
                                            <tr>
                                                @if($notificationData['show_button'] == 1)
                                                    <div style="display: flex; margin-top: 2rem;" class="mt-2">
                                                        <a href="{{ $notificationData['button_url'] }}" target="_blank" 
                                                        class="btn btn-primary ms-auto me-auto" 
                                                        style="background-color: #405189; color: white; padding: 5px;">
                                                            {{ $notificationData['button_name'] }}
                                                        </a>
                                                    </div>
                                                @endif
                                            </tr>
                                            <tr>
                                                <td class="content-block">
                                                @if($notificationData['show_img'] == 1)
                                                    <div style="display: flex; align-items: center;" >
                                                        <img class="img-fluid mt-4" width="600" height="300" src="{{ asset('storage/uploads/website/images/' . $notificationData['banner_img']) }}" alt="banner-img">
                                                  
                                                    </div>
                                                    @endif
                                                </td>
                                            </tr>
                                        </tbody></table>
                                    </td>
                                </tr>
                            </tbody></table>

                            <div class="p-3 bg-light rounded mb-4 mt-5" style="padding: 1rem; margin-bottom: 2rem; margin-top: 1rem;">
                                <div class="row g-2" >
                                  <div style="text-align: center;">{!! $notificationData['footer_content'] !!}</div>
                                  <div style="text-align: center; margin: 0px auto;">
                              
                                    <ul style="list-style: none;display: flex; justify-content: center; padding-top: 15px;padding-left: 0px; margin-bottom: 20px; font-family: 'Roboto', sans-serif;margin-left:25%;text-align:center;">
                                        <li >
                                            <a href="#" style="color: #495057;">Subscribe</a>
                                        </li>
                                        <div style="border-right: 2px; border-right-style: solid; border-right-color:#98a6ad;margin-right: 15px;margin-left: 15px;"></div>
                                        <li >
                                            <a href="#" style="color: #495057;">Privacy</a>
                                        </li>
                                        <div style="border-right: 2px; border-right-style: solid; border-right-color:#98a6ad;margin-right: 15px;margin-left: 15px;"></div>
                                        <li >
                                            <a href="#" style="color: #495057;">Web</a>
                                        </li>
                                    </ul>
                                    <div class="row d-flex align-items-center justify-content-center mt-5" style="display: flex;align-items: center;justify-content: center; margin-top: 2rem;margin-left:25%;;text-align:center;">
                                      <div >
                                        @if($notificationData['show_fbicon'] == 1)
                                            <a href="{{ $notificationData['footer']['footer_fblink'] }}">
                                                    <img class="img-fluid" src="{{ asset('images/facebook.png') }}" alt="facebook" width="24" style="margin-right:2rem">
                                            </a>        
                                        @endif                                     
                                      </div>
                                      <div >
                                      @if($notificationData['show_instaicon'] == 1)
                                        <a href="{{ $notificationData['footer']['footer_instalink'] }}">
                                                <img class="img-fluid" src="{{ asset('images/instagram.png') }}" alt="Instagram" width="24" style="margin-right:2rem">
                                        </a>
                                    @endif
 
                                      </div>
                                      <div >
                                      @if($notificationData['show_twittericon'] == 1)
                                      <a href="{{ $notificationData['footer']['footer_twitterlink'] }}">
                                            <img class="img-fluid" src="{{ asset('images/twitter.png') }}" alt="twitter" width="24" style="margin-right:2rem">
                                      </a>
                                      @endif
                                      </div>
                                      <div >
                                      @if($notificationData['show_linkedinicon'] == 1)
                                      <a href="{{ $notificationData['footer']['footer_linkedinlink'] }}">
                                            <img class="img-fluid" src="{{ asset('images/linkedin.png') }}" alt="linkedin" width="24" style="margin-right:2rem">
                                      </a>    
                                      @endif
                                      </div>
                                    </div>
                                    <div class="mt-4" style="margin-top:1rem;">
                                      <p style="font-family: 'Roboto', sans-serif; font-size: 14px;color: #98a6ad; margin: 0px;">
                                        {{ $notificationData['footer_copyrights'] }}
                                    </p>
                                    </div>

                                </div>   
                                </div>
                            </div>                    
                        </div>
                    </td>
                </tr>
            </tbody></table>
    </div>
