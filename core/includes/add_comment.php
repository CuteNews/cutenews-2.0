<?php if (!defined('EXEC_TIME')) die('Access restricted');

global $PHP_SELF, $_SESS;

list($id, $action) = GET('id, action');
$id = cn_id_alias($id);

// ------------------------------------
$user = member_get();

if ($user)
{
    $logged_as_member = TRUE;
    $name = $user['name'];
    $mail = $user['email'];

    // Extrn call from internal widget (login), if checkbox 'remember_me' is set
    if ((isset($_POST['CN_COOKIE_POSTPROCESS'])) && isset($_POST['cn_remember_me']))
        cn_save_session(TRUE);
}
else
{
    $logged_as_member = FALSE;
    $name = trim(REQ('name', 'POST'));
    $mail = trim(REQ('mail', 'POST'));
}

// Can't add comment
if ($user && !test('Mac'))
{
    echo '<div class="cn_error_comment">'.i18n("You can't add comment").'. <a href="'.$refer.'">Go back</a></div>';
    return FALSE;
}

$comment    = trim(REQ('comments', 'POST'));
$refer      = cn_htmlspecialchars(REQ('referer'));
$regex_site = '/(ftps?|n?ntp|pop3|https?):\/\/[^\s]+/is';

// ----------------------------------
if ($action == 'comment_process')
{
    cn_dsi_check();

    list($comm_delete) = GET('comm_delete');
    if (is_array($comm_delete)) foreach ($comm_delete as $cid) db_comm_delete($id, $cid);

    // Redirect...
    echo '<script type="text/javascript">window.location="'.addslashes(REQ('referer')).'";</script>';
    echo '<div><a href="'.$refer.'">click there</a> if automatic redirect not work</div>';

    return FALSE;
}

// ----------------------------------
if (empty($comment))
{
    echo '<div class="blocking_posting_comment">'.i18n('Sorry but the comment cannot be blank').'. <a href="'.$refer.'">Go back</a></div>';
    return FALSE;
}

if (getoption('comment_max_long') && strlen($comment) > getoption('comment_max_long'))
{
    echo '<div class="cn_error_comment">'.i18n('Your comment is too long!').'. <a href="'.$refer.'">Go back</a></div>';
    return FALSE;
}

if (!$logged_as_member)
{
    if (strlen($name) > 50)
    {
        echo '<div class="cn_error_comment">'.i18n('Your name is too long!').'. <a href="'.$refer.'">Go back</a></div>';
        return FALSE;
    }

    if (strlen($name) == 0)
    {
        echo '<div class="cn_error_comment">'.i18n('Your must specify name').'. <a href="'.$refer.'">Go back</a></div>';
        return FALSE;
    }

    // Show login form for already registered user
    $user_exist = db_user_by_name($name);
    if ($user_exist)
    {
        echo '<div class="cn_error_comment">'.i18n('This name is already exist').'. <a href="'.$refer.'">Go back</a></div>';

        // Keep Referer + $_POST data
        login_guest($_POST, $name);

        return FALSE;
    }

    if (strlen($mail) > 50)
    {
        echo '<div class="cn_error_comment">'.i18n('Your e-mail is too long!').'. <a href="'.$refer.'">Go back</a></div>';
        return FALSE;
    }

    if (getoption('use_captcha'))
    {
        $cm_captcha = REQ('cm_captcha', 'POST');
        if ($_SESS['CSW'] && $_SESS['CSW'] !== $cm_captcha)
        {
            echo '<div class="cn_error_comment">'.i18n('Invalid CAPTCHA code').'. <a href="'.$refer.'">Go back</a></div>';
            return FALSE;
        }
    }

    $email_check = FALSE;
    if (getoption('allow_url_instead_mail') && preg_match($regex_site, $mail))
        $email_check = TRUE;

    if (check_email($mail))
        $email_check = TRUE;

    if (!$email_check)
    {
        echo '<div class="cn_error_comment">'.i18n('Email is invalid').'. <a href="'.$refer.'">Go back</a></div>';
        return FALSE;
    }
}

if (preg_match($regex_site, $comment))
{
    echo '<div class="cn_error_comment">'.i18n('Your not allowed to put URL\'s in the comments field.').'. <a href="'.$refer.'">Go back</a></div>';
    return FALSE;
}

if (getoption('only_registered_comment') && !$logged_as_member)
{
    echo '<div class="cn_error_comment">'.i18n('Only registered users can post comments').'. <a href="'.$refer.'">Go back</a></div>';
    return FALSE;
}

// Check ip/nick ban filter ----
$block_list = getoption('#ipban');
foreach ($block_list as $ip_test => $_t)
{
    // Create test string
    $match = '/'.str_replace('\x2a', '.*?', preg_sanitize($ip_test)).'/';

    if (preg_match($match, CLIENT_IP) || !$logged_as_member && preg_match($match, $name))
    {
        $block_list[$ip_test][0]++;
        setoption('#ipban', $block_list);

        echo '<div class="cn_error_comment">'.i18n('Sorry but you have been blocked from posting comments').' (IP='.cn_htmlspecialchars(CLIENT_IP).'). <a href="'.$refer.'">Go back</a></div>';
        return FALSE;
    }
}

// Check for flood (if enabled)
if ($flood_time = getoption('flood_time'))
{
    if (!file_exists($fn = SERVDIR.'/cdata/flood.txt'))
        fclose(fopen($fn, 'w+'));

    $flood = file($fn);

    $found = FALSE;
    $w = fopen($fn, 'w+');
    flock($w, LOCK_EX);

    foreach ($flood as $item)
    {
        list($ip, $time) = explode('|', $item);

        if (time() <= intval($time))
        {
            fwrite($w, "$ip|$time");
            if (CLIENT_IP == $ip) $found = TRUE;
        }
    }

    // Not found client ip, add to end of file
    if (!$found) fwrite($w, CLIENT_IP."|".(time() + $flood_time)."\n");

    flock($w, LOCK_UN);
    fclose($w);

    // Flood detected
    if ($found)
    {
        echo '<div class="cn_error_comment">'.i18n('Flood protection activated! You have to wait %1 seconds after your last comment before posting again at this article', $flood_time).'<a href="'.$refer.'"> Go back</a></div>';
        return FALSE;
    }
}

// YEAH! Do add comment!
$nloc = db_get_nloc($id);
$db   = db_news_load($nloc);

//check user login 
if (!$logged_as_member)
{
    $is_true_user=TRUE;
    foreach ($db[$id]['co'] as $dnews)
    {
        if ($dnews['u']==$name && $dnews['e'] != $mail)
        {
            $is_true_user=FALSE;
            break;
        }
    }
    if(!$is_true_user)
    {
        echo '<div class="cn_error_comment">'.i18n('This user name alredy exist, choose another').'. <a href="'.$refer.'">Go back</a></div>';
        return FALSE;
    }
}

// Can edit comment?
$acl_edit_comment = FALSE;
$edit_id          = intval(REQ('edit_id'));
$_target_user     = db_user_by_name($db[$id]['co'][$edit_id]['u']);

// Check: self [if can], group, and edit all
if ($edit_id && (test('Mes') && $_target_user && $_target_user['name'] == $user['name'] || test('Meg', $_target_user) || test('Mea')))
    $acl_edit_comment = TRUE;

// Check access for edit comment
if ($acl_edit_comment && REQ('cm_edit_comment', 'POST'))
{
    $cid = $edit_id;
}
else
{
    $cid = ctime();
    while (isset($db['co'][$cid])) $cid++;
}

//convert to right encoding
if(getoption('frontend_encoding')!='UTF-8'&&function_exists('iconv'))
{
    $bkp=$comment;
    $comment=  iconv(getoption('frontend_encoding'),'UTF-8//TRANSLIT' , $comment);
    if(!$comment)
    {
        $comment=$bkp;
    }
}

// ID => [u]ser, [c]comment text, [e]mail, [ip] */
$db[$id]['co'][$cid] = array
(
    'id' => $cid,
    'u'  => $name,
    'e'  => $mail,
    'ip' => CLIENT_IP,
    'c'  => $comment,
    'ed' => $edit_id,
);

db_save_news($db, $nloc); // save db piece
db_comm_sync($id, $cid);  // update latest comments

// Hook comment checker
if ( hook('add_comment_checker', FALSE) ) return FALSE;

// Notify for New Comment
if (getoption('notify_comment'))
{
    $url     = $_SERVER['HTTP_REFERER'];
    $date    = date(getoption('timestamp_active'), ctime());
    $subject = i18n("CuteNews - New Comment Added");
    $message = i18n("New Comment was added by %1 on %3 at %4\n\n%2 ", $name, $comment, $date, $url);
    cn_send_mail(getoption('notify_email'), $subject, $message);
}

// Also, remember non authorized user
if (!$logged_as_member && isset($_POST['cn_remember_me']))
    cn_guest_auth($name, $mail);

// Redirect...
echo '<script type="text/javascript">window.location="'.addslashes(REQ('referer')).'";</script>';
echo '<div><a href="'.$refer.'">click there</a> if automatic redirect not work</div>';

return FALSE;