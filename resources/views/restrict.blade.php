<!DOCTYPE html>
<html>
<head>
    <title>403 Access denied!</title>

    <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">

    <style>
        html, body {
            height: 100%;
        }

        body {
            margin: 0;
            padding: 0;
            width: 100%;
            color: #B0BEC5;
            display: table;
            font-weight: 100;
            font-family: 'Lato', sans-serif;
        }

        .container {
            text-align: center;
            display: table-cell;
            vertical-align: middle;
        }

        .content {
            text-align: center;
            display: inline-block;
        }

        blockquote{
            display:block;
            background: #fff;
            padding: 15px 20px 15px 45px;
            margin: 0 0 20px;
            position: relative;

            /*Font*/
            font-family: Georgia, serif;
            font-size: 16px;
            line-height: 1.2;
            color: #666;
            text-align: justify;

            /*Borders - (Optional)*/
            border-left: 15px solid #c76c0c;
            border-right: 2px solid #c76c0c;

            /*Box Shadow - (Optional)*/
            -moz-box-shadow: 2px 2px 15px #ccc;
            -webkit-box-shadow: 2px 2px 15px #ccc;
            box-shadow: 2px 2px 15px #ccc;
        }

        blockquote::before{
            content: "\201C"; /*Unicode for Left Double Quote*/

            /*Font*/
            font-family: Georgia, serif;
            font-size: 60px;
            font-weight: bold;
            color: #999;

            /*Positioning*/
            position: absolute;
            left: 10px;
            top:5px;
        }

        blockquote::after{
            /*Reset to make sure*/
            content: "";
        }

        blockquote a{
            text-decoration: none;
            background: #eee;
            cursor: pointer;
            padding: 0 3px;
            color: #c76c0c;
        }

        blockquote a:hover{
            color: #666;
        }

        blockquote em{
            font-style: italic;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="content">
        <blockquote>
            403 <a href="#">Access denied!</a> ,  Application will exit!
        </blockquote>
    </div>
</div>
</body>
</html>
