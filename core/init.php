<?php

// check PHP version
if (substr(PHP_VERSION, 0, 5) < '4.1.0')
    die('PHP Version is '.PHP_VERSION.', need great than PHP &gt;= 4.1.0 to start cutenews');

// definitions
error_reporting(E_ALL ^ E_NOTICE);

define('EXEC_TIME',     microtime(true));
define('VERSION',       '2.0');
define('VERSION_ID',    200);
define('VERSION_NAME',  'CuteNews v.' . VERSION);
define('SERVDIR',       dirname(dirname(__FILE__).'.html'));
define('MODULE_DIR',    SERVDIR . '/core/modules');
define('SKIN',          SERVDIR . '/skins/base');
define('CN_DEBUG',      FALSE);

// include necessary libs
require_once SERVDIR . '/core/core.php';
require_once SERVDIR . '/core/security.php';
require_once SERVDIR . '/core/news.php';
require_once SERVDIR . '/core/captcha/captcha.php';

// magic quotes = ON, filtering it
if (ini_get('magic_quotes_gpc')) cn_filter_magic_quotes();

// catch errors
set_error_handler("user_error_handler");

// create cutenews caches
$_CN_SESS_CACHE     = array();
$_CN_cache_block_id = array();
$_CN_cache_block_dt = array();

// Define ALL privelegies and behaviors
$_CN_access = array
(
    // configs
    'C' => 'Cd,Cvm,Csc,Cp,Cc,Ct,Ciw,Cmm,Cum,Cg,Cb,Ca,Cbi,Caf,Crw,Csl,Cwp,Cmt,Cpc,Can,Cvn,Ccv,Cen,Clc,Csr,Com',
    // news
    'N' => 'Nes,Neg,Nea,Nvs,Nvg,Nva,Nua,Nud,Ncd',
    // comments
    'M' => 'Mes,Meg,Mea,Mds,Mdg,Mda,Mac',
    // behavior
    'B' => 'Bd,Bs',
);

// v2.0 init sections
cn_config_load();
cn_lang_init();
cn_db_init();
cn_rewrite_load();
cn_parse_url();
cn_detect_user_ip();
cn_load_session();
cn_load_plugins();
cn_online_counter();
db_installed_check();

// load modules
include SERVDIR.'/core/modules/init.php';

hook('init/finally');
