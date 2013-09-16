<?php // Show code snippets

require_once (dirname(__FILE__).'/core/init.php');

// Get extrn variables
list($snippet) = GET('snippet', 'GPG');

// Default values
if (!$snippet) $snippet = 'sandbox';

$_snipdb = getoption('#snippets');
$html = isset($_snipdb[$snippet]) ? $_snipdb[$snippet] : '';

$_assign = array();

// Catch all brackets
if (preg_match_all('/\[(.*?)\]/is', $html, $c, PREG_SET_ORDER))
{
    foreach ($c as $vs)
    {
        $echo = '';

        $options = array();
        list($mod, $opt) = explode('|', $vs[1], 2);

        $opts = spsep($opt);
        foreach ($opts as $opt)
        {
            list($id, $value) = explode('=', $opt, 2);
            $options[$id] = is_null($value) ? TRUE : $value;
        }

        // MODULES
        if ($mod == 'news')
        {
            foreach ($options as $id => $var) $$id = $var;
            ob_start(); include dirname(__FILE__).'/show_news.php'; $echo = ob_get_clean();
        }

        // do replace
        $html = str_replace($vs[0], $echo, $html);
    }
}

echo $html;
unset($_snipdb, $_assign, $options, $snippet);