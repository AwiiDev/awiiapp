    <div>
        <table class="body-wrap">
            <tbody>
                <tr>
                    <td class="container" width="600">
                        <div class="content">
                            <table class="main" width="100%" cellpadding="0" cellspacing="0" itemprop="action" itemscope="" itemtype="http://schema.org/ConfirmAction" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; border-radius: 3px; margin: 0; border: none;">
                                <tbody>
                                    <tr>
                                        <td class="content-wrap">
                                            <meta itemprop="name" content="Confirm Email" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                            <table width="100%" cellpadding="0" cellspacing="0" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                <tbody>
                                                    <tr style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                        <td class="content-block" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;" valign="top">
                                                            <div style="margin-bottom: 15px;" >
                                                                <img src="{{ asset('storage/uploads/website/images/' . $notificationData['logo_img']) }}" alt="logo-img" height="23" >
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td  class="content-block" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 20px; line-height: 1.5; font-weight: 500; vertical-align: top; margin: 0; padding: 0 0 10px;" valign="top">
                                                        {!! $app_name !!} | {{$notificationData['email_subject']}}
                                                        </td>
                                                    </tr> 
                                                    <tr>
                                                        {!! $notificationData['mail_body'] !!}
                                                    </tr>

                                                    <div id="divID">

                                                    <div style="margin-left: -30px; padding: 0">
                                                        <table bgcolor="#eeeeee" border="0" cellpadding="0" cellspacing="0" width="100%" >
                                                            <tbody>
                                                                <tr>
                                                                    <td bgcolor="#EEEEEE" style="padding: 20px 15px 0 15px">
                                                                        <!-- <div align="center"> -->
                                                    <table border="0" cellpadding="0" cellspacing="0" width="660" >
                                                    <tbody>
                                                    <tr>
                                                    <td bgcolor="#ffffff"
                                                    style="padding: 24px 14px 0 14px; border-top-left-radius: 4px; border-top-right-radius: 4px">
                                                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                                    <tbody>
                                                    <tr>
                                                    <td width="40%" align="left">
                                                    <table>
                                                    <tbody>
                                                    <tr>
                                                    <td
                                                    style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 14px; color: #707070; font-weight: normal; line-height: 16px">
                                                    {{ $data['data']['completed_at'] ?? 'N/A' }}
                                                    <br />
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    </td>
                                                    <td width="60%" align="right">
                                                    <table border="0" cellpadding="0" cellspacing="0">
                                                    <tbody>
                                                    <tr>
                                                    <td align="right" style="padding: 0 0 5px 0">
                                                    <a href="https://evoj.com/"
                                                    style="float: right" rel="noreferrer"
                                                    target="_blank">
                                                    @if ($logo && $logo->value)
                                                        <img src="{{ asset('storage/uploads/system-admin/logo/' . $logo->value) }}" alt="Logo" class="logo"
                                                        style="display: flex; float: none; text-align: center; width: 150px;margin-top:20px;">
                                                    @else
                                                        <p>No logo available</p>
                                                    @endif
                                                    <!-- <img width="76"
                                                    style="display: inline; float: none; text-align: center; width: 196px"
                                                    src="{{ asset('invoice/logo.png') }}"
                                                    alt="" class="CToWUd" /> -->
                                                    </a>
                                                    <br>
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    </td>
                                                    </tr>
                                                    <tr>
                                                    <td style="color: #ffffff; line-height: 10px">
                                                    <img src="{{ asset('invoice/Invoice_White_line.png') }}"
                                                    style="width: 100%" class="CToWUd" />
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    </td>
                                                    </tr>
                                                    <tr>
                                                    <td bgcolor="#ffffff" align="center"
                                                    style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 40px; font-weight: bold; color: #000000">
                                                    <table>
                                                    <tbody>
                                                    <tr>
                                                    <td style="padding-left: 15px">
                                                    <table>
                                                    <tbody>
                                                    <tr>
                                                    <td
                                                    style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 40px; font-weight: bold; color: #000000; padding-right: 10px">
                                                    {{ $data['data']['requested_currency_symbol'] }}
                                                    {{ number_format($data['data']['requestBill']['data']['driver_commision'], 2) }}
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    </td>
                                                    <td style="padding-top: 10px; padding-left: 6px"></td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    </td>
                                                    </tr>
                                                    <tr>
                                                    <td bgcolor="#ffffff" align="center"
                                                    style="padding-right: 20px; padding-left: 20px; padding-bottom: 2px; font-family: Helvetica, 'Helvetica Neue', Arial, sans-serif; color: #777777">
                                                    <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center">
                                                    <tbody>
                                                    <tr>
                                                    <td align="center" style="
                                                    padding-top: 10px;
                                                    background-image: url('Invoice_background.png');
                                                    background-position: center 100%;
                                                    background-repeat: no-repeat;
                                                    ">
                                                    <span style="
                                                    background-color: #ffffff;
                                                    padding-top: 0px;
                                                    padding-left: 0px;
                                                    padding-right: 0px;
                                                    font-size: 14px;
                                                    font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
                                                    color: #707070;
                                                    ">
                                                    {{--
                                                    &nbsp;&nbsp;&nbsp;{{ $data['data']['driver_invoice_no'] }}&nbsp;&nbsp;&nbsp;<br />  --}}
                                                    </span>
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    </td>
                                                    </tr>
                                                    <tr>
                                                    <td bgcolor="#ffffff" align="center"
                                                    style="font-family: Helvetica, Helvetica Neue, Arial, sans-serif; font-size: 14px; word-spacing: -0.3px; color: #000000; padding-bottom: 0px; padding-top: 17px">
                                                    Thanks for driving with us, {{  $data['data']['driverDetail']['data']['name'] }}
                                                    </td>
                                                    </tr>
                                                    <tr>
                                                    <td bgcolor="#ffffff" align="center"
                                                    style="padding-left: 16px; padding-right: 16px">
                                                    <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                                    <tbody>
                                                    <tr>
                                                    <td
                                                    style="border-bottom: 3px solid #ececec; width: 100%; text-align: center; color: #ffffff">
                                                    <img src="{{ asset('invoice/Invoice_White_line.png') }}"
                                                    style="width: 100%" class="CToWUd" />
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    <!-- </div> -->
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    <table bgcolor="#eeeeee" border="0" cellpadding="0" cellspacing="0" width="100%">
                                                    <tbody>
                                                    <tr>
                                                    <td bgcolor="#EEEEEE" align="center" style="padding: 0 15px 0 15px">
                                                    <table border="0" cellpadding="0" cellspacing="0" width="660">
                                                    <tbody>
                                                    <tr>
                                                    <td>
                                                    <table cellspacing="0" cellpadding="0" border="0" width="100%">
                                                    <tbody>
                                                    <tr>
                                                    <td bgcolor="#ffffff" valign="top"
                                                    style="padding-top: 28px; padding-bottom: 0; padding-left: 0px; padding-right: 0px">
                                                    <table cellpadding="0" cellspacing="0" border="0" width="49.5%"
                                                    align="left">
                                                    <tbody>
                                                    <tr>
                                                    <td style="padding-left: 14px; padding-right: 14px">
                                                    <table cellpadding="0" cellspacing="0" border="0"
                                                    width="100%">
                                                    <tbody>
                                                    <tr>
                                                    <td align="center" style="
                                                    font-family: Helvetica, 'Helvetica Neue', Arial, sans-serif;
                                                    color: #333333;
                                                    font-size: 16px;
                                                    padding-bottom: 16px;
                                                    font-weight: bold;
                                                    border-bottom: 1px solid #d7d7d7;
                                                    ">
                                                    Trip Details
                                                    </td>
                                                    </tr>
                                                    <tr>
                                                    <td style="padding: 9px 0 0 0">
                                                    <table cellspacing="0"
                                                    cellpadding="0" border="0"
                                                    width="100%">
                                                    <tbody>
                                                    <tr>
                                                    <td valign="top"
                                                    style="border-bottom: 1px solid #eeeeee; padding-bottom: 10px; padding-left: 14px">
                                                    <table
                                                    cellpadding="0"
                                                    cellspacing="0"
                                                    border="0"
                                                    width="100%"
                                                    style="width: 100%"
                                                    align="left">
                                                    <tbody>
                                                    <tr>
                                                    <td align="left"
                                                    style="padding-left: 16px">
                                                    <table>
                                                    <tbody>
                                                    <tr>
                                                    <td
                                                    style="font-family: Helvetica, 'Helvetica Neue', Arial, sans-serif; color: #000000; font-size: 16px; line-height: 18px">

                                                    For:
                                                    {{  $data['data']['driverDetail']['data']['name'] }}
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    </td>
                                                    </tr>
                                                    <tr>
                                                    <td
                                                    style="color: #ffffff; line-height: 12px !important; font-size: 10px !important">
                                                    <img src="{{ asset('invoice/Invoice_White_line.png') }}"
                                                    style="width: 100%"
                                                    class="CToWUd" />
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    </td>
                                                    </tr>
                                                    <tr>
                                                    <td style="padding: 0 0 0 0">
                                                    <table cellspacing="0"
                                                    cellpadding="0" border="0"
                                                    width="100%">
                                                    <tbody>
                                                    <tr>
                                                    <td valign="top"
                                                    style="border-bottom: 1px solid #eeeeee; padding-bottom: 12px; padding-left: 14px; padding-top: 5px">
                                                    <table
                                                    cellpadding="0"
                                                    cellspacing="0"
                                                    border="0"
                                                    width="100%"
                                                    style="width: 100%"
                                                    align="left">
                                                    <tbody>
                                                    <tr>
                                                    <td align="left"
                                                    style="width: 42px; padding-left: 2px"
                                                    width="42">
                                                    <img width="40"
                                                    style="width: 40px"
                                                    src="{{ asset('invoice/Invoice_dash_icon_2x.png') }}"
                                                    alt=""
                                                    class="CToWUd" />
                                                    </td>
                                                    <td align="left"
                                                    style="font-family: Helvetica, Helvetica Neue, Arial, sans-serif; color: #000000; font-size: 14px; padding-left: 16px; padding-top: 8px">
                                                    Total
                                                    Kms:
                                                    {{ number_format($data['data']['total_distance'], 2) }}
                                                    km
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    </td>
                                                    </tr>
                                                    <tr>
                                                    <td
                                                    style="color: #ffffff; line-height: 12px; font-size: 10px">
                                                    <img src="{{ asset('invoice/Invoice_White_line.png') }}"
                                                    style="width: 100%"
                                                    class="CToWUd" />
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    </td>
                                                    </tr>
                                                    <tr>
                                                    <td style="padding: 0 0 0 0">
                                                    <table cellspacing="0"
                                                    cellpadding="0" border="0"
                                                    width="100%">
                                                    <tbody>
                                                    <tr>
                                                    <td valign="top"
                                                    style="border-bottom: 1px solid #eeeeee; padding-bottom: 12px; padding-left: 14px; padding-top: 5px">
                                                    <table
                                                    cellpadding="0"
                                                    cellspacing="0"
                                                    border="0"
                                                    width="100%"
                                                    style="width: 100%"
                                                    align="left">
                                                    <tbody>
                                                    <tr>
                                                    <td align="left"
                                                    style="width: 42px; padding-left: 2px"
                                                    width="42">
                                                    <img width="40"
                                                    style="width: 40px"
                                                    src="{{ asset('invoice/Invoice_time_icon_2x.png') }}"
                                                    alt=""
                                                    class="CToWUd" />
                                                    </td>
                                                    <td align="left"
                                                    style="font-family: Helvetica, Helvetica Neue, Arial, sans-serif; color: #000000; font-size: 14px; padding-left: 16px; padding-top: 8px">
                                                    Total
                                                    Mins:
                                                    {{ $data['data']['total_time'] }}
                                                    min
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    </td>
                                                    </tr>
                                                    <tr>
                                                    <td
                                                    style="color: #ffffff; line-height: 12px; font-size: 10px">
                                                    <img src="{{ asset('invoice/Invoice_White_line.png') }}"
                                                    style="width: 100%"
                                                    class="CToWUd" />
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    </td>
                                                    </tr>
                                                    <tr>
                                                    <td style="padding: 0 0 0 0">
                                                    <table cellspacing="0"
                                                    cellpadding="0" border="0"
                                                    width="100%">
                                                    <tbody>
                                                    <tr>
                                                    <td valign="top"
                                                    style="border-bottom: 1px solid #eeeeee; padding-bottom: 7px; padding-left: 14px; padding-top: 5px">
                                                    <table
                                                    cellpadding="0"
                                                    cellspacing="0"
                                                    border="0"
                                                    width="100%"
                                                    style="width: 100%"
                                                    align="left">
                                                    <tbody>
                                                    <tr>
                                                    <td align="left"
                                                    style="width: 42px; padding-left: 2px"
                                                    width="42">
                                                    <img width="38"
                                                    style="width: 38px"
                                                    src="{{ asset('invoice/Invoice_driver_default.png') }}"
                                                    alt=""
                                                    class="CToWUd" />
                                                    </td>
                                                    <td align="left"
                                                    style="
                                                    padding-top: 4px;
                                                    font-family: Helvetica, 'Helvetica Neue', Arial, sans-serif;
                                                    color: #000000;
                                                    font-size: 14px;
                                                    padding-left: 16px;
                                                    line-height: 16px;
                                                    ">
                                                    {{ $data['data']['vehicle_type_name'] }}
                                                    - {{ strtoupper($data['data']['driverDetail']['data']['car_number'])}}
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    </td>
                                                    </tr>
                                                    <tr>
                                                    <td
                                                    style="color: #ffffff; line-height: 8px">
                                                    <img src="{{ asset('invoice/Invoice_White_line.png') }}"
                                                    style="width: 100%"
                                                    class="CToWUd" />
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    </td>
                                                    </tr>
                                                    <tr style="vertical-align: top; text-align: left; display: block; background-color: #ffffff; padding-bottom: 10px; padding-top: 5px"
                                                    align="left" bgcolor="#ffffff">
                                                    <td style="word-break: break-word; border-collapse: collapse !important; vertical-align: top; text-align: left; display: inline-block; padding: 10px 0 0 14px"
                                                    align="left" valign="top">
                                                    <table
                                                    style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: auto; padding: 0">
                                                    <tbody>
                                                    <tr style="vertical-align: top; text-align: left; width: 100%; padding-top: 5px"
                                                    align="left">
                                                    <td style="
                                                    word-break: break-word;
                                                    border-collapse: collapse !important;
                                                    vertical-align: top;
                                                    text-align: left;
                                                    display: table-cell;
                                                    width: 80px !important;
                                                    line-height: 16px;
                                                    height: auto;
                                                    padding: 0 0 0 0;
                                                    " align="left" valign="top">
                                                    <span
                                                    style="font-size: 14px; font-family: Helvetica, 'Helvetica Neue', Arial, sans-serif; font-weight: normal; color: #000000 !important">
                                                    <span> <a
                                                    style="text-decoration: none !important; color: #000000 !important"
                                                    rel="noreferrer">{{ $data['data']['trip_start_time'] ?? 'N/A' }}</a>
                                                    </span>
                                                    </span>
                                                    </td>
                                                    <td rowspan="2" style="
                                                    word-break: break-word;
                                                    border-collapse: collapse !important;
                                                    vertical-align: top;
                                                    text-align: left;
                                                    display: table-cell;
                                                    width: 17px !important;
                                                    padding: 3px 2px 10px 2px;
                                                    " align="left" valign="top">
                                                    <img width="6"
                                                    height="84px"
                                                    src="{{ asset('invoice/Invoice_src_dest.png') }}"
                                                    style="outline: none; text-decoration: none; float: left; clear: both; display: block; width: 6px !important; height: 84px; padding-top: 5px"
                                                    align="left"
                                                    class="CToWUd" />
                                                    </td>
                                                    <td style="
                                                    word-break: break-word;
                                                    border-collapse: collapse !important;
                                                    vertical-align: top;
                                                    text-align: left;
                                                    display: table-cell;
                                                    width: 197px;
                                                    line-height: 16px;
                                                    height: 57px;
                                                    padding: 0 10px 10px 0;
                                                    " align="left" valign="top">
                                                    <span style="
                                                    font-size: 14px;
                                                    font-family: Helvetica, 'Helvetica Neue', Arial, sans-serif;
                                                    color: #000000 !important;
                                                    line-height: 16px;
                                                    text-decoration: none;
                                                    ">
                                                    {{ $data['data']['pick_address'] }}
                                                    </span>
                                                    </td>
                                                    </tr>
                                                    <tr style="vertical-align: top; text-align: left; width: 100%; padding: 0"
                                                    align="left">
                                                    <td style="
                                                    word-break: break-word;
                                                    border-collapse: collapse !important;
                                                    vertical-align: top;
                                                    text-align: left;
                                                    display: table-cell;
                                                    width: 80px !important;
                                                    line-height: 16px;
                                                    height: auto;
                                                    padding: 0 0 0 0;
                                                    " align="left" valign="top">
                                                    <span
                                                    style="font-size: 14px; font-family: Helvetica, 'Helvetica Neue', Arial, sans-serif; font-weight: normal; color: #000000 !important">
                                                    <span>
                                                    <a style="text-decoration: none !important; color: #000000 !important"
                                                    rel="noreferrer">{{ $data['data']['completed_at'] ?? 'N/A' }}</a>
                                                    </span>
                                                    </span>
                                                    </td>
                                                    <td style="
                                                    word-break: break-word;
                                                    border-collapse: collapse !important;
                                                    vertical-align: top;
                                                    text-align: left;
                                                    display: table-cell;
                                                    width: 197px;
                                                    line-height: 16px;
                                                    height: auto;
                                                    padding: 0 0px 0 0;
                                                    " align="left" valign="top">
                                                    <span style="
                                                    font-size: 14px;
                                                    font-family: Helvetica, 'Helvetica Neue', Arial, sans-serif;
                                                    color: #000000 !important;
                                                    line-height: 16px;
                                                    text-decoration: none;
                                                    ">
                                                    {{ $data['data']['drop_address'] }}
                                                    </span>
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    <table cellpadding="0" cellspacing="0" border="0" width="49.5%"
                                                    align="left">
                                                    <tbody>
                                                    <tr>
                                                    <td style="padding-right: 14px; padding-left: 14px">
                                                    <table cellpadding="0" cellspacing="0" border="0"
                                                    width="100%">
                                                    <tbody>
                                                    <tr>
                                                    <td align="center" style="
                                                    font-family: Helvetica, 'Helvetica Neue', Arial, sans-serif;
                                                    color: #333333;
                                                    font-size: 16px;
                                                    padding-bottom: 16px;
                                                    font-weight: bold;
                                                    border-bottom: 1px solid #d7d7d7;
                                                    ">
                                                    Bill Details
                                                    </td>
                                                    </tr>
                                                    <tr>
                                                    <td bgcolor="#ffffff">
                                                    <table cellspacing="0"
                                                    cellpadding="0" border="0"
                                                    width="100%">
                                                    <tbody>
                                                    <tr>
                                                    <td border="0"
                                                    cellpadding="0"
                                                    cellspacing="0"
                                                    valign="top"
                                                    style="padding-left: 7px; padding-right: 13px; padding-top: 10px; padding-bottom: 2px">
                                                    <table
                                                    cellpadding="0"
                                                    cellspacing="0"
                                                    border="0"
                                                    width="72%"
                                                    style="width: 72%"
                                                    align="left">
                                                    <tbody>
                                                    <tr>
                                                    <td>
                                                    <table
                                                    cellpadding="0"
                                                    cellspacing="0"
                                                    border="0"
                                                    width="100%">
                                                    <tbody>
                                                    <tr>
                                                    <td
                                                    align="left">
                                                    <table>
                                                    <tbody>
                                                    <tr>
                                                    <td align="left"
                                                    style="font-family: Arial, Helvetica, 'Helvetica Neue', sans-serif; color: #707070; font-size: 14px">
                                                    Total Distance Driven
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    <table
                                                    cellpadding="0"
                                                    cellspacing="0"
                                                    border="0"
                                                    width="27%"
                                                    style="width: 27%"
                                                    align="right">
                                                    <tbody>
                                                    <tr>
                                                    <td
                                                    style="padding: 0 0 0 0">
                                                    <table
                                                    cellpadding="0"
                                                    cellspacing="0"
                                                    border="0"
                                                    width="100%">
                                                    <tbody>
                                                    <tr>
                                                    <td
                                                    align="left">
                                                    <table
                                                    align="right">
                                                    <tbody>
                                                    <tr>
                                                    <td
                                                    style="font-family: Arial, Helvetica, 'Helvetica Neue', sans-serif; color: #707070; font-size: 14px">
                                                    {{number_format($data['data']['requestBill']['data']['total_distance'],2) }} km
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    </td>
                                                    </tr>
                                                    <tr>
                                                    <td
                                                    style="color: #ffffff; line-height: 8px !important; padding: 0 0 0 0; font-size: 13px">
                                                    <img style="width: 100%"
                                                    src="{{ asset('invoice/Invoice_White_line.png') }}"
                                                    class="CToWUd" />
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    </td>
                                                    </tr>


                                                    <!-- Ride fare-->

                                                    <tr>
                                                    <td bgcolor="#ffffff">
                                                    <table cellspacing="0"
                                                    cellpadding="0" border="0"
                                                    width="100%">
                                                    <tbody>
                                                    <tr>
                                                    <td border="0"
                                                    cellpadding="0"
                                                    cellspacing="0"
                                                    valign="top"
                                                    style="padding-top: 6px; padding-bottom: 6px; padding-left: 7px; padding-right: 13px;">
                                                    <table
                                                    cellpadding="0"
                                                    cellspacing="0"
                                                    border="0"
                                                    width="72%"
                                                    style="width: 72%"
                                                    align="left">
                                                    <tbody>
                                                    <tr>
                                                    <td
                                                    style="padding: 0 0 0 0">
                                                    <table
                                                    cellpadding="0"
                                                    cellspacing="0"
                                                    border="0"
                                                    width="100%">
                                                    <tbody>
                                                    <tr>
                                                    <td
                                                    align="left">
                                                    <table>
                                                    <tbody>
                                                    <tr>
                                                    <td align="left"
                                                    style="
                                                    font-family: Helvetica, 'Helvetica Neue', Arial, sans-serif;
                                                    color: #000000;
                                                    font-size: 14px;
                                                    font-weight: normal;
                                                    line-height: 18px;
                                                    ">
                                                    Ride Fare
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    <table
                                                    cellpadding="0"
                                                    cellspacing="0"
                                                    border="0"
                                                    width="27%"
                                                    style="width: 27%"
                                                    align="right">
                                                    <tbody>
                                                    <tr>
                                                    <td
                                                    style="padding: 0 0 0 0">
                                                    <table
                                                    cellpadding="0"
                                                    cellspacing="0"
                                                    border="0"
                                                    width="100%">
                                                    <tbody>
                                                    <tr>
                                                    <td
                                                    align="left">
                                                    <table
                                                    align="right">
                                                    <tbody>
                                                    <tr>
                                                    <td style="
                                                    font-family: Helvetica, 'Helvetica Neue', Arial, sans-serif;
                                                    color: #000000;
                                                    font-size: 14px;
                                                    font-weight: normal;
                                                    line-height: 18px;
                                                    ">
                                                    {{ $data['data']['requested_currency_symbol'] }}
                                                    {{ $data['data']['accepted_ride_fare'] }}
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    </td>
                                                    </tr>
                                                    <tr>
                                                    <td
                                                    style="color: #ffffff; line-height: 8px !important; padding: 0 0 0 0; font-size: 13px">
                                                    <img style="width: 100%"
                                                    src="{{ asset('invoice/Invoice_White_line.png') }}"
                                                    class="CToWUd" />
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    </td>
                                                    </tr>


                                                    <tr>
                                                    <td bgcolor="#ffffff">
                                                    <table cellspacing="0"
                                                    cellpadding="0" border="0"
                                                    width="100%">
                                                    <tbody>
                                                    <tr>
                                                    <td border="0"
                                                    cellpadding="0"
                                                    cellspacing="0"
                                                    valign="top"
                                                    style="padding-left: 7px; padding-right: 13px; padding-bottom: 2px">
                                                    <table
                                                    cellpadding="0"
                                                    cellspacing="0"
                                                    border="0"
                                                    width="72%"
                                                    style="width: 72%"
                                                    align="left">
                                                    <tbody>
                                                    <tr>
                                                    <td
                                                    style="padding: 0 0 0 0">
                                                    <table
                                                    cellpadding="0"
                                                    cellspacing="0"
                                                    border="0"
                                                    width="100%">
                                                    <tbody>
                                                    <tr>
                                                    <td
                                                    align="left">
                                                    <table>
                                                    <tbody>
                                                    <tr>
                                                    <td align="left"
                                                    style="
                                                    font-family: Helvetica, 'Helvetica Neue', Arial, sans-serif;
                                                    color: #707070;
                                                    font-size: 14px;
                                                    font-weight: normal;
                                                    line-height: 18px;
                                                    ">
                                                    Cancellation
                                                    Fee
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    <table
                                                    cellpadding="0"
                                                    cellspacing="0"
                                                    border="0"
                                                    width="27%"
                                                    style="width: 27%"
                                                    align="right">
                                                    <tbody>
                                                    <tr>
                                                    <td
                                                    style="padding: 0 0 0 0">
                                                    <table
                                                    cellpadding="0"
                                                    cellspacing="0"
                                                    border="0"
                                                    width="100%">
                                                    <tbody>
                                                    <tr>
                                                    <td
                                                    align="left">
                                                    <table
                                                    align="right">
                                                    <tbody>
                                                    <tr>
                                                    <td style="
                                                    font-family: Helvetica, 'Helvetica Neue', Arial, sans-serif;
                                                    color: #707070;
                                                    font-size: 14px;
                                                    font-weight: normal;
                                                    line-height: 18px;
                                                    ">
                                                    {{ $data['data']['requested_currency_symbol'] }}
                                                    {{ $data['data']['requestBill']['data']['cancellation_fee'] }}
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    </td>
                                                    </tr>


                                                    <tr>
                                                    <td
                                                    style="color: #ffffff; line-height: 8px !important; padding: 0 0 0 0; font-size: 13px">
                                                    <img style="width: 100%"
                                                    src="{{ asset('invoice/Invoice_White_line.png') }}"
                                                    class="CToWUd" />
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    </td>
                                                    </tr>


                                                    <!-- Driver Convenience Fee -->
                                                    <tr>
                                                    <td bgcolor="#ffffff">
                                                    <table cellspacing="0"
                                                    cellpadding="0" border="0"
                                                    width="100%">
                                                    <tbody>
                                                    <tr>
                                                    <td border="0"
                                                    cellpadding="0"
                                                    cellspacing="0"
                                                    valign="top"
                                                    style="padding-top: 6px; padding-bottom: 6px; padding-left: 7px; padding-right: 13px;">
                                                    <table
                                                    cellpadding="0"
                                                    cellspacing="0"
                                                    border="0"
                                                    width="72%"
                                                    style="width: 72%"
                                                    align="left">
                                                    <tbody>
                                                    <tr>
                                                    <td
                                                    style="padding: 0 0 0 0">
                                                    <table
                                                    cellpadding="0"
                                                    cellspacing="0"
                                                    border="0"
                                                    width="100%">
                                                    <tbody>
                                                    <tr>
                                                    <td
                                                    align="left">
                                                    <table>
                                                    <tbody>
                                                    <tr>
                                                    <td align="left"
                                                    style="
                                                    font-family: Helvetica, 'Helvetica Neue', Arial, sans-serif;
                                                    color: #000000;
                                                    font-size: 14px;
                                                    font-weight: normal;
                                                    line-height: 18px;
                                                    ">
                                                    Admin Fee
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    <table
                                                    cellpadding="0"
                                                    cellspacing="0"
                                                    border="0"
                                                    width="27%"
                                                    style="width: 27%"
                                                    align="right">
                                                    <tbody>
                                                    <tr>
                                                    <td
                                                    style="padding: 0 0 0 0">
                                                    <table
                                                    cellpadding="0"
                                                    cellspacing="0"
                                                    border="0"
                                                    width="100%">
                                                    <tbody>
                                                    <tr>
                                                    <td
                                                    align="left">
                                                    <table
                                                    align="right">
                                                    <tbody>
                                                    <tr>
                                                    <td style="
                                                    font-family: Helvetica, 'Helvetica Neue', Arial, sans-serif;
                                                    color: #000000;
                                                    font-size: 14px;
                                                    font-weight: normal;
                                                    line-height: 18px;
                                                    ">
                                                    - {{ $data['data']['requested_currency_symbol'] }}
                                                    {{ number_format($data['data']['requestBill']['data']['admin_commision'], 2) }}
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    </td>
                                                    </tr>
                                                    <tr>
                                                    <td
                                                    style="color: #ffffff; line-height: 8px !important; padding: 0 0 0 0; font-size: 13px">
                                                    <img style="width: 100%"
                                                    src="{{ asset('invoice/Invoice_White_line.png') }}"
                                                    class="CToWUd" />
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    </td>
                                                    </tr>



                                                    <tr>
                                                    <td bgcolor="#ffffff">
                                                    <table cellspacing="0"
                                                    cellpadding="0" border="0"
                                                    width="100%">
                                                    <tbody>
                                                    <tr>
                                                    <td border="0"
                                                    cellpadding="0"
                                                    cellspacing="0"
                                                    valign="top"
                                                    style="padding-top: 6px; padding-bottom: 6px; padding-left: 7px; padding-right: 13px; background-color: #b9efaf">
                                                    <table
                                                    cellpadding="0"
                                                    cellspacing="0"
                                                    border="0"
                                                    width="72%"
                                                    style="width: 72%"
                                                    align="left">
                                                    <tbody>
                                                    <tr>
                                                    <td
                                                    style="padding: 0 0 0 0">
                                                    <table
                                                    cellpadding="0"
                                                    cellspacing="0"
                                                    border="0"
                                                    width="100%">
                                                    <tbody>
                                                    <tr>
                                                    <td
                                                    align="left">
                                                    <table>
                                                    <tbody>
                                                    <tr>
                                                    <td align="left"
                                                    style="
                                                    font-family: Helvetica, 'Helvetica Neue', Arial, sans-serif;
                                                    color: #000000;
                                                    font-size: 14px;
                                                    font-weight: normal;
                                                    line-height: 18px;
                                                    ">
                                                    Settlement Amount

                                                    <span
                                                    style="font-family: Helvetica, 'Helvetica Neue', Arial, sans-serif; color: #707070; font-size: 13px; font-weight: normal">(rounded
                                                    off)</span>
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    <table
                                                    cellpadding="0"
                                                    cellspacing="0"
                                                    border="0"
                                                    width="27%"
                                                    style="width: 27%"
                                                    align="right">
                                                    <tbody>
                                                    <tr>
                                                    <td
                                                    style="padding: 0 0 0 0">
                                                    <table
                                                    cellpadding="0"
                                                    cellspacing="0"
                                                    border="0"
                                                    width="100%">
                                                    <tbody>
                                                    <tr>
                                                    <td
                                                    align="left">
                                                    <table
                                                    align="right">
                                                    <tbody>
                                                    <tr>
                                                    <td style="
                                                    font-family: Helvetica, 'Helvetica Neue', Arial, sans-serif;
                                                    color: #000000;
                                                    font-size: 14px;
                                                    font-weight: normal;
                                                    line-height: 18px;
                                                    ">
                                                    {{ $data['data']['requested_currency_symbol'] }}
                                                    {{ number_format($data['data']['requestBill']['data']['driver_commision'], 2) }}
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    </td>
                                                    </tr>
                                                    <tr>
                                                    <td
                                                    style="color: #ffffff; line-height: 8px !important; padding: 0 0 0 0; font-size: 13px">
                                                    <img style="width: 100%"
                                                    src="{{ asset('invoice/Invoice_White_line.png') }}"
                                                    class="CToWUd" />
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    <table bgcolor="#eeeeee" border="0" cellpadding="0" cellspacing="0" width="100%">
                                                    <tbody>
                                                    <tr>
                                                    <td bgcolor="#eeeeee" align="center" style="padding: 0 15px 0 15px">
                                                    <table border="0" bgcolor="#ffffff" cellpadding="0" cellspacing="0" width="660">
                                                    <tbody>
                                                    <tr>
                                                    <td bgcolor="#ffffff" align="center" style="padding-left: 15px; padding-right: 15px">
                                                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                                    <tbody>
                                                    <tr>
                                                    <td align="center" style="
                                                    font-family: Arial, Helvetica, 'Helvetica Neue', sans-serif;
                                                    color: #000000;
                                                    font-size: 16px;
                                                    font-weight: bold;
                                                    padding-top: 10px;
                                                    padding-bottom: 10px;
                                                    border-bottom: 1px solid #eeeeee;
                                                    ">
                                                    Payment
                                                    </td>
                                                    </tr>
                                                    <tr>
                                                    <td style="color: #ffffff; line-height: 14px">.</td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    </td>
                                                    </tr>
                                                    <tr>
                                                    <td bgcolor="#ffffff" style="padding: 0 15px 0 15px">
                                                    <table cellspacing="0" cellpadding="0" border="0" width="100%">
                                                    <tbody>
                                                    <tr>
                                                    <td border="0" cellpadding="0" cellspacing="0" valign="top"
                                                    style="padding-bottom: 3px">
                                                    <table cellpadding="0" cellspacing="0" border="0" width="50%"
                                                    style="width: 50%" align="left">
                                                    <tbody>
                                                    <tr>
                                                    <td style="padding: 0 0 0 0">
                                                    <table cellpadding="0" cellspacing="0" border="0"
                                                    width="100%">
                                                    <tbody>
                                                    <tr>
                                                    <td align="left"
                                                    style="padding-left: 70px">
                                                    <table>
                                                    <tbody>
                                                    <tr>
                                                    <td width="30"
                                                    style="width: 30px">
                                                    <img style="float: none; display: inline; width: 20px"
                                                    width="20"
                                                    src="{{ asset('invoice/cash_international.png') }}"
                                                    alt=""
                                                    class="CToWUd" />
                                                    </td>
                                                    <td align="left"
                                                    style="padding-bottom: 5px; padding-left: 8px; font-family: Arial, Helvetica, 'Helvetica Neue', sans-serif; color: #707070; font-size: 14px">
                                                    @php
                                                    $payment = 'Cash';
                                                    @endphp
                                                    @if ($data['data']['payment_type_string'] == 1)
                                                    @php
                                                    $payment = 'Cash';
                                                    @endphp
                                                    @elseif($data['data']['payment_type_string']
                                                    == 0)
                                                    @php
                                                    $payment = 'Card';
                                                    @endphp
                                                    @elseif($data['data']['payment_type_string']
                                                    == 2)
                                                    @php
                                                    $payment = 'Card';
                                                    @endphp
                                                    @endif
                                                    Paid by
                                                    {{ $payment }}
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    <table cellpadding="0" cellspacing="0" border="0" width="27%"
                                                    style="width: 27%" align="right">
                                                    <tbody>
                                                    <tr>
                                                    <td style="padding: 0 0 0 0">
                                                    <table cellpadding="0" cellspacing="0" border="0"
                                                    width="100%">
                                                    <tbody>
                                                    <tr>
                                                    <td align="left"
                                                    style="font-family: Arial, Helvetica, 'Helvetica Neue', sans-serif; color: #707070; font-size: 14px; padding-bottom: 5px">
                                                    {{ $data['data']['requested_currency_symbol'] }}
                                                    {{ number_format($data['data']['requestBill']['data']['total_amount'], 2) }}
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    </td>
                                                    </tr>
                                                    <tr>
                                                    <td style="color: #ffffff; line-height: 10px">
                                                    <img src="{{ asset('invoice/Invoice_White_line.png') }}"
                                                    style="width: 100%" class="CToWUd" />
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    <table bgcolor="#eeeeee" border="0" cellpadding="0" cellspacing="0" width="100%">
                                                    <tbody>
                                                    <tr>
                                                    <td bgcolor="#EEEEEE" align="center" style="padding: 0 15px 20px 15px">
                                                    <table border="0" cellpadding="0" cellspacing="0" width="660">
                                                    <tbody>
                                                    <tr>
                                                    <td
                                                    style="background-color: #ffffff; padding: 0 15px 0px 15px; border-bottom-left-radius: 4px; border-bottom-right-radius: 4px;">
                                                    <table align="left" width="100%" border="0" cellspacing="0" cellpadding="0">
                                                    <tbody>
                                                    <tr>
                                                    <td align="left" style="
                                                    font-size: 13px;
                                                    font-family: Arial, Helvetica, 'Helvetica Neue', sans-serif;
                                                    color: #777777;
                                                    padding-top: 16px;
                                                    padding-bottom: 10px;
                                                    padding-left: 16px;
                                                    border-top: 1px solid #eeeeee;
                                                    text-align: center;
                                                    "> <a style="text-decoration: none; color: #5d93bb" href="{{ config('app.url') }}privacy" rel="noreferrer"
                                                    target="_blank">
                                                    Privacy Policy</a>
                                                    |
                                                    <a style="text-decoration: none; color: #5d93bb"
                                                    href="{{ config('app.url') }}terms" rel="noreferrer"
                                                    target="_blank">
                                                    Terms</a>
                                                    <a style="text-decoration: none; color: #000000"
                                                    href="mailto:dilip@misoftwares.com" rel="noreferrer"
                                                    target="_blank">

                                                    | Questions? Email us at dilip@misoftwares.com
                                                    </a>
                                                    </td>

                                                    </tr>
                                                    <tr>
                                                    <td style="color: #ffffff; line-height: 9px"><img
                                                    src="{{ asset('invoice/Invoice_White_line.png') }}"
                                                    style="width: 100%" class="CToWUd" /></td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    </td>
                                                    </tr>
                                                    </tbody>
                                                    </table>
                                                    </div>
                                                    </div>

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
                                                                <div style="display: flex; align-items: center;margin-top:10px;" >
                                                                    <img class="img-fluid mt-4" width="600" height="300" src="{{ asset('storage/uploads/website/images/' . $notificationData['banner_img']) }}" alt="banner-img">                                                    
                                                                </div>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <div class="p-1 bg-light rounded mb-4 mt-5" style="padding: 1rem; margin-bottom: 2rem; margin-top: 1rem;">
                                <div class="row g-2" >
                                  <div style="text-align: center;">{!! $notificationData['footer_content'] !!}</div>
                                    <div style="text-align: center; margin: 0px auto;">                              
                                        <ul style="list-style: none;display: flex; justify-content: center; padding-top: 15px;padding-left: 0px; margin-bottom: 20px; font-family: 'Roboto', sans-serif;margin-left:25%;text-align:center;">
                                            <li>
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
                                        <div class="row d-flex align-items-center justify-content-center mt-5" style="display: flex;align-items: center;justify-content: center; margin-top: 2rem;margin-left:30%;;text-align:center;">
                                            <div >
                                                @if($notificationData['show_fbicon'] == 1)
                                                    <a href="{{ $notificationData['footer']['footer_fblink'] }}">
                                                        <img class="img-fluid" src="{{ asset('images/facebook.png') }}" alt="facebook" width="24" style="margin-right:1rem">
                                                    </a>        
                                                @endif                                     
                                            </div>
                                            <div>
                                                @if($notificationData['show_instaicon'] == 1)
                                                    <a href="{{ $notificationData['footer']['footer_instalink'] }}">
                                                        <img class="img-fluid" src="{{ asset('images/instagram.png') }}" alt="Instagram" width="24" style="margin-right:1rem">
                                                    </a>
                                                @endif 
                                            </div>
                                            <div>
                                                @if($notificationData['show_twittericon'] == 1)
                                                <a href="{{ $notificationData['footer']['footer_twitterlink'] }}">
                                                    <img class="img-fluid" src="{{ asset('images/twitter.png') }}" alt="twitter" width="24" style="margin-right:1rem">
                                                </a>
                                                @endif
                                            </div>
                                            <div>
                                                @if($notificationData['show_linkedinicon'] == 1)
                                                    <a href="{{ $notificationData['footer']['footer_linkedinlink'] }}">
                                                        <img class="img-fluid" src="{{ asset('images/linkedin.png') }}" alt="linkedin" width="24" style="margin-right:1rem">
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
            </tbody>
        </table>
    </div>
</div>
