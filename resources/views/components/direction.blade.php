
    <style>
    
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
    <h4 style="text-align: center;">{{$titre}}</h4>
<h4>{{$periode}}</h4>
    <div class="direction-container">

        <div class="direction"></div>
        <div class="text">
            <h3>
            {{$depart}}
            </h3>
            <h3>
            {{$arrive}}
            </h3>
        </div>

    </div>


