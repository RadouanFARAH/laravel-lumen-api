<style>
    .rigth {
        float: left;
        font-size: 25px;
    }

    .left {
        float: right;
        font-size: 25px;
    }

    .block {
        display: block;
        padding: 20px;
        margin-bottom: 50px;
    }
    .clear{
        clear: both;
    }
</style>
<div class="block">
    <h3 class="left">{{$left}}</h3>
    <h2 class="rigth">{{$rigth}}</h2>
    <span class="clear"></span>

</div>