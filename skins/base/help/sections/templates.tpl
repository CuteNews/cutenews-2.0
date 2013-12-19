<h1>Understanding Templates</h1>

Templates are used for easy editing the news look and the way news are displayed.<br>
<br>
You can view the different parts of the template that are used for different parts of your news look.
For example the "Active News" part of the default template is used to modify the look of the active news, "Full Story" for the way your full story will look like,
"Comment" is the part corresponding to the appearance of the comments posted by users etc.<br>
<br>
When editing parts of the template you can use HTML in them to build different structures etc. Now you'll need to add some special tags in your templates
to specify the place where the title of your news will be displayed and the author name and the date when the news was pasted etc... these tags are
explained above each part of the template.<br>

Lets take the "Active News" part for example: when you expand this part, a list of allowed tags for this part will be displayed and under them will be the
text area. One very common and easy tag is <b>{title}</b>, wherever you put this tag in your template it will be replaced with the real title of your news.<br>
<br>
You can have more than one template, this is useful if you want to include the news on different pages of your site with different look of the news.<br>
After creating more templates you must use specific code when including news to indicate whit which template the news to be shown.<br>
example code:

<div class="code">&lt;?PHP<br>
    $template = "my_test_template";<br>include("path/to/show_news.php");<br>
    ?&gt;
</div>

With the above code, all news will be showed using the my_test_template that you have created yourself.
if you don't specify what template to use, all news will use Default template which cannot be deleted.