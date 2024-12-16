<style>
    .flex_payement {
        flex-direction: row;
        display: flex;
        justify-content: space-between;
        padding: 12px;

    }

    .colorPay {
        color: grey !important;
    }
    .payDivider{
        height: 1px; 
        width: 100%;
        background-color: grey;
    }
    .trailing{
        font-size: 16px;
    }
</style>

<div class="flex_payement colorPay">
    <div class="leading">{{$leading}}</div>
    <div>
        <div class="trailing">{{$trailing}}</div>
        <div class="sub-title">{{$sub}}</div>


    </div>
</div>
@if ($hasDivider == 1)
<div class="payDivider"></div>
@endif