
@component('mail::message')
<table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td style="background:#f7f7f7;width:10%;padding:20px;text-align:center;"><img src="{{asset('storage/admin/app_icon.png')}}" alt="Mail Template Logo" width="100px" /></td>
            <td style="background:#7e8bc2;width:90%;padding:20px;text-align:center;">
                <p style="font-family:verdana;font-size:30px;font-weight:bold;color:#fff;margin:0px;padding-bottom:5px;line-height:normal;text-align:left;">Dalorum.</p>
            </td>
        </tr>
    </table>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td style="padding: 30px;">
            <p style="font-family:verdana;font-size:13px;margin:0px;padding-bottom:15px;line-height:normal;">Dear Admin</p>
            <p style="display:block;text-align:center;margin:0px;padding-bottom:25px;line-height:normal;"><button class="button button-green">A balance withdraw request has been sent by {{$user->first_name}} {{$user->last_name}}</button></p>
            <p style="font-family:verdana;font-size:12px;color:#000;margin:0px;padding-bottom:5px;line-height:normal;">Best regards,</p>
            <p style="font-family:verdana;font-size:12px;color:#000;margin:0px;padding-bottom:5px;line-height:normal;">Dalorum</p>
        </td>
    </tr>
</table>

