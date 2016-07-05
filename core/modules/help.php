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

    $downloader = new Downloader();
    $errorget   = false;
    $version    = $downloader->get_remote_file("http://cutephp.com/latest_version.php?licenseid=$reg_site_key&version_id=".VERSION_ID);

    if ($version) {

        $version_holder = $version['content'];

        // Check responce format
        if (preg_match('/^[0-9]+$/', $version_holder)) {

            $version = (int)$version_holder;
            if ($version == VERSION_ID) {
                $version_holder = '<span style="color:#080;">Your Cutenews copy is up to date</span>';
            }
            else {
                $version_holder  = "<script>alert('There is a newer version with important security fixes, please update cuteNews!');</script>";
                $version_holder .= "<br/><span style='color:red;font-size:18px;font-weight: bold;'>Upgrade Needed!!!</span>";
            }

        } else {
            $errorget = true;
        }
    } else {
        $errorget = true;
    }

    if ($errorget) {
        $version_holder = "<span style='color:red;font-size:18px;'>---";
    }

    // ---
    cn_assign('registered, reg_site_key, version_holder', $registered, $reg_site_key, $version_holder);
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