<?php

    include('core/init.php');

    // plugin tells us: he is fork, stop
    if ( hook('fork_captcha', false) ) return;

    require_once SERVDIR.'/core/captcha/captcha.php';

    $captcha = new SimpleCaptcha();
    $captcha->imageFormat   = 'png';
    $captcha->session_var   = 'CSW';
    $captcha->scale         = 2;
    $captcha->blur          = true;
    $captcha->resourcesPath = SERVDIR.'/core/captcha/resources';

    // Image generation
    $captcha->CreateImage();
    die();