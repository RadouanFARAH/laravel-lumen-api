
<html><head>
    <style>
        body {
            padding: 20px;
            font-size: 14px;
        }

        .header {
            background-color: #2091BC;
            height: 50px;
            width: 100%;
        }

        .imgheader {
            float: right;
            width: 50px;
            height: 40px;
            margin: 4px;
        }

        .footer {
            background-color: #dddddd;
            padding: 8px;
        }

        .social {
            background-color: #292D2F;
            height: 150;
            width: auto;
            margin: 15px;
            padding: 10px;
            display: flex;
            justify-content: space-evenly;
        }

        .color {
            color: #2091BC;
            padding-left: 15px;
        }

        .header_text {
            font-size: medium;
            font-weight: bold;
            color: #5D7881;
        }

        .btn {
            background-color: #2091BC;
            color: white;
            padding: 1px;
            width: auto;
            height: 70px;
            border-color: transparent;
            border-radius: 10px;
            margin-top: 10px;
        }
    </style>
</head>

<body>

    <span class="header_text">@lang('notifications.trajet_enligne.email.header')</span>

    <div class="header">
        <img src="https://file.innov237.com/logo.jpeg" class="imgheader">
    </div>

    <h3>@lang('notifications.trajet_enligne.email.title', ['date' => $date, 'source' => $source, 'destination' => $destination])</h3>
    <p></p>
    <div>
            @lang('notifications.trajet_enligne.email.body', ['name' => $name])
    </div>
    <button class="btn">@lang('notifications.trajet_enligne.email.button')</button>
    <p></p>

    <!-- Shared Footer -->
    <div class="footer">
        <h4 class="color">@lang('notifications.footer.company_name')</h4>
        <p class="color"><a href="#">@lang('notifications.footer.help')</a></p>
        <p class="color"><a href="#">@lang('notifications.footer.unsubscribe')</a></p>
        <p class="color">@lang('notifications.footer.address.line1')</p>
        <p class="color">@lang('notifications.footer.address.line2')</p>
        <p class="color"><a href="#">@lang('notifications.footer.website')</a></p>
        <p class="color"><a href="#">@lang('notifications.footer.privacy')</a></p>
        <p class="color"><a href="#">@lang('notifications.footer.terms')</a></p>
    </div>



</body></html>