<?php

if (!defined('EXEC_TIME')) die('Access restricted');

// Loading filters
require_once SERVDIR . '/core/modules/hooks/common.php';

// Require module -----
$_module = REQ('mod', 'GPG');

// Loading all modules (internal + external)
$_init_modules = hook('modules/init_modules', array
(
    'main'      => array('path' => 'dashboard', 'acl' => 'Cd'),
    'addnews'   => array('path' => 'add_news',  'acl' => 'Can'),
    'editnews'  => array('path' => 'edit_news', 'acl' => 'Cvn'),
    'media'     => array('path' => 'media',     'acl' => 'Cmm'),
    'maint'     => array('path' => 'maint',     'acl' => 'Cmt'),
    'help'      => array('path' => 'help',      'acl' => ''),
    'logout'    => array('path' => 'logout',    'acl' => ''),
));

// Required module not exist
if (!isset($_init_modules[$_module]))
{
    // external module chk
    $_module = hook('modules/init', 'main', $_module);
}

// Check restrictions, if user is authorized
if (member_get() && defined('AREA') && AREA == 'ADMIN')
{
    if (test($_init_modules[$_module]['acl']))
    {
        // Request module
        $_mod_cfg = $_init_modules[$_module];
        include MODULE_DIR . '/'. $_mod_cfg['path'] . '.php';
    }
    else
    {
        msg_info('Section ['.cn_htmlspecialchars($_module).'] disabled for you', PHP_SELF);
    }
}
