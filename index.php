<?php

/***************************************************************************
 * @Developer CuteNews CutePHP.com
 * @Copyrights Copyright (с)  2012-2013 Cutenews Team
 * @Type Bootstrap
 ***************************************************************************/

define('AREA', "ADMIN");
include dirname(__FILE__).'/core/init.php';

cn_sendheaders();
cn_load_skin();
cn_deprecated_check();
cn_register_form();

if (cn_login())
    hook('index/invoke_module', array($_module) );
else
    cn_login_form();
