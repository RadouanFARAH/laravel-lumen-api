<style>
    body {
        padding: 20px;
        font-size: 14px;
    }

    .hero {
        display: flex;
    }

    .container {
        margin: 60px;
    }

    .placeholder {
        background-color: #EFEFEF;

        padding: 12px;
    }

    .direction-container {
        display: flex;
    }

    .direction {
        background-color: blue;
        width: 10px;
        height: 90px;
        margin-right: 20px;
    }
</style>
<x-header :titre="__('notifications.nouveau_message.title', ['departCity' => departCity, 'arriveCity' => arriveCity])">
    {{ sender }} @lang('notifications.nouveau_message.header')
</x-header>
<div class="hero">
    <img src="https://file.innov237.com/logo.jpeg" class="imgheader" />
    <h2>{{ sender }}</h2>
</div>
<p class="container"></p>
<div class="placeholder">
    <h4>{{ message }}</h4>
    <h4>{{ datetime }}</h4>
</div>
<h4>{{ tripdate }}</h4>
<div class="direction-container">
    <div class="direction"></div>
    <div class="text">
        <h3>
            {{ departCity }}
        </h3>
        <h3>
            {{ arriveCity }}
        </h3>
    </div>
</div>
<x-button>@lang('notifications.nouveau_message.button')</x-button>
<x-footer></x-footer>
