#default
*active
 <div style="width: 100%; margin-bottom:30px;">     
     <div>
        <div style="word-wrap:break-word; width:300px; float:left;"><strong>[link]{title}[/link]</strong></div>
        <div style="text-align:right;">[print]printable version[/print]</div>      
     </div>
     <div style="clear:both;"></div>
     <div style="text-align:justify; padding:3px; margin-top:3px; margin-bottom:5px; border-top:1px solid #D3D3D3;">{short-story}
         <div style="margin-top:10px;">[full-link target=_blank]Read more... [/full-link]</div>
     </div>
     <div style="margin: 0 0 8px 0;">{tagline}</div>
     <div style="float: right;">[com-link]Count of comments: {comments-num} [/com-link]</div>
     <div>{avatar}<em>Posted on {date} by {author}</em></div>
     <div>{fb-comments}</div>
     <div>
          <span class="soc-buttons-left">{fb-like}</span> <span class="soc-buttons-left">{gplus}</span> <span class="soc-buttons-left">{twitter}</span>
     </div>
     <div style="clear:both;"></div>
 </div>

*full
 <div style="width: 100%; margin-bottom:15px;">
     <div><strong>{title}</strong></div>
     <div style="text-align:justify; padding:3px; margin-top:3px; margin-bottom:5px; border-top:1px solid #D3D3D3;">{full-story}</div>
     <div style="float: right;">Count of comments: {comments-num}</div>
     <div>{avatar}<em>Posted on {date} by {author}</em></div>
     <div>{fb-comments}</div> 
     <div>
         <span class="soc-buttons-left">{fb-like}</span> <span class="soc-buttons-left">{gplus}</span> <span class="soc-buttons-left">{twitter}</span>
     </div>
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
 <strong>{title} @ <small>{date}</small></strong>
 <hr/>{full-story}<hr/>
 <small>News powered by CuteNews - http://cutephp.com</small>
 </body></html>

#blog
*active
 <div class="blog-item">
   <div class="blog-content card">
      <h3>[link]{title}[/link]</h3>
      <div class="entry-meta">
		  <span><i class="icon-calendar icon-blog-mini"></i> {date}</span>
		  <span><i class="icon-user icon-blog-mini"></i> By {author}</span>
		  <!--span><i class="icon-folder-close icon-blog-mini"></i> {tagline}</span-->
		  <span><i class="icon-comment icon-blog-mini"></i> [com-link]{comments-num} Comments[/com-link]</span>
      </div>
	  {short-story}
      [link target=_blank]View & Comment <i class="icon-angle-right"></i> [/link]
   </div>
 </div><!--blog-item-->

*full
 <div class="blog-item">
  <div class="blog-content card">
    <h3>[link]{title}[/link]</h3>
	<div class="entry-meta">
		  <span><i class="icon-calendar icon-blog-mini"></i> {date}</span>
		  <span><i class="icon-user icon-blog-mini"></i> By {author}</span>
		  <!--span><i class="icon-folder-close icon-blog-mini"></i> {tagline}</span-->
		  <span><i class="icon-comment icon-blog-mini"></i> [com-link]{comments-num} Comments[/com-link]</span>
		  <span> {fb-like}</span><span>  {gplus}</span><span> {twitter}</span>
    </div>
    {full-story}
	<a class="btn btn-default" >[full-link target=_blank class="btn-danger btn-sm"] View & Comment [/full-link] <i class="icon-angle-right"></i></a>

 </div>
 </div><!--blog-item-->
 <div class="row">
   <div class="col-sm-4">{fb-like}</div>
   <div class="col-sm-4">{gplus}</div>
   <div class="col-sm-4">{twitter}</div>
 </div>
 <div class="card">
    <h3>Leave a comment via facebook</h3>
    {fb-comments}
 </div>

*comment
 <div class="media card">
    <div class="pull-left">
        <!--img class="avatar img-circle" src="images/blog/avatar1.png" alt=""-->
        {avatar}
    </div>

    <div class="media-body">
          <div class="well">
              <div class="media-heading">
                   [delete]%cbox[/delete]<strong>{author}</strong>&nbsp; <small>@ {date}</small>
                   <div class="pull-right">[edit]<i class="icon-edit"></i> [Edit comment][/edit] </div>
              </div>

              <p>{comment}</p>
              [edited](<i>Edited: %edited</i>)[/edited]
         </div>

    </div>
 </div><!--/.media-->

*form
    <div id="comment-form" class="card">
        <h3>Leave a comment</h3>
        <div class="row">
            <div class="col-sm-4"><div class="form-group">{input_username}</div></div>
            <div class="col-sm-4"><div class="form-group">{input_email} (optional)</div></div>
            <div class="col-sm-4"><div class="form-group">{remember_me}</div></div>
        </div>
        <div class="row"><div class="col-sm-12"><div class="form-group">{input_commentbox}</div></div></div>
        <div class="row"><div class="col-sm-12"><div class="form-group">Smiles:{smilies} [submit]Add comment[/submit]</div></div></div>
    </div>

*prev_next
 <p align="center">[prev-link]<< Previous page[/prev-link] {pages} [next-link]Next page >>[/next-link]</p>

*comments_prev_next
 <p align="center">[prev-link]<< Previous page[/prev-link] ({pages}) [next-link]Next page >>[/next-link]</p>

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
 <strong>{title} @ <small>{date}</small></strong>
 <hr/>{full-story}<hr/>
 <small>News powered by CuteNews - http://cutephp.com</small>
 </body></html>

#blog-mini
*active
 <div class="blog-mini-item col-xs-12 col-sm-6 col-md-4 col-lg-4">
   <div class="blog-mini-content card">
      <h3>[link]{title}[/link]</h3>

       <div class="entry-meta">
          <span><i class="icon-calendar icon-blog-mini"></i> {date}</span>
          <span><i class="icon-user icon-blog-mini"></i> By {author}</span>
          <!--span><i class="icon-folder-close icon-blog-mini"></i>{tagline}</span-->
          <span><i class="icon-comment icon-blog-mini"></i> [com-link]{comments-num} Comments[/com-link]</span>
       </div>
       [truncate=25] {short-story} [/truncate]
	   [link target=_blank]View & Comment <i class="icon-angle-right"></i> [/link]
     </div>
 </div><!--blog-mini-item-->

*full

*comment

*form

*prev_next

*comments_prev_next

*search

*tagline

*print

#blog-headlines
*active

 <div class="row">

	<div class="col-xs-7">
	   [truncate=4]{short-story} [/truncate]
	</div>

	<div class="col-xs-5">
		<h5 class="media-heading">[link]{title}[/link] </h5>
	   <small> <span><i class="icon-calendar"></i> {date}</span><br/>
		<span><i class="icon-user"></i> By {author}</span></small>
		<p></p>
	</div>

 </div>

#headlines
*active
 [full-link]{title}[/full-link], posted on {date} by {author}<br />

*full
 <div style="width:100%; margin-bottom:15px;">
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

*tagline
 <a href="{url}" target="_blank" class="cn_tag_item{tag:selected| cn_tag_selected}">{tag}</a>{comma| }

*print

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

#bootstrap
*active

    <div class="panel panel-default">
        <div class="panel-heading "  style='padding:5px;'>
            <div class="row">
                <div class="col-xs-12 col-sm-3 col-md-2" style='padding:0 5px;'>
                    <div class="col-xs-2 col-sm-4" style='padding:0 5px;'>{avatar}</div>
                    <div class="col-xs-10 col-sm-8 text-center" style='padding:0px 5px;'>{date}<br />[ {author} ]</div>
                </div>
                <div class="col-xs-12 col-sm-8 col-md-10 text-left" style='padding:5px;'>
                    <span class="h3">[link]{title}[/link]</span>
                </div>
            </div>
        </div>

        <div class="panel-body">
            {short-story}
        </div>

        <div class="panel-footer">
            <div class="row">
                <div class="pull-left"  style="padding:2px;">
                    [full-link target=_blank]<button type="button" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-eye-open"> </span> Read more </button>[/full-link]
                    [com-link]<button type="button" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-education"> </span> comments: {comments-num}</button>[/com-link]
                    [print]<button type="button" class="btn btn-info btn-xs"><span class="glyphicon glyphicon-print"> </span> print</button>[/print]
                </div>
                <div class="pull-left"  style="padding:2px;">{twitter}</div>
                <div class="pull-left"  style="padding:2px;">{gplus}</div>
                <div class="pull-left"  style="padding:2px;">{fb-like}</div>

            </div>
            <div class="row">
                <div class="col-xs-12"><strong>{tagline}</strong></div>
            </div>
        </div>
    </div>

*full

    <div class="panel panel-default">
        <div class="panel-heading "  style='padding:5px;'>
            <div class="row">
                <div class="col-xs-12 col-sm-3 col-md-2" style='padding:0 5px;'>
                    <div class="col-xs-3 col-sm-5" style='padding: 10px;'>{avatar}</div>
                    <div class="col-xs-9 col-sm-7 text-center" style='padding:0px 5px;'>{date}<br />
                        [ {author} ]<br />
                        [com-link]<button type="button" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-education"> </span> comments: {comments-num}</button>[/com-link]
                    </div>
                </div>
                <div class="col-xs-12 col-sm-8 col-md-10 text-left" style='padding:5px;'>
                    <span class="h3">[link]{title}[/link]</span>
                </div>
            </div>
        </div>

        <div class="panel-body">
            {full-story}
        </div>

        <div class="panel-footer">
            <div class="row">
                <div class="pull-left"  style="padding:2px;">
                    [print]<button type="button" class="btn btn-info btn-xs"><span class="glyphicon glyphicon-print"> </span> print</button>[/print]
                </div>
                <div class="pull-left"  style="padding:2px;">{twitter}</div>
                <div class="pull-left"  style="padding:2px;">{gplus}</div>
                <div class="pull-left"  style="padding:2px;">{fb-like}</div>

            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-12"><strong>{tagline}</strong></div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-12">{fb-comments}</div>
        </div>
    </div>

*comment

    <div class="panel panel-default">
        <div class="panel-heading">[delete]%cbox[/delete] by <strong>{author}</strong> @ {date} [edited](<i>Edited: %edited</i>)[/edited] [edit][Edit comment][/edit]
        </div>
        <div class="panel-body">{comment}
        </div>
    </div>

*form

    <div class="panel panel-default panel-footer col-xs-12 col-md-4 col-sm-6 "><br />
        {remember_me}<br />
        &ensp; {input_username} {input_email} {input_commentbox} {smilies}<br />
        <br>[captcha]{captcha}[/captcha]<br />
        [submit]Add comment[/submit]<br />
        &ensp;
    </div>

*prev_next

    <div class="text-center">
        [prev-link]<button type="button" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-triangle-left"> </span> Previous page</button>[/prev-link]
        <span class="h4"><strong>{pages}</strong></span>
        [next-link]<button type="button" class="btn btn-primary btn-xs"> Next page <span class="glyphicon glyphicon-triangle-right"></span> </button>[/next-link]
    </div>

*comments_prev_next

    <div class="text-center">
        [prev-link]<button type="button" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-triangle-left"> </span> Previous page</button>[/prev-link]
        <span class="h4"><strong>{pages}</strong></span>
        [next-link]<button type="button" class="btn btn-primary btn-xs"> Next page <span class="glyphicon glyphicon-triangle-right"></span> </button>[/next-link]
    </div>

*search


    <div class="panel panel-default panel-footer col-xs-12 col-md-4 col-sm-6 "><br />
        <form action="{php_self}" method="GET" class="form cn_search_form">
            <div>{search_basic} Author: {author} {in_archives} In archives</div>
            <div>{select=year:from} {select=mon:from} {select=day:from} &ndash; {select=year:to} {select=mon:to} {select=day:to}</div>
            <div>[submit]Search[/submit]</div>
        </form>
    </div>


*tagline
    <a href="{url}" target="_blank" class="cn_tag_item{tag:selected| cn_tag_selected}"><button type="button" class="btn btn-default btn-xs">{tag}</button></a>{comma| }

*print
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    </head>
    <body bgcolor="#ffffff" text="#000000" onload="window.print()">
    <strong>{title} @ <small>{date}</small></strong>
    <hr/>{full-story}<hr/>
    <small>News powered by CuteNews - http://cutephp.com</small>
    </body>
    </html>
