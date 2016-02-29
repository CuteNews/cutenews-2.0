<?php

require_once (dirname(__FILE__).'/core/init.php');

list($id, $template, $popup) = GET('id, template, popup');

$id = cn_id_alias($id);

if (!$template) $template = 'default';
$ent = db_news_load(db_get_nloc($id));

if (isset($ent[$id]))
{
    echo entry_make($ent[$id], 'print', $template);
}
else
{
    echo i18n("ID not found for active news");
}
