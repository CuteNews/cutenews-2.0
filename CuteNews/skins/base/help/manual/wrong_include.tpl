<html>
<head>
    <title>Wrong include</title>
    <style>body { font-family: Arial, tahoma, serif; font-size: 14px; }</style>
</head>
<body>
    <h4>CuteNews has detected that you are including show_news.php using the URL to this file</h4>
    <p>This is incorrect and you must include it using the PATH to show_news.php. <b>Example:</b></p>

    <div>this is <span style="color: red;">WRONG</span> :&nbsp;&nbsp; &lt;?php include("http://yoursite.com/cutenews/show_news.php"); ?&gt;</div>
    <div>this is <span style="color: green;">CORRECT</span>:&nbsp;&nbsp; &lt;?php include("cutenews/show_news.php"); ?&gt;</div>

    <br/>
    <div style="font-size: 13px; color: #606060;">if you think this message shouldn't be shown, open current file and delete check_direct_including() function, at own risk!</div>
</body>
</html>