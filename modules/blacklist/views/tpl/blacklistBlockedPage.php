<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php _e('Blocked', SWR_LANG_CODE)?></title>
    <style>
        .blockMsg {
            width: 500px;
            background-color: #eee;
            border: 1px solid #ccc;
            padding: 10px;
            margin: 40px auto;
            box-shadow: 1px 1px 5px rgba(0, 0, 0, .25);
        }

        h1 {
            font-size: 1.3em;
            border-bottom: 1px solid #ccc;
            margin-top: 0;
        }
    </style>
</head>
<body>
<div class="blockMsg">
    <h1><?php _e('You are in blacklist', SWR_LANG_CODE)?></h1>
    <p><?php _e('Your IP address, or Country, or Browser trapped into blacklist of this site. If this is error - contact site admin to resolve this issue.', SWR_LANG_CODE)?></p>
</div>

</body>
</html>