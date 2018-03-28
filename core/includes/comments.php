<?php if (!defined('EXEC_TIME')) { die('Access restricted'); }

//desable/enable standart comment thread
$is_comments=getoption('comments_std_show');
if(!$is_comments)    
{
    return TRUE;
}

global $PHP_SELF, $_SESS;

// Scan external query
list($id, $template, $start_from) = GET('id, template, start_from', 'GPG');
$id = cn_id_alias($id);

if (!$template) 
{
    $template = 'Default';
}

if ($id == 0) 
{
    die("@SYSLOG: INTERNAL ERROR[1]");
}

$id = intval($id);
$ent = db_news_load(db_get_nloc($id));
$entry = $ent[$id];
$user = member_get();
$start_from = intval($start_from);

// Clean sig
if (isset($_GET['__signature_dsi_inline']))
{
    unset($_GET['__signature_dsi_inline']);
}

// Load system configurations
$comm_number   = intval( getoption('comments_per_page') );

$comments = $entry['co'];
$total_comments = count($comments);

// ---------------------------------------------------------------------------------------------------------------------
$com = array();
global $_comment_iterator;

$user_encoding = getoption('frontend_encoding');
$is_encode = ($user_encoding != 'UTF-8') && function_exists('iconv');

/* Comment struct: ID => [u]ser, [c]comment text, [e]mail, [ip] */
foreach ($comments as $comment)
{
    if ($is_encode)
    {
        $bkp = $comment['c'];
        $comment['c'] = iconv('UTF-8', $user_encoding.'//TRANSLIT', $comment['c']);
        if (!$comment['c'])
        {
            $comment['c'] = $bkp;
        }
    }

    $com[] = entry_make($comment, 'comment', $template, 'comm');
    $_comment_iterator++;
}

// remove temporary
unset($_comment_iterator);

if (getoption('reverse_comments'))
{
    ksort($com);
}

// Comment per page is set
if ($com_by_page = getoption('comments_per_page'))
{
    $com = array_slice($com, $start_from, $com_by_page, TRUE);
    $cpn = cn_get_template('comments_prev_next', $template);

    // Make prev link
    if ($start_from)
    {
        $st = $start_from - $com_by_page;
        if ($st < 0) 
        {
            $st = 0;
        }

        if (getoption('rw_engine'))
        {
            $hrefA = '<a href="'.cn_rewrite('comments', $id, $st).'">\\1</a>';
        }
        else
        {
            $hrefA = '<a href="'.cn_url_modify('start_from='.$st).'">\\1</a>';
        }
    }
    else
    {
        $hrefA = '\\1';
    }

    // Make next link
    if (($st = $start_from + $com_by_page) < $total_comments)
    {
        if (getoption('rw_engine'))
        {
            $hrefB = '<a href="'.cn_rewrite('comments', $id, $st).'">\\1</a>';
        }
        else
        {
            $hrefB = '<a href="'.cn_url_modify('start_from='.$st).'">\\1</a>';
        }
    }
    else
    {
        $hrefB = '\\1';
    }

    // No comments to show
    if ($hrefA == '\\1' && $hrefB == '\\1')
    {
        $cpn = '';
    }

    // Replace
    $cpn = preg_replace('/\[prev-link\](.*?)\[\/prev-link\]/i', $hrefA, $cpn);
    $cpn = preg_replace('/\[next-link\](.*?)\[\/next-link\]/i', $hrefB, $cpn);
    $cpn = str_replace('{pages}', intval($start_from / $com_by_page) + 1, $cpn);
}
else
{
    $cpn = '';
}

echo '<br style="clear:both;"/>';

if ($total_comments && (test('Mda') || test('Mds')))
{
    if (getoption('reverse_comments')) {
        $comments = array_reverse($comments);
    }

    $comment_url = getoption('rw_engine') ? $_SERVER['REQUEST_URI'] : PHP_SELF;

    // Action with comments box
    echo '<form id="comments_frm" name="comment_frm" action="'.$comment_url.'" method="POST">';
    
    echo '<input type="hidden" name="id" value="'.$id.'" />';
    echo '<input type="hidden" name="subaction" value="addcomment" />';
    echo '<input type="hidden" name="action" value="comment_process" />';
    echo '<input type="hidden" name="popup" value="'.cn_htmlspecialchars(REQ('popup')).'" />';
    echo '<input type="hidden" name="referer" value="'.cn_htmlspecialchars($_SERVER['REQUEST_URI']).'" />';
    cn_snippet_digital_signature();
}

// show comments
echo join('', $com);
echo $cpn;

if ($total_comments && (test('Mda')||test('Mds')))
{
    // Some operations, e.g. remove comments
    if (test('Mda')||test('Mds'))
    {
        echo '<div id="del_btn" class="cn_comment_submit" style="visibility:hidden;"><input class="btn btn-default btn-danger" type="submit" id="btn_delete" value="Delete comments"/></div>';
    }    
    echo '</form>';
    echo '<script type="text/javascript">function d(){var a=document.getElementById("del_btn"); var ck=document.getElementsByName("comm_delete[]"); var i=0; var en="visibility:hidden;"; var dl=document.getElementById("btn_delete"); var cheked=0; '
        . 'for(i=0;i<ck.length;i++){ if(ck[i].checked){ cheked++; en="visibility:visible;"; }} a.setAttribute("style", en); '
        . 'var btn_name="Delete comment"; if(cheked>1){btn_name=btn_name+"s";} dl.setAttribute("value",btn_name);}';
    echo 'var ss=document.getElementsByName("comm_delete[]"); var i=0; for (i=0; i<ss.length; i++){ ss[i].onclick=d; }</script>';
    
}

// ---------------------------------------------------------------------------------------------------------------------
/* Available placeholders:

 - {input_username}
 - {input_email}
 - {input_commentbox}
 - {smiles}
 - [captcha] ... {captcha} ... [/captcha] - if captcha enabled only
 - [submit]..[/submit] - make submit box
*/

$member = member_get();

if (($member && test('Mac')) || !$member)
{
    $comment_url = getoption('rw_engine') ? $_SERVER['REQUEST_URI'] : PHP_SELF;

    echo '<form class="form-signin" role="form" name="comment_frm" action="'.$comment_url.'" method="POST"/>';
    echo '<input type="hidden" name="id" value="'.$id.'" />';
    echo '<input type="hidden" name="subaction" value="addcomment" />';
    echo '<input type="hidden" name="popup" value="'.cn_htmlspecialchars(REQ('popup')).'" />';
    echo '<input type="hidden" name="referer" value="'.cn_htmlspecialchars($_SERVER['REQUEST_URI']).'" />';

    $edit_id = intval(REQ('edit_id'));
    if ($edit_id) 
    {
        echo '<input id="edt_comm_mode" type="hidden" name="edit_id" value="'.intval($edit_id).'" />';
    }
        
    if($is_encode)
    {
        $comments = $entry['co'];
        foreach ($comments as $item)
        {            
            $ni= iconv('UTF-8',$user_encoding.'//TRANSLIT',$item['c']);                            
            if($ni)
            {
                $entry['co'][$item['id']]['c']=$ni;
            }
        }
        
    }    
    $echo = entry_make($entry, 'form', $template, 'comm');

    // Keep [bb]codes[/bb]
    if ($edit_id) 
    {
        $echo = str_replace('&amp;#91;', '[', $echo);
    }

    echo $echo;
    echo '</form>';
}
else
{
    echo '<div class="cn_error_comment">'.i18n("Comments disabled for you.").'</div>';
}

return TRUE;