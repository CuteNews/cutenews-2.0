<?php if (!defined('EXEC_TIME')) die('Access restricted');

add_hook('index/invoke_module', '*help_invoke');

function help_invoke()
{
    list($action) = GET('action');

    if ($action == 'about') help_invoke_about();
    elseif ($action == 'code') echo exec_tpl('help/cutecode');
    else help_invoke_main();
}

// Since 2.0
function help_invoke_about()
{
    global $reg_site_key;

    // Try license key
    if (file_exists(SERVDIR."/cdata/reg.php")) include(SERVDIR."/cdata/reg.php");

    $registered = file_exists(SERVDIR.'/cdata/reg.php');

    // ---
    cn_assign('registered, reg_site_key', $registered, $reg_site_key);
    echoheader('', 'Help/About Cutenews'); echo exec_tpl('help/about'); echofooter();
}

// Since 2.0
function help_invoke_main()
{
    $section = REQ('section');
    $path = SKIN.'/help/sections/';
    $scan = scan_dir($path);

    $result = array();
    foreach ($scan as $id)
    {
        $id = str_replace('.tpl', '', $id);
        if (!$section || $section && $section == $id)
            $result[$id] = proc_tpl("help/sections/$id");
    }

    cn_assign('help_sections', $result);

    if ($section)
    {
        echo exec_tpl('window', "style=help/style.css", "title=HELP - $section", 'content='.exec_tpl('help/main'));
    }
    else
    {
        echoheader('-@help/style.css', 'Help section');
        echo exec_tpl('help/main');
        echofooter();
    }

}