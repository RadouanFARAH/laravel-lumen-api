
    <style>
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

   
    </style>
<span class="header_text">{{$titre}}</span>


<div class="header">
    <img src="https://file.innov237.com/logo.jpeg" class="imgheader" />
</div> 
<h3>{{$slot}}</h3>