<h1>Template variables</h1>

<br/>
<h2>Active news, Full story, Print</h2>
<table width="100%">
    <tr bgcolor="#fefee0"> <td class="r" style="font-size: 16px;">Common variables</td> <td>&nbsp;</td>  </tr>

    <tr> <td class="r">{title}</td>            <td>Title of the article</td>  </tr>
    <tr> <td class="r">{short-story}</td>     <td>Short story of news item</td>  </tr>
    <tr> <td class="r">{full-story}</td>      <td>The full story</td>  </tr>
    <tr> <td class="r">{date}</td>            <td>Date when the story is written</td>  </tr>
    <tr> <td class="r">{author}</td>          <td>Author of the article, with link to his email (if any)</td>  </tr>
    <tr> <td class="r">{author-name}</td>     <td>The name of the author, without email</td>  </tr>
    <tr> <td class="r">{comments-num}</td>    <td>This will display the number of comments posted for article</td>  </tr>
    <tr> <td class="r">{category}</td>        <td>Name of the category where article is posted (if any). If there is no category for the article, it is empty.</td>  </tr>
    <tr> <td class="r">{category-icon}</td>   <td>Shows the category icon. If there is no category for the article, it is empty.</td>  </tr>
    <tr> <td class="r">{month}</td>           <td>Month name from system config</td>  </tr>
    <tr> <td class="r">{weekday}</td>         <td>Equivalent to date('l')</td>  </tr>
    <tr> <td class="r">{year}</td>            <td>Equivalent to date('Y')</td>  </tr>
    <tr> <td class="r">{day}</td>             <td>Equivalent to date('d')</td>  </tr>
    <tr> <td class="r">{hours}</td>           <td>Equivalent to date('H')</td>  </tr>
    <tr> <td class="r">{minute}</td>          <td>Equivalent to date('i')</td>  </tr>
    <tr> <td class="r">{since}</td>           <td>Display, for ex. "N minutes ago."</td>  </tr>
    <tr> <td class="r">{tagline}</td>         <td>Tags line</td>  </tr>
    <tr> <td class="r">{page_alias}</td>      <td>Show page alias</td>  </tr>

    <tr bgcolor="#fefee0"> <td class="r" style="font-size: 16px;">System variables</td> <td>&nbsp;</td>  </tr>
    <tr> <td class="r">{phpself}</td>          <td>$PHP_SELF value</td>  </tr>
    <tr> <td class="r">{go-back}</td>          <td>Go back button</td>  </tr>
    <tr> <td class="r">{cute-http-path}</td>   <td>CuteNews root path</td>  </tr>
    <tr> <td class="r">{news-id}</td>          <td>News ID</td>  </tr>
    <tr> <td class="r">{category-id}</td>      <td>Category ID</td>  </tr>
    <tr> <td class="r">{archive-id}</td>       <td>Archive ID</td>  </tr>
    <tr> <td class="r">{rss-news-include-url}</td> <td>Link to include rss</td>  </tr>

    <tr bgcolor="#fefee0"> <td class="r" style="font-size: 16px;">Social buttons</td> <td>&nbsp;</td>  </tr>
    <tr> <td class="r">{fb-comments}</td> <td>Facebook comments</td>  </tr>
    <tr> <td class="r">{fb-like}</td> <td>Facebook Like button</td>  </tr>
    <tr> <td class="r">{twitter}</td> <td>Twitter send button</td>  </tr>
    <tr> <td class="r">{gplus}</td> <td>G+ button</td>  </tr>

    <tr bgcolor="#fefee0"> <td class="r" style="font-size: 16px;">Wrappers</td> <td>&nbsp;</td>  </tr>
    <tr> <td class="r">[link] .. [/link]</td> <td>Will generate a permanent link to the full story</td>  </tr>
    <tr> <td class="r">[print] ... [/print]</td> <td>Will generate a print link</td>  </tr>
    <tr> <td class="r">[full-link] ... [/full-link]</td> <td>Link to the full story of article, only if there is full story</td>  </tr>
    <tr> <td class="r">[com-link] ... [/com-link]</td> <td>Generate link to the comments of article</td>  </tr>
    <tr> <td class="r">[edit] ... [/edit]</td>    <td>Make link to edit article at admin panel</td>  </tr>
    <tr> <td class="r">[truncate=words] ... [/truncate]</td>    <td>Truncate specified words number</td>  </tr>
    <tr> <td class="r">[mail] ... [/mail]</td> <td>Will generate a link to the author mail (if any) eg. [mail] email [/mail]</td>  </tr>
    <tr> <td class="r">[cat=CATID] ... [$catid] ... [/cat]</td> <td>Show code only for CATID</td>  </tr>
    <tr> <td class="r">[loggedin] ... [/loggedin]</td> <td>Show a template fragment for an authorized user</td>  </tr>

    <tr> <td class="r">[img] ... [/img]</td> <td>Make image link</td>  </tr>
    <tr> <td class="r">[more] ... [/more]</td> <td>Make "more" field</td>  </tr>
    <tr> <td class="r">[youtube] ... [/youtube]</td> <td>Make youtube link</td>  </tr>
    <tr> <td class="r">[cdata] ...[bb-tags]... [/cdata]</td> <td>Save the text unchanged</td>  </tr>

    {$More_Active_News}
</table>
<br/>
<h2>Comment, Add coment form</h2>
<table width="100%">
    <tr> <td class="r">{author}</td>        <td>Name of the comment poster</td>  </tr>
    <tr> <td class="r">{date}</td>          <td>Date when the comment was posted</td>  </tr>
    <tr> <td class="r">{smilies}</td>       <td>Show smiles tab</td>  </tr>
    <tr> <td class="r">{remember_me}</td>   <td>Remember form</td>  </tr>
    <tr> <td class="r">[captcha] {captcha} [/captcha]</td>   <td>Captcha image</td>  </tr>
    <tr> <td class="r">[submit] {submit} [/submit]</td>   <td>Captcha image</td>  </tr>

    <tr> <td class="r">{input_username}</td>   <td>Input username form</td>  </tr>
    <tr> <td class="r">{input_email}</td>   <td>Input email form</td>  </tr>
    <tr> <td class="r">{input_commentbox}</td>   <td>Input comment box form</td>  </tr>

    <tr> <td class="r">{edited}</td>        <td>Show edited date of comment</td>  </tr>
    <tr> <td class="r">{comment}</td>       <td>The comment</td>  </tr>
    <tr> <td class="r">[edit] ... [/edit]</td>       <td>Text, show if edit allowed</td>  </tr>
    <tr> <td class="r">[delete] ... [/delete]</td>       <td>Delete, show if edit allowed</td>  </tr>

    <tr> <td class="r">{mail}</td>                  <td>E-mail of the poster</td>  </tr>
    <tr> <td class="r">{username}</td>              <td>If user logged, show username</td>  </tr>
    <tr> <td class="r">{usermail}</td>              <td>If user logged, show e-mail</td>  </tr>
    <tr> <td class="r">{comment-id}</td>            <td>The Comment ID</td>  </tr>
    <tr> <td class="r">{comment-iteration}</td>     <td>Show the sequential number of individual comment</td>  </tr>

    {$More_Comment_Form}
</table>

<h2>Stylize search form</h2>
<table width="100%">

    <tr> <td class="r">{php_self}</td>              <td>$PHP_SELF</td>  </tr>
    <tr> <td class="r">{search_basic}</td>          <td>Input box for search phrase</td>  </tr>
    <tr> <td class="r">{author}</td>                <td>Input box for author</td>  </tr>
    <tr> <td class="r">{in_archives}</td>           <td>Checkbox "in archives"</td>  </tr>
    <tr> <td class="r">[submit]...[/submit]</td>    <td>Submit button wrapper</td>  </tr>
    <tr> <td class="r">[hid={FIELD}]</td>           <td>HIDDEN input box for custom user query</td>  </tr>
    <tr> <td class="r">{select=year:from}<br>{select=mon:from}<br>{select=day:from}<br>{select=year:to}<br>{select=mon:to}<br>{select=day:to}</td>  <td>SELECT box for date range</td>  </tr>

    {$More_Search_Pages}
</table>

<br/>
<h2>News and comments pagination</h2>
<table width="100%">
    <tr> <td class="r">[prev-link] ... [/prev-link]</td>    <td>Will generate a link to previous page (if there is)</td>  </tr>
    <tr> <td class="r">[next-link] ... [/next-link]</td>    <td>Will generate a link to next page (if there is)</td>  </tr>
    <tr> <td class="r">{pages}</td>                         <td>Shows current page number</td></tr>
    {$More_Com_Pages}
</table>

{$More_Sections}