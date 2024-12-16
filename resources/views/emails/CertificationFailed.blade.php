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
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</head>

<body>

    <span class="header_text">@lang('notifications.certification_failed_email.email.header') </span>

    <div class="header">
        <img src="https://file.innov237.com/logo.jpeg" class="imgheader" />
    </div>

    <h3>@lang('notifications.certification_failed_email.email.title') </h3>

    <p>@lang('notifications.certification_failed_email.email.greeting', ['name' => $name]) </p>

    <p>@lang('notifications.certification_failed_email.email.body') </p>
    <button class="btn">@lang('notifications.certification_failed_email.email.button') </button>

    <x-footer></x-footer>
</body>

</html>
