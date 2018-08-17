<?php if (!defined('EXEC_TIME')) { die('Access restricted'); }
// FLAT file system data requestss


// ACL: basic access control level
define('ACL_LEVEL_ADMIN',         1);
define('ACL_LEVEL_EDITOR',        2);
define('ACL_LEVEL_JOURNALIST',    3);
define('ACL_LEVEL_COMMENTER',     4);
define('ACL_LEVEL_BANNED',        5);

function path_construct()
{
    $arg_list = func_get_args();
    if ($arg_list[0][0] == DIRECTORY_SEPARATOR) {
        $arg_list[0] = substr($arg_list[0], 1);
    }

    return DIRECTORY_SEPARATOR . join(DIRECTORY_SEPARATOR, $arg_list);
}

// Since 2.0: Check users exists. If no, require install script
function db_installed_check()
{
    $is_dir = SERVDIR.DIRECTORY_SEPARATOR.'cdata';
    $cfile = false;
    
    if (is_dir($is_dir) && is_writable($is_dir)) {
        $cfile = cn_touch(SERVDIR. path_construct('cdata', 'users.txt'));
    }

    if (empty($cfile) || filesize($cfile) < 4) {
        cn_require_install();
    }

    return TRUE;
}

// Since 2.0: Make backup file
function db_make_bk($fn)
{
    return $fn.'-'.mt_rand().'.bak';
}

// ------------------------------------------------------------------------------------------------------------ USER ---

// Since 2.0: Get user by any indexed field (id, ...) [x2 slowed, than by_name]
function db_user_by($eid, $match = 'id')
{
    $cu = cn_touch_get(SERVDIR. path_construct('cdata','users',substr(md5($eid), 0, 2).'.php'));

    // Translate id -> name [reference]
    if (!isset($cu[$match][$eid]))
    {
        return NULL;
    }
    else
    {
        return db_user_by_name($cu[$match][$eid]);
    }
}

// Since 2.0: Get user by id
function db_user_by_name($name, $index = FALSE)
{
    $uex = array();

    // Get from php-serialized array
    $cu = cn_touch_get(SERVDIR. path_construct('cdata','users',substr(md5($name), 0, 2).'.php'));

    // Check at index
    if ($index)
    {
        $rd = fopen(cn_touch(SERVDIR . path_construct('cdata', 'users', 'users.txt')), 'r');
        while ($a = fgets($rd))
        {
            list($uid) = explode(':', 2);
            $uex[base_convert($uid, 36, 10)] = TRUE;
        }
        fclose($rd);

        // user exists, but not in index
        if (isset($cu['name'][$name]) && !isset($uex[ $cu['name'][$name]['id'] ]))
        {
            return NULL;
        }
    }

    if (!isset($cu['name'][$name]))
    {
        return NULL;
    }

    // Decode serialized more data
    $pdata = $cu['name'][$name];    
    if (isset($pdata['more']) && $pdata['more'])
    {
        $pdata['more'] = unserialize(base64_decode($pdata['more']));
    }
    else
    {
        $pdata['more'] = array();
    }

    return $pdata;
}

// Since 2.0: Add user to DB
function db_user_add($username, $acl, $user_id = 0)
{
    if ($user_id == 0)
    {
        $user_id = ctime();
    }
    
    $acl = intval($acl);

    // Already exists
    if (db_user_by_name($username, TRUE))
    {
        return NULL;
    }
    
    // add to index
    $a = fopen(SERVDIR.path_construct('cdata','users.txt'), 'a+');
    fwrite($a, base_convert($user_id, 10, 36).':'.$acl."\n");
    fclose($a);

    // add to database
    db_user_update($username, "id=$user_id", "name=$username", "acl=$acl");

    return $user_id;
}

function db_user_update_index($user_id,$acl)
{
    $path=cn_touch(SERVDIR. path_construct('cdata','users.txt'));

    $key=base_convert($user_id, 10, 36);
    $uh=fopen($path,'r');
    $idx=array();
    while($row=  fgets($uh))
    {
        if(trim($row)=='')
        {
            continue;
        }
        $row=  str_replace("\n", '', $row);
        list($uk,$uacl)=  explode(':', $row);
        if($uk==$key)
        {
            $uacl=$acl;
        }
        $idx[]=$uk.':'.$uacl."\n";
    }
    
    $bk = db_make_bk($path);
    $wh=  fopen($bk, 'w+');
    foreach ($idx as $str)
    {
        fwrite($wh, $str);
    }
    
    fclose($uh);
    fclose($wh);
    
    return rename($bk,$path);
}


// Since: 2.0: Delete user from database
function db_user_delete($username)
{
    $fn =SERVDIR.path_construct('cdata','users',substr(md5($username), 0, 2).'.php');
    $cu = cn_touch_get($fn);

    if (isset($cu['name'][$username]))
    {
        $id   = $cu['name'][$username]['id'];
        $id36 = base_convert($id, 10, 36);
        
        // remove avatar file
        if(isset($cu['name'][$username]['avatar']))
        {
            $avatar_file=SERVDIR.path_construct('uploads',$cu['name'][$username]['avatar']);
            if(file_exists($avatar_file))
            {
                unlink($avatar_file);
            }
        }
        
        // delete user
        unset($cu['name'][$username]);

        // delete reference
        unset($cu['id'][$id]);

        // save new database
        cn_fsave($fn, $cu);

        // delete user index
        $fn = cn_touch(SERVDIR. path_construct('cdata','users.txt'));
        $bk = db_make_bk($fn);

        $r = fopen($fn, 'r');
        $w = fopen($bk, 'w+');
        while ($v = fgets($r))
        {
            if (!$v)
            {
                break;
            }
            list($eid) = explode(':', $v, 2);
            if ($eid !== $id36) 
            {
                fwrite($w, $v);                
            }
        }
        fclose($r);
        fclose($w);

        return rename($bk, $fn);
    }

    return FALSE;
}

// Since 2.0: if no user diffs, delete user
function db_user_update()
{
    $cp         = array();
    $args       = func_get_args();
    $username   = array_shift($args);

    if (!$username)
    {
        return NULL;
    }

    // -------
    $fn =SERVDIR. path_construct( 'cdata','users',substr(md5($username), 0, 2).'.php');
    $cu = cn_touch_get($fn);

    foreach ($args as $v)
    {
        list($a, $b) = explode('=', $v, 2);
        $cp[$a] = $b;
    }

    // Create main block
    if (!isset($cu['name']))
    {
        $cu['name'] = array();
    }
    
    if (!isset($cu['name'][$username])) 
    {
        $cu['name'][$username] = array();
    }

    // Update fields
    foreach ($cp as $i => $v) 
    {
        $cu['name'][$username][$i] = $v;
    }

    // Save DB
    cn_fsave($fn, $cu);

    //update index
    if(isset($cp['acl']))
    {
        db_user_update_index($cu['name'][$username]['id'],$cp['acl']);
    }
    
    // -------
    // Make references
    if (isset($cp['id'])) // ID -> USERNAME
    {
        $cu = cn_touch_get($lc =SERVDIR. path_construct( 'cdata','users',substr(md5($cp['id']), 0, 2).'.php'));

        if (!isset($cu['id'])) 
        {
            $cu['id'] = array();
        }
        $cu['id'][$cp['id']] = $username;
        cn_fsave($lc, $cu);
    }

    if (isset($cp['email'])) // EMAIL -> USERNAME
    {
        $cu = cn_touch_get($lc =SERVDIR. path_construct( 'cdata','users',substr(md5($cp['email']), 0, 2).'.php'));

        if (!isset($cu['email'])) 
        {
            $cu['email'] = array();
        }
        $cu['email'][$cp['email']] = $username;
        cn_fsave($lc, $cu);
    }

    return TRUE;
}

// Since 2.0: Fetch index
function db_user_list()
{
    $fn = cn_touch(SERVDIR. path_construct('cdata','users.txt'));

    $ls = array();
    $fc = file($fn);
    foreach ($fc as $v)
    {
        list($id, $acl) = explode(':', $v, 2);
        $ls[base_convert($id, 36, 10)] = array('acl' => $acl);
    }

    return $ls;
}

// ------------------------------------------------------------------------------------------------------------ NEWS ---

// Since 2.0: Tranform $ID to date
function db_get_nloc($id)
{
    return date('Y-m-d', $id);
}

// Since 2.0: Save block database entries
// @permanent alias of cn_fsave for Flat DB structure
function db_save_news($es, $location)
{
    if (substr($location, 0, 4) == '1970')
    {
        return FALSE;
    }

    return cn_fsave(SERVDIR . path_construct('cdata', 'news', $location.'.php'), $es);
}

// Since 2.0: Load block database entries
// @permanent alias of cn_touch_get for Flat DB structure
function db_news_load($location)
{
    if (substr($location, 0, 4) == '1970')
    {
        return array();
    }
    
    return cn_touch_get(SERVDIR. path_construct('cdata','news',$location.'.php'));
}

// Since 2.0: Helper for db_index_(load|save)
function db_index_file_detect($source = '')
{
    $fn = SERVDIR. path_construct('cdata','news');

    // Aliases for active news
    if ($source == 'iactive' || $source == 'postpone'  || $source == 'A2')
    {
        $source = '';
    }
    
    if ($source == '')
    {
        $fn .=DIRECTORY_SEPARATOR. 'iactive.txt';
    }
    elseif ($source == 'draft') 
    {
        $fn .=DIRECTORY_SEPARATOR. 'idraft.txt';
    }
    elseif (substr($source, 0, 7) == 'archive') 
    {
        $fn .= DIRECTORY_SEPARATOR.$source.'.txt';
    }
    elseif (substr($source, 0, 4) == 'meta')
    {
        $source = substr($source, 5);
        if (!$source) 
        {
            $source = 'iactive';
        }
        $fn .=DIRECTORY_SEPARATOR. 'meta-'.$source.'.txt';
    }
    elseif(substr($source,0,5)=='group')
    {
        $fn.=DIRECTORY_SEPARATOR.$source.'.txt';
    }
    
    if (!file_exists($fn)) 
    {
        fclose(fopen($fn, "w+"));
    }

    return $fn;
}

// Since 2.0: Get bind for index file
function db_index_bind($source = '')
{
    $fn = db_index_file_detect($source);
    $fm = db_index_file_detect("meta-$source");

    $bind = array
    (
        'sc' => $source,
        'fn' => $fn,
        'mt' => $fm,
        'rs' => fopen($fn, 'r'),
    );

    return $bind;
}

// Since 2.0: Get next item from index
function db_index_next($bind)
{
    $e = trim(fgets($bind['rs']));
    if (!$e) 
    {
        return NULL;
    }

    $parts=explode(':', $e);
    $id =   isset($parts[0]) ? base_convert($parts[0], 36, 10)  : FALSE;
    $c  =   isset($parts[1]) ? $parts[1]                        : FALSE;   
    $ui =   isset($parts[2]) ? base_convert($parts[2], 36, 10)  : FALSE;
    $co =   isset($parts[3]) ? $parts[3]                        : FALSE;
        
    return array('id' => $id, 'c' => $c, 'uid' => $ui, 'co' => $co);
}

// Since 2.0: Close bind file
function db_index_unbind($bind)
{
    if (is_resource($bind['rs']))
    {
        fclose($bind['rs']);
    }
}

// Since 2.0: Load index, $source = [|draft|archive]
function db_index_load($source = '')
{
    $ls = array();    
    $rd = fopen(db_index_file_detect($source), 'r');    
    while ($id = trim(fgets($rd)))
    {
        // Extract index information        
        list($id, $cid, $ui, $co) = explode(':', $id);

        $id = intval(base_convert($id, 36, 10));
        $ui = intval(base_convert($ui, 36, 10));
        $ls[$id] = array($cid, $ui, $co);
    }
    fclose($rd);

    return $ls;
}

// Since 2.0: Save index, $source = [|draft|archive]
function db_index_save($idx, $source = '')
{
    $ls = array();
    $fn = db_index_file_detect($source);

    foreach ($idx as $id => $ix)
    {
        // 0 - category, 1 - user_id, 2 - comments number
        $ls[$id] = base_convert($id, 10, 36).':'.$ix[0].':'.base_convert($ix[1], 10, 36).':'.$ix[2].':'."\n";
    }

    // Order by latest
    krsort($ls);

    $dest = db_make_bk($fn);
    $w = fopen($dest, 'w+');
    fwrite($w, join('', $ls));
    fclose($w);

    // after, set new index
    return rename($dest, $fn);
}

// Since 2.0: Load metadata from index
// @Return: array $uids, array $locs, int $coms
function db_index_meta_load($source = '', $load_users = FALSE)
{
    $fn = db_index_file_detect("meta-$source");
    $ls = unserialize(base64_decode(join('', file($fn))));

    // Decode userids
    $uids = array();

    if ($load_users)
    {
        if(isset($ls['uids']))
        {
            foreach ($ls['uids'] as $id => $ct)
            {
                $id = base_convert($id, 36, 10);
                $user = db_user_by($id);

                if ($user['name'])
                {
                    $uids[ $user['name'] ] = $ct;
                }
            }
        }
        $ls['uids'] = $uids;
    }

    return $ls;
}

// Since 2.0: Update overall data about index
function db_index_update_overall($source = '')
{
    $ct = ctime();
    $period = 30*24*3600;

    $fn = db_index_file_detect($source);
    $ls = file($fn);

    $index_data = array
    (
        'uids' => array(),
        'locs' => array(),
        'coms' => 0,
        'min_id' => $ct,
    );

    foreach ($ls as $vi)
    {        
        $vips=explode(':', $vi);
        $id=isset($vips[0])?$vips[0]:false;
        $ui=isset($vips[2])?$vips[2]:false;
        $co=isset($vips[3])?$vips[3]:false;                

        if($id!==FALSE)
        {
            $id  = base_convert($id, 36, 10);
            $loc = db_get_nloc($id);            
            if(isset($index_data['locs'][$loc]))
            {
                $index_data['locs'][$loc]++;
            }
            else 
            {
                $index_data['locs'][$loc]=1;
            }

            if ($index_data['min_id'] > $id)
            {
                $index_data['min_id'] = $id;            
            }
        }
        if($ui!==FALSE)
        {
            if(isset($index_data['uids'][$ui]))
            {
                $index_data['uids'][$ui]++;
            }
            else
            {
                $index_data['uids'][$ui]=1;
            }
        }
        if($co!==FALSE)
        {
            $index_data['coms'] += $co;
        }
    }

    // Active news is many, auto archive it (and user is hasn't draft rights)
    if ($source == '' && $index_data['min_id'] < $ct - $period && getoption('auto_archive') && !test('Bd'))
    {
        $cc = db_make_archive(0, ctime());
        cn_throw_message('Autoarchive performed');

        if (getoption('notify_archive'))
        {
            cn_send_mail(getoption('notify_email'), i18n("Auto archive news"), i18n("Active news has been archived (%1 articles)", $cc));
        }

        // Refresh overall index
        return db_index_update_overall();
    }

    // save meta-data
    $meta = db_index_file_detect("meta-$source");
    if($w = fopen($meta, "w+"))
    {
        fwrite($w, base64_encode(serialize($index_data))); 
        fclose($w);
    }
    
    return TRUE;
}

// Since 2.0: Create index by $entry
function db_index_create($e)
{
    $u = db_user_by_name($e['u']);
    return array($e['c'], $u['id'], count($e['co']));
}

// Since 2.0: Append to index new entry
function db_index_add($id, $category, $uid, $source = '')
{
    $fn   = db_index_file_detect($source);
    $dest = db_make_bk($fn);

    $i = TRUE;
    $s = base_convert($id, 10, 36).':'.$category.":".base_convert($uid, 10, 36).':0::'."\n";

    $r = fopen($fn, 'r');
    $w = fopen($dest, 'w+');

    while ($a = fgets($r))
    {
        list($i36) = explode(':', $a);

        if (base_convert($i36, 36, 10) < $id && $i)
        {
            $i = FALSE;
            fwrite($w, $s);
        }

        fwrite($w, $a);
    }

    // Not inserted before, insert after
    if ($i) fwrite($w, $s);

    fclose($r);
    fclose($w);

    return rename($dest, $fn);
}


// ------------------------------------------------------------------------------------------------ ARCHIVES -----------

function db_make_archive($id_from, $id_to)
{
    $archive_id = ctime();

    $cc = 0;
    $fc = db_index_file_detect();
    $fn = db_index_file_detect('archive-'.$archive_id);
    $al = cn_touch(SERVDIR. path_construct('cdata','news','archive.txt'));
    $bk = db_make_bk($fc);

    $rs = fopen($fc, 'r');
    $ws = fopen($bk, 'w+');
    $as = fopen($fn, 'w+');

    while ($ln = fgets($rs))
    {
        list($id36) = explode(':', $ln);
        $id = base_convert($id36, 36, 10);

        if ($id >= $id_from && $id <= $id_to)
        {
            fwrite($as, $ln);
            $cc++;
            continue;
        }

        fwrite($ws, $ln);
    }

    fclose($ws);
    fclose($rs);
    fclose($as);

    if ($cc)
    {
        $a = fopen($al, 'a+');
        fwrite($a, $archive_id.'|'.$id_from.'|'.$id_to.'|'.$cc."|\n");
        fclose($a);

        // finalize
        rename($bk, $fc);

        // update indexes
        db_index_update_overall();
        db_index_update_overall("archive-$archive_id");
    }
    else
    {
        unlink($fn);
    }

    return $cc;
}

// Since 2.0: Add, Delete (count=0) or Update archive line
function db_archive_meta_update($archive_id, $min, $max, $count)
{
    $fn = db_index_file_detect("archive");
    $bk = db_make_bk($fn);

    $rs = fopen($fn, 'r');
    $ws = fopen($bk, 'w+');

    $updated = FALSE;
    while ($ln = fgets($rs))
    {
        list($id) = explode('|', $ln);
        if ($id == $archive_id)
        {
            $updated = TRUE;

            // save archive, if count > 0
            if ($count) 
            {
                fwrite($ws, "$id|$min|$max|$count\n");
            }
            // if count = 0, delete archive
            else
            {
                unlink(db_index_file_detect("meta-archive-$id"));
                unlink(db_index_file_detect("archive-$id"));
            }
        }
        else
        {
            fwrite($ws, $ln);
        }
    }

    if (!$updated)
    {
        fwrite($ws, "$archive_id|$min|$max|$count\n");
    }

    fclose($ws);
    fclose($rs);

    return rename($bk, $fn);
}

// Since 2.0: Extract
function db_extract_archive($archive_id)
{
    $ls = array();
    $fc = db_index_file_detect();
    $fn = db_index_file_detect('archive-'.$archive_id);
    $al = cn_touch(SERVDIR. path_construct('cdata','news','archive.txt'));
    $bk = db_make_bk($fc);

    // Load archive entries
    $rs = fopen($fn, 'r');
    while ($ln = fgets($rs))
    {
        list($id36) = explode(':', $ln, 2);
        $ls[base_convert($id36, 36, 10)] = $ln;
    }
    fclose($rs);

    // Load active entries for concatenate
    $rs = fopen($fc, 'r');
    while ($ln = fgets($rs))
    {
        list($id36) = explode(':', $ln, 2);
        $ls[base_convert($id36, 36, 10)] = $ln;
    }
    fclose($rs);

    // Concat & save new active
    krsort($ls);

    $w = fopen($bk, "w+");
    foreach ($ls as $it) fwrite($w, $it);
    fclose($w);

    // Delete index from archive index
    $ar = file($al);
    $ws = fopen($al, 'w+');
    foreach ($ar as $v)
    {
        list($ra) = explode('|', $v, 2);
        if ($ra != $archive_id) fwrite($ws, $v);
    }
    fclose($ws);

    // Finalize
    if (!rename($bk, $fc))
        return FALSE;

    // update indexes
    db_index_update_overall();

    unlink(db_index_file_detect('meta-archive-'.$archive_id));
    unlink($fn);

    return TRUE;
}

// Since 2.0: Get all archives
function db_get_archives()
{
    $archs = array();
    $fn = db_index_file_detect('archive');
    $ti = file($fn);
    
    foreach ($ti as $vi)
    {
        list($archid, $min_id, $max_id, $count) = explode('|', $vi);
        $archs[$archid] = array('id' => $archid, 'c' => $count, 'min' => $min_id, 'max' => $max_id);
    }
    
    // By last added date
    krsort($archs);    
    return $archs;
}

// ----------------------------------------------------------------------------------------------------- COMMENTS ------
// Since 2.0.1
function db_comm_sync($id, $comm_id)
{
    $chain = bt_get_id('lc:top', 'comm');
    if (is_null($chain)) $chain = array(array(), null, 0); // entries, << back

    // Save ID, CommentId, referer
    $chain[0][] = "$id:$comm_id:".$_REQUEST['referer'];

    // Is over limit (64 entries by cluster)
    if (count($chain[0]) > 64)
    {
        $new_entity = md5(mt_rand());
        bt_set_id("lc:$new_entity", $chain, 'comm');

        // Make clean
        $chain[0] = array();
        $chain[1] = $new_entity;
    }

    $chain[2]++;
    bt_set_id('lc:top', $chain, 'comm');
}

// Since 2.0.1
function db_comm_delete($id, $comm_id)
{
    $nloc = db_get_nloc($id);
    $db   = db_news_load($nloc);
    $bt   = bt_get_id('lc:top', 'comm');

    // Action delete if exists
    if (isset($db[$id]['co'][$comm_id]))
    {
        $bt[2]--;
        unset($db[$id]['co'][$comm_id]);
    }

    bt_set_id('lc:top', $bt, 'comm');
    db_save_news($db, $nloc);
}

// Since 2.0.1
function db_comm_lst($start_from = 0, $clusters = 1)
{
    $cluster = intval( $start_from / 64 );

    $bt = bt_get_id('lc:top', 'comm');
    $cn = $bt[2];
    $bc = $cb = array();

    // Fetch descending order comments
    for ($i = 0; $i < $cluster + $clusters; $i++)
    {
        if(isset($bt[0]))
        {
            $bt[0] = array_reverse($bt[0]);
            if ($i >= $cluster) 
            {
                $bc = array_merge($bc, $bt[0]);
            }
        }
        if(isset($bt[1]))
        {
            $bt = bt_get_id('lc:'.$bt[1], 'comm');
        }
    }
    if(!empty($bc))
    {
        foreach ($bc as $citem)
        {
            list($_id, $_cid, $_ref) = explode(':', $citem, 3);
            $blk = db_news_load(db_get_nloc($_id));    
            if(isset($blk[$_id])&&isset($blk[$_id]['co'][$_cid]))
            {
                $cb[] = array(intval($_id), intval($_cid), $blk[$_id]['co'][$_cid], $_ref);
            }
        }
    }
    return array($cb, $cn);
}

class FlatDB
{
    // news_id storage
    var $stor = array();

    // list news index
    var $list = array();

    // Count of postponed news
    var $_item_postponed = 0;

    // Since 2.0: Get detailed info about table / tables
    function db__show_table( $table = NULL )
    {
        $_st = array();
        $DB  = SERVDIR . path_construct('cdata','news');

        if (is_null($table))
        {
            $_sd = scan_dir($DB, '\.db$');
            foreach ($_sd as $v) $_st[] = preg_replace('/\.db$/i', '', $v);
            return $_st;
        }
        else
        {
            // file .frm
            return $_st;
        }
    }

    // Since 2.0: Seek index at idx table
    function db_seek_index( $FKEY, $tbl )
    {
        fclose( fopen($fn = SERVDIR . path_construct("cdata", "news", $tbl.".idx"), 'a+') );
        $rd = fopen($fn, 'r'); fseek($rd, 0, SEEK_SET);

        while ($row = trim(fgets($rd)))
        {
            list($fv, $data) = explode('|', $row, 2);
            if ($fv == $FKEY) { fclose($rd); return $data; }
        }

        fclose($rd);
        return NULL;
    }

    // Since 2.0: UPDATE/DELETE index
    function db_update_index( $FKEY, $vm, $tbl )
    {
        fclose( fopen($fn = SERVDIR .path_construct( "cdata","news",$tbl.".idx"), 'a+') );
        $fk = SERVDIR . path_construct("cdata","news",$tbl.".idx." . mt_rand(0, 10000));

        $rd = fopen($fn, 'r');
        $wt = fopen($fk, 'w+');

        while ($row = trim(fgets($rd)))
        {
            list($fv) = explode('|', $row, 2);

            if ($fv == $FKEY)
            {
                if (!is_null($vm)) fwrite($wt, "$FKEY|$vm\n");
            }
            else
            {
                fwrite($wt, "$row\n");
            }
        }

        fflush($rd); fclose($rd);
        fflush($wt); fclose($wt);

        // Replace previous data
        return rename($fk, $fn);
    }

    // Since 2.0: Insert index to DB, save index order [descending]
    function db_insert_index( $FKEY, $vm, $tbl )
    {
        $DIS = FALSE;
        fclose( fopen($fn = SERVDIR . path_construct("cdata","news",$tbl.".idx"), 'a+') );
        $fk = SERVDIR . path_construct("cdata","news",$tbl.".idx." . mt_rand(0, 10000));

        $r = fopen($fn, 'r');
        $w = fopen($fk, 'w+');

        while ($row = trim(fgets($r)))
        {
            list($fv) = explode('|', $row, 2);

            if ($fv < $FKEY && $DIS == false)
            {
                $DIS = TRUE;
                fwrite($w, "$FKEY|$vm\n");
            }

            fwrite($w, "$row\n");
        }

        // At end...
        if ($DIS == FALSE) fwrite($w, "$FKEY|$vm\n");

        fflush($r); fclose($r);
        fflush($w); fclose($w);
        
        // Replace previous data
        return rename($fk, $fn);
    }

    // ------------------------------------------

    // Since 2.0: Add news_id to separated categories
    function cn_add_categories( $comma_cat, $news_id )
    {
        // Append indexes to specific categories
        $cat_ids = $comma_cat ? explode(',', $comma_cat) : array(0);

        foreach ($cat_ids as $CID)
        {
            $opt = $this->db_seek_index($CID, 'cat');

            if (is_null($opt))
            {
                $this->db_insert_index($CID, $news_id, 'cat');
            }
            else
            {
                $opt = explode(',', $opt); $opt[] = $news_id;
                $this->db_update_index($CID, join(',', $opt), 'cat');
            }
        }
    }

    // Since 2.0: Remove from index separated categories
    function cn_remove_categories( $comma_cat, $news_id )
    {
        // Get previous categories entry
        $cat_pre = $comma_cat ? explode(',', $comma_cat) : array(0);

        foreach ($cat_pre as $CID)
        {
            $opt = $this->db_seek_index($CID, 'cat');
            if (is_null($opt)) continue;

            // Remove from flat id list
            $opt = explode(',', $opt);
            $opt = array_flip($opt);
            unset($opt[ $news_id ]);

            // Remove, if not exists
            $Da = count($opt) ? join(',', array_flip($opt)) : NULL;

            $this->db_update_index($CID, $Da, 'cat');
        }
    }

    // Since 2.0: Date is UNIX_TIMESTAMP
    function cn_update_date( $next_id, $prev_id = 0, $comments = 0 )
    {
        // No changes
        if ($next_id && $next_id == $prev_id) 
        {
            return;
        }
        
        // Update current (for comments count)
        if (is_null($prev_id)) 
        {
            $this->db_update_index($next_id, NULL, 'date');
        }        
        // Remove previous, if exists
        elseif ($prev_id) 
        {
            $this->db_update_index($prev_id, NULL, 'date');
        }

        // Add / Update
        if ($next_id)
        {
            $this->db_insert_index($next_id, $comments, 'date');
        }
    }

    // Since 2.0: Link news with user
    function cn_user_sync( $user, $news_id, $prev_id = 0 )
    {
        $opt = $this->db_seek_index($user, 'users');

        if (is_null($opt))
        {
            $this->db_insert_index($user, $news_id, 'users');
        }
        else
        {
            $opt = explode(',', $opt);

            if ($prev_id)
            {
                $opt = array_flip($opt);
                unset($opt[$prev_id]);

                // No one news by user, remove db row
                if (count($opt) == 0) $opt = NULL; else $opt = join(',', array_flip($opt));
            }
            else
            {
                $opt[] = $news_id;
                $opt = join(',', $opt);
            }

            $this->db_update_index($user, $opt, 'users');
        }
    }

    // ---------------------------------------------------------

    // Since 2.0: Add to tag DB
    function cn_add_tags($comma_taglist, $news_id)
    {
        $tag_ids = $comma_taglist ? explode(',', $comma_taglist) : array();

        foreach ($tag_ids as $tag)
        {
            $tag = trim($tag);
            $opt = $this->db_seek_index($tag, 'tags');

            if (is_null($opt))
            {
                $this->db_insert_index($tag, $news_id, 'tags');
            }
            else
            {
                $opt = explode(',', $opt);
                $opt[] = $news_id;

                $this->db_update_index($tag, join(',', $opt), 'tags');
            }
        }
    }

    // Since 2.0: Remove from index separated tags
    function cn_remove_tags( $comma_taglist, $news_id )
    {
        if ($comma_taglist === '') return;
        $tag_ids = explode(',', $comma_taglist);

        foreach ($tag_ids as $tag)
        {
            $tag = trim($tag);
            $opt = $this->db_seek_index($tag, 'tags');
            if (is_null($opt)) continue;

            // Remove from flat id list
            $opt = array_flip( explode(',', $opt) );
            unset($opt[ $news_id ]);

            // Remove, if not exists
            $Da = count($opt) ? join(',', array_flip($opt)) : NULL;
            $this->db_update_index($tag, $Da, 'tags');
        }
    }

    // ------------------------------------

    // Since 2.0: Load all news from DB
    // $target means section, where * - active news

    function loadall()
    {
        $path = SERVDIR . path_construct("cdata", "news", "date.idx");
        if (file_exists($path))
        {
            $r = fopen($path, 'r');

            while ($row = trim(fgets($r)))
            {
                list($ts) = explode('|', $row);

                $this->stor[] = $ts;
            }

            fclose($r);
        }

        return $this->stor;
    }

    // Since 2.0.3: Weed by comma separated categories   
    function find_category( $cc )
    {                
        // Receive ids by category
        $summary_by_cat = array();

        foreach ($cc as $cid)
        {
            $rid = $this->db_seek_index($cid, 'cat');

            // Category is exists
            if (!is_null($rid))
            {
                $intersect = array_intersect($this->stor, explode(',', $rid));
                $summary_by_cat = array_merge($summary_by_cat, $intersect);
            }            
        }
        
        if (!empty($summary_by_cat))
        {
            $this->stor = array_unique($summary_by_cat);        
        }
        
        rsort( $this->stor );
    }

    // Since 2.0: Weed by user (at ->stor must be data)
    function weed_user( $uc )
    {
        if (count($uc) == 0) return;

        $UList = array();
        foreach ($uc as $u)
        {
            $U = $this->db_seek_index($u, 'users');
            if (is_null($U)) continue;

            $UList = array_merge($UList, explode(',', $U));
        }

        $this->stor = array_intersect($this->stor, array_unique($UList));
    }

    // Since 2.0: Weed by tagname
    function weed_tags( $tag )
    {
        if ($tag === '') 
        {
            return;
        }

        $t = $this->db_seek_index($tag, 'tags');
        if ($t)
        {
            $T = explode(',', $t); 
        }
        else 
        {
            $T = array();
        }

        $this->stor = array_intersect($this->stor, $T);
    }

    // Since 2.0: Search exact page ID
    function find_page_alias( $PA )
    {
        return bt_get_id($PA, 'pg_ts');
    }

    // Since 2.0: Update item for section
    function cn_source_update( $news_id, $section = '', $action = 'add' )
    {
        // Select index source
        if ($section == '') $tbl = 'so_news';
        elseif ($section == 'D') $tbl = 'so_draft';
        elseif (preg_match('/^[0-9]+$/', $section)) $tbl = "so_$section";
        else $tbl = 'so_postpone';

        // Add or remove from index
        return $this->db_insert_index($news_id, $action  == 'add' ? '*' : NULL, $tbl);
    }

    // Since 2.0: Get tag cloud
    function get_tagcloud()
    {
        // stub
    }

    // Since 2.0.3: Decode index item
    function item_idx_decode($s)
    {
        $news = array();
        $item = explode(':', $s);

        $news['id']        = base_convert($item[0], 36, 10);
        $news['category']  = $item[1] ? explode(',',$item[1]) : array();
        $news['user_id']   = base_convert($item[2], 36, 10);
        $news['comments']  = intval($item[3]);

        return $news;
    }

    // Since 2.0.3: Get ids | users | comments by active news
    function load_by($file = 'iactive.txt')
    {
        $counter = 0;

        if (file_exists($path = SERVDIR . path_construct('cdata', 'news', $file))) {

            $f = file($path);
            foreach ($f as $item)
            {
                $news = $this->item_idx_decode($item);
                $news['id'] = intval($news['id']);

                $this->list[ $news['id'] ] = $news;
                $counter++;
            }

            return $counter;
        }

        return false;
    }

    // Since 2.0.3: Get active and archive news
    function load_overall()
    {
        $cache_file = SERVDIR . path_construct('cdata', 'news', 'cache_A2.txt');
        $cache_en = (defined('CACHE_DISABLE') && CACHE_DISABLE) ? 0 : 1;

        // Load data from cache (if exists)
        if ($cache_en && file_exists($cache_file))
        {
            $this->list = unserialize(file_get_contents($cache_file));
            return true;
        }

        // First, load all active news
        $this->load_by();

        // Scan archives
        if (file_exists($archives = SERVDIR . path_construct('cdata', 'news', 'archive.txt')))
        {
            $arch_list = file($archives);
            foreach ($arch_list as $item)
            {
                $item = explode('|', $item);
                $this->load_by('archive-'.$item[0].'.txt');
            }
        }

        // Save cache file
        file_put_contents($cache_file, serialize($this->list));

        return true;
    }

    // Since 2.0.3: Append list by required data
    function load_ext_by($options)
    {
        $loadn = array();
        $klist = array_keys($this->list);

        asort($klist);

        // Load tags and extend data
        if ($options['tg']) { $loadn[] = 'tg'; }
        if ($options['title']) { $loadn[] = 'title'; }
        if ($options['author']) { $loadn[] = 'author'; }
        if ($options['vcnt']) { $loadn[] = 'vcnt'; }

        // Do ext fields load
        if ($loadn)
        {
            $oloc = null;

            foreach ($klist as $id)
            {
                $nloc  = db_get_nloc($id);

                // Cache feature [floating window]
                if ($nloc !== $oloc) {
                    $ndata = db_news_load($nloc);
                    $oloc  = $nloc;
                }

                // Append by needed fields
                foreach ($loadn as $field) if (isset($ndata)) {

                    if ($field === 'author') {
                        $this->list[ $id ][$field] = $ndata[ $id ]['u'];
                    }
                    elseif ($field === 'title') {
                        $this->list[ $id ][$field] = $ndata[ $id ]['t'];
                    }
                    elseif ($field === 'vcnt') {
                        $this->list[ $id ][$field] = $ndata[ $id ]['vcnt'];
                    }
                    else {
                        $this->list[ $id ][$field] = $ndata[ $id ][$field];
                    }
                }
            }
        }
    }

    // Since 2.0.3: Category, users and tags filter
    // $nocat - show news without category
    function filters($cat, $users, $tags, $nocat, $by_date, $nlpros)
    {
        $this->_item_postponed = 0;
        $tc_time = ctime();

        if ($tags)
        {
            if (is_string($tags)) { $tags = explode(',', $tags); }
            $tags = array_map('trim', $tags);
        }

        // Filter each item
        foreach ($this->list as $id => $item)
        {
            // Prepare tags, if exists
            // If tag is not exists, explode anyway - as empty string
            if (isset($item['tg'])) { $item['tg'] = explode(',', $item['tg']); }

            // If $nocat present, show news without category (if category not requested)
            if ($nocat && empty($cat) && $item['category'])
            {
                unset($this->list[$id]);
                continue;
            }
            // Category ids is present, if cat_id is NOT intersect with template - remove from listing
            elseif ($cat && !array_intersect($item['category'], $cat))
            {
                unset($this->list[$id]);
                continue;
            }

            // Same as category filter
            if ($users && !array_intersect(array($item['user_id']), $users))
            {
                unset($this->list[$id]);
                continue;
            }

            // Test tag presence
            if (isset($item['tg']) && $tags && !array_intersect($item['tg'], $tags))
            {
                unset($this->list[$id]);
                continue;
            }

            // By date is enabled
            if ($by_date)
            {
                $success = true;
                $date = explode('-', date("Y-m-d", $id));

                // Year is set, but not match
                if ($by_date[0] && $date[0] != $by_date[0]) {
                    $success = false;
                }
                // Month is set, but not match
                elseif ($by_date[1] && $date[1] != $by_date[1]) {
                    $success = false;
                }
                // Days is set, but not match
                elseif ($by_date[2] && $date[2] != $by_date[2]) {
                    $success = false;
                }

                if ($success === false) {
                    unset($this->list[$id]);
                    continue;
                }
            }

            // Counting postponed and skip, if needed
            if ($id > $tc_time)
            {
                $this->_item_postponed++;

                if (!$nlpros) {
                    unset($this->list[$id]);
                    continue;
                }
            }
        }
    }

    // Sort method
    var $_sort_by = '';
    var $_sort_as = 0;

    // Since 2.0.3: UserSort
    function sorting_function($A, $B)
    {
        $C0 = 0 + $this->_sort_as;
        $C1 = 1 - $this->_sort_as;

        return $A[ $this->_sort_by ] < $B[ $this->_sort_by ] ? $C0 : $C1;
    }

    // Since 2.0.3: Sort incoming listing
    // $sort must be 'date', 'author', 'comments', 'title' or ''
    // $by   must be 'R' or 'D' (reverse) or any char (asc)
    // ---------------------------------------------
    function sorting($sort, $by)
    {
        if ($sort)
        {
            $this->_sort_by = $sort;
            $this->_sort_as = ($by === 'R' || $by === 'D') ? 1 : 0;

            // Correcting fields names
            if ($this->_sort_by === 'date') { $this->_sort_by = 'id'; }
            elseif ($this->_sort_by === 'tag') { $this->_sort_by = 'tg'; }

            // Do sort
            uasort($this->list, array($this, 'sorting_function'));
        }
    }

    // Since 2.0.3: Get slice for pagination
    function slicing($st, $per_page)
    {
        if ($per_page)
        {
            $this->list = array_slice($this->list, $st, $per_page, true);
        }
        elseif ($st)
        {
            $this->list = array_slice($this->list, $st, null, true);
        }
    }

    // Since 2.0.3: Load entries by key of $this->list array
    function load_entries()
    {
        $blocks = array_keys($this->list);
        asort($blocks);

        $oloc = null;
        foreach ($blocks as $id)
        {
            $nloc  = db_get_nloc($id);

            // Bulk requests
            if ($nloc !== $oloc)
            {
                $ndata = db_news_load($nloc);
                $oloc  = $nloc;
            }

            if (isset($ndata[$id]) && $ndata[$id]) {
                $this->list[$id] = $ndata[$id];
            }
            else {
                unset($this->list[$id]);
            }
        }

        return $this->list;
    }

    // Since 2.0.3: Prepare users_id
    function load_users_id($ufilter)
    {
        foreach ($ufilter as $key => $user_name)
        {
            $u = db_user_by_name($user_name);
            $ufilter[$key] = $u['id'];
        }

        return $ufilter;
    }

    // Since 2.0.3: Lookup users in index
    // 1) Fetch user list
    // 2) Test any user by name
    function user_lookup($username)
    {
        $users = db_user_list();

        foreach ($users as $uid => $acl)
        {
            $user = db_user_by($uid);
            if (isset($user['name']) && $user['name'] == $username)
            {
                return $user;
            }
        }

        return null;
    }

    // Since 2.0.4: Check cache file exists
    function cache_not_exists($md5)
    {
        $cache_file = SERVDIR . path_construct('cdata', 'news', 'cache_A2_'.$md5.'.txt');

        if (file_exists($cache_file)) {
            $this->list = unserialize(file_get_contents($cache_file));
            return false;
        }

        return true;
    }

    // Since 2.0.4: Save cache file
    function cache_save($md5)
    {
        $cache_file = SERVDIR . path_construct('cdata', 'news', 'cache_A2_'.$md5.'.txt');
        file_put_contents($cache_file, serialize($this->list));
    }

    // Since 2.0.4: Clean cache
    function cache_clean()
    {
        $cache_dir = SERVDIR . path_construct('cdata', 'news');
        $dat = scan_dir($cache_dir);

        foreach ($dat as $name)
        {
            if (preg_match('/^cache_A2/', $name)) {
                $fp = $cache_dir . DIRECTORY_SEPARATOR . $name;
                unlink($fp);
            }
        }
    }
}