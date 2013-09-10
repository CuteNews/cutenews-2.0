<h1>Using multiple includes on one page</h1>

Now will examine the situation when you want to use more than one include on a single page. For example if you want to show 5 headlines (using template headlines)
and below them to be shown all other news. The problem in this situatuion situation is that when you click on a headline CuteNews won't understand where to show the result,
on the headlines part or where all other news are included. The solution is to use the variable <b>$static = TRUE;</b> before including the headlines. When you use
this variable CuteNews won't display any result on the place where you use $static.<br>
Here is the correct code of the above example with the headlines and news:

<div class="code">&lt;?PHP<br>
    <u>Our Latest 5 Headlines</u>:<br>
    $static = TRUE;<br>
    $number = "5";<br>
    $template = "Headlines";<br>
    include("path/to/show_news.php");<br>
    <br>
    <u>The News</u><br>
    include("path/to/show_news.php");<br>
    ?&gt;
</div>

Now When you click on a headline it will be displayed on the place of the other news and the
list with the latest 5 headlines will still be showed.<br><br>
Make some test with <b>$static = TRUE;</b> yourself to understand how it works exactly and how powerful
it can be.