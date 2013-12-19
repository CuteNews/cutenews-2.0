<?php // Show code snippets

require_once (dirname(__FILE__).'/core/init.php');

// Get extrn variables
list($snippet) = GET('snippet', 'GPG');

// Default values
if (!$snippet) $snippet = 'sandbox';

$_snipdb = getoption('#snippets');
$_html = isset($_snipdb[$snippet]) ? $_snipdb[$snippet] : '';

$_assign = array();

// Catch all brackets
if (preg_match_all('/\[(.*?)\]/is', $_html, $_c, PREG_SET_ORDER))
{
    foreach ($_c as $_vs)
    {
        $_echo = '';

        $_options = array();
        list($_mod, $_opt) = explode('|', $_vs[1], 2);

        $_opts = spsep($_opt);
        foreach ($_opts as $_opt)
        {
            list($_id, $_value) = explode('=', $_opt, 2);
            $_options[$_id] = is_null($_value) ? TRUE : $_value;
        }

        // MODULES
        if ($_mod == 'news')
        {
            $_gGET = $_GET; $_GET = array();
            foreach ($_options as $_id => $_var) $$_id = $_var;
            ob_start(); include dirname(__FILE__).'/show_news.php'; $_echo = ob_get_clean();
            $_GET = $_gGET;
        }

        // do replace
        $_html = str_replace($_vs[0], $_echo, $_html);
    }
}

echo $_html;
unset($_gGET, $_snipdb, $_assign, $_c, $_vs, $_echo, $_options, $_id, $_mod, $_opt, $_value, $_opts, $_var, $snippet);
