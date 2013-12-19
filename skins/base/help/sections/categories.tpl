<h1>What are Categories and How To Use Them</h1>

When adding new category, it will appear in the "Add News" section. Therefore when you post your news you'll be able to
specify a category to which the article will belong (selecting category it optional), that way you can organize your news.
Now, when adding new category you'll be able to specify name for this category and optionally URL to icon for this category.
Automatically an ID number will be set for the new category, this ID is used when using include script to show all news <b>only</b>
from this category. Example code:

<div class="code">&lt;?PHP<br>
    $category = "<b>2</b>";<br>include("path/to/show_news.php");<br>
    ?&gt;
</div>

the above PHP code included on your page will display all news from category with ID 2<br>
If you for example have 5 categories and want to display news on one page from only 3 of the categories, you can use the fallowing code:

<div class="code">&lt;?PHP<br>
    $category = "<b>2</b>,<b>3</b>,<b>5</b>";<br>include("path/to/show_news.php");<br>
    ?&gt;
</div>

the above code will display all news from categories with ID 2, 3 and 5.<br>
When you does <b>not</b> use $category = "&lt;ID&gt;"; CuteNews will display the news from all categories, but when using this code, only news from the specified category(s) will be shown.<br><br>
The category icon can be shown together with your news, to do this you must put {category-icon} in your news templates.