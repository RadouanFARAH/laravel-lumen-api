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
    </style>
</head>

<body>

    <span class="header_text"> @lang('notifications.demande_enligne_email.email.header') </span>

    <div class="header">
        <img src="https://file.innov237.com/logo.jpeg" class="imgheader" />
    </div>

    <h3> @lang('notifications.demande_enligne_email.email.title', ['source' => $source, 'destination' => $destination]) </h3>
    <p> @lang('notifications.demande_enligne_email.email.body', ['name' => $name]) </p>
    <button class="btn"> @lang('notifications.demande_enligne_email.email.button') </button>

    <!-- Shared Footer -->
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
