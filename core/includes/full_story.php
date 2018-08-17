<?php if (!defined('EXEC_TIME')) die('Access restricted');

global $PHP_SELF;

// Scan external query
// $qhl - query search highlight
list($id, $template, $qhl) = GET('id, template, qhl', 'GPG');

// Get alias of ID
$id = cn_id_alias($id);

if (!$template) $template = 'Default';
if ($id == 0) die("@SYSLOG: INTERNAL ERROR[2]");

$nloc = db_get_nloc($id);
$ent = db_news_load( $nloc );

if (!isset($ent[$id]))
{
    echo '<div style="text-align: center;">'.i18n('Cannot find an article with id').': <strong>'. intval($id).'</strong></div>';
    return FALSE;
}
else
{
    $entry = $ent[ $id ];
    $text  = entry_make($entry, 'full', $template);    
    $text = cn_snippet_search_hl($text, $qhl);

    // View statistics
    $ent[$id]['vcnt'] = isset($entry['vcnt']) ? $entry['vcnt'] + 1 : 1;
    db_save_news($ent, db_get_nloc($id));

    echo $text;
}

return TRUE;