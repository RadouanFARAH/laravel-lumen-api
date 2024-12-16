<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            padding: 20px;
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

        .store {
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

        .iconContainer {
            height: 50px;
            width: 50px;
        }

        .storeContainer2 {
            height: 50px;
            width: auto;
        }

        .storeContainer1 {
            height: 50px;
            width: auto;
        }

        .header_text {
            font-size: medium;
            font-weight: bold;
            color: #5D7881;
        }

        .btn {
            background-color: #2091BC;
            color: white;
            padding: 15px;
            width: auto;
            height: 70px;
            border-color: transparent;
            border-radius: 10px;
        }
    </style>
</head>

<body>

    <span class="header_text">trans('notifications.conditions_generales_updated.email.header') </span>

    <div class="header">
        <img src="https://file.innov237.com/logo.jpeg" class="imgheader" />
    </div>

    <p>
    <div>
        trans('notifications.conditions_generales_updated.email.greeting', ['name' => $name])
    </div>
    trans('notifications.conditions_generales_updated.email.body')
    trans('notifications.conditions_generales_updated.email.summary')
    </p>

    <p>
    <ul>
        @foreach ($conditions as $condition)
        <li>$condition </li>
        @endforeach
    </ul>
    </p>
    <p>
        @lang('notifications.conditions_generales_updated.terms', [
        'date' => $date,
        'terms_link' => $terms_link,
        'privacy_link' => $privacy_link,
        'previous_terms_link' => $previous_terms_link
        ])
    </p>
    <!-- Shared Footer -->
    <div class="footer">
        <h4 class="color">@lang('notifications.footer.company_name') </h4>
        <p class="color"><a href="#">@lang('notifications.footer.help') </a></p>
        <p class="color"><a href="#">@lang('notifications.footer.unsubscribe') </a></p>
        <p class="color">@lang('notifications.footer.address.line1') </p>
        <p class="color">@lang('notifications.footer.address.line2') </p>
        <p class="color"><a href="#">@lang('notifications.footer.website') </a></p>
        <p class="color"><a href="#">@lang('notifications.footer.privacy') </a></p>
        <p class="color"><a href="#">@lang('notifications.footer.terms') </a></p>
    </div>
</body>

</html>