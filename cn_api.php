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
    db_index_add($id, $data['c'], $user['id'], $data['is_draft']);
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
