<?php if (!defined('EXEC_TIME')) die('Access restricted');

global $PHP_SELF, $_SESS;

// Scan external query
list($id, $template, $start_from) = GET('id, template, start_from');
$id = cn_id_alias($id);

if (!$template) $template = 'Default';
if ($id == 0) die("@SYSLOG: INTERNAL ERROR[1]");

$id = intval($id);
$ent = db_news_load(db_get_nloc($id));
$entry = $ent[$id];
$user = member_get();
$start_from = intval($start_from);

// Clean sig
if (isset($_GET['__signature_dsi_inline']))
    unset($_GET['__signature_dsi_inline']);

// Load system configurations
$comm_number   = intval( getoption('comments_per_page') );

// $comm_addcomm  = cn_get_template('form', $template);
// $comm_paginate = cn_get_template('prev_next', $template);

$comments = $entry['co'];
$total_comments = count($comments);

// ---------------------------------------------------------------------------------------------------------------------
$com = array();
global $_comment_iterator;

$user_encoding=getoption('frontend_encoding');
$is_encode=($user_encoding!='UTF-8')&&function_exists('iconv');
/* Comment struct: ID => [u]ser, [c]comment text, [e]mail, [ip] */
foreach ($comments as $comment)
{
    if($is_encode)
    {
        $bkp=$comment['c'];
        $comment['c']=iconv('UTF-8',$user_encoding.'//TRANSLIT',$comment['c']);        
        if(!$comment['c']) $comment['c']=$bkp;
    }
    $com[] = entry_make($comment, 'comment', $template, 'comm');
    $_comment_iterator++;
}

// remove temporary
unset($_comment_iterator);

if (getoption('reverse_comments'))
    ksort($com);

// Comment per page is set
if ($com_by_page = getoption('comments_per_page'))
{
    $com = array_slice($com, $start_from, $com_by_page, TRUE);
    $cpn = cn_get_template('comments_prev_next', $template);

    // Make prev link
    if ($start_from)
    {
        $st = $start_from - $com_by_page;
        if ($st < 0) $st = 0;

        if (getoption('rw_engine'))
            $hrefA = '<a href="'.cn_rewrite('comments', $id, $st).'">\\1</a>';
        else
            $hrefA = '<a href="'.cn_url_modify('start_from='.$st).'">\\1</a>';
    }
    else $hrefA = '\\1';

    // Make next link
    if (($st = $start_from + $com_by_page) < $total_comments)
    {
        if (getoption('rw_engine'))
            $hrefB = '<a href="'.cn_rewrite('comments', $id, $st).'">\\1</a>';
        else
            $hrefB = '<a href="'.cn_url_modify('start_from='.$st).'">\\1</a>';
    }
    else
        $hrefB = '\\1';

    // No comments to show
    if ($hrefA == '\\1' && $hrefB == '\\1')
        $cpn = '';

    // Replace
    $cpn = preg_replace('/\[prev-link\](.*?)\[\/prev-link\]/i', $hrefA, $cpn);
    $cpn = preg_replace('/\[next-link\](.*?)\[\/next-link\]/i', $hrefB, $cpn);
    $cpn = str_replace('{pages}', intval($start_from / $com_by_page) + 1, $cpn);
}
else
{
    $cpn = '';
}

if ($total_comments && test('Mda'))
{
    if (getoption('reverse_comments')) $comments = array_reverse($comments);

    // Action with comments box
    echo '<form action="'.PHP_SELF.'" method="POST">';
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

if ($total_comments && test('Mda'))
{
    // Some operations, e.g. remove comments
    if (test('Mda'))
        echo '<div class="cn_comment_submit"><input type="submit" value="Delete comments"/></div>';

    echo '</form>';
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
if ($member && test('Mac') || !$member)
{
    echo '<form action="'.PHP_SELF.'" method="POST"/>';
    echo '<input type="hidden" name="id" value="'.$id.'" />';
    echo '<input type="hidden" name="subaction" value="addcomment" />';
    echo '<input type="hidden" name="popup" value="'.cn_htmlspecialchars(REQ('popup')).'" />';
    echo '<input type="hidden" name="referer" value="'.cn_htmlspecialchars($_SERVER['REQUEST_URI']).'" />';

    $edit_id = REQ('edit_id');
    if ($edit_id) echo '<input id="edt_comm_mode" type="hidden" name="edit_id" value="'.intval($edit_id).'" />';
        
    if($is_encode)
    {
        $comments=$entry['co'];
        foreach ($comments as $item)
        {            
            $ni= iconv('UTF-8',$user_encoding.'//TRANSLIT',$item['c']);                            
            if($ni) $entry['co'][$item['id']]['c']=$ni;
        }
        
    }
    $echo = entry_make($entry, 'form', $template, 'comm');

    // Keep [bb]codes[/bb]
    if ($edit_id) $echo = str_replace('&amp;#91;', '[', $echo);

    echo $echo;
    echo '</form>';
}
else
{
    echo '<div class="cn_error_comment">'.i18n("Comments disabled for you.").'</div>';
}

return TRUE;