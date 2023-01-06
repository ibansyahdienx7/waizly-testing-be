@extends('mails.layout')

@section('content_email')
    <div style="margin-top: 30px !important;margin-bottom: 20px">

        <div
            style="margin-top: 20px; padding: 20px 20px; border-bottom: 2px solid #fff;background: #F2F2F2; color: #5B5B5B">
            <h4 style="text-align:center; font-size: 20px; color: #000"> {{ $data['title'] }}
            </h4>
            <p style="text-align:center; color: #000">
                Halo, <strong>{{ $data['emailto'] }}</strong><br/>
                Looks like you want to change the password?
            </p>
            <hr />
            <p style="color: #000 !important; text-align:center">
                If you don't want to change your password, please ignore this email. In order to change the password, please click the button below:

                <center>
                <a href="{{ config('app.url_web') }}/reset-password/{{ $data['token'] }}" target="_blank" class="btn-custom">
                    Change password
                </a>
                </center>
            </p>

            <center>
                <p style="margin-top: 50px; text-align:center">
                    For the security of your account,
                </p>
                <p style="text-align:center">
                    immediately change the password.
                </p>
                <p style="text-align:center">
                    The button above is only valid for 1 x 24 hours.
                </p>
            </center>

            <br />

            <div style='margin-top: 10px'>
                <p style='text-align:center; color: #000 !important'>
                    This message is an automated message from the Waizly - Testing Backend Developer
                </p>
            </div>
        </div>
        <br />
    </div>
@endsection

@section('footer')
    <p style='text-align:center'>
        Warm regards, Iban Syahdien Akbar
    </p>
@endsection
