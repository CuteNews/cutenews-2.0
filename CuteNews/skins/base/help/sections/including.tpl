<h1>Including The News</h1>

After CuteNews is successfully installed you can start posting your news. To display the news you must include the
file <b>show_news.php</b> (located in the main cutenews folder). To include show_news.php on your page you must use a code like this:
<div class="code">&lt;?PHP<br>
    include("path/to/show_news.php");<br>
    ?&gt;</div>
And you must replace <i>path/to/show_news.php</i> with the real path to show_news.php, <b>NOT</b> the URL !!!<br>
Examples:<br>
<span style="color: green;">CORRECT</span>: include("cutenews/show_news.php");<br>
<span style="color: red;">WRONG&nbsp;&nbsp;&nbsp;</span>: include("http://site.com/cutenews/show_news.php");<br>
<br>
Remember that to be able to use the php code, the extension of the file where you include it must be .php<br>
If you want to include the code in .html page, you can rename he .html to .php and everything will work normal