

@component('mail::message')
<table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td style="background:#f7f7f7;width:10%;padding:20px;text-align:center;"><img src="{{asset('storage/admin/app_icon.png')}}" alt="Mail Template Logo" width="100px" /></td>
            <td style="background:#7e8bc2;width:90%;padding:20px;text-align:center;">
                <p style="font-family:verdana;font-size:30px;font-weight:bold;color:#fff;margin:0px;padding-bottom:5px;line-height:normal;text-align:left;">Recipty.</p>
            </td>
        </tr>
    </table>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td style="padding: 30px;">
            <h1>Forget Password Email</h1>
            <p style="font-family:verdana;font-size:13px;margin:0px;padding-bottom:15px;line-height:normal;">Welcome to Recipty. We are glad to have you on board. Please verify your email address to log in.Use the following OTP.</p>
            <p style="display:block;text-align:center;margin:0px;padding-bottom:25px;line-height:normal;"><button class="button button-green">{{$token}}</button></p>
            <p style="font-family:verdana;font-size:12px;color:#000;margin:0px;padding-bottom:15px;line-height:normal;">Our team is here for your support. Please contact us if you have any queries.</p>
            <p style="font-family:verdana;font-size:12px;color:#000;margin:0px;padding-bottom:5px;line-height:normal;">Best regards,</p>
            <p style="font-family:verdana;font-size:12px;color:#000;margin:0px;padding-bottom:5px;line-height:normal;">Recipty</p>
        </td>
    </tr>
</table>
