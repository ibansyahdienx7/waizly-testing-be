<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">

    <title>Mail - {{ config('app.name') }}</title>
    <link href="{{ config('app.url') . '/assets/css/style-mail.css' }}" rel="stylesheet">

    <style type="text/css">
        .btn-custom{
            font-family: arial,'helvetica neue',helvetica,sans-serif;
            font-size: 14px;
            color: #fff !important;
            border-style: solid;
            border-color: #f8a51c;
            border-width: 20px 30px;
            display: inline-block;
            background: #f8a51c;
            border-radius: 10px;
            font-weight: bold;
            font-style: normal;
            line-height: 17px;
            width: auto;
            text-decoration: none;
            text-align: center;
            transition: all .3s ease-in-out;
            margin-top: 40px;
            text-transform: capitalize;
            cursor: pointer;
        }

        .btn-custom:hover{
            font-family: arial,'helvetica neue',helvetica,sans-serif;
            font-size: 14px;
            color: #ffffff !important;
            border-style: solid;
            border-color: #af761c;
            border-width: 20px 30px;
            display: inline-block;
            background: #af761c;
            text-transform: capitalize;
            border-radius: 10px;
            font-weight: bold;
            margin-top: 40px;
            font-style: normal;
            text-decoration: none;
            line-height: 17px;
            width: auto;
            text-align: center;
            transition: all .3s ease-in-out;
            cursor: pointer;
        }

        .img-custom{
            width: 20%;
        }

        @media(max-width: 992px)
        {
            .img-custom{
                width: 15%;
            }
        }
    </style>
</head>

<body>

    <table class="wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td align="center">
                <table class="content" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                    {{-- Header --}}
                    <tr>
                        <td class="header">
                            <center>
                                <a href="{{ $url ?? '' }}" style="display: inline-block;">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/9a/Laravel.svg/985px-Laravel.svg.png" class="img-custom" alt="{{ config('app.brand') }}" title="{{ config('app.brand') }}">
                                </a>
                            </center>
                        </td>
                    </tr>

                    <!-- Email Body -->
                    <tr>
                        <td class="body" width="100%" cellpadding="0" cellspacing="0">
                            <table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0"
                                role="presentation">
                                <!-- Body content -->
                                <tr>
                                    <td class="content-cell">

                                        @yield('content_email')

                                        <table class="subcopy" width="100%" cellpadding="0" cellspacing="0"
                                            role="presentation">
                                            <tr>
                                                <td>
                                                    @yield('footer')
                                                </td>
                                            </tr>
                                        </table>

                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <table class="footer" align="center" width="570" cellpadding="0" cellspacing="0"
                                role="presentation">
                                <tr>
                                    <td class="content-cell" align="center">
                                        <p style="color: #202020">
                                            &copy; {{ date('Y') }}
			                                Copyright <strong><span>Waizly - Testing Backend Developer</span></strong>. All Rights Reserved
                                        </p>
                                        <p style="color: #202020">
                                            v{{ Illuminate\Foundation\Application::VERSION }}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>

</html>
