<?php if (!defined('EXEC_TIME')) die('Access restricted');

global $PHP_SELF;

// Scan external query
// $qhl - query search highlight
list($id, $template, $qhl) = GET('id, template, qhl');

// Get alias of ID
$id = cn_id_alias($id);

if (!$template) $template = 'Default';
if ($id == 0) die("@SYSLOG: INTERNAL ERROR[2]");

$ent = db_news_load(db_get_nloc($id));
if (!isset($ent[$id]))
{
    echo '<div style="text-align: center;">'.i18n('Cannot find an article with id').': <strong>'. intval($id).'</strong></div>';
    return FALSE;
}
else
{
    $entry = $ent[$id];
    $text  = entry_make($entry, 'full', $template);
    $text = cn_snippet_search_hl($text, $qhl);

    echo $text;
}

return TRUE;