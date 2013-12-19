#default
*active
 <div style="width: 100%; margin-bottom:30px;">
     <div style="clear:both;">
          
        <div style="word-wrap:break-word; width:300px; float:left;"><strong>[link]{title}[/link]</strong></div>
         <div style="text-align:right;">[print]printable version[/print]</div>      
     </div>
     <div style="text-align:justify; padding:3px; margin-top:3px; margin-bottom:5px; border-top:1px solid #D3D3D3;">{short-story}
         <div style="margin-top:10px;">[full-link target=_blank]Read more... [/full-link]</div>
     </div>
     <div style="margin: 0 0 8px 0;">{tagline}</div>
     <div style="float: right;">[com-link]{comments-num} Comments[/com-link]</div>
     <div><em>Posted on {date} by {author}</em></div>
     {fb-comments} {fb-like} {gplus} {twitter}
 </div>

*full
 <div style="width: 100%; margin-bottom:15px;">
     <div><strong>{title}</strong></div>
     <div style="text-align:justify; padding:3px; margin-top:3px; margin-bottom:5px; border-top:1px solid #D3D3D3;">{full-story}</div>
     <div style="float: right;">{comments-num} Comments</div>
     <div><em>Posted on {date} by {author}</em></div>
     {fb-comments} {fb-like} {gplus} {twitter}
 </div>

*comment
 <div style="width: 100%; margin-bottom:20px;">
    <div style="border-bottom:1px solid black;">[delete]%cbox[/delete] by <strong>{author}</strong> @ {date} [edited](<i>Edited: %edited</i>)[/edited] [edit][Edit comment][/edit]</div>
    <div style="padding: 2px; background-color:#F9F9F9">{comment}</div>
 </div>

*form
 <table border="0" width="370" cellspacing="0" cellpadding="0">
    <tr><td width="60">Name:</td><td>{input_username} {remember_me}</td></tr>
    <tr><td>E-mail:</td><td>{input_email} (optional)</td></tr>
    <tr><td>Smile:</td><td>{smilies}</td></tr>
    <tr><td colspan="2">{input_commentbox}</td></tr>
    [captcha]<tr><td>Captcha</td><td>{captcha}</td></tr>[/captcha]    
    <tr><td colspan="2" align="right">[submit]Add comment[/submit]</td></tr>
 </table>

*prev_next
 <p align="center">[prev-link]<< Previous[/prev-link] {pages} [next-link]Next >>[/next-link]</p>

*comments_prev_next
 <p align="center">[prev-link]<< Older[/prev-link] ({pages}) [next-link]Newest >>[/next-link]</p>

*search
 <form action="{php_self}" method="GET" class="cn_search_form">
  <div>{search_basic} Author: {author} {in_archives} In archives</div>
  <div>{select=year:from} {select=mon:from} {select=day:from} &ndash; {select=year:to} {select=mon:to} {select=day:to}</div>
  <div>[submit]Search[/submit]</div>
 </form>

*tagline
 <a href="{url}" target="_blank" class="cn_tag_item{tag:selected| cn_tag_selected}">{tag}</a>{comma| }

*print
 <html>
 <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
 </head>
 <body bgcolor="#ffffff" text="#000000" onload="window.print()">
 <strong>{title} @ <small>{date}</small></strong></div>
 <hr/>{full-story}<hr/>
 <small>News powered by CuteNews - http://cutephp.com</small>
 </body></html>

#headlines
*active
 [full-link]{title}[/full-link], posted on {date} by {author}<br />

*full
 <div style="width:420px; margin-bottom:15px;">
 <div><strong>{title}</strong></div>
 <div style="text-align:justify; padding:3px; margin-top:3px; margin-bottom:5px; border-top:1px solid #D3D3D3;">{full-story}</div>
 <div style="float: right;">{comments-num} Comments</div>
 <div><em>Posted on {date} by {author}</em></div>
 </div>

*comment

 <div style="width: 100%; margin-bottom:20px;">
    <div style="border-bottom:1px solid black;">[delete]%cbox[/delete] by <strong>{author}</strong> @ {date} [edited](<i>Edited: %edited</i>)[/edited] [edit][Edit comment][/edit]</div>
    <div style="padding: 2px; background-color:#F9F9F9">{comment}</div>
 </div>

*form
 <table border="0" width="370" cellspacing="0" cellpadding="0">
    <tr><td width="60">Name:</td><td>{input_username} {remember_me}</td></tr>
    <tr><td>E-mail:</td><td>{input_email} (optional)</td></tr>
    <tr><td>Smile:</td><td>{smilies}</td></tr>
    [captcha]<tr><td>Captcha</td><td>{captcha}</td></tr>[/captcha]
    <tr><td colspan="2">{input_commentbox}</td></tr>
    <tr><td>[submit]Add comment[/submit]</td></tr>
 </table>

*prev_next

*comments_prev_next

*search
 <form action="{php_self}" method="GET" class="cn_search_form">
  <div>{search_basic} Author: {author} {in_archives} In archives</div>
  <div>{select=year:from} {select=mon:from} {select=day:from} &ndash; {select=year:to} {select=mon:to} {select=day:to}</div>
  <div>[submit]Search[/submit]</div>
 </form>

*print

*tagline
 <a href="{url}" target="_blank" class="cn_tag_item{tag:selected| cn_tag_selected}">{tag}</a>{comma| }

#rss
*active
 <item>
    <title><![CDATA[{title}]]></title>
    <link>{rss-news-include-url}</link>
    <description><![CDATA[{short-story}]]></description>
    <guid isPermaLink="false">{news-id}</guid>
    <pubDate>{date}</pubDate>
 </item>

#mail
*password_change
 Dear %username%!
 Your password has been changed. Authorisation data:

   Login: %username%
   Password: %password%

*resend_activate_account
 Dear %username%!
 Click to this activation link %url% for restore your account. Secret word: %secret%

*notify_unapproved
 The user %username% (journalist) posted article '%article_title%' which needs first to be Approved