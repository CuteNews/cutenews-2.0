<?php // API: External function for CN

require_once dirname(__FILE__).'/core/init.php';

// Input: $id - timestamp, $data - entry data
function cn_api_add_news($id, $data)
{
    $nloc = db_get_nloc($id);
    $db   = db_news_load($nloc);
    $db[$id] = $data;

    // add data
    db_save_news($db, $nloc);

    $user = db_user_by_name($data['u']);

    // add index data
    $w = fopen(db_index_file_detect(), 'a+');
    fwrite($w, base_convert($id, 10, 36).':'.$data['c'].':'.base_convert($user['id'], 10, 36).':'.count($data['co']).'::'."\n");
    fclose($w);
}

// Since 2.0: Get news entry
function cn_api_get_entry($id = NULL)
{
    if (is_null($id))
        $id = REQ('id');

    $id = cn_id_alias($id);
    $db = db_news_load(db_get_nloc($id));

    // Other meta-information
    if (isset($db[$id]))
    {
        $_cot = array();
        $_cat = cn_get_categories(true);
        $_cts = spsep($db[$id]['c']);

        foreach ($_cts as $cid) $_cot[$cid] = $_cat[$cid]['name'];

        $db[$id][':cot'] = $_cot;
        return $db[$id];
    }
    else
        return array();
}

// Since 2.0: Get tagcloud
function cn_api_tagcloud($flat = true)
{
    $tags = cn_touch_get('/cdata/news/tagcloud.php');

    if ($flat)
    {
        foreach ($tags as $id => $v)
        {
            $url = cn_rewrite('tag', $id);
            if (!$url) $url = cn_url_modify('tag='.$id);

            $tags[$id] = array('weight' => $v, 'url' => $url);
        }
        return $tags;
    }

    return null;
}