<?php

require_once (dirname(__FILE__).'/core/init.php');

list($id, $template, $popup) = GET('id, template, popup');

$id = cn_id_alias($id);

if (!$template) $template = 'default';
$ent = db_news_load(db_get_nloc($id));

if (isset($ent[$id]))
{
        echo "<html><head><style>        
                body { margin: 0; padding: 0; }
                body, td
                {
                    font-family: verdana, arial, sans-serif;
                    color: red;
                    font-size: 12px;
                    font-weight: normal;
                    line-height: 1.3em;
                }
                input { border-radius: 3px; }
                input.text { background: #ffffff; border: 1px solid gray; }
                input.submit { background: #f0f0f0; border: 1px groove #808080; }
                input.submit:hover { background: #ffffff; cursor: pointer; }
                .cn_comm_textarea { width: 450px; height: 150px; }
            </style></head><body>";
        if ($popup == 'comment') $subaction = 'only_comments';
        include SERVDIR . '/show_news.php';
        echo "</body></html>";
}
else
{
    echo i18n("ID not found for active news");
}
