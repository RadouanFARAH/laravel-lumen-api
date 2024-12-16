<style>
    .flex_user {
        flex-direction: row;
        display: flex;
        justify-content: space-between;
        padding: 12px;

    }

    .colorUser {
        color: grey !important;
    }

    .userDivider {
        height: 1px;
        width: 100%;
        background-color: grey;
    }

    .trailing {
        font-size: 16px;
    }
    .avatar-leading{
        height: 50px;
        width: 50px; 
        border-radius: 100px;
        background-color: blue;
    }

    
</style>

<div class="flex_user colorUser">
    <div>
        <div class="trailing"><b>{{$title}}</b></div>
        <div class="sub-title">{{$sub}}</div>
    </div>
    <div class="avatar-leading">{{$avatar}}</div>

</div>

