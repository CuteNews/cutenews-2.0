<?php // FLAT file system data requestss

if (!defined('EXEC_TIME')) die('Access restricted');

// ACL: basic access control level
define('ACL_LEVEL_ADMIN',         1);
define('ACL_LEVEL_EDITOR',        2);
define('ACL_LEVEL_JOURNALIST',    3);
define('ACL_LEVEL_COMMENTER',     4);
define('ACL_LEVEL_BANNED',        5);

// Since 2.0: Check users exists. If no, require install script
function db_installed_check()
{
    $cfile = cn_touch('/cdata/users.txt');

    if (filesize($cfile) < 4)
        cn_require_install();

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
    $cu = cn_touch_get('/cdata/users/'.substr(md5($eid), 0, 2).'.php', TRUE);

    // Translate id -> name [reference]
    if (!isset($cu[$match][$eid]))
        return NULL;
    else
        return db_user_by_name($cu[$match][$eid]);
}

// Since 2.0: Get user by id
function db_user_by_name($name, $index = FALSE)
{
    $uex = array();

    // Get from php-serialized array
    $cu = cn_touch_get('/cdata/users/'.substr(md5($name), 0, 2).'.php', TRUE);

    // Check at index
    if ($index)
    {
        $rd = fopen(cn_touch('/cdata/users/users.txt'), 'r');
        while ($a = fgets($rd))
        {
            list($uid) = explode(':', 2);
            $uex[base_convert($uid, 36, 10)] = TRUE;
        }
        fclose($rd);

        // user exists, but not in index
        if (isset($cu['name'][$name]) && !isset($uex[ $cu['name'][$name]['id'] ]))
            return NULL;
    }

    if (!isset($cu['name'][$name]))
        return NULL;

    // Decode serialized more data
    $pdata = $cu['name'][$name];
    if (isset($pdata['more']) && $pdata['more'])
        $pdata['more'] = unserialize($pdata['more']);
    else
        $pdata['more'] = array();

    return $pdata;
}

// Since 2.0: Add user to DB
function db_user_add($username, $acl, $user_id = 0)
{
    if ($user_id == 0)
        $user_id = ctime();

    $acl = intval($acl);

    // Already exists
    if (db_user_by_name($username, TRUE))
        return NULL;

    // add to index
    $a = fopen(SERVDIR.'/cdata/users.txt', 'a+');
    fwrite($a, base_convert($user_id, 10, 36).':'.$acl."\n");
    fclose($a);

    // add to database
    db_user_update($username, "id=$user_id", "name=$username", "acl=$acl");

    return $user_id;
}

// Since: 2.0: Delete user from database
function db_user_delete($username)
{
    $fn = '/cdata/users/'.substr(md5($username), 0, 2).'.php';
    $cu = cn_touch_get($fn, TRUE);

    if (isset($cu['name'][$username]))
    {
        $id   = $cu['name'][$username]['id'];
        $id36 = base_convert($id, 10, 36);

        // delete user
        unset($cu['name'][$username]);

        // delete reference
        unset($cu['id'][$id]);

        // save new database
        cn_fsave($fn, $cu);

        // delete user index
        $fn = cn_touch('/cdata/users.txt');
        $bk = db_make_bk($fn);

        $r = fopen($fn, 'r');
        $w = fopen($bk, 'w+');
        while ($v = fgets($r))
        {
            if (!$v) break;
            list($eid) = explode(':', $v, 2);
            if ($eid !== $id36) fwrite($w, $v);
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
        return NULL;

    // -------
    $fn = '/cdata/users/'.substr(md5($username), 0, 2).'.php';
    $cu = cn_touch_get($fn, TRUE);

    foreach ($args as $v)
    {
        list($a, $b) = explode('=', $v, 2);
        $cp[$a] = $b;
    }

    // Create main block
    if (!isset($cu['name'])) $cu['name'] = array();
    if (!isset($cu['name'][$username])) $cu['name'][$username] = array();

    // Update fields
    foreach ($cp as $i => $v) $cu['name'][$username][$i] = $v;

    // Save DB
    cn_fsave($fn, $cu);

    // -------
    // Make references
    if (isset($cp['id'])) // ID -> USERNAME
    {
        $cu = cn_touch_get($lc = '/cdata/users/'.substr(md5($cp['id']), 0, 2).'.php', TRUE);

        if (!isset($cu['id'])) $cu['id'] = array();
        $cu['id'][$cp['id']] = $username;
        cn_fsave($lc, $cu);
    }

    if (isset($cp['email'])) // EMAIL -> USERNAME
    {
        $cu = cn_touch_get($lc = '/cdata/users/'.substr(md5($cp['email']), 0, 2).'.php', TRUE);

        if (!isset($cu['email'])) $cu['email'] = array();
        $cu['email'][$cp['email']] = $username;
        cn_fsave($lc, $cu);
    }

    return TRUE;
}

// Since 2.0: Fetch index
function db_user_list()
{
    $fn = cn_touch('/cdata/users.txt');

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
        return FALSE;

    return cn_fsave('/cdata/news/'.$location.'.php', $es);
}

// Since 2.0: Load block database entries
// @permanent alias of cn_touch_get for Flat DB structure
function db_news_load($location)
{
    if (substr($location, 0, 4) == '1970')
        return array();

    return cn_touch_get('/cdata/news/'.$location.'.php');
}

// Since 2.0: Helper for db_index_(load|save)
function db_index_file_detect($source = '')
{
    $fn = SERVDIR . '/cdata/news';

    // Aliases for active news
    if ($source == 'iactive' || $source == 'postpone'  || $source == 'A2')
        $source = '';

    if ($source == '') $fn .= '/iactive.txt';
    elseif ($source == 'draft') $fn .= '/idraft.txt';
    elseif (substr($source, 0, 7) == 'archive') $fn .= '/'.$source.'.txt';
    elseif (substr($source, 0, 4) == 'meta')
    {
        $source = substr($source, 5);
        if (!$source) $source = 'iactive';
        $fn .= '/meta-'.$source.'.txt';
    }
    elseif(substr($source,0,5)=='group')
    {
        $fn.='/'.$source.'.txt';
    }
    
    if (!file_exists($fn)) fclose(fopen($fn, "w+"));

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
    if (!$e) return NULL;

    list($id, $c, $ui, $co) = explode(':', $e);

    $id = base_convert($id, 36, 10);
    $ui = base_convert($ui, 36, 10);

    return array('id' => $id, 'c' => $c, 'uid' => $ui, 'co' => $co);
}

// Since 2.0: Close bind file
function db_index_unbind($bind)
{
    if (is_resource($bind['rs']))
        fclose($bind['rs']);
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
    $ls = unserialize(join('', file($fn)));

    // Decode userids
    $uids = array();

    if ($load_users)
    {
        foreach ($ls['uids'] as $id => $ct)
        {
            $id = base_convert($id, 36, 10);
            $user = db_user_by($id);

            if ($user['name'])
                $uids[ $user['name'] ] = $ct;
        }

        $ls['uids'] = $uids;
    }

    return $ls;
}

// Since 2.0: Update external (e.g. tags)
function db_update_aux($entry, $type = 'add', $storent = array())
{
    // --- do update tags
    $tags = cn_touch_get('/cdata/news/tagcloud.php');

    $tg = spsep($entry['tg']);
    foreach ($tg as $i => $v) $tg[$i] = trim($v);

    // Update tags require diffs
    if ($type == 'update')
    {
        $st = spsep($storent['tg']);
        foreach ($st as $i => $v) $st[$i] = trim($v);

        $tdel = array_diff($st, $tg);
        $tadd = array_diff($tg, $st);

        // add & delete
        foreach ($tadd as $tag) $tags[$tag]++;
        foreach ($tdel as $tag)
        {
            $tags[$tag]--;
            if ($tags[$tag] <= 0) unset($tags[$tag]);
        }
    }
    else
    {
        foreach ($tg as $tag)
        {
            // Add news
            if ($type == 'add')
            {
                $tags[$tag]++;
            }
            // Delete news
            elseif ($type == 'delete')
            {
                $tags[$tag]--;
                if ($tags[$tag] <= 0) unset($tags[$tag]);
            }
        }
    }

    cn_fsave('/cdata/news/tagcloud.php', $tags);
}

// Since 2.0: Update overall data about index
function db_index_update_overall($source = '')
{
    $ct = ctime();
    $period = 30*24*3600;

    $fn = db_index_file_detect($source);
    $ls = file($fn);

    $i = array
    (
        'uids' => array(),
        'locs' => array(),
        'coms' => 0,
        'min_id' => $ct,
    );

    foreach ($ls as $vi)
    {
        list($id,,$ui,$co) = explode(':', $vi);

        $id  = base_convert($id, 36, 10);
        $loc = db_get_nloc($id);

        $i['uids'][$ui]++;
        $i['coms'] += $co;
        $i['locs'][$loc]++;

        if ($i['min_id'] > $id)
            $i['min_id'] = $id;
    }

    // Active news is many, auto archive it (and user is hasn't draft rights)
    if ($source == '' && $i['min_id'] < $ct - $period && getoption('auto_archive') && !test('Bd'))
    {
        $cc = db_make_archive(0, ctime());
        cn_throw_message('Autoarchive performed');

        if (getoption('notify_archive'))
            cn_send_mail(getoption('notify_email'), i18n("Auto archive news"), i18n("Active news has been archived (%1 articles)", $cc));

        // Refresh overall index
        return db_index_update_overall();
    }

    // save meta-data
    $meta = db_index_file_detect("meta-$source");
    $w = fopen($meta, "w+"); fwrite($w, serialize($i)); fclose($w);

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

function db_count_news($by='date')
{
    $idx=  db_index_load_cnt('key:val+', $by);
    $val_key=  db_pvt_get_valueble_key($idx['format']);
    $sum=0;
    foreach ($idx['idx'] as $item)
    {
        $sum+=intval($item[$val_key]);
    }
    return $sum;
}

/*
 * Since 2.0: Load index by format
 * 
 * @param string $source index name
 * @param string $format keys position in index
 * 
 * @return array result index with elements maped by format
 */
function db_index_load_cnt($format='key:subj+', $source='date')
{
    $format_keys=  explode(':', 'id:'.$format);
    $res_idx=array('format'=>$format_keys,'type'=>$source, 'idx'=>array());
    
    $reader=fopen(db_index_file_detect('group_index_'.$source.'_cnt'),'r');
    while($item= trim(fgets($reader)))
    {
        if(trim($item)=='') continue;
        $elems=  explode(':', $item);
        $res_idx['idx'][$elems[0]]=  array_combine($format_keys, $elems);
    }
    fclose($reader);
    return $res_idx;
}

/*
 * Since 2.0: Add item to index
 * 
 * @param array $idx index to add
 * @param array $item added element
 * @param false|true $overwrite overwrite element if it exist on index
 * 
 * @return false|true if fail return false
 */
function db_index_add_cnt($idx,$item,$overwrite=FALSE)
{
    $add_id=  db_pvt_get_id($item);
    $add_item=array('id'=>$add_id);
    foreach ($item as $key=>$val)
    {
        $add_item[$key]=$val;
    }
    $is_exist=array_key_exists($add_id, $idx['idx']);
    if($is_exist&&!$overwrite)
    {
        return FALSE;
    }
    $idx['idx'][$add_id]= $add_item;
    return $idx;
}

/*
 * Since 2.0: Close and save index to file
 * 
 * @param array $idx saved index
 * 
 * @return void
 */
function db_index_close_cnt($idx)
{
    $fn=  db_index_file_detect('group_index_'.$idx['type'].'_cnt');
    $bkp= db_make_bk($fn);
    $write_idx='';
    foreach ($idx['idx'] as $val)
    {
        $write_idx.=join(':',$val)."\n";
    }
    $w=  fopen($bkp, 'w+');
    fwrite($w, $write_idx);
    fclose($w);
    rename($bkp, $fn);
}

/*
 * Since 2.0: Utitlt, generate id for index by element content
 * 
 * @param array $item element od index
 * 
 * @return void
 */
function db_pvt_get_id($item)
{    
    return base_convert(array_shift($item), 36, 10);
}

/*
 * Since 2.0: Execute operation with valuable elemets of index
 * 
 * @param array $idx index
 * @param array $operand elemet of index
 * @param true|false $add_new regulate will be add new element to index if it not exist
 * 
 * @return array result index
 */
function db_index_operation($idx,$operand,$add_new=TRUE)
{
    $op_id=  db_pvt_get_id($operand);
    $key=  db_pvt_get_valueble_key($idx['format']);
    if(array_key_exists($op_id, $idx['idx']))
    {              
        $idx['idx'][$op_id][$key]=  intval($idx['idx'][$op_id][$key])+$operand[$key];
        if($idx['idx'][$op_id][$key]<0){$idx['idx'][$op_id][$key]=0;}
    }
    elseif($add_new&&$operand[$key]>=0)
    {
        $idx=db_index_add_cnt($idx, $operand,TRUE);
    }
    return $idx;
}

/*
 * Since 2.0: Serch in format string valueble key marked as +
 * 
 * @param array $format_keys splited format string
 * 
 * @return string key
 */
function db_pvt_get_valueble_key($format_keys)
{
    $res_key='';
    foreach ($format_keys as $item)
    {
        if(!strpos($item,'+')) continue;
        $res_key=$item;
        break;
    }
    return $res_key;
}

// ------------------------------------------------------------------------------------------------ ARCHIVES -----------

function db_make_archive($id_from, $id_to)
{
    $archive_id = ctime();

    $cc = 0;
    $fc = db_index_file_detect();
    $fn = db_index_file_detect('archive-'.$archive_id);
    $al = cn_touch('/cdata/news/archive.txt');
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
            if ($count) fwrite($ws, "$id|$min|$max|$count\n");
            // if count = 0, delete archive
            else
            {
                unlink(db_index_file_detect("meta-archive-$id"));
                unlink(db_index_file_detect("archive-$id"));
            }
        }
        else
            fwrite($ws, $ln);
    }

    if (!$updated)
        fwrite($ws, "$archive_id|$min|$max|$count\n");

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
    $al = cn_touch('/cdata/news/archive.txt');
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
        $bt[0] = array_reverse($bt[0]);
        if ($i >= $cluster) $bc = array_merge($bc, $bt[0]);
        $bt = bt_get_id('lc:'.$bt[1], 'comm');
    }
       
    foreach ($bc as $citem)
    {
        list($_id, $_cid, $_ref) = explode(':', $citem, 3);
        $blk = db_news_load(db_get_nloc($_id));    
        $cb[] = array(intval($_id), intval($_cid), $blk[$_id]['co'][$_cid], $_ref);
    }

    return array($cb, $cn);
}
