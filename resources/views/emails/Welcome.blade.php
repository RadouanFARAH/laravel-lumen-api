<!DOCTYPE html>
<html>

<head>
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

    <span class="header_text"> @lang('notifications.welcome_email.email.header', ['name' => $name]) </span>

    <div class="header">
        <img src="https://file.innov237.com/logo.jpeg" class="imgheader" />
    </div>

    <h3> @lang('notifications.welcome_email.email.ready') </h3>
    <p>
        <div>
             @lang('notifications.welcome_email.email.description')
        </div>
        <button class="btn"> @lang('notifications.welcome_email.email.button') </button>
    </p>

    <center>
        <img src="https://file.innov237.com/home.png" class="homecss" />

        <h3> @lang('notifications.welcome_email.email.how_it_works') </h3>
        <img src="https://file.innov237.com/plane.png" class="plane" />

        <h3> @lang('notifications.welcome_email.email.open_app') </h3>
        <p> @lang('notifications.welcome_email.email.open_app_description') </p>

        <img src="https://file.innov237.com/mail.png" class="plane" />
        <h3> @lang('notifications.welcome_email.email.make_reservation') </h3>
        <p> @lang('notifications.welcome_email.email.make_reservation_description') </p>

        <img src="https://file.innov237.com/people.png" class="plane" />
        <h3> @lang('notifications.welcome_email.email.help_community') </h3>
        <p> @lang('notifications.welcome_email.email.help_community_description') </p>
    </center>

    <div class="footer">
        <h4 class="color"> @lang('notifications.footer.company_name') </h4>
        <p class="color"><a href="#"> @lang('notifications.footer.help') </a></p>
        <p class="color"><a href="#"> @lang('notifications.footer.unsubscribe') </a></p>
        <p class="color"> @lang('notifications.footer.address.line1') </p>
        <p class="color"> @lang('notifications.footer.address.line2') </p>
        <p class="color"><a href="#"> @lang('notifications.footer.website') </a></p>
        <p class="color"><a href="#"> @lang('notifications.footer.privacy') </a></p>
        <p class="color"><a href="#"> @lang('notifications.footer.terms') </a></p>
    </div>
</body>

</html>
