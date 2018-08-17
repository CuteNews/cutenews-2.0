<?php if (!defined('EXEC_TIME')) { die('Access restricted'); }

// External implemented modules ----------------------------------------------------------------------------------------

// @url http://www.php.net/manual/de/function.utf8-decode.php#100478
// Since 1.5.0: UTF-8 to HTML-Entities
function UTF8ToEntities($string)
{      
    if (is_array($string))
    {
        return $string;
    }
    
    // @Note May be deprecated in next versions
    $HTML_SPECIAL_CHARS_UTF8 = array
    (
        'c2a1' => '&iexcl;',
        'c2a2' => '&cent;',
        'c2a3' => '&pound;',
        'c2a4' => '&curren;',
        'c2a5' => '&yen;',
        'c2a6' => '&brvbar;',
        'c2a7' => '&sect;',
        'c2a8' => '&uml;',
        'c2a9' => '&copy;',
        'c2aa' => '&ordf;',
        'c2ab' => '&laquo;',
        'c2bb' => '&raquo;',
        'c2ac' => '&not;',
        'c2ae' => '&reg;',
        'c2af' => '&macr;',
        'c2b0' => '&deg;',
        'c2ba' => '&ordm;',
        'c2b1' => '&plusmn;',
        'c2b9' => '&sup1;',
        'c2b2' => '&sup2;',
        'c2b3' => '&sup3;',
        'c2b4' => '&acute;',
        'c2b7' => '&middot;',
        'c2b8' => '&cedil;',
        'c2bc' => '&frac14;',
        'c2bd' => '&frac12;',
        'c2be' => '&frac34;',
        'c2bf' => '&iquest;',
        'c380' => '&Agrave;',
        'c381' => '&Aacute;',
        'c382' => '&Acirc;',
        'c383' => '&Atilde;',
        'c384' => '&Auml;',
        'c385' => '&Aring;',
        'c386' => '&AElig;',
        'c387' => '&Ccedil;',
        'c388' => '&Egrave;',
        'c389' => '&Eacute;',
        'c38a' => '&Ecirc;',
        'c38b' => '&Euml;',
        'c38c' => '&Igrave;',
        'c38d' => '&Iacute;',
        'c38e' => '&Icirc;',
        'c38f' => '&Iuml;',
        'c390' => '&ETH;',
        'c391' => '&Ntilde;',
        'c392' => '&Ograve;',
        'c393' => '&Oacute;',
        'c394' => '&Ocirc;',
        'c395' => '&Otilde;',
        'c396' => '&Ouml;',
        'c397' => '&times;',
        'c398' => '&Oslash;',
        'c399' => '&Ugrave;',
        'c39a' => '&Uacute;',
        'c39b' => '&Ucirc;',
        'c39c' => '&Uuml;',
        'c39d' => '&Yacute;',
        'c39e' => '&THORN;',
        'c39f' => '&szlig;',
        'c3a0' => '&agrave;',
        'c3a1' => '&aacute;',
        'c3a2' => '&acirc;',
        'c3a3' => '&atilde;',
        'c3a4' => '&auml;',
        'c3a5' => '&aring;',
        'c3a6' => '&aelig;',
        'c3a7' => '&ccedil;',
        'c3a8' => '&egrave;',
        'c3a9' => '&eacute;',
        'c3aa' => '&ecirc;',
        'c3ab' => '&euml;',
        'c3ac' => '&igrave;',
        'c3ad' => '&iacute;',
        'c3ae' => '&icirc;',
        'c3af' => '&iuml;',
        'c3b0' => '&eth;',
        'c3b1' => '&ntilde;',
        'c3b2' => '&ograve;',
        'c3b3' => '&oacute;',
        'c3b4' => '&ocirc;',
        'c3b5' => '&otilde;',
        'c3b6' => '&ouml;',
        'c3b7' => '&divide;',
        'c3b8' => '&oslash;',
        'c3b9' => '&ugrave;',
        'c3ba' => '&uacute;',
        'c3bb' => '&ucirc;',
        'c3bc' => '&uuml;',
        'c3bd' => '&yacute;',
        'c3be' => '&thorn;',
        'c3bf' => '&yuml;',
        'c592' => '&OElig;',
        'c593' => '&oelig;',
        'c5a0' => '&Scaron;',
        'c5a1' => '&scaron;',
        'c5b8' => '&Yuml;',
        'cb86' => '&circ;',
        'cb9c' => '&tilde;',
        'c692' => '&fnof;',
        'ce91' => '&Alpha;',
        'ce92' => '&Beta;',
        'ce93' => '&Gamma;',
        'ce94' => '&Delta;',
        'ce95' => '&Epsilon;',
        'ce96' => '&Zeta;',
        'ce97' => '&Eta;',
        'ce98' => '&Theta;',
        'ce99' => '&Iota;',
        'ce9a' => '&Kappa;',
        'ce9b' => '&Lambda;',
        'ce9c' => '&Mu;',
        'ce9d' => '&Nu;',
        'ce9e' => '&Xi;',
        'ce9f' => '&Omicron;',
        'cea0' => '&Pi;',
        'cea1' => '&Rho;',
        'cea3' => '&Sigma;',
        'cea4' => '&Tau;',
        'cea5' => '&Upsilon;',
        'cea6' => '&Phi;',
        'cea7' => '&Chi;',
        'cea8' => '&Psi;',
        'cea9' => '&Omega;',
        'ceb1' => '&alpha;',
        'ceb2' => '&beta;',
        'ceb3' => '&gamma;',
        'ceb4' => '&delta;',
        'ceb5' => '&epsilon;',
        'ceb6' => '&zeta;',
        'ceb7' => '&eta;',
        'ceb8' => '&theta;',
        'ceb9' => '&iota;',
        'ceba' => '&kappa;',
        'cebb' => '&lambda;',
        'cebc' => '&mu;',
        'cebd' => '&nu;',
        'cebe' => '&xi;',
        'cebf' => '&omicron;',
        'cf80' => '&pi;',
        'cf81' => '&rho;',
        'cf82' => '&sigmaf;',
        'cf83' => '&sigma;',
        'cf84' => '&tau;',
        'cf85' => '&upsilon;',
        'cf86' => '&phi;',
        'cf87' => '&chi;',
        'cf88' => '&psi;',
        'cf89' => '&omega;',
        'cf91' => '&thetasym;',
        'cf92' => '&upsih;',
        'cf96' => '&piv;',
        'e2809d' => '&rdquo;',
        'e2809c' => '&ldquo;',
        'e284a2' => '&trade;',
        'e28099' => '&rsquo;',
        'e28098' => '&lsquo;',
        'e280b0' => '&permil;',
        'e280a6' => '&hellip;',
        'e282ac' => '&euro;',
        'e28093' => '&ndash;',
        'e28094' => '&mdash;',
        'e280a0' => '&dagger;',
        'e280a1' => '&Dagger;',
        'e280b9' => '&lsaquo;',
        'e280ba' => '&rsaquo;',
        'e280b2' => '&prime;',
        'e280b3' => '&Prime;',
        'e280be' => '&oline;',
        'e28498' => '&weierp;',
        'e28491' => '&image;',
        'e2849c' => '&real;',
        'e284b5' => '&alefsym;',
        'e28690' => '&larr;',
        'e28691' => '&uarr;',
        'e28692' => '&rarr;',
        'e28693' => '&darr;',
        'e28694' => '&harr;',
        'e286b5' => '&crarr;',
        'e28790' => '&lArr;',
        'e28791' => '&uArr;',
        'e28792' => '&rArr;',
        'e28793' => '&dArr;',
        'e28794' => '&hArr;',
        'e28880' => '&forall;',
        'e28882' => '&part;',
        'e28883' => '&exist;',
        'e28885' => '&empty;',
        'e28887' => '&nabla;',
        'e28888' => '&isin;',
        'e28889' => '&notin;',
        'e2888b' => '&ni;',
        'e2888f' => '&prod;',
        'e28891' => '&sum;',
        'e28892' => '&minus;',
        'e28897' => '&lowast;',
        'e2889a' => '&radic;',
        'e2889d' => '&prop;',
        'e2889e' => '&infin;',
        'e288a0' => '&ang;',
        'e288a7' => '&and;',
        'e288a8' => '&or;',
        'e288a9' => '&cap;',
        'e288aa' => '&cup;',
        'e288ab' => '&int;',
        'e288b4' => '&there4;',
        'e288bc' => '&sim;',
        'e28985' => '&cong;',
        'e28988' => '&asymp;',
        'e289a0' => '&ne;',
        'e289a1' => '&equiv;',
        'e289a4' => '&le;',
        'e289a5' => '&ge;',
        'e28a82' => '&sub;',
        'e28a83' => '&sup;',
        'e28a84' => '&nsub;',
        'e28a86' => '&sube;',
        'e28a87' => '&supe;',
        'e28a95' => '&oplus;',
        'e28a97' => '&otimes;',
        'e28aa5' => '&perp;',
        'e28b85' => '&sdot;',
        'e28c88' => '&lceil;',
        'e28c89' => '&rceil;',
        'e28c8a' => '&lfloor;',
        'e28c8b' => '&rfloor;',
        'e29fa8' => '&lang;',
        'e29fa9' => '&rang;',
        'e2978a' => '&loz;',
        'e299a0' => '&spades;',
        'e299a3' => '&clubs;',
        'e299a5' => '&hearts;',
        'e299a6' => '&diams;',
    );

    // Decode UTF-8 code-table
    $HTML_SPECIAL_CHARS = array();
    foreach ($HTML_SPECIAL_CHARS_UTF8 as $hex => $html)
    {
        $key = '';
        if (strlen($hex) == 4)      
        {
            $key = pack("CC",  hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)));
        }
        else if (strlen($hex) == 6)
        {
            $key = pack("CCC", hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2)));
        }

        if ($key) 
        {
            $HTML_SPECIAL_CHARS[$key] = $html;
        }
    }

    // Common conversion
    $string = str_replace(array_keys($HTML_SPECIAL_CHARS), array_values($HTML_SPECIAL_CHARS), $string);

    /* note: apply htmlspecialchars if desired /before/ applying this function
    /* Only do the slow convert if there are 8-bit characters */
    /* avoid using 0xA0 (\240) in ereg ranges. RH73 does not like that */
    if (!preg_match("~[\200-\237]~", $string) and ! preg_match("~[\241-\377]~", $string))
    {
        return $string;
    }

    // reject too-short sequences
    $string = preg_replace("/[\302-\375]([\001-\177])/", "&#65533;\\1", $string);
    $string = preg_replace("/[\340-\375].([\001-\177])/", "&#65533;\\1", $string);
    $string = preg_replace("/[\360-\375]..([\001-\177])/", "&#65533;\\1", $string);
    $string = preg_replace("/[\370-\375]...([\001-\177])/", "&#65533;\\1", $string);
    $string = preg_replace("/[\374-\375]....([\001-\177])/", "&#65533;\\1", $string);
    $string = preg_replace("/[\300-\301]./", "&#65533;", $string);
    $string = preg_replace("/\364[\220-\277]../", "&#65533;", $string);
    $string = preg_replace("/[\365-\367].../", "&#65533;", $string);
    $string = preg_replace("/[\370-\373]..../", "&#65533;", $string);
    $string = preg_replace("/[\374-\375]...../", "&#65533;", $string);
    $string = preg_replace("/[\376-\377]/", "&#65533;", $string);
    $string = preg_replace("/[\302-\364]{2,}/", "&#65533;", $string);

    // decode four byte unicode characters
    $string = preg_replace_callback(
        "/([\360-\364])([\200-\277])([\200-\277])([\200-\277])/",
        function ($matches)
        {
            return '&#'.((ord($matches[1])&7)<<18 | (ord($matches[2])&63)<<12 |(ord($matches[3])&63)<<6 | (ord($matches[4])&63)).';';
        },
        $string);    
    
    // decode three byte unicode characters        
    $string = preg_replace_callback(
        "/([\340-\357])([\200-\277])([\200-\277])/",
        function ($matches)
        {
            return '&#'.((ord($matches[1])&15)<<12 | (ord($matches[2])&63)<<6 | (ord($matches[3])&63)).';';
        },        
        $string);       
        
    // decode two byte unicode characters
    $string = preg_replace_callback(
        "/([\300-\337])([\200-\277])/",
        function ($matches)
        {
            return '&#'.((ord($matches[1])&31)<<6 | (ord($matches[2])&63)).';';
        },
        $string);    

    // reject leftover continuation bytes
    $string = preg_replace("/[\200-\277]/", "&#65533;", $string);

    return $string;
}

//Since 2.0.3 crossplatform path generator
function cn_path_construct()
{
    $args = array();
    $arg_list = func_get_args();

    foreach ($arg_list as $varg)
    {
        if ($varg !== '') { $args[] = $varg; }
    }

    return implode(DIRECTORY_SEPARATOR, $args) . DIRECTORY_SEPARATOR;
}

//Since 2.0.1: tranliterate for cyrilic page aliases
function cn_transliterate($input)
{ 
    $gost = array(  "Є"=>"YE","І"=>"I","Ѓ"=>"G","і"=>"i","№"=>"-","є"=>"ye","ѓ"=>"g", "А"=>"A","Б"=>"B",
                    "В"=>"V","Г"=>"G","Д"=>"D", "Е"=>"E","Ё"=>"YO","Ж"=>"ZH", "З"=>"Z","И"=>"I","Й"=>"J",
                    "К"=>"K","Л"=>"L", "М"=>"M","Н"=>"N","О"=>"O","П"=>"P","Р"=>"R", "С"=>"S","Т"=>"T",
                    "У"=>"U","Ф"=>"F","Х"=>"X", "Ц"=>"C","Ч"=>"CH","Ш"=>"SH","Щ"=>"SHH","Ъ"=>"'", 
                    "Ы"=>"Y","Ь"=>"","Э"=>"E","Ю"=>"YU","Я"=>"YA", "а"=>"a","б"=>"b","в"=>"v","г"=>"g",
                    "д"=>"d", "е"=>"e","ё"=>"yo","ж"=>"zh", "з"=>"z","и"=>"i","й"=>"j","к"=>"k","л"=>"l", 
                    "м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r", "с"=>"s","т"=>"t","у"=>"u","ф"=>"f",
                    "х"=>"x", "ц"=>"c","ч"=>"ch","ш"=>"sh","щ"=>"shh","ъ"=>"", "ы"=>"y","ь"=>"","э"=>"e",
                    "ю"=>"yu","я"=>"ya", "À"=>"A", "à"=>"a", "Á"=>"A", "á"=>"a", "Â"=>"A", "â"=>"a", 
                    "Ä"=>"A", "ä"=>"a", "Ã"=>"A", "ã"=>"a", "Å"=>"A", "å"=>"a", "Æ"=>"AE", "æ"=>"ae", 
                    "Ç"=>"C", "ç"=>"c", "Ð"=>"D", "È"=>"E", "è"=>"e", "É"=>"E", "é"=>"e", "Ê"=>"E", 
                    "ê"=>"e", "Ì"=>"I", "ì"=>"i", "Í"=>"I", "í"=>"i", "Î"=>"I", 
                    "î"=>"i", "Ï"=>"I", "ï"=>"i", "Ñ"=>"N", "ñ"=>"n", "Ò"=>"O", "ò"=>"o", "Ó"=>"O", 
                    "ó"=>"o", "Ô"=>"O", "ô"=>"o", "Ö"=>"O", "ö"=>"o", "Õ"=>"O", "õ"=>"o", "Ø"=>"O", 
                    "ø"=>"o", "Œ"=>"OE", "œ"=>"oe", "Š"=>"S", "š"=>"s", "Ù"=>"U", "ù"=>"u", "Û"=>"U", 
                    "û"=>"u", "Ú"=>"U", "ú"=>"u", "Ü"=>"U", "ü"=>"u", "Ý"=>"Y", "ý"=>"y", "Ÿ"=>"Y", 
                    "ÿ"=>"y", "Ž"=>"Z", "ž"=>"z", "Þ"=>"B", "þ"=>"b", "ß"=>"Ss", "£"=>"pf", "¥"=>"ien", 
                    "§"=>"pr", "ð"=>"eth", "ѓ"=>"r", " "=>"_","—"=>"_",","=>"_",
                    "!"=>"_","@"=>"_", "#"=>"-","$"=>"","%"=>"", "^"=>"","&"=>"","*"=>"", "("=>"",")"=>"",
                    "+"=>"","="=>"",";"=>"",":"=>"", "'"=>"","\""=>"","~"=>"","`"=>"","?"=>"","/"=>"", 
                    "\\"=>"","["=>"","]"=>"","{"=>"","}"=>"","|"=>"" ); 
    return strtr($input, $gost);    
}

// DEBUG functions -----------------------------------------------------------------------------------------------------

// error_dump.log always 0600 for deny of all
// User-defined error handler for catch errors

// Since 2.0: DEBUG function
function dbg($out) { echo "<PRE>"; var_dump($out); echo "</PRE>"; }
function mcs() { global $dbg_microtime; $dbg_microtime = microtime(1); }
function mce() { global $dbg_microtime; dbg("Microtime: ".(microtime(1) - $dbg_microtime)); $dbg_microtime =  microtime(1); }

// Since 1.5.0: Handle user errors
function user_error_handler($errno, $errmsg, $filename, $linenum, $vars)
{
    $errtypes = array
    (
        E_ERROR             => "Error",
        E_WARNING           => "Warning",
        E_PARSE             => "Parsing Error",
        E_NOTICE            => "Notice",
        E_CORE_ERROR        => "Core Error",
        E_CORE_WARNING      => "Core Warning",
        E_COMPILE_ERROR     => "Compile Error",
        E_COMPILE_WARNING   => "Compile Warning",
        E_USER_ERROR        => "User Error",
        E_USER_WARNING      => "User Warning",
        E_USER_NOTICE       => "User Notice",
        E_STRICT            => "Runtime Notice",
        E_DEPRECATED        => "Deprecated"
    );

    // Debug log not enabled, see in error.log
    if (!file_exists(SERVDIR.'/cdata/debug'))
    {
        return;
    }
    
    // E_NOTICE skip
    if ($errno == E_NOTICE) 
    {
        return;
    }
    
    $out = $errtypes[$errno].': '.$errmsg.'; '.trim($filename).':'.$linenum.";";
    $out = str_replace(array("\n", "\r", "\t"), ' ', $out);

    // Store data
    $dbg_info = '';

    // show debug if php >= 4.3.0
    if (function_exists('debug_backtrace') && CN_DEBUG)
    {
        foreach (debug_backtrace() as $item)
        {
            if ($item['function'] != 'user_error_handler')
            {
                $dbg_info .= '   '.str_replace(SERVDIR, '', $item['file']).":".$item['line']." ".$item['function'].'('.count($item['args']).')'."\n";
            }
        }

        $dbg_info .= "\n";
    }

    $str = trim(str_replace(array("\n","\r",SERVDIR), array(" ", " ", ''), $out));
    if (is_writable(cn_path_construct(SERVDIR,'cdata','log')))
    {
        $time = time();
        $log = fopen(cn_path_construct(SERVDIR,'cdata','log').'error_dump.log', 'a');
        fwrite($log, '['.$time.'] '.date('Y-m-d H:i:s', $time).'|'.$str."\n$dbg_info");
        fclose($log);
    }
}

// Since 2.0: Since time format
function time_since_format($diff)
{
    $out = array();

    if ($diff > 31557600) { $out['y'] = intval($diff / 31557600); $diff %= 31557600; } // years
    if ($diff > 2629800) { $out['mon'] = intval($diff / 2629800); $diff %= 2629800; } // month
    if ($diff > 86400) { $out['d'] = intval($diff / 86400); $diff %= 86400; } // days
    if ($diff > 3600) { $out['h'] = intval($diff / 3600); $diff %= 3600; } // hours
    if ($diff > 60) { $out['m'] = intval($diff / 60); $diff %= 60; } // minutes
    if ($diff > 0) { $out['s'] = $diff; } // seconds

    return $out;
}

// Since 2.0: Get BTree by unique id hash
function bt_get_id($id, $area = 'std')
{
    $m5 = md5($id);
    $vd = cn_touch_get(cn_path_construct(SERVDIR, 'cdata','btree').substr($m5, 0, 2).'.php');
    return isset($vd[$area][$m5]) ? $vd[$area][$m5] : NULL;
}

// Since 2.0: Set by BTree uniquie data
function bt_set_id($id, $data, $area = 'std')
{
    $m5 = md5($id);
    $sn = cn_path_construct(SERVDIR, 'cdata','btree').substr($m5, 0, 2).'.php';
    $vd = cn_touch_get($sn);

    if (!isset($vd[$area])) 
    {
        $vd[$area] = array();
    }
    $vd[$area][$m5] = $data;

    cn_fsave($sn, $vd);
}

// Since 2.0: Delete B-Tree index
function bt_del_id($id, $area)
{
    $m5 = md5($id);
    $sn = cn_path_construct(SERVDIR,'cdata','btree').substr($m5, 0, 2).'.php';
    $vd = cn_touch_get($sn);

    if (isset($vd[$area][$m5]))
    {
        unset($vd[$area][$m5]);
    }

    cn_fsave($sn, $vd);
}

// Since 2.0: Organize category into tree
function cn_category_struct($cats, $nc = array(), $parent = 0, $level = 0)
{
    $ic = array();
    $lc = array();
    
    foreach ($cats as $id => $vc)
    {                        
        if ($id == '#') {
            continue;
        }

        if ($vc['parent'] == $parent)
        {
            $nc[$id] = $vc;
            $nc[$id]['level'] = $level;
            
            // get childrens nodes
            list($nc, $ch) = cn_category_struct($cats, $nc, $id, $level + 1);
            
            // all childrens for node
            $nc[$id]['ac'] = $ch;

            // linear child (current)
            $lc[] = $id;

            // all inner childs
            $ic = array_unique(array_merge($ic, $ch));
        }
    }
    
    return array($nc, array_merge($ic, $lc));
}

// Since 2.0: Test User ACL. Test for groups [user may consists requested group]
function test($requested_acl, $requested_user = NULL, $is_self = FALSE)
{
    $user = member_get();

    // Deny ANY access of unathorized member
    if (!$user) return FALSE;

    // Always allow for Admin
    if ($user['acl'] == 1) {
        return in_array($requested_acl, ['Bd', 'Bs']) ? false : true;
    }

    $acl = $user['acl'];
    $grp = getoption('#grp');
    $ra  = spsep($requested_acl);

    // This group not exists, deny all
    if (!isset($grp[$acl]))
        return FALSE;

    // Decode ACL, GRP string
    $gp = spsep($grp[$acl]['G']);
    $rc = spsep($grp[$acl]['A']);

    // If requested acl not match with real allowed, break
    foreach ($ra as $Ar)
    {        
        if (!in_array($Ar, $rc)) return FALSE;
    }

    // Test group or self
    if ($requested_user)
    {
        // if self-check, check name requested user and current user
        if ($is_self && $requested_user['name'] !== $user['name'])
            return FALSE;

        // if group check, check: requested uses may be in current user group
        if (!$is_self)
        {
            if ($gp && !in_array($requested_user['acl'], $gp))
                return FALSE;
            else if (!$gp)
                return FALSE;
        }
    }

    return TRUE;
}

// Since 2.0: Test category accessible for current user
function test_cat($cat)
{
    $user = member_get();
    $grp = getoption('#grp');

    if (!$user) return FALSE;

    // Get from cache
    if ($cc = mcache_get('#categories'))
        $catgl = $cc;
    else
    {
        $catgl = getoption('#category');
        mcache_set('#categories', $catgl);
    }

    // View all category
    if (test('Ccv'))
        return TRUE;

    $acl = $user['acl'];
    $cat = spsep($cat) ;

    // Overall ACL test, with groups + own
    $acl = array_unique(array_merge(array($acl), spsep($grp[$acl]['G'])));

    foreach ($cat as $ct)
    {
        // Requested cat not exists, skip
        if (!isset($catgl[$ct])) continue;

        // Group list included (partially/fully) in group list for category
        $sp = spsep($catgl[$ct]['acl']);
        $is = array_intersect($sp, $acl);
        if (!$is) return FALSE;
    }

    return TRUE;
}

// Since 2.0: Extended assign
function cn_assign()
{
    $args = func_get_args();
    $keys = explode(',', array_shift($args));

    foreach ($args as $id => $arg)
    {
        // Simple assign
        if (isset($keys[$id]))
        {
            $KEY = trim($keys[$id]);
            $GLOBALS[ $KEY ] = $arg;
        }
        else // Inline assign
        {
            list($k, $v) = explode('=', $arg, 2);
            $GLOBALS[$k] = $v;
        }
    }
}

// Since 2.0: Translate phrase to code
function hi18n($ft)
{
    $sph = '';

    $ex = spsep($ft, ' ');
    foreach ($ex as $w)
    {
        $sx = soundex($w);
        if ($sx[0] === '0') continue;
        $sph .= $sx;
    }

    // long phrases
    return substr($sph, 0, 32);
}

// Since 2.0: Short names to full
function i18n()
{
    $i8 = mcache_get('#i18n');
    $va = func_get_args();
    $ft = array_shift($va);

    if (is_array($ft)) list($ft, $sph) = $ft; else $sph = '';
    $sph .= hi18n($ft);

    // match soundex found
    if (isset($i8[$sph]))
        $ft = UTF8ToEntities($i8[$sph]);

    // Replace placeholders
    foreach ($va as $id => $vs)
        $ft = str_replace("%".($id+1), $vs, $ft);

    return $ft;
}

// Since 2.0: Extended extract
function _GL($v)
{
    $vs = explode(',', $v);
    $result = array();
    foreach ($vs as $vc)
    {
        $el   = explode(':', $vc, 2);
        $vc   = isset($el[0]) ? $el[0]:false;
        $func = isset($el[1]) ? $el[1]:false;

        $var = false;
        if ($vc) $var = isset($GLOBALS[trim($vc)]) ? $GLOBALS[trim($vc)] : false;
        if ($func) $var = call_user_func($func, $var);

        $result[] = $var;
    }

    return $result;
}

// Since 2.0: Execute PHP-template
// 1st argument - template name, other - variables
function exec_tpl()
{
    $args = func_get_args();
    $tpl  = preg_replace('/[^a-z0-9_\/]/i', '', array_shift($args));
    $open = SKIN.'/'.($tpl?$tpl:'master').'.php';

    foreach ($args as $arg)
    {
        if (is_array($arg))
        {
            foreach ($arg as $k0 => $v) 
            { 
                $k = "__$k0"; 
                $$k = $v;                 
            }
        }
        else
        {
            list($k, $v) = explode('=', $arg, 2);

            // <-- make local variable
            $k = "__$k";
            $$k = $v;
        }
    }

    if (file_exists($open))
    {        
        ob_start(); include $open; $echo = ob_get_clean();
        return $echo;
    }

    return '';
}

// Since 1.5.1: Simply read template file
function read_tpl($tpl = 'index')
{
    // get from cache
    $cached = mcache_get("tpl:$tpl");
    if ($cached) 
    {
        return $cached;
    }

    // Get asset path
    if (preg_match('/\.(css|js)/i', $tpl)) 
    {
        $fine = ''; 
    }
    else 
    {
        $fine = '.tpl';
    }

    // Get plugin path
    if  ($tpl[0] == '/')
    {
         $open =  cn_path_construct(SERVDIR,'cdata','plugins').substr($tpl, 1).$fine;
    }
    else 
    {
        $open = SKIN.DIRECTORY_SEPARATOR.($tpl? $tpl : 'default') . $fine;
    }

    // Try open
    $not_open = false;
    $r = fopen($open, 'r') or $not_open = true;
    if ($not_open)
    {
        return false;
    }

    ob_start();
    fpassthru($r);
    $ob = ob_get_clean();
    fclose($r);

    // caching file
    mcache_set("tpl:$tpl", $ob);
    return $ob;
}

// Since 1.5.1: More process for template {$args}
function proc_tpl()
{
    $vars = array();
    $extr_args = func_get_args();

    $args = array();
    $tpl = array_shift($extr_args);

    // Parse input arguments
    foreach ($extr_args as $A)
    {
        if (is_array($A))
        {
            foreach ($A as $i => $v) 
            {
                $args[$i] = $v;
            }
        }
        else
        {
            list($i, $v) = explode('=', $A, 2);
            $args[$i] = $v;
        }
    }

    // predefined arguments
    $args['PHP_SELF'] = PHP_SELF;

    // Globals are saved too
    foreach ($GLOBALS as $gi => $gv)
    {
        if (in_array($gi, array('session', '_CN_SESS_CACHE', '_HOOKS', 'HTML_SPECIAL_CHARS', '_SESS', 'GLOBALS', '_ENV', '_REQUEST', '_SERVER', '_FILES', '_COOKIE', '_POST', '_GET')))
        {
            continue;
        }

        if (!isset($args[$gi])) 
        {
            $args[$gi] = $gv;
        }
    }

    // reading template
    $d = read_tpl($tpl);

    /*
    * Catch Foreach Cycles. Usage:
    *
    * {foreach from=variable_name}
    *     {$variable_name.} -- display first level (array)
    *     {$variable_name.name} -- display sublevel (struct array->array)
    * {/foreach}
    *
    */

    if ( preg_match_all('~{foreach from\=([^}]+)}(.*?){/foreach}~is', $d, $rep, PREG_SET_ORDER) )
    {
        foreach ($rep as $v)
        {
            $rpl = false;
            if (is_array($args[ $v[1] ]))
            {
                foreach ($args[ $v[1] ] as $x)
                {
                    $bulk = $v[2];

                    // String simply replaces {$FromValue.}, Array -> {$FromValue.Precise}
                    if  (is_array($x))
                    {
                        foreach ($x as $ik => $iv) 
                        {
                            $bulk = str_replace('{$'.$v[1].".$ik}", $iv, $bulk);
                        }
                    }
                    else 
                    {
                        $bulk = str_replace('{$'.$v[1].".}", $x, $bulk);
                    }

                    $rpl .= $bulk;
                }
            }

            $d = str_replace($v[0], $rpl, $d);
        }
    }

    /*
     * Process template variables. Syntax:
     *
     * {$variable}
     * {$variable|function} -- apply function to variable
     * {$variable|function:param2:param3...} -- more static args
     * {$variable|function:param2:...|function2:param2} == chain 2nd function
     * {"text variable"|func"} = func('text variable')
     *
     */

    if (preg_match_all('/\{(["$])([^\}]+)\}/i', $d, $c, PREG_SET_ORDER))
    {
        foreach ($c as $ct) // iterate each var-tpl
        {
            $ctp=explode('|', $ct[2], 2); // extract func + modifiers
            $av=isset($ctp[0])?$ctp[0]:'';
            $modify = isset($ctp[1])?$ctp[1]:'';

            $var  = $args[ $av ];
            $mods = explode('|', $modify);

            // apply modifier [+params]
            foreach ($mods as $func) 
            {
                if ($func)
                {
                    $varx = $func = explode(':', $func);

                    // it's variable or string
                    $varx[0] = ($ct[1] == '$')? $var : substr($av, 0, -1);

                    // process the variable
                    if (function_exists($func[0])) 
                    {
                        $var = call_user_func_array($func[0], $varx);
                    }
                }
            }
            // save
            $vars[ $ct[0] ] = $var;
        }
    }

    // Apply all parameters
    $d = str_replace(array_keys($vars), array_values($vars), $d);

    /*
     * Catch {if} constructions. Usage
     *
     * {if $var1} ... {/if}
     * {if !$var1} ... {/if}
     *
     */

    if ( preg_match_all('~{if\s+(.*?)}(.*?){/if}~is', $d, $rep, PREG_SET_ORDER))
    {
        foreach ($rep as $vs)
        {
            $var = 0;
            $vs[1] = trim($vs[1]);
            if      ($vs[1][0] == '$') $var = $args[ substr($vs[1], 1) ];
            else if ($vs[1][1] == '$') $var = $args[ substr($vs[1], 2) ];

            // If boolean logic OK, replace
            if ($vs[1][0] == '$' && $var)            $d = str_replace($vs[0], $vs[2], $d);
            else if ($vs[1][0] == '!' && empty($var)) $d = str_replace($vs[0], $vs[2], $d);
            else $d = str_replace($vs[0], false, $d);
        }
    }

    // override process template (filter)
    list($d) = hook('func_proc_tpl', array($d, $tpl, $args));

    // truncate unused
    $d = preg_replace('~{\$[^}]+}+~s', '', $d);

    // code obfuscation
    if (preg_match_all('/<jstidy>(.*?)<\/jstidy>/is', $d, $jt, PREG_SET_ORDER))
    {
        foreach ($jt as $jtv)
        {
            $jsc = preg_replace('/^\s*\/\/.*$/im', '', $jtv[1]); // remove comment
            $jsc = preg_replace("/\s{2,}/is", ' ', str_replace("\n", ' ', $jsc));
            $d = str_replace($jtv[0], $jsc, $d);
        }
    }

    // replace all
    return ( hook('return_proc_tpl', $d) );
}

// Since 2.0: Add message to front-end
function cn_front_message($message, $area = 'n')
{
    global $cn_FE_Messages;

    // Create message
    if (isset($cn_FE_Messages)) $cn_FE_Messages = array();
    else if (!is_array($cn_FE_Messages)) $cn_FE_Messages = array();

    // Create area
    if (!isset($cn_FE_Messages[$area])) $cn_FE_Messages[$area] = array();

    $cn_FE_Messages[$area][] = array($message);
}

// Since 2.0: Show front messages
function cn_front_msg_show($area, $css = 'fe_css')
{
    global $cn_FE_Messages;
    if (!is_array($cn_FE_Messages[$area])) return;

    foreach ($cn_FE_Messages[$area] as $msg)
    {
        echo '<div class="'.$css.'">'.$msg[0].'</div>';
    }
}

// Since 2.0: @bootstrap Select DB mechanism
function cn_db_init()
{
    // basic CN db
    require_once SERVDIR.'/core/db/coreflat.php';
}

// Since 2.0: Language codes initialize
function cn_lang_init()
{
    $lang = getoption('cn_language');
    if (!$lang) {
        $lang = 'en';
    }

    $st = array();
    $ln = file(SERVDIR.'/core/lang/'.$lang.'.txt');
    foreach ($ln as $vi) {
        list($S532, $RS) = explode(': ', trim($vi), 2);
        $st[$S532] = $RS;
    }

    mcache_set('#i18n', $st);
}

// Since 2.0: @bootstrap Make & load configuration file
function cn_config_load()
{
    global $_CN_access;

    // Checking permission for load config
    $conf_dir = cn_path_construct(SERVDIR, 'cdata');
    if (!is_dir($conf_dir) || !is_writable($conf_dir)) {
        return false;
    }

    $conf_path = cn_path_construct(SERVDIR, 'cdata') . 'conf.php';
    $cfg = cn_touch_get($conf_path);
    if (!$cfg)
    {
        if(defined('SHOW_NEWS'))
        {
            echo 'Sorry, but news not available by technical reason.';
            die();
        }
        else
        {
            //echo 'Need convert data - run migration_update_data.php';
            $cfg = cn_touch_get($conf_path, true);
            
        }
    }
    // make site section
    $cfg['%site'] = isset($cfg['%site']) ? $cfg['%site'] : array();

    $default_conf = array
    (
        'skin'                          => 'default',
        'frontend_encoding'             => 'UTF-8',
        'useutf8'                       => 1,
        'utf8html'                      => 1,
        'wysiwyg'                       => 0,
        'news_title_max_long'           => 100,
        'date_adjust'                   => 0,
        'smilies'                       => 'smile,wink,wassat,tongue,laughing,sad,angry,crying',
        'allow_registration'            => 1,
        'registration_level'            => 4,
        'ban_attempts'                  => 3,
        'allowed_extensions'            => 'gif,jpg,png,bmp,jpe,jpeg',
        'reverse_active'                => 0,
        'full_popup'                    => 0,
        'full_popup_string'             => 'HEIGHT=400,WIDTH=650,resizable=yes,scrollbars=yes',
        'show_comments_with_full'       => 1,
        'timestamp_active'              => 'd M Y',
        'use_captcha'                   => 1,
        'reverse_c  omments'            => 0,
        'flood_time'                    => 15,
        'comments_std_show'             => 1,
        'comment_max_long'              => 1500,
        'comments_per_page'             => 5,
        'only_registered_comment'       => 0,
        'allow_url_instead_mail'        => 1,
        'comments_popup'                => 0,
        'comments_popup_string'         => 'HEIGHT=400,WIDTH=650,resizable=yes,scrollbars=yes',
        'show_full_with_comments'       => 1,
        'timestamp_comment'             => 'd M Y h:i a',
        'mon_list'                      => 'January,February,March,April,May,June,July,August,September,October,November,December',
        'week_list'                     => 'Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
        'active_news_def'               => 20,
        'thumbnail_with_upload'         => 0,
        'max_thumbnail_width'            => 256,
        'auto_news_alias'               =>0,                    
        // 'phpself_full'                  => '',
        // 'phpself_popup'                 => '',
        // 'phpself_paginate'              => '',

        // Notifications
        'notify_registration'           => 0,
        'notify_comment'                => 0,
        'notify_unapproved'             => 0,
        'notify_archive'                => 0,
        'notify_postponed'              => 0,

        // Social buttons
        'i18n'                          => 'en_US',
        'gplus_width'                   => 350,
        'fb_comments'                   => 3,
        'fb_box_width'                  => 550,

        // CKEditor settings
        'ck_ln1'                        => "Source,Maximize,Scayt,PasteText,Undo,Redo,Find,Replace,-,SelectAll,RemoveFormat,NumberedList,BulletedList,Outdent,Indent",
        'ck_ln2'                        => "Image,Table,HorizontalRule,Smiley",
        'ck_ln3'                        => "Link,Unlink,Anchor",
        'ck_ln4'                        => "Format,FontSize,TextColor,BGColor",
        'ck_ln5'                        => "Bold,Italic,Underline,Strike,Blockquote",
        'ck_ln6'                        => "JustifyLeft,JustifyCenter,JustifyRight,JustifyBlock",
        'ck_ln7'                        => "",
        'ck_ln8'                        => "",

        // Rewrite
        'rw_htaccess'                   => '',
        'rw_prefix'                     => '/news/',
    );

    // Set default values
    foreach ($default_conf as $k => $v) 
    {
        if (!isset($cfg['%site'][$k])) 
        {
            $cfg['%site'][$k] = $v;
        }
    }

    // Set basic groups
    if (!isset($cfg['grp'])) 
    {
        $cfg['grp'] = array();
    }

    // Make default groups
    $cgrp = file(cn_path_construct(SKIN,'defaults').'groups.tpl');
    foreach ($cgrp as $G)
    {
        $G = trim($G);
        if ($G[0] === '#') 
        {
            continue;
        }

        list($id, $name, $group, $access) = explode('|', $G);
        $id = intval($id);

        // Is empty row
        if (empty($cfg['grp'][$id]))
        {
            $cfg['grp'][$id] = array
            (
                'N' => $name,
                'G' => $group,
                '#' => TRUE,
                'A' => ($access === '*') ? $_CN_access['C'].','.$_CN_access['N'].','.$_CN_access['M'] : $access,
            );
        }
    }

    // Admin has ALL privilegies
    $cfg['grp'][1]['A'] = $_CN_access['C'].','.$_CN_access['N'].','.$_CN_access['M'];
    
    // Set config
    mcache_set('config', $cfg);

    // Make crypt-salt [after config sync]
    if (!getoption('#crypt_salt'))
    {
        $salt = SHA256_hash(mt_rand().mt_rand().mt_rand().mt_rand().mt_rand().mt_rand().mt_rand().mt_rand());
        setoption("#crypt_salt", $salt);
    }

    if (!getoption('#grp'))
    {
        setoption("#grp", $cfg['grp']);
    }
    
    return TRUE;
}

// Since 2.0.1: Filter magic quotes gpc
function cn_filter_magic_quotes($in = null, $lv = 0)
{
    if ($lv == 0)
    {
        $_GET = cn_filter_magic_quotes($_GET, 1);
        $_POST = cn_filter_magic_quotes($_POST, 1);
        $_COOKIE = cn_filter_magic_quotes($_COOKIE, 1);

        return TRUE;
    }
    else if (is_array($in))
    {
        foreach ($in as $a => $b)
        {
            $in[$a] = cn_filter_magic_quotes($b, $lv + 1);
        }
        return $in;
    }
    else
    {
        return stripslashes($in);
    }
}

// Since 2.0: Save whole config
function cn_config_save($cfg = null)
{
    if ($cfg === null) 
    {
        $cfg = mcache_get('config');
    }

    $fn = cn_path_construct(SERVDIR,'cdata').'conf.php';
    $dest = $fn.'-'.mt_rand().'.bak';

    // save all config
    $fx = fopen($dest, 'w+');
    fwrite($fx, "<?php die(); ?>\n" . base64_encode(serialize($cfg)) );
    fclose($fx); rename($dest, $fn);

    mcache_set('config', $cfg);
    return $cfg;
}

// Since 1.5.0: Send Mail
function cn_send_mail($to, $subject, $message, $alt_headers = NULL)
{
    if (!isset($to)) return FALSE;
    if (!$to) return FALSE;

    $tos = spsep($to);
    $from = 'Cutenews <cutenews@'.$_SERVER['SERVER_NAME'].'>';

    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/plain;\r\n";
    $headers .= 'From: '.$from."\r\n";
    $headers .= 'Reply-to: '.$from."\r\n";
    $headers .= 'Return-Path: '.$from."\r\n";
    $headers .= 'Message-ID: <' . md5(uniqid(time())) . '@' . $_SERVER['SERVER_NAME'] . ">\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion()."\r\n";
    $headers .= "Date: " . date('r', time()) . "\r\n";

    if (!is_null($alt_headers)) $headers = $alt_headers;
    foreach ($tos as $v) if ($v) mail($v, $subject, $message, $headers);

    return true;
}

// Since 1.5.3: UTF8-Cutenews compliant
function utf8decrypt($str, $oldhash)
{
    $len = strlen($str) * 3;
    while($len >= 16) $len -= 16;
    $len = floor($len / 2);

    $salt = substr($oldhash, $len, 10);
    $pass = SHA256_hash($salt.$str.'`>,');
    $pass = substr($pass, 0, $len).$salt.substr($pass, $len);

    return $pass;
}

// Since 1.5.2: Directory scan
function scan_dir($dir, $cond = '')
{
    $files = array();
    if($dh = opendir($dir))
    {
        while (false !== ($filename = readdir($dh)))
        {            
            if (!in_array($filename, array('.', '..')) && ($cond == '' || $cond && preg_match("/$cond/i", $filename)))
            {
                $files[] = $filename;
            }
        }
    }
    return $files;
}

// Since 1.5.2: Delete directory
function del_dir($dir)
{
    $files = array_diff(scan_dir($dir), array('.','..'));

    foreach ($files as $file)
    {
        if (is_dir("$dir/$file"))
        {
            del_dir("$dir/$file");
        }
        else
        {
            unlink("$dir/$file");
        }
    }
    return rmdir($dir);
}

// Add hook to CN system
// Syntax:
//
//   $hook = '+myhook' (priority high)
//   $func = '*called_function' (give args) or 'called_function' (1 arg)

// Since 1.5.0: Add hook to system
function add_hook($hook, $func)
{
    global $_HOOKS;

    $prior = 1;
    if ($hook[0] == '+') {
        $hook = substr($hook, 1);
    }

    if ($hook[0] == '-') {
        $prior = 0;
        $hook = substr($hook, 1);
    }

    if (!isset($_HOOKS[$hook])) {
        $_HOOKS[$hook] = array();
    }

    // priority (+/-)
    if ($prior) {
        array_unshift($_HOOKS[$hook], $func);
    } else {
        $_HOOKS[$hook][] = $func;
    }
}

// Since 1.5.0: Cascade Hooks
function hook($hook, $args = null) {

    global $_HOOKS;

    // Plugin hooks
    if (!empty($_HOOKS[$hook]) && is_array($_HOOKS[$hook])) {
        foreach ($_HOOKS[$hook] as $hookfunc) {

            if ($hookfunc[0] == '*') {
                $_args = call_user_func_array(substr($hookfunc, 1), $args);
            } else {
                $_args = call_user_func($hookfunc, $args);
            }

            if (!is_null($_args)) {
                $args = $_args;
            }
        }
    }

    return $args;
}

//  Since 1.5.2: Format the size of given file
function format_size($file_size) {

    if ($file_size >= 1073741824)    $file_size = round($file_size / 1073741824 * 100) / 100 . " Gb";
    else if($file_size >= 1048576)   $file_size = round($file_size / 1048576 * 100) / 100 . " Mb";
    else if($file_size >= 1024)      $file_size = round($file_size / 1024 * 100) / 100 . " Kb";
    else                            $file_size = $file_size . " B";
    return $file_size;
}

// Since 2.0: Short message form
function msg_info($title, $go_back = null)
{
    echoheader('info', i18n("Permission check"));

    if ($go_back === null) {
        $go_back = $_POST['__referer'];
    }

    if (empty($go_back)) {
        $go_back = PHP_SELF;
    }

    echo '<section><div class="container"><div class="alert alert-warning">';
    echo '<strong>'.i18n('Warning!').' </strong> '.i18n($title).' <a href="'.$go_back.'" class="btn btn-warning btn-xs">OK</a>';
    echo '</div></div></section>';

    echofooter();
    DIE();
}

// Since 2.0: Check if first request
function confirm_first()
{
    return isset($_POST['__my_confirm']) ? 0 : 1;
}

// Since 2.0: Make confirm form with callbacks
function confirm_post($text, $required = 'mod,action,subaction,source')
{
    $sp = spsep($required);
    $required = array();
    foreach ($sp as $v) $required[trim($v)] = REQ(trim($v), 'GETPOST');

    if (REQ('__my_confirm') == '_confirmed') return TRUE; // Click "confirm"
    else if (REQ('__my_confirm') == '_decline') return FALSE; // Click "decline"

    // Echo message form -----------------------
    echoheader('question', i18n('Confirm action?'));

    $post = array();
    foreach ($required as $id => $v) if ($v) $post[] = array('name' => $id, 'var' => cn_htmlspecialchars($v));

    // remove not needed line
    if (isset($_POST['__post_data'])) unset($_POST['__post_data']);
    if (isset($_POST['__my_confirm'])) unset($_POST['__my_confirm']);

    $post_data = base64_encode( serialize($_POST) );

    echo proc_tpl('confirm', array('text' => $text, 'post' => $post, 'post_data' => $post_data));
    echofooter();
    die();
}

// Displays header skin
// $image = img@custom_style_tpl
function echoheader($image, $header_text, $bread_crumbs = false) {

    global $skin_header, $lang_content_type, $skin_menu, $skin_prefix, $_SESS, $_SERV_SESS, $SiteTitle;

    $header_time = date('H:i:s M, d', ctime());
    $SiteTitle = empty($SiteTitle) ? 'CuteNews' : $SiteTitle;

    $customs = explode("@", $image);
    $image = isset($customs[0])?$customs[0]:'';
    $custom_style = isset($customs[1])?$customs[1]:false;
    $custom_js = isset($customs[2])?$customs[2]:false;

    /*
      Get hook for additional items
      USAGE:
      Params:
       -- version (0)
      Result:
       -- Array of $items[]
       $item = [ 0 => target url, 1 => title ]
      If "target url" must starts from '#', else got PHP_SELF
    */

    $menu_result = '';
    $headermenu = hook('core/headermenu', array('version' => array()));
    foreach ($headermenu as $menu_key => $vol) {

        $menu_target = isset($vol[0]) ? $vol[0] : false;
        $menu_title  = isset($vol[1]) ? $vol[1] : false;

        if (($menu_target) && ($menu_target[0] == '#')) {
            $menu_target = substr($menu_target, 1);
        } else {
            $menu_target = PHP_SELF;
        }

        if ($menu_title) {
            $menu_result .= '<li><a class="nav-top-item" href="'.$menu_target.'">'.i18n($menu_title).'</a></li>';
        } else {
            $menu_result .= '<li><a class="nav-top-item" href="'.PHP_SELF.'">'.VERSION_NAME.'</a></li>';
        }
    }

    if (isset($_SESSION['user'])) {
        $skin_header = preg_replace("/{menu}/", $skin_menu, $skin_header);
    } else {
        $skin_header = preg_replace("/{menu}/", $menu_result, $skin_header);
    }

	$theme_bs = preg_replace('~[^a-z]~i','', getoption('skin'));
    $theme_bs = $theme_bs ? $theme_bs : 'default';

    $skin_header = get_skin($skin_header);
    $skin_header = str_replace('{title}', ($header_text? $header_text.' / ' : '') . $SiteTitle, $skin_header);
	$skin_header = str_replace('{theme}', $theme_bs, $skin_header); //theme
	$skin_header = str_replace("{image-name}", $skin_prefix.$image, $skin_header);
    $skin_header = str_replace("{header-text}", $header_text, $skin_header);
    $skin_header = str_replace("{header-time}", $header_time, $skin_header);
    $skin_header = str_replace("{content-type}", $lang_content_type, $skin_header);
    $skin_header = str_replace("{breadcrumbs}", $bread_crumbs, $skin_header);

    if ($custom_style) {
        $custom_style = read_tpl($custom_style);
    }

    $skin_header = str_replace("{CustomStyle}", $custom_style, $skin_header);

    if ($custom_js) {
        $custom_js = '<script type="text/javascript">'.read_tpl($custom_js).'</script>';
    }

    $skin_header = str_replace("{CustomJS}", $custom_js, $skin_header);
    echo hook('core/skin_header', $skin_header);
}

// Displays footer skin
function echofooter()
{
    global $is_loged_in, $skin_footer, $lang_content_type, $skin_menu;

    if ($is_loged_in == TRUE)
         $skin_footer = str_replace("{menu}", $skin_menu, $skin_footer);
    else $skin_footer = str_replace("{menu}", " &nbsp; ".VERSION_NAME, $skin_footer);

    $skin_footer = get_skin($skin_footer);
    $skin_footer = str_replace("{content-type}", $lang_content_type, $skin_footer);
    $skin_footer = str_replace("{exec-time}", round(microtime(true) - EXEC_TIME, 3), $skin_footer);

    die($skin_footer);
}

// And the duck fly away.
function b64dck()
{
    $cr = bd_config('e2NvcHlyaWdodHN9');
    $shder = bd_config('c2tpbl9oZWFkZXI=');
    $sfter = bd_config('c2tpbl9mb290ZXI=');

    global $$shder, $$sfter;

    $HDpnlty = bd_config('PGNlbnRlcj48aDE+Q3V0ZU5ld3M8L2gxPjxhIGhyZWY9Imh0dHA6Ly9jdXRlcGhwLmNvbSI+Q3V0ZVBIUC5jb208L2E+PC9jZW50ZXI+PGJyPg==');
    $FTpnlty = bd_config('PGNlbnRlcj48ZGl2IGRpc3BsYXk9aW5saW5lIHN0eWxlPVwnZm9udC1zaXplOiAxMXB4XCc+UG93ZXJlZCBieSA8YSBzdHlsZT1cJ2ZvbnQtc2l6ZTogMTFweFwnIGhyZWY9XCJodHRwOi8vY3V0ZXBocC5jb20vY3V0ZW5ld3MvXCIgdGFyZ2V0PV9ibGFuaz5DdXRlTmV3czwvYT4gqSAyMDA1ICA8YSBzdHlsZT1cJ2ZvbnQtc2l6ZTogMTFweFwnIGhyZWY9XCJodHRwOi8vY3V0ZXBocC5jb20vXCIgdGFyZ2V0PV9ibGFuaz5DdXRlUEhQPC9hPi48L2Rpdj48L2NlbnRlcj4=');

    if (!stristr($$shder,$cr) and !stristr($$sfter,$cr))
    {
        $$shder = $HDpnlty.$$shder;
        $$sfter = $$sfter.$FTpnlty;
    }
}

function fcutenewslic()
{
    global $reg_site_key;

    $clst = bd_config("PGRpdiBzdHlsZT0ibWFyZ2luLXRvcDoxNXB4O3dpZHRoOjEwMCU7dGV4dC1hbGlnbjpjZW50ZXI7Zm9udDo5cHggVmVyZGFuYTsiPlBvd2VyZWQgYnkgPGEgaHJlZj0iaHR0cDovL2N1dGVwaHAuY29tLyIgdGl0bGU9IkN1dGVOZXdzIC0gUEhQIE5ld3MgTWFuYWdlbWVudCBTeXN0ZW0iIHRhcmdldD0iX2JsYW5rIj5DdXRlTmV3czwvYT48L2Rpdj4=");
    if (!file_exists( cn_path_construct(SERVDIR,'cdata').'reg.php')) {
        echo $clst;         
    } else {
        include SERVDIR."/cdata/reg.php"; 
        if (!preg_match(base64_decode("L1xBKFx3ezZ9KS1cd3s2fS1cd3s2fVx6Lw=="), $reg_site_key)) {
            echo $clst;
        }        
    } 
    return 0;
}

// ===================== ACL SECTION =====================

// Since 2.0: Get cached categories with acl test
function cn_get_categories($is_frontend = TRUE)
{
    if ($cc = mcache_get('#categories'))
    {
        $catgl = $cc;
    }
    else
    {
        $catgl = getoption('#category');
        mcache_set('#categories', $catgl);
    }

    // Delete not allowed cats
    foreach ($catgl as $id => $v)
    {
        if ($id == '#') 
        {
            unset($catgl[$id]);
        }
        elseif (!test_cat($id) && !$is_frontend) 
        {
            unset($catgl[$id]);
        }
    }

    return $catgl;
}

// Since 2.0: Get additional fields in groups
function cn_get_more_fields($defined = array())
{
    $morefields = array();
    $mgrp = getoption('#more_list');

    foreach ($mgrp as $name => $item)
    {
        if (!($grp = $item['grp'])) $grp = '#basic';
        if (!isset($morefields[$grp])) $morefields[$grp] = array();

        if (isset($defined[$name]))
            $item['#value'] = $defined[$name];

        $morefields[$grp][$name] = $item;
    }
    return array($morefields, $mgrp);
}

// Since 2.0: Add more fields to entry
function cn_more_fields_apply($e, $dt)
{
    $deny = FALSE;
    $applied = array();

    if (!$dt) $dt = array();

    $mgrp = getoption('#more_list');
    foreach ($dt as $id => $data)
    {
        if (!isset($mgrp[$id]))
        {
            $deny = i18n('Field ID not exists', $id);
            break;
        }
        elseif ($mgrp[$id]['req'] && $data === '')
        {
            $deny = i18n('Fill required field ['.$id.']');
            break;
        }
        else
        {
            $applied[$id] = $data;
        }
    }

    $e['mf'] = $applied;
    return array($e, $deny);
}

// Since 1.4.7: Insert smilies for adding into news/comments
function insert_smilies($insert_location, $break_location = FALSE, $admincp = FALSE)
{
    $i          = 0;
    $output     = false;
    $config_http_script_dir = getoption('http_script_dir');
    $smilies    = spsep(getoption('smilies'));

    foreach ($smilies as $smile)
    {
        $i++;
        $smile = trim($smile);

        if ($admincp)
        {
            $output .= '<a href="#" onclick="insertAtCursor(document.getElementById(\''.$insert_location.'\'), \' :'.$smile.': \'); return false;"><img alt="'.$smile.'" src="skins/emoticons/'.$smile.'.gif" /></a>';
        }
        else
        {
            if (getoption('base64_encode_smile'))
            {
                $url = "data:image/png;base64,".base64_encode(join('', file(SERVDIR.'/skins/emoticons/'.$smile.'.gif')));
            }
            else
            {
                $url = getoption('http_script_dir')."/skins/emoticons/".$smile.".gif";
            }
            $output .= "<a href='#' onclick='insertext(\":$smile:\", \"$insert_location\"); return false;'><img style=\"border: none;\" alt=\"$smile\" src=\"$url\" /></a>";
        }

        if ( isset($break_location) && intval($break_location) > 0 && $i % $break_location == 0)
        {
             $output .= "<br />";
        }
        else 
        {
            $output .= "&nbsp;";
        }
    }

    return $output;
}

// Hello skin!
function get_skin($skin)
{
    $licensed = false;
    if (!file_exists(cn_path_construct(SERVDIR,'cdata').'reg.php')) {
        $stts = base64_decode('KHVucmVnaXN0ZXJlZCk=');
    } else {

        include (SERVDIR.'/cdata/reg.php');
        if (isset($reg_site_key) == false) {
            $reg_site_key = false;
        }
        
        $mmbrid = null;
        if (preg_match('/\\A(\\w{6})-\\w{6}-\\w{6}\\z/', $reg_site_key, $mmbrid)) {
            if ( !isset($reg_display_name) or !$reg_display_name or $reg_display_name == '') {
                 $stts = "<!-- (-$mmbrid[1]-) -->";
            } else {
                $stts = "<label title='(-$mmbrid[1]-)'>". base64_decode('TGljZW5zZWQgdG86IA==').$reg_display_name.'</label>';
            }
            $licensed = true;
        } else {
            $stts = '!'.base64_decode('KHVucmVnaXN0ZXJlZCk=').'!';
        }
    }

    $msn  = bd_config('c2tpbg==');
    $cr   = bd_config('e2NvcHlyaWdodHN9');
    $lct  = bd_config('PGRpdiBzdHlsZT0iZm9udC1zaXplOiA5cHgiPlBvd2VyZWQgYnkgPGEgc3R5bGU9ImZvbnQtc2l6ZTogOXB4IiBocmVmPSJodHRwOi8vY3V0ZXBocC5jb20vY3V0ZW5ld3MvIiB0YXJnZXQ9Il9ibGFuayI+Q3V0ZU5ld3Mge2N2ZXJzaW9ufTwvYT4gJmNvcHk7IDIwMDImbmRhc2g7e2RhdGV9IDxhIHN0eWxlPSJmb250LXNpemU6IDlweCIgaHJlZj0iaHR0cDovL2N1dGVwaHAuY29tLyIgdGFyZ2V0PSJfYmxhbmsiPkN1dGVQSFA8L2E+Ljxicj57bC1zdGF0dXN9PC9kaXY+');
    $lct  = preg_replace("/{date}/", date('Y'), $lct);
    $lct  = preg_replace("/{l-status}/", $stts, $lct);
    $lct  = preg_replace("/{cversion}/", VERSION, $lct);

    if ($licensed == true) 
    {
        $lct = false;
    }
    $$msn = preg_replace("/$cr/", $lct, $$msn);

    return $$msn;
}

// Make category by category id(s)
function news_make_category($category)
{
    // User has selected multiple categories
    if (is_array($category))
    {
        $nc = array();
        foreach ($category as $cvalue)
        {
            if (!test_cat($cvalue))
            {
                msg_info('Not allowed category');
            }

            $nc[] = intval($cvalue);
        }
        return implode(',', $nc);
    }
    // Single or nothing cat
    else
    {
        if (test_cat($category))
        {
            msg_info(i18n('Not allowed category'));
        }

        return $category;
    }
}

// Since 1.5.2: Make HTML code for postponed date
function make_postponed_date($gstamp = 0)
{
    $_dateD = $_dateM = $_dateY = false;

    // Use current timestamp if no present
    if ($gstamp == 0) 
    {
        $gstamp = ctime();
    }

    $day    = date('j', $gstamp);
    $month  = date('n', $gstamp);
    $year   = date('Y', $gstamp);
    $ml     = explode(',', getoption('mon_list'));

    for ($i = 1; $i < 32; $i++)
    {
        if ($day == $i) 
        {
            $_dateD .= "<option selected value=$i>$i</option>";
        }
        else
        {
            $_dateD .= "<option value=$i>$i</option>";
        }
    }

    for ($i = 1; $i < 13; $i++)
    {
        $timestamp = mktime(0, 0, 0, $i, 1, 2003);
        $curr_mont = date('n', $timestamp) - 1;

        if ($ml && isset($ml[ $curr_mont]))
        {
            $month_name = $ml[ $curr_mont ];
        }
        else
        {
            $month_name = date("M", $timestamp);
        }

        // ---
        if ($month == $i) 
        {
            $_dateM .= "<option selected value=$i>" . $month_name . "</option>";
        }
        else
        {
            $_dateM .= "<option value=$i>" . $month_name . "</option>";
        }
    }

    for ($i = 2003; $i < (date('Y') + 8); $i++)
    {
        if ($year == $i) 
        {
            $_dateY .= "<option selected value=$i>$i</option>";
        }
        else
        {
            $_dateY .= "<option value=$i>$i</option>";
        }
    }

    return array($_dateD, $_dateM, $_dateY, date('H', $gstamp), date('i', $gstamp), date('s', $gstamp));
}

// Since 1.5.0: Force relocation
function cn_relocation($url)
{
    header("Location: $url");
    echo '<html><head><title>Redirect...</title><meta http-equiv="refresh" content="0;url='.cn_htmlspecialchars($url).'"></head><body>'.i18n('Please wait... Redirecting to ').cn_htmlspecialchars($url).'...<br/><br/></body></html>';
    die();
}

// Since 1.4.x
function bd_config($str) { return base64_decode($str); }

// Since 1.5.1: Validate email
function check_email($email)
{
    return (preg_match("/^[\.A-z0-9_\-\+]+[@][A-z0-9_\-]+([.][A-z0-9_\-]+)+[A-z]{1,4}$/", $email));
}

// Since 1.5.3: Get variable from cache
function mcache_get($name)
{
    global $_CN_SESS_CACHE;
    return isset($_CN_SESS_CACHE[$name]) ? $_CN_SESS_CACHE[$name] : FALSE;
}

// Since 1.5.3: Set cache variable
function mcache_set($name, $var)
{
    global $_CN_SESS_CACHE;
    $_CN_SESS_CACHE[$name] = $var;
}

// Since 2.0: "Clever" Truncature for HTML tags
function clever_truncate($html, $ln = 75)
{
    // Result
    $R = '';

    // Word Count
    $C = 0;

    // IS HTML
    if (preg_match_all('/<([^>]+)>/s', $html, $fetch, PREG_SET_ORDER))
    {
        // Tag depth
        $D = array();

        // Next In
        $N = 0;

        // List of single html tags
        $SGL = array('img', 'br', 'area', 'base', 'basefont', 'bgsound', 'col', 'command', 'embed', 'hr', 'input', 'isindex', 'keygen', 'link', 'meta', 'param', 'source', 'track', 'wbr');

        foreach ($fetch as $ft)
        {
            // Get inner tag
            $T = trim($ft[1]);

            // Search next HTML tag
            $S = strpos($html, $ft[0], $N);

            // String before
            $SE = trim(substr($html, $N, $S - $N));
            $N  = $S + strlen($ft[0]);

            // Tag detection
            $G = strtolower($ft[1]);

            $W2 = explode(' ', $SE);
            if ($SE) foreach ($W2 as $w) if (trim($w))
            {
                $C++; $R .= " $w ";

                // Close ALL tags before and exit
                if ($C == $ln) { $R .= '...'; foreach ($D as $t) $R .= "</$t>"; return $R; }
            }

            // Append next tag
            $R .= $ft[0];

            if ($T[0] === '/')
            {
                // Overflow protection
                if (count($D)) array_shift($D);
            }
            // Is not single hmtl tag
            elseif (!in_array($G, $SGL) && !preg_match('/\/\s*>/', $ft[0]))
            {
                if (preg_match('/^\w+/', $G, $c))
                    array_unshift($D, $c[0]);
                else array_unshift($D, NULL);
            }
        }

        // Estimated...
        $SE = substr($html, $N);
        $W2 = explode(' ', $SE);
        if ($SE) foreach ($W2 as $w) if (trim($w))
        {
            $C++; $R .= " $w ";
            if ($C == $ln) { $R .= '...'; break; }
        }

        // HTML autocomplete
        foreach ($D as $t) $R .= "</$t>";
    }
    // Text
    elseif ($html)
    {
        $W2 = explode(' ', $html);
        foreach ($W2 as $_id => $w) if (trim($w))
        {
            $C++; $R .= $_id ? " $w" : "$w";
            if ($C == $ln) return "$R...";
        }
    }

    return $R;
}

// Since 2.0: Get option from #CFG or [%site][<opt_name>]
// Usage: #level1/level2/.../levelN or 'option_name' from %site
function getoption($opt_name = '')
{
    $cfg = mcache_get('config');

    if ($opt_name === '')
    {
        return $cfg;
    }
    if ($opt_name[0] == '#')
    {
        $cfn = spsep(substr($opt_name, 1), '/');
        foreach ($cfn as $id)
        {
            if (isset($cfg[$id])) 
            {
                $cfg = $cfg[$id];
            }
            else
            {
                $cfg = array();
                break;
            }
        }

        return $cfg;
    }
    else
    {
        return isset($cfg['%site'][$opt_name]) ? $cfg['%site'][$opt_name] : FALSE;
    }
}

// Since 2.0: @Helper recursive function
function setoption_rc($names, $var, $cfg)
{
    $the_name = array_shift($names);

    if (count($names) == 0)
    {
        $cfg[$the_name] = $var;
    }
    else
    {
        if (!isset($cfg[$the_name])) { $cfg[$the_name] = ''; }
        $cfg[$the_name] = setoption_rc($names, $var, $cfg[$the_name]);
    }

    return $cfg;
}

// Since 2.0: Save option to config
// Usage: #level1/level2/.../levelN or 'option_name' from %site
function setoption($opt_name, $var)
{
    $cfg = mcache_get('config');

    if ($opt_name[0] == '#')
    {
        $c_names = spsep(substr($opt_name, 1), '/');
        $cfg = setoption_rc($c_names, $var, $cfg);
    }
    else
    {
        $cfg['%site'][$opt_name] = $var;
    }

    cn_config_save($cfg);
}

$preg_sanitize_af = array();
$preg_sanitize_at = array();

// Since 1.5.0
// Sanitize regexp [$rev=true -- revert]
function preg_sanitize($s, $rev = false)
{
    global $preg_sanitize_af, $preg_sanitize_at;

    if (empty($preg_sanitize_af) && empty($preg_sanitize_at))
    {
        $codes = '/\\#!=~.|[]+*?()-{}$^';
        for ($i = 0; $i < strlen($codes); $i++)
        {
            $preg_sanitize_af[] = $codes[$i];
            $preg_sanitize_at[] = '\\x' . dechex(ord($codes[$i]));
        }
    }

    if ($rev)
    {
         return str_replace($preg_sanitize_at, $preg_sanitize_af, $s);
    }
    else 
    {
        return str_replace($preg_sanitize_af, $preg_sanitize_at, $s);
    }
}

// Since 1.5.0
// Separate string to array: imporved "explode" function
function spsep($separated_string, $seps = ',')
{
    if (strlen($separated_string) == 0 ) 
    {
        return array();
    }
    $ss = explode($seps, $separated_string);
    return $ss;
}

// Since 2.0: Check server request type
function request_type($type = 'POST')
{
    return $_SERVER['REQUEST_METHOD'] === $type ? TRUE : FALSE;
}

// Since 2.0: Current adjusted time
function ctime()
{
    return time() + 60*getoption('date_adjust');
}

/*  ---------- Sanitize: get POST vars (default) --------
    POST [def] only POST
    GET only GET
    POSTGET -- or POST or GET
    GETPOST -- or GET or POST
    REQUEST -- from REQUEST
    COOKIES -- from COOKIES
    GLOB -- from GLOBALS
    + combination (comma separated)
*/

// Since 1.5.3
function GET($var, $method = 'GETPOST')
{
    $result = array();
    $vars   = spsep($var);
    $method = strtoupper($method);

    if ($method == 'GETPOST') 
    {
        $methods = array('GET','POST');
    }
    elseif ($method == 'POSTGET') 
    {
        $methods = array('POST','GET');
    }
    elseif ($method == 'GPG') 
    {
        $methods = array('POST','GET','GLOB');
    }
    else 
    {
        $methods = spsep($method);
    }

    foreach ( $vars as $var )
    {
        $var = trim($var);
        $value = null;

        foreach ($methods as $method)
        {
            if ($method == 'GLOB' && isset($GLOBALS[$var])) 
            {
                $value = $GLOBALS[$var];
            }
            elseif ($method == 'POST' && isset($_POST[$var])) 
            {
                $value = $_POST[$var];
            }
            elseif ($method == 'GET' && isset($_GET[$var])) 
            {
                $value = $_GET[$var];
            }
            elseif ($method == 'POSTGET')
            {
                if (isset($_POST[$var])) 
                {
                    $value = $_POST[$var];
                }
                elseif (isset($_GET[$var])) 
                {
                    $value = $_GET[$var];
                }
            }
            elseif ($method == 'GETPOST')
            {
                if (isset($_GET[$var]))
                {
                    $value = $_GET[$var];
                }
                elseif (isset($_POST[$var])) 
                {
                    $value = $_POST[$var];
                }
            }
            elseif ($method == 'REQUEST' && isset($_REQUEST[$var])) 
            {
                $value = $_REQUEST[$var];
            }
            elseif ($method == 'COOKIE' && isset($_COOKIE[$var])) 
            {
                $value = $_COOKIE[$var];
            }

            if (!is_null($value)) 
            {
                break;
            }
        }

        $result[] = $value;
    }
    return $result;
}

// Since 1.5.3
// GET Helper for single value
// $method[0] = * ---> htmlspecialchars ON
function REQ($var, $method = 'GETPOST')
{
    if ($method[0] == '*')
    {
        list($value) = ( GET($var, substr($method, 1)) );
        return cn_htmlspecialchars($value);
    }
    else
    {    
        list($value) = GET($var, $method);
        return $value;
    }
}

// Since 1.5.3: Member get (current or any)
function member_get()
{
    // Not authorized
    if (empty($_SESSION['user']))
    {
        return NULL;
    }

    // No in cache
    if ($member = mcache_get('#member'))
    {
        return $member;
    }

    mcache_set('#member', $user = db_user_by_name($_SESSION['user']));
    return $user;
}

// Since 2.0: @bootstrap
function cn_detect_user_ip()
{
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
    {
        $IP = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    elseif (isset($_SERVER['HTTP_CLIENT_IP']))
    {
        $IP = $_SERVER['HTTP_CLIENT_IP'];
    }
            
    if (empty($IP) && isset($_SERVER['REMOTE_ADDR'])) 
    {
        $IP = $_SERVER['REMOTE_ADDR'];
    }
    if (empty($IP)) 
    {
        $IP = false;
    }

    if (!preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/', $IP))
    {
        $IP = '';
    }
    
    define('CLIENT_IP', $IP);
    // CRYPT_SALT consists an IP
    define('CRYPT_SALT', (getoption('ipauth') == '1'? CLIENT_IP : '').'@'.getoption('#crypt_salt'));    
}

// Since 2.0: @bootstrap
function cn_load_skin()
{
    // $config_skin = preg_replace('~[^a-z]~i','', getoption('skin')); // @deprecated?
    if (file_exists($master_skin = SERVDIR."/skins/master.skin.php")) {
        include($master_skin);
    } else {
        die("Can't load skin $master_skin");
    }
}

// Since 2.0: Create file
function cn_touch($fn, $php_safe = FALSE)
{
    if (!file_exists($fn))
    {
        $w = fopen($fn, 'w+');

        if ($php_safe) 
        {
            fwrite($w, "<?php die('Direct call - access denied'); ?>\n");
        }
        fclose($w);
    }

    return $fn;
}

// Since 2.0: Save serialized array
function cn_fsave($dest, $data = array())
{  
    $fn = $dest;
    $bk = $fn.'-'.mt_rand().'.bak';

    $w = fopen($bk, 'w+') or die("Can't save data at [$bk]");
    fwrite($w, "<?php die('Direct call - access denied'); ?>\n");
    fwrite($w, base64_encode(serialize($data)));
    fclose($w);
    
    return rename($bk, $fn);
}

// Since 2.0: Read serialized array from php-safe file (or create file)
function cn_touch_get($target)
{
    $fn = cn_touch($target, TRUE);
    $fc = file($fn); 
    unset($fc[0]);

    $fc = join('', $fc);

    if (!$fc)
    {
        $fc = array(); 
    }
    else
    {
        $data = unserialize(base64_decode($fc));        
        if ($data === FALSE)
        {
            $fc = unserialize($fc);
        }
        else 
        {
            $fc = $data;
        }
    }
        
    return $fc;
}

// Since 2.0: @bootstrap
function cn_load_plugins()
{
    global $_HOOKS;

    $_HOOKS = array();
    if (is_dir(cn_path_construct( SERVDIR, 'cdata','plugins')))
    {
        $plugins = scan_dir( cn_path_construct(SERVDIR , 'cdata','plugins'));
        foreach ($plugins as $plugin)
        {
            if (preg_match('~\.php$~i', $plugin))
            {
                include (SERVDIR . '/cdata/plugins/' . $plugin);
            }
        }
    }
}

// Since 2.0: @bootstrap
function cn_load_session()
{
    session_name('CUTENEWS_SESSION');
    session_start();

    if(isset($_COOKIE['session']) && ($user = cn_cookie_restore()))
    {        
        $_SESSION['user'] =  $user;
    }
}

// Since 2.0: Users online
function cn_online_counter()
{
    if ($expire = getoption('client_online'))
    {
        $online = cn_touch_get(cn_path_construct(SERVDIR, 'cdata'). 'online.php');

        $ct       = time();
        $uniq     = array();
        $online[] = $ct.'|'.CLIENT_IP;

        foreach ($online as $id => $v)
        {
            if ($id == '%')
            {
                continue;
            }
            
            list($t, $ip) = explode('|', $v);
            if ($t < $ct - $expire)
            {
                unset($online[$id]);
            }
            else 
            {
                $uniq[$ip]++;
            }
        }

        $online['%'] = $uniq;
        cn_fsave(cn_path_construct(SERVDIR, 'cdata'). 'online.php', $online);
    }
}

// Since 1.5.0: Hash type MD5 and SHA256
function hash_generate($password, $md5hash = false)
{
    $try = array
    (
        0 => md5($password),
        1 => utf8decrypt($password, $md5hash),
        2 => SHA256_hash($password),
    );

    return $try;
}

// Since 2.0: @bootstrap in case if UTF-8 used in Admin Panel
function cn_sendheaders()
{
    header( 'X-Frame-Options:sameorigin' );
    header( 'Expires: Sat, 26 Jul 1997 05:00:00 GMT' );
    header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
    header( 'Cache-Control: no-store, no-cache, must-revalidate' );
    header( 'Cache-Control: post-check=0, pre-check=0', false );
    header( 'Pragma: no-cache' );

    if (getoption('useutf8'))
    {
        header( 'Content-Type: text/html; charset=UTF-8', true);
        header( 'Accept-Charset: UTF-8', true);
    }

    b64dck();
}

// Since 2.0: security reason
function check_direct_including($incln)
{
    global $PHP_SELF;

    $Uri = '//'.dirname( $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );

    if (strpos(getoption('http_script_dir'), $Uri) !== false && strpos($PHP_SELF, $incln) !== false)
    {
        die(proc_tpl('help/manual/wrong_include', array('category' => REQ('category','GPG'))));
    }
}

// Since 2.0: Get messages
function cn_get_message($area, $method = 's') // s-show, c-count
{
    $es = mcache_get('msg:stor');
    if(isset($es[$area]))
    {
        if ($method == 's') return $es[$area];
        elseif ($method == 'c') return count($es[$area]);
    }
    return null;
}

// Since 2.0: Add message
function cn_throw_message($msg, $area = 'n')
{
    $es = mcache_get('msg:stor');

    if (!isset($es[$area])) $es[$area] = array();
    $es[$area][] = i18n($msg);

    mcache_set('msg:stor', $es);
    return FALSE;
}

// Since 2.0
function cn_decomposite_options($options)
{
    if (is_array($options))
    {
        return $options;
    }
    
    $result = array();
    $_options = explode(',', $options);
    foreach ($_options as $vc)
    {
        list($a, $b) = explode('=', $vc, 2);
        $result[$a] = $b;
    }

    return $result;
}

// Since 2.0: Translate category names to ids
function cn_category_trans($cts, $_ct_names, $_cat_conf)
{
    $outc = array();
    $_cts = spsep($cts);

    foreach ($_cts as $cat_id)
    {
        if (preg_match('/[a-z]/i', $cat_id))
        {
            if (isset($_ct_names[$cat_id]))
            {
                $cat_id = $_ct_names[$cat_id];
                $outc[$cat_id] = $cat_id;
            }
        }
        else
        {
            $outc[$cat_id] = $cat_id;
        }

        // Subtree category expanded
        if (isset($_cat_conf[$cat_id]) && $_cat_conf[$cat_id]['ac'])
        {
            foreach ($_cat_conf[$cat_id]['ac'] as $expand_id)
            {
                $outc[$expand_id] = $expand_id;
            }
        }
    }

    return $outc;
}

// Since 2.0: basic function: decode category + ucat/nocategory
function cn_get_requested_cats($category, $ucat = '', $nocategory = '')
{
    /* $is_in_category == 1 in case
     *
     * 1. category exists, no ucat, show all
     * 2. category exists, $ucat intersect - show; else don't show
     * 3. category not exists, show all
     *
     * after all, decode by category name
     */

    // Make aliases
    $_ct_names = array();
    $cat_conf  = getoption('#category');
    foreach ($cat_conf as $id => $cn) {
        if ($id !== '#') {
            $_ct_names[$cn['name']] = $id;
        }
    }

    // decode categories name to id
    $_rq_category = cn_category_trans($category, $_ct_names, $cat_conf);
    $_uc_category = cn_category_trans($ucat, $_ct_names, $cat_conf);
    $_no_category = cn_category_trans($nocategory, $_ct_names, $cat_conf);

    $requested_cats = array();
    $is_in_category = 1;

    foreach ($_rq_category as $_cat => $_t) 
    {        
        if ($_cat)
        {
            // no-category param erase category ids
            if (isset($_no_category[$_cat])) 
            {
                continue;
            }

            // ucat exists, check each entries
            if (count($_uc_category) && !isset($_uc_category[$_cat])) 
            {
                continue;
            }

            $requested_cats[$_cat] = 1;
        }
    }
    
    // 'nocategory' or/and 'ucat' erase all category id's - don't show news
    if (count($_rq_category) && count($requested_cats) == 0) 
    {
        $is_in_category = 0;
    }

    return array($requested_cats, $is_in_category);
}

// Since 2.0: Modify inclusion parameters by filter
function cn_translate_active_news($entry, $translate)
{
    global $PHP_SELF, $_bc_PHP_SELF;

    // Reset: If PHP_SELF is not changed
    $PHP_SELF = $_bc_PHP_SELF;

    // Disable Rewrite (once)
    mcache_get(':disable_rw', FALSE);

    if (empty($translate) || !is_array($translate))
    {
        return null;
    }
    // Check filters
    $apply = array();
    foreach ($translate as $rule => $changes)
    {
        list($ID, $RULE) = explode('=', $rule, 2);

        if ($ID == 'category' && array_intersect( spsep($RULE), spsep($entry['c']) ))
        {
            $apply[] = $changes;
        }
    }

    // Apply changes, if exists
    foreach ($apply as $sh)
    {
        list($ID, $value) = explode('=', $sh, 2);
        if ($ID == 'php_self') 
        {
            $PHP_SELF = $value;
        }
        if ($ID == ':disable_rw') 
        {
            mcache_get(':disable_rw', TRUE);
        }
    }
}

// Engine V2.0 ---------------------------------------------------------------------------------------------------------

// Since 2.0: Replace text with holders
function cn_replace_text()
{
    $args = func_get_args();
    $text = array_shift($args);
    $replace_holders = explode(',', array_shift($args));

    foreach ($replace_holders as $holder)
    {
        $text = str_replace(trim($holder), array_shift($args), $text);
    }

    return $text;
}

// Since 2.0: Decode "defaults/templates" to list
function cn_template_list()
{
    $config = file(cn_path_construct( SKIN,'defaults').'templates.tpl');
    $tbasic  = getoption('#templates_basic');    
    $tbasic['hash']=isset($tbasic['hash'])?$tbasic['hash']:'';
    
    // template file is changed
    if ($tbasic['hash'] !== ($nhash = md5(join(',', $config))))
    {
        $templates        = array();
        $current_tpl_name = $_tpl_var = '';

        foreach ($config as $line)
        {
            if ($line[0] == '#')
            {
                $current_tpl_name = trim(substr($line, 1));
                $templates[ $current_tpl_name ] = array();
                continue;
            }

            // Subtemplate markers
            if ($line[0] == '*')
            {
                $_tpl_var = trim(substr($line, 1));
                if ($_tpl_var) $template_vars[$_tpl_var] = '';
            }
            // Subtemplate codes
            elseif (preg_match('/\s/', $line[0]) || $line[0] === '')
            {
                if(isset($templates[ $current_tpl_name ][$_tpl_var]))
                {
                    $templates[ $current_tpl_name ][$_tpl_var] .= substr($line, 1);
                }
                else
                {
                    $templates[ $current_tpl_name ][$_tpl_var] = substr($line, 1);
                }
            }
        }

        // set <change hash> var and parsed templates
        $tbasic['hash'] = $nhash;
        $tbasic['templates'] = $templates;

        setoption('#templates_basic', $tbasic);
    }

    return isset($tbasic['templates'])?$tbasic['templates']:array();
}

// Since 2.0: Get template (if not exists, create from defaults)
function cn_get_template($subtemplate, $template_name = 'default')
{
    $templates      = getoption('#templates');
    $template_name  = strtolower($template_name);

    // User template not exists in config... get from defaults
    if (isset($templates[$template_name]))
    {
        return $templates[$template_name][$subtemplate];
    }
    
    $list = cn_template_list();             
    if(isset($list[$template_name][$subtemplate]))
    {
        return $list[$template_name][$subtemplate];
    }
    
    return false;
}

// Since 2.0: Replace all {name} and [name..] .. [/name] in template file
function entry_make($entry, $template_name, $template_glob = 'default', $section = '')
{
    global $_raw_md5;

    $_raw_md5 = array();
    $template = cn_get_template($template_name, strtolower($template_glob));

    // Get raw data
    list($template, $raw_vars) = cn_extrn_raw_template($template);

    // Extrn function for replace
    $template = cn_extrn_morefields($template, $entry, $section);

    // Hooks before
    list($template) = hook('core/entry_make_start', array($template, $entry, $template_name, $template_glob));
    
    // Catch { ... }
    if (preg_match_all('/\{(.*?)\}/is', $template, $tpls, PREG_SET_ORDER) ) {

        foreach ($tpls as $tpl) {

            $result = '';
            $tplp = explode('|', $tpl[1], 2);
            $tplc = isset($tplp[0])?$tplp[0]:'';
            $tpla = isset($tplp[1])?$tplp[1]:'';

            // send modifiers
            $short  = "cn_modify_" . ($section ? $section.'_' : "");
            $short .= preg_replace('/[^a-z]/i', '_', $tplc);
            if (function_exists($short)) {
                $result = call_user_func($short, $entry, explode('|', $tpla));
            }
            $template = str_replace($tpl[0], $result, $template);
        }
    }

    // Extern function [middle]
    $template = cn_extrn_if_cond($template);

    // Hooks middle
    list($template) = hook('core/entry_make_mid', array($template, $entry, $template_name, $template_glob));

    // Catch[bb-tag]...[/bb-tag]
    if (preg_match_all('/\[([\w-]+)(.*?)\](.*?)\[\/\\1\]/is', $template, $tpls, PREG_SET_ORDER) ) {

        foreach ($tpls as $tpl) {

            $result = '';
            $short  = "cn_modify_bb_" . ($section ? $section.'_' : "");
            $short .= preg_replace('/[^a-z]/i', '_', $tpl[1]);
            if (function_exists($short)) {
                $result = call_user_func($short, $entry, $tpl[3], $tpl[2]); // entry, text, options
            }

            $template = str_replace($tpl[0], $result, $template);
        }
    }

    // Hooked
    list($template) = hook('core/entry_make_end', array($template, $entry, $template_name, $template_glob));

    // UTF-8 -- convert to entities on frontend
    if ($section == 'comm' && getoption('comment_utf8html')) {
        $template = UTF8ToEntities($template);
    } elseif (!$section && getoption('utf8html')) {
        $template = UTF8ToEntities($template);
    }
    
    // Return raw data
    list($template) = cn_extrn_raw_template($template, $raw_vars);
    return $template;
}

// URL LIBRARY ---------------------------------------------------------------------------------------------------------

// Since 2.0: Grab from $_POST all parameters
function cn_parse_url()
{
    // Decode post data
    $post_data = array();

    if (isset($_POST['__post_data'])) {
        $post_data = unserialize(base64_decode($_POST['__post_data']));
    }

    // A. Click "confirm"
    if (REQ('__my_confirm') == '_confirmed')
    {
        // In case if exists another data from form
        $APPEND = isset($_POST['__append']) ? $_POST['__append'] : array();

        $_POST  = $post_data;
        $_POST['__my_confirm'] = '_confirmed';

        // Return additional parameters in POST
        if (is_array($APPEND)) foreach ($APPEND as $id => $v) $_POST[$id] = $v;

        return TRUE;
    }
    // B. Click "decline"
    elseif (REQ('__my_confirm') == '_decline')
    {
        $_POST['__referer'] = $post_data['__referer'];
        return FALSE;
    }
    // C. First access
    else
    {
        $_POST['__referer'] = isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'';
    }

    // Set POST required params to GET
    if (REQ('mod', 'POST')) $_GET['mod'] = REQ('mod', 'POST');
    if (REQ('opt', 'POST')) $_GET['opt'] = REQ('opt', 'POST');
    if (REQ('sub', 'POST')) $_GET['sub'] = REQ('sub', 'POST');

    // Unset signature dsi
    unset($_GET['__signature_key'], $_GET['__signature_dsi']);

    return FALSE;
}

// Since 2.0: Pack only required parameters
function cn_pack_url($GET, $URL = PHP_SELF)
{
    $url = $result = array();

    foreach ($GET as $k => $v) if ($v !== '') $result[$k] = $v;
    foreach ($result as $k => $v) $url[] = "$k=".urlencode($v);

    list($ResURL) = hook('core/url_rewrite', array( $URL . ($url ? '?' . join('&', $url) : '' ), $URL, $GET ) );
    return $ResURL;
}

// Since 2.0: Set GET params
function cn_set_GET($e)
{
    $ex = spsep($e);
    foreach ($ex as $id)
    {                
        if ($dt = REQ($id, 'GPG'))
        {            
            if(is_array($dt))
            {
                //use only string
                $dt=array_pop($dt);
            }
            
            $idp = explode('=', $id, 2);
            $id  = isset($idp[0])?$idp[0]:false; 
            $def = isset($idp[1])?$idp[1]:false;
            
            // By default, skip this
            if (isset($def) && $def && strtolower($def) == strtolower($dt))
            {
                continue;
            }
            
            if($id) 
            {
                $_GET[trim($id)] = trim($dt);
            }
        }
    }    
}

// Since 2.0: Remove from GET
function cn_rm_GET($e)
{
    $ex = spsep($e);
    foreach ($ex as $id)
        if (isset($_GET[$id]))
            unset($_GET[$id]);
}

// Since 2.0.1: Decode strings like a=b,b=c,d...
function cn_params($params)
{
    $opts = array();
    $vt = spsep($params);
    foreach ($vt as $v0)
    {
        list($a, $b) = explode('=', $v0, 2);

        if (is_null($b)) $b = TRUE;
        $opts[$a] = $b;
    }
    return $opts;
}

// Since 2.0: modify $_GET (+/-) and result is url string
function cn_url_modify()
{
    global $PHP_SELF;

    $GET = $_GET;    
    $args = func_get_args();
    $SN   = $PHP_SELF;

    // add new params
    foreach ($args as $ks)
    {
        // 1) Control
        if (is_array($ks))
        {
            foreach ($ks as $vs)
            {
                $id=$val='';
                
                if(strpos($vs, '=')!==FALSE)
                {
                    list($id, $var) = explode('=', $vs, 2);
                }
                else
                {
                    $id=$vs;
                }
                if ($id == 'self') 
                {
                    $SN = $var;
                }
                elseif ($id == 'reset') 
                {
                    $GET = array();
                }
                elseif ($id == 'group') 
                {
                    foreach ($vs as $a => $b) 
                    {
                        $GET[$a] = $b;
                    }
                }
            }
        }
        // 2) Subtract
        elseif (strpos($ks, '=') === FALSE)
        {
            $keys = explode(',', $ks);

            foreach ($keys as $key)
            {
                $key = trim($key);
                if (isset($GET[$key])) 
                {
                    unset($GET[$key]);
                }
            }
        }
        // 3) Add
        else
        {
            list($k, $v) = explode('=', $ks, 2);

            $GET[$k] = $v;
            if ($v === '') 
            {
                unset($GET[$k]);
            }
        }
    }

    return cn_pack_url($GET, $SN);
}

// Since 2.0: Make 'Top menu'
function cn_get_menu()
{
    $modules = hook('core/cn_get_menu', array
    (
        'main'      => array('Cd', 'Dashboard'),
        'addnews'   => array('Can', 'Add News'),
        'editnews'  => array('Cvn', 'Edit News', NULL, 'source,year,mon,day,sort,dir'),
        'help'      => array('', 'Help/About', 'about'),
        'logout'    => array('', 'Logout', 'logout'),
    ));

    if (getoption('main_site')) {
        $modules['site'] = array( '#'.getoption('main_site'), 'Visit site', 'site');
    }

    $result = '';
    $mod = REQ('mod', 'GPG');

    foreach ($modules as $mod_key => $var)
    {
        $acl = isset($var[0]) ? $var[0] : false;
        $name = isset($var[1]) ? $var[1] : '';
        $title = isset($var[2]) ? $var[2] : '';
        $app = isset($var[3]) ? $var[3] : '';

        $link = null;

        // If # in start of ACL, it's reserved for DIRECT LINK
        // Else: for ACL test
        if ($acl) {
            if ($acl[0] == '#') {
                $link = substr($acl, 1);
            }
            else if (!test($acl)) {
                continue;
            }
        }

        if (isset($title) && $title) {
            $action = '&amp;action='.$title;
        } else {
            $action = '';
        }

         // active menu bootstrap Mod Shixel current
		if ($mod == $mod_key) {
            $select = ' active ';
        } else {
            $select = '';
        }

        // Append urls for menu (preserve place)
        if (isset($app) && $app) {

            $actions = array();
            $mv = spsep($app);

            foreach ($mv as $vx)
                if ($dt = REQ($vx))
                    $actions[] = "$vx=".urlencode($dt);

            if ($actions) $action .= '&amp;'.join('&amp;', $actions);
        }

        if (!$link) { // It is system mod switcher (no link)
            $link = PHP_SELF.'?mod=' . $mod_key . $action;
        }

		$result .= '<li class='.$select.'><a class="nav-top-item" href="'.$link.'">'.i18n($name).'</a></li>'; // Mod Shixel
    }

    return $result;
}

// Since 2.0: Unpack cookie for ACP
function cn_cookie_unpack($cookie)
{
    $list = array();

    $cookies = explode(',', $cookie);
    foreach ($cookies as $c) {
        $c = trim($c);
        if (isset($_COOKIE[$c])) {
            $list[] = unserialize( base64_decode($_COOKIE[$c]) );
        } else {
            $list[] = array();
        }
    }

    return $list;
}

// Since 2.0: Pack cookie for ACP
function cn_cookie_pack()
{
    $args   = func_get_args();
    $cookie = array_shift($args);

    $cookies = explode(',', $cookie);
    foreach ($cookies as $id => $cookie) {
        $cookie = trim($cookie);
        if ($args[$id]) {
            $data = base64_encode( serialize($args[$id]) ); 
        } else {
            $data = null;
        }
        setcookie($cookie, $data);
    }
}

// Since 2.0: Add user log
function cn_user_log($msg)
{
    if (!getoption('userlogs'))
    {
        return;
    }
    
    if (!file_exists($ul =  cn_path_construct(SERVDIR,'cdata','log'). 'user.log'))
    {
        fclose(fopen($ul, 'w+'));
    }
    
    $a = fopen($ul, 'a');
    fwrite($a, time().'|'.str_replace("\n", ' ', $msg)."\n");
    fclose($a);
}

// Since 2.0: File users.php not exists, call installation script
function cn_require_install()
{
    global $_SESS;

    if (defined('AREA') && AREA == 'ADMIN')
    {
        $_SESSION = array();
        include SERVDIR . '/skins/master.skin.php';

        // Submit
        if (request_type('POST'))
        {
            $username = REQ('username', 'POST');
            $pass1 = REQ('password1', 'POST');
            $pass2 = REQ('password2', 'POST');
            $email = REQ('email', 'POST');

            // Check Username
            if (!$username) 
            {
                cn_throw_message('Enter username', 'e');
            }
            elseif (strlen($username) < 2) 
            {
                cn_throw_message('Too short username (must be 2 char min)', 'e');
            }

            // Check Password
            if (!$pass1) 
            {
                cn_throw_message('Enter password', 'e');
            }
            elseif (strlen($pass1) < 4) 
            {
                cn_throw_message('Too short password (must be 4 char min)', 'e');
            }

            // Check email
            if (!check_email($email))
            {
                cn_throw_message('Invalid email', 'e');
            }
            
            if ($pass1 !== $pass2)
            {
                cn_throw_message("Confirm don't match", 'e');
            }
            // All OK
            if (cn_get_message('e', 'c') == 0)
            {
                // Add new user
                db_user_add($username, ACL_LEVEL_ADMIN);
                db_user_update($username, "email=$email", "pass=" . SHA256_hash($pass1));

                // Authorize user
                $_SESSION['user'] = $username;

                // Detect self pathes
                $SN = dirname($_SERVER['SCRIPT_NAME']);
                $script_path = "http://".$_SERVER['SERVER_NAME'] . (($SN == '/') ? '' : $SN);

                setoption('http_script_dir', $script_path);
                setoption('uploads_dir', cn_path_construct(SERVDIR , 'uploads'));
                setoption('uploads_ext',     $script_path . '/uploads');
                setoption('rw_layout',       SERVDIR .DIRECTORY_SEPARATOR. 'example.php');

                // Greets page
                cn_relocation("http://cutephp.com/thanks.php?referer=".urlencode(base64_encode('http://'.$_SERVER['SERVER_NAME'] . PHP_SELF)));
            }
        }

        // --- quick check permissions ---
        $pc = array
        (
            'cdata'         => FALSE,
            'uploads'       => FALSE,
            'cdata/news'    => FALSE,
            'cdata/btree'   => FALSE,
            'cdata/users'   => FALSE,
            'cdata/plugins' => FALSE,
            'cdata/backup'  => FALSE,
            'cdata/log'     => FALSE,
        );

        $permission_ok = TRUE;
        foreach ($pc as $id => $_t)
        {
            $fndir = cn_path_construct(SERVDIR,$id);                        
            if (is_dir($fndir) && is_writable($fndir))
            {
                $pc[$id] = TRUE;                
            }
            else 
            {
                $permission_ok = FALSE;
            }
        }

        cn_assign('pc, permission_ok', $pc, $permission_ok);
        echoheader('-@dashboard/style.css', 'Install CuteNews'); echo exec_tpl('install'); echofooter();
    }
}

function cn_cookie_remember($client = false)
{    
    // String serialize
    $cookie = strtr(base64_encode( xxtea_encrypt(serialize($_SESSION['user']), CRYPT_SALT) ), '=/+', '-_.');
    if ($client)
    {
        echo '<script type="text/javascript">cn_set_cookie("session", "'.$cookie.'")</script>';
        echo "<noscript>Your browser is not Javascript enable or you have turn it off. COOKIE not saved</noscript>";
    }
    else
    {
        setcookie('session', $cookie, time() + 60*60*24*2, '/');
    }
}

function cn_cookie_restore()
{       
    $xb64d = xxtea_decrypt( base64_decode( strtr($_COOKIE['session'], '-_.', '=/+') ), CRYPT_SALT );
    
    if($xb64d)
    {    
        return unserialize($xb64d);
    }
    
    return false;
}

function cn_cookie_unset()
{
    setcookie('session', '', 0, '/');
}

// Since 2.0.3: Logout user and clean session
function cn_logout($relocation=PHP_SELF)
{    
    cn_cookie_unset();
    session_unset();
    session_destroy();
    cn_relocation($relocation);        
}

// Since 2.0: Cutenews login routines
function cn_login()
{
    // Get logged username
    $logged_username = isset($_SESSION['user']) ? $_SESSION['user'] : FALSE;
    
    // Check user exists. If user logged, but not exists, logout now
    if ($logged_username && !db_user_by_name($logged_username))
    {        
        cn_logout();
    }

    $is_logged = false; 

    list($action) = GET('action', 'GET,POST');
    list($username, $password, $remember) = GET('username, password, rememberme', 'POST');
    
    // user not authorized now
    if (!$logged_username)
    {
        // last url for return after user logged in
        if ($_SERVER['REQUEST_METHOD'] == 'GET')
        {
            $_SESSION['RQU'] = preg_replace('/[^\/\.\?\=\&a-z_0-9]/i', '', $_SERVER['REQUEST_URI']);
        }
        
        if ($action == 'dologin')
        {
            if ($username && $password)
            {
                $member   = db_user_by_name($username);
                $ban_time = isset($member['ban']) ? (int)$member['ban'] : 0;

                // ban limit
                if ($ban_time && $ban_time > time())
                {
                    msg_info('Too frequent queries. Wait '.($ban_time - time().' sec.'));
                }
                
                $compares = hash_generate($password);

                if (!isset($member['pass'])) { $member['pass'] = ''; }

                if (in_array($member['pass'], $compares))
                {
                    $is_logged = true;
                    
                    // set user to session
                    $_SESSION['user'] = $username;

                    // Save remember flag
                    $_SESSION['@rem'] = $remember;

                    if ($remember) {
                        cn_cookie_remember();
                    }

                    // save last login status, clear ban
                    db_user_update($username, 'lts='.time(), 'ban=0');
                    
                    // send return header (if exists)
                    if (isset($_SESSION['RQU']))
                    {                        
                        cn_relocation($_SESSION['RQU']);
                    }                    
                }
                else
                {
                    cn_throw_message("Invalid password or login", 'e');
                    cn_user_log('User "'.substr($username, 0, 32).'" ('.CLIENT_IP.') login failed');

                    db_user_update($username, 'ban='.(time() + getoption('ban_attempts')));
                }
            }
            else 
            {
                cn_throw_message('Enter login or password', 'e');
            }
        }
    }
    else
    {
        $is_logged=true;
    }
    // --------
    if ($action == 'logout')
    {
        $is_logged = false;
        cn_logout();
    }

    // clear require url
    if ($is_logged && isset($_SESSION['RQU']))
    {
        unset($_SESSION['RQU']);
    }
        
    return $is_logged;
}

// Since 2.0: Save auth data for Guest user
function cn_guest_auth($name, $email, $client = TRUE)
{
    $_SESSION['guest_name']  = $name;
    $_SESSION['guest_email'] = $email;
}

// Since 2.0: Show login form
function cn_login_form($admin = TRUE)
{
    if ($admin)
    {
        echoheader("user", i18n("Please Login"));
    }

    echo exec_tpl('auth/login');

    if ($admin)
    {
        echofooter();
        die();
    }
}

// Since 2.0: Show register form
function cn_register_form($admin = TRUE)
{
    global $_SESS;

    $flatDb = new FlatDB();

    // Restore active status
    if (isset($_GET['lostpass']) && $_GET['lostpass'])
    {
        $d_string = base64_decode($_GET['lostpass']);
        $d_string = xxtea_decrypt($d_string, MD5(CLIENT_IP) . getoption('#crypt_salt') );
        $d_string = substr($d_string, 64);

        if ($d_string)
        {
            list(,$d_username) = explode(' ', $d_string, 2);

            // All OK: authorize user
            $_SESSION['user'] = $d_username;

            cn_relocation(cn_url_modify('lostpass'));
            die();
        }

        msg_info('Fail: invalid string');
    }

    // Resend activation
    if (request_type('POST') && isset($_POST['register']) && isset($_POST['lostpass']))
    {
        $user = db_user_by_name(REQ('username'));

        if (is_null($user)) { msg_info('User not exists'); }

        $email = isset($user['email']) ? $user['email'] : '';

        // Check user name & mail
        if ($user && $email && $email == REQ('email'))
        {
            $rand = '';
            $set = 'qwertyuiop[],./!@#$%^&*()_asdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM';
            for ($i = 0; $i < 64; $i++) $rand .= $set[ mt_rand() % strlen($set) ];

            $secret = str_replace(' ', '', REQ('secret'));

            $url = getoption('http_script_dir') . '?lostpass='.urlencode( base64_encode( xxtea_encrypt( $rand . $secret . ' ' . REQ('username'), MD5(CLIENT_IP) . getoption('#crypt_salt') ) ) );
            cn_send_mail($user['email'], i18n('Resend activation link'), cn_replace_text( cn_get_template('resend_activate_account', 'mail'), '%username%, %url%, %secret%', $user['name'], $url, $secret ) );

            msg_info('For you send activate link');
        }

        msg_info('Enter required field: email');
    }

    // is not registration form
    if (is_null(REQ('register','GET')))
        return FALSE;

    // Lost password: disabled registration - no affected
    if (!is_null(REQ('lostpass', 'GET')))
    {
        $Action = 'Lost password';
        $template = 'auth/lost';
    }
    else
    {
        if (getoption('allow_registration'))
        {
            $Register_OK = FALSE;
            $errors = array();
            list($regusername, $regnickname, $regpassword, $confirm, $regemail, $captcha) = GET('regusername, regnickname, regpassword, confirm, regemail, captcha', "POST");

            // Do register
            if ($_SERVER['REQUEST_METHOD'] == 'POST')
            {
                if ($regusername === '') $errors[] = i18n("Username field can't be blank");
                if ($regemail === '')    $errors[] = i18n("Email field can't be blank");
                if ($regpassword === '') $errors[] = i18n("Password field can't be blank");
                if (!preg_match('/[\w]\@[\w]/i', $regemail)) $errors[] = i18n("Email is invalid");

                if ($regpassword !== $confirm) $errors[] = i18n("Confirm password not match");
                if ($captcha !== $_SESSION['CSW']) $errors[] = i18n("Captcha not match");

                if (strlen($regpassword) < 3) $errors[] = i18n('Too short password');

                // Do register
                if (empty($errors))
                {
                    // get real user in index file
                    $user = $flatDb->user_lookup($regusername);

                    if (is_null($user))
                    {
                        $user = db_user_by($regemail, 'email');

                        if (is_null($user))
                        {
                            $pass = SHA256_hash($regpassword);
                            $acl_groupid_default = intval(getoption('registration_level'));

                            db_user_add($regusername, $acl_groupid_default);
                            db_user_update($regusername, "email=$regemail", "name=$regusername", "nick=$regnickname", "pass=$pass", "acl=$acl_groupid_default");

                            $Register_OK = TRUE;
                        }
                        else { $errors[] = i18n("Email already exists"); }
                    }
                    else
                    {
                        $errors[] = i18n("Username already exists");
                    }
                }

                // Registration OK, authorize user
                if ($Register_OK === TRUE)
                {
                    $_SESSION['user'] = $regusername;

                    // Clean old data
                    if (isset($_SESSION['RQU'])) 
                    {
                        unset($_SESSION['RQU']);
                    }
                    
                    if (isset($_SESSION['CSW'])) 
                    {
                        unset($_SESSION['CSW']);
                    }

                    // Send notify about register
                    if (getoption('notify_registration'))
                    {
                        cn_send_mail(getoption('notify_email'), i18n("New registration"), i18n("User %1 (email: %2) registered", $regusername, $regemail));
                    }

                    header('Location: '.PHP_SELF);
                    die();
                }
            }
            cn_assign('errors_result, regusername, regnickname, regemail', $errors, $regusername, $regnickname, $regemail);
        }
        else
        {
            msg_info(i18n('Registration disabled'));
        }

        $Action = 'Register user';
        $template = 'auth/register';
    }

    if (empty($template))
    {
        return FALSE;
    }
    
    if ($admin) 
    {
        echoheader('Register', $Action);
    }
    echo exec_tpl( $template );
    if ($admin)
    {
        echofooter();
        die();
    }

    return TRUE;
}

// Since 2.0: Cutenews HtmlSpecialChars
function cn_htmlspecialchars($str)
{
    $key = array('&'=>'&amp;','"' => '&quot;', "'" => '&#039;', '<' => '&lt;', '>' => '&gt;');
    $matches=null;
    preg_match('/(&amp;)+?/', $str,$matches);
    if(count($matches)!=0) 
    {
        array_shift($key);
    }
    return str_replace(array_keys($key), array_values($key), $str);
}

function cn_unhtmlspecialchars($str)
{
    $key = array( '&quot;'=>'"' ,   '&#039;'=>"'",   '&lt;'=>'<',   '&gt;'=>'>', '&amp;'=>'&');
    return str_replace(array_keys($key),  array_values($key), $str);
}

/*
 * Since 2.0: Clear html from tags and javascript
 * 
 * @param string $str text for claring
 * 
 * @return string clear html text
 */
function cn_htmlclear($str)
{
    $matches = array();

    // Cut <script> tags
    $str = preg_replace('/<\s*script[^>]*>.*?<\s*\/\s*script\s*>/is', '', $str);

    // Also, unclosed script tag
    $str = preg_replace('/<\s*script[^>]*>.*/is', '', $str);

    if (preg_match_all('/<[^>]+>/is', $str, $matches, PREG_PATTERN_ORDER))
    {            
        foreach ($matches[0] as $match)
        {                                    
            if (trim($match) != '')
            {
                $tag = preg_replace('/\son[^=]+=([^\s\'"]*)/is', '', $match);
                $str = str_replace($match, $tag, $str);
            }
        }
    }

    return $str;
}

// Since 2.0: Check CSRF challenge
function cn_dsi_check()
{
    list($key, $dsi) = GET('__signature_key, __signature_dsi', 'GETPOST');

    if (empty($key) && empty($dsi))
    {
        list($dsi_inline) = GET('__signature_dsi_inline', 'GETPOST');

        if ($dsi_inline)
        {
            list($dsi, $key) = explode('.', $dsi_inline, 2);
        }
        else
        {
            die('CSRF attempt! No data');
        }

        // cn_url_modify
        unset($_GET['__signature_dsi_inline']);
    }
    else
    {
        // cn_url_modify
        unset($_GET['__signature_key'], $_GET['__signature_dsi']);
    }

    $member = member_get();
    list(,$username) = explode('-', $key, 2);

    if ($member['name'] !== $username)
        die('CSRF attempt! Username invalid');

    // Get signature
    $signature = MD5( $key . $member['pass'] . MD5(getoption('#crypt_salt')) );

    if ($dsi !== $signature)
        die('CSRF attempt! Signatures not match');
}

// Since 2.0: Add frontend widgets [ref is callback]
function cn_add_widget($name, $ref)
{
    $widgets = mcache_get('cn:widgets');
    if (!is_array($widgets)) $widgets = array();
    if (!is_array($widgets[$name])) $widgets[$name] = array();

    $widgets[$name][] = $ref;
    mcache_set('cn:widgets', $widgets);
}

// Since 2.0: Call registered widget (cn_add_widget)
function cn_widget()
{
    $wret   = NULL;
    $args   = func_get_args();
    $widget = array_shift($args);

    // Call internal widget
    if (substr($widget, 0, 5) == 'intl:')
    {
        $widget = substr($widget, 5);
        if (function_exists($fn = "widget_intl_$widget")) return call_user_func_array($fn, $args);
    }
    else
    {
        $widgets = mcache_get('cn:widgets');
        if (isset($widgets[$widget]))
        {
            $widgets = $widgets[$widget];
            foreach ($widgets as $fn)
            {
                if (function_exists($fn))
                {
                    $wret = call_user_func_array($fn, $args);
                }
            }
        }
    }

    return $wret;
}

// Since 2.0: Make basic URL for rewrite
function cn_rewrite()
{
    global $PHP_SELF;

    if (!getoption('rw_engine'))
    {
        return NULL;
    }
    
    $args     = func_get_args();
    $area     = array_shift($args);
    $rss_area = FALSE;
    $postfix  = array();
    $prefix   = '';
    
    if (preg_match('/\/rss\.php/i', $PHP_SELF))
    {
        $rss_area = TRUE;
    }

    // Check prefix
    $to_plist = preg_replace('/(\/|\\\\)/', '|', $PHP_SELF);
    $plist    = explode('|', $to_plist);

    // Check manual layout
    $playo = explode(DIRECTORY_SEPARATOR, getoption('rw_layout'));
    $plvar = $plist[ count($plist) - 1 ];
    $pyvar = $playo[ count($playo) - 1 ];

    // If manual layout and current PHP_SELF is EQU, then use given prefix from config ["rw_prefix"]
    if (!$PHP_SELF || $plvar == $pyvar || $rss_area)
    {
        $prefix = dirname(getoption('rw_prefix').'/.html');
    }
    // Else, use self prefix (aka PHP_SELF)
    else
    {
        $prefix = array();

        foreach ($plist as $_id) 
        {
            if ($_id)
            {
                if (preg_match('/^(.*)\./', $_id, $c)) 
                {
                    $prefix[] = $c[1];
                }
                elseif ($_id) 
                {
                    $prefix[] = $_id;
                }    
            }
        }

        $prefix = '/' . join('/', $prefix);
    }

    // Disable twice slashes
    if ($prefix == '/' || $prefix == '\\')
    {
        $prefix = '';
    }
        
    $param  = isset($args[0])? $args[0]:'';
    $param2 = isset($args[1])? $args[1]:false;
    $param3 = array();
        
    if (is_array($param2))
    {
        $param3 = $param2;
        $param2 = false;
    }
    elseif (isset($args[2])&&is_array($args[2]))
    {
        $param3 =  $args[2];
    }
    
    // Make postfix from GET-parameter
    foreach ($param3 as $id => $pfx)
    {
        if (is_numeric($id) && REQ($pfx))
        {
            $postfix[] = $pfx.'='.urlencode(REQ($pfx));
        }
        elseif ($pfx !== '')
        {
            $postfix[] = $id.'='.urlencode($pfx);
        }
    }

    // GET-params
    $postfix = $postfix ? '?'.join('&amp;', $postfix) : '';

    // After PageAlias
    $postfix = getoption('rw_use_shorten') ?  $postfix : '.html' . $postfix;
    
    // ----
    if ($area == 'full_story')
    {
        return $prefix . '/' . $param . $postfix;
    }
    if ($area == 'print')
    {
        return $prefix . '/print-' . $param . $postfix;
    }
    elseif ($area == 'comments')
    {
        if ($param2)
        {
            return $prefix . '/comments-' . $param . '-'.$param2 . $postfix;
        }
        else
        {
            return $prefix . '/comments-' . $param . $postfix;
        }
    }
    elseif ($area == 'list')
    {
        if ($param2)
        {
            if ($param)
            {
                return $prefix . '/archive-' . $param2 . '-'.$param . $postfix;
            }
            else
            {
                return $prefix . '/archive-' . $param2 . $postfix;
            }
        }

        return $prefix . '/list-' . $param . $postfix;
    }
    elseif ($area == 'archive')
    {
        return $prefix . '/archive-' . $param . $postfix;
    }
    elseif ($area == 'rss')
    {
        return 'http://' . parse_url(getoption('http_script_dir'), PHP_URL_HOST) . $prefix . '/' . $param  . $postfix;
    }
    elseif ($area == 'tag')
    {
        if ($param2)
        {
            return $prefix . '/tag-' . urlencode($param) . '/'.$param2 . $postfix;
        }
        else
        {
            return $prefix . '/tag-' . urlencode($param) . $postfix;
        }
    }

    return NULL;
}

// Since 2.0: Load & decode rewrite rules
function cn_rewrite_load()
{
    global $PHP_SELF;

    // rewrite module detected
    if (isset($_GET['cn_rewrite_url']) && ($cn_rewrite_url = $_GET['cn_rewrite_url']))
    {
        $layout = getoption('rw_layout');

        // Make compatible
        if ($cn_rewrite_url[0] !== '/') 
        {
            $cn_rewrite_url = "/$cn_rewrite_url";
        }

        // Try get target php file
        $request_uri = $_SERVER['REQUEST_URI'];
        $basedir = dirname(getoption('rw_htaccess')).DIRECTORY_SEPARATOR;
        
        // Rule matched, test pathes
        if (preg_match('/^(\/[^\?]+)/', $request_uri, $c))
        {
            $tf = explode('/', $c[1]);

            // Decremental search
            for ($i = count($tf); $i > 0; $i--)
            {
                $sp = array_slice($tf, 0, $i);
                $ch = $basedir . join(DIRECTORY_SEPARATOR, $sp) . '.php';

                // PHP-File is Founded!
                if (file_exists($ch)) 
                {
                    $layout = $ch;
                }
            }
        }

        // Decode request URI
        if (preg_match('/\?/', $request_uri))
        {
            $RI = preg_replace('/^.*\?/', '', $request_uri);
            $AR = explode('&', $RI);
            foreach ($AR as $v)
            {
                list($l, $r) = explode('=', $v, 2);
                $_GET[$l] = $r;
            }
        }

        $post_fix = getoption('rw_use_shorten') ? '$' : '\.html';

        // --------
        if (preg_match('/\/tag\-(.*)\/([0-9]+)'.$post_fix.'/i', $cn_rewrite_url, $c))
        {
            $_GET['tag'] = $c[1];
            $_GET['start_from'] = $c[2];
        }
        elseif (preg_match('/\/rss-([0-9]+)'.$post_fix.'/i', $cn_rewrite_url, $c))
        {
            $_GET['number'] = $c[1];
            $layout = SERVDIR.'rss.php';
        }
        elseif (preg_match('/\/print\-([0-9a-z_\-\.]+)'.$post_fix.'/i', $cn_rewrite_url, $c))
        {
            $_GET['id'] = $c[1];
            $layout = SERVDIR.'print.php';
        }
        elseif (preg_match('/\/comments\-([0-9a-z_\-\.]+)-(\d+)'.$post_fix.'/i', $cn_rewrite_url, $c))
        {
            $_GET['id'] = $c[1];
            $_GET['start_from'] = $c[2];
            $_GET['subaction'] = 'showcomments';
        }
        elseif (preg_match('/\/comments\-([0-9a-z_\-\.]+)'.$post_fix.'/i', $cn_rewrite_url, $c))
        {
            $_GET['id'] = $c[1];
            $_GET['subaction'] = 'showcomments';
        }
        elseif (preg_match('/\/list\-(\d+)'.$post_fix.'/i', $cn_rewrite_url, $c))
        {
            $_GET['start_from'] = $c[1];
        }
        elseif (preg_match('/\/archive\-(\d+)-(\d+)'.$post_fix.'/i', $cn_rewrite_url, $c))
        {
            $_GET['archive'] = $c[1];
            $_GET['start_from'] = $c[2];
        }
        elseif (preg_match('/\/archive\-(\d+)'.$post_fix.'/i', $cn_rewrite_url, $c))
        {
            $_GET['archive'] = $c[1];
        }
        elseif (preg_match('/\/tag\-(.*)'.$post_fix.'/i', $cn_rewrite_url, $c))
        {           
            $_GET['tag'] = $c[1];
        }
        elseif (preg_match('/\/([0-9a-z_\-\.]+)'.$post_fix.'/i', $cn_rewrite_url, $c))
        {              
            $_GET['id'] = $c[1];
        }
        else
        {     
            header("HTTP/1.0 404 Not Found");
            die("404 Not Found");
        }

        define('CN_REWRITE', $layout);
        if (isset($PHP_SELF)) {
            define('PHP_SELF', $PHP_SELF);
        } else {
            define('PHP_SELF',  pathinfo(str_replace($basedir, '', $layout),PATHINFO_BASENAME));
        }
    }
    else
    {
        if (isset($PHP_SELF)) {
            define('PHP_SELF', $PHP_SELF);
        } else {
            define('PHP_SELF', $_SERVER["SCRIPT_NAME"]);
        }
        define('CN_REWRITE', FALSE);
    }

    // const PHPSELF = SCRIPT_NAME ($PHP_SELF user may replace)
    if (!isset($PHP_SELF) && empty($PHP_SELF)) 
    {
        $PHP_SELF = PHP_SELF;
    }
}

// Since 2.0: Add BreadCrumb
function cn_bc_add($name, $url = '')
{
    $bc = mcache_get('.breadcrumbs');
    $bc[] = array('name' => $name, 'url' => $url);
    mcache_set('.breadcrumbs', $bc);
}

// ---------------------------------------- SNIPPETS -------------------------------------------------------------------

// Since 2.0: Generate CSRF stamp (only for members)
// @Param: type = std (input hidden), a (inline in a)
function cn_snippet_digital_signature($type = 'std')
{
    $member = member_get();

    // Is not member - is fatal error
    if (is_null($member)) die("Exception with generating signature");

    // Make signature
    $sign_extr = MD5( time() . mt_rand() ).'-'.$member['name'];
    $signature = MD5( $sign_extr . $member['pass'] . MD5(getoption('#crypt_salt')) );

    if ($type == 'std')
    {
        echo '<input type="hidden" name="__signature_key" value="'.cn_htmlspecialchars($sign_extr).'" />';
        echo '<input type="hidden" name="__signature_dsi" value="'.cn_htmlspecialchars($signature).'" />';
    }
    elseif ($type == 'a')
    {
        return '__signature_dsi_inline='.$signature.'.'.urlencode($sign_extr);
    }

    return FALSE;
}

// Since 2.0; HTML show errors
function cn_snippet_messages($area = 'new')
{
    $delay = 10000;
    $success = '';
    $failed = '';

    for ($i = 0; $i < strlen($area); $i++) {

        $messages = cn_get_message($area[$i], 's');

        $type_text = 'Success - ';
		$type_class = 'success';

        if ($area[$i] == 'e') {
            $type_text = 'Error - ';
			$type_class ='danger';

        } elseif ($area[$i] == 'w') {
            $type_text = 'Warning - ';
			$type_class = 'warning';
        }

		if ($messages) {
            foreach ($messages as $msg) {
                if ($type_class == 'success') {
                    $success .= '<div class="alert alert-success fade in alert-dismissable"><b>'.$type_text.'</b> '.$msg.'<a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a></div>';
                } else {
                    $failed .= '<div class=\"alert alert-'.$type_class.' lead\" ><b>'.$type_text.'</b> '.$msg.'</div>';
                }
            }
        }
    }

    if ($failed) {

        $msg_swal = '<script>swal({ title: "info", html: "'.$failed.'", type: "info" });</script>';
        echo $msg_swal;
    }
    elseif ($success) {

        echo '<section><div class="container">'.$success.'</div></section>';
    }
}

// Since 2.0: Write default input=hidden fields
function cn_form_open($fields)
{
    $fields = explode(',', $fields);
    foreach ($fields as $field)
    {
        $_field = REQ(trim($field), 'GPG');
        echo '<input type="hidden" name="'.trim($field).'" value="'.cn_htmlspecialchars($_field).'" />';
    }

    cn_snippet_digital_signature();
}

// Since 2.0: convert all GET to hidden fields
function cn_snippet_get_hidden($ADD = array())
{
    $hid = '';
    $GET = $_GET + $ADD;
    foreach ($GET as $k => $v)
    {
        if ($v !== '')
        {
            $hid .= '<input type="hidden" name="'.cn_htmlspecialchars($k).'" value="'.cn_htmlspecialchars($v).'" />';
        }
    }
    
    return $hid;
}

// Since 2.0: Create snippet for open external window
function cn_snippet_open_win($url, $params = array(), $title = 'CN Window' )
{
    if (empty($params['w'])) 
    {
        $params['w'] = 550;
    }        
    if (empty($params['h'])) 
    {
        $params['h'] = 500;
    }
    if (empty($params['t'])) 
    {
        $params['t'] = 100;
    }
    if (empty($params['l'])) 
    {
        $params['l'] = 100;
    }
    if (empty($params['sb'])) 
    {
        $params['sb'] = 1;
    }
    if (empty($params['rs'])) 
    {
        $params['rs'] = 1;
    }

    $echo = '';
    if ($params['l'] === 'auto')
    {
        $echo .= 'var lp=(window.innerWidth - '.$params['w'].') / 2; ';
    }
    else
    {
        $echo .= 'var lp='.$params['l'].'; ';
    }

    return $echo . "window.open('$url', '$title', 'scrollbars={$params['sb']},resizable={$params['rs']},width={$params['w']},height={$params['h']},left='+lp+',top={$params['t']}'); return false;";
}

// Since 2.0: Show breadcrumbs
function cn_snippet_bc($sep = '&gt;')
{
    $bc = array_values(mcache_get('.breadcrumbs'));
    if ($bc) {

        echo '<section><div class="container"><div class="breadcrumb lead">';

        $ls = array();
        if (is_array($bc)) {

            $kcount = count($bc) - 1;
            foreach ($bc as $i => $item) {

                $title = $item['name'];
                if ($kcount == $i) {
                    $ls[] = '<span class="bcitem">' . i18n($item['name']) . '</span>';
                } else {
                    $ls[] = '<span class="bcitem"><a href="' . $item['url'] . '">' . i18n($item['name']) . '</a></span>';
                }
            }
        }

        echo join(' <span class="bcsep">' . '<i class="fa fa-angle-right"></i>' . '</span> ', $ls);
        echo '</div></div></section>';
    }
}

function cn_snippet_ckeditor($ids = '')
{
    // pre-init
    $CKSmiles = $CKBar = array();
    for ($i = 1; $i <= 8; $i++)
    {
        $ck_bar = getoption("ck_ln{$i}");
        if ($ck_bar) $CKBar[] = '["'.join('","', explode(',', cn_htmlspecialchars($ck_bar))).'"]';
    }

    $smiles = explode(',', getoption('smilies'));
    foreach ($smiles as $smile) $CKSmiles[] = "'$smile.gif'";

    $CKSmiles = join(', ', $CKSmiles);
    $CKBar    = join(', ', $CKBar);
    $Cklang   = getoption('cklang');
    if (empty($Cklang)) $Cklang = 'en';

    // show
    echo '<script src="'.getoption('http_script_dir').'/core/ckeditor/ckeditor.js"></script>';
    echo '<script type="text/javascript">'."\n";
    echo "(function() { var settings = {"."\n";
    echo "skin: 'moono', width: 'auto', height: 350, customConfig: '', language: '$Cklang', entities_latin: false, entities_greek: false, \n";
    echo "toolbar: [ ". hook('settings/CKEDITOR_customize', $CKBar) . " ], \n";

    $add_opt  = array();
    $compound = array();

    $add_opt['filebrowserBrowseUrl'] = PHP_SELF.'?mod=media&opt=inline';
    $add_opt['filebrowserImageBrowseUrl'] = PHP_SELF.'?mod=media&opt=inline';

    $add_opt = hook('settings/CKEDITOR_filemanager', $add_opt);
    foreach ($add_opt as $I => $V) $compound[] = "$I: \"$V\"";

    // Insert updated FileBrowser
    echo join(', ', $compound) . '};' . "\n";

    // Smilies
    echo 'CKEDITOR.config.smiley_path = "'.getoption('http_script_dir').'/skins/emoticons/"; '."\n";
    echo 'CKEDITOR.config.smiley_images = [ '.hook('settings/CKEDITOR_emoticons', $CKSmiles).' ];'."\n";
    echo 'CKEDITOR.config.smiley_descriptions = [];'."\n";
    echo "CKEDITOR.config.allowedContent = true;";

    $ids = spsep($ids);
    foreach ($ids as $id) 
    {
        echo "CKEDITOR.replace( '".trim($id)."', ".hook('settings/CKEDITOR_SetsName', 'settings')." );"."\n";
    }

    echo hook('settings/CKEDITOR_Settings');

    echo '})(); </script>';
}

// Since 2.0: [helper] $c - categories source [string], $cat - check [array]
// If category present (if any), TRUE
function hlp_check_cat($c, $cat)
{
    $cats = spsep($c);
    return array_intersect($cat, $cats);
}

// Since 2.0: [helper] $c - tags [string], $tag - requested [string]
function hlp_check_tag($t, $tag)
{
    $tags = spsep($t);
    foreach ($tags as $i => $v) 
    {
        $tags[$i] = strtolower(trim($v));
    }
    return in_array($tag, $tags);
}

// Since 2.0: [helper]
function hlp_req_cached_nloc($id)
{      
    global $_CN_cache_block_id;
    global $_CN_cache_block_dt;
    
    $nloc = db_get_nloc($id);
    if (!isset($_CN_cache_block_id["nloc-$nloc"]))
    {
        $_CN_cache_block_id["nloc-$nloc"] = TRUE;
        $_CN_cache_block_dt[$nloc]=db_news_load($nloc);
    }    
    return $_CN_cache_block_dt[$nloc];
}

// Since 2.0: Clear cache blocks
function cn_cache_block_clear($id)
{
    global $_CN_cache_block_id;

    foreach ($_CN_cache_block_id as $ccid => $_t)
    {
        if (substr($ccid, 0, strlen($id)) === $id)
        {
            unset($_CN_cache_block_id[$ccid]);
        }
    }
}

// Since 2.0: Transform ID to TimeStamp
function cn_id_alias($id)
{
    if ($id)
    {
        if ($_id = bt_get_id($id, 'nid_ts')) 
        {
            $id = $_id;
        }
        elseif ($_id = bt_get_id($id, 'pg_ts')) 
        {
            $id = $_id;
        }

        $_GET['id'] = $id;
    }

    return $id;
}

// Since 2.0: Transform TS to alias/id
function cn_put_alias($id)
{
    if ($_id = bt_get_id($id, 'ts_pg')) 
    {
        $id = $_id;
    }
    elseif ($_id = bt_get_id($id, 'nts_id')) 
    {
        $id = $_id;
    }
    return $id;
}

// Since 2.0: Basic function for list news
function cn_get_news($opts)
{    
    $FlatDB = new FlatDB();

    // Source must be:
    // -----------------
    // null -- active news only
    // 'draft'
    // 'archive'
    // 'A2' -- active news and archives
    // -----------------

    $source     = isset($opts['source']) ? $opts['source'] : '';
    $archive_id = isset($opts['archive_id']) ? intval($opts['archive_id']) : 0;

    // Sorting
    $sort       = isset($opts['sort']) ? $opts['sort'] : '';
    $dir        = isset($opts['dir']) ? strtoupper($opts['dir']) : '';

    // Pagination
    $st         = isset($opts['start']) ? intval($opts['start']) : 0;
    $per_page   = isset($opts['per_page']) ? intval($opts['per_page']) : 10;

    // Filters
    $page_alias = isset($opts['page_alias']) ? $opts['page_alias'] : '';
    $cfilter    = isset($opts['cfilter']) ? $opts['cfilter'] : array();
    $ufilter    = isset($opts['ufilter']) ? $opts['ufilter'] : array();
    $tag        = isset($opts['tag']) ? trim(strtolower($opts['tag'])) : '';
    $only_active= isset($opts['only_active']) ? $opts['only_active'] : false;

    // System
    $nocat      = isset($opts['nocat']) ? $opts['nocat'] : false;
    $by_date    = isset($opts['by_date']) ? $opts['by_date'] : '';
    $nlpros     = isset($opts['nlpros']) ? intval($opts['nlpros']) : 0;

    /* ============================================================================================================== */

    // Prepare vars
    // ------------------

    if ($only_active) { $source = ''; }

    $overall  = 0;
    $ufilter  = $FlatDB->load_users_id($ufilter);
    $date_out = spsep($by_date, '-');

    // Match news by page-alias
    // -------------------
    if ($page_alias)
    {
        if ($_id = bt_get_id($page_alias, 'pg_ts'))
        {
            $FlatDB->list = array($_id => array());
        }
    }
    // Preloading indexes
    // ------------------
    else
    {
        if ($source === '') {
            $FlatDB->load_by();
        }
        elseif ($source === 'archive') {
            $FlatDB->load_by("archive-$archive_id.txt");
        }
        elseif ($source === 'draft') {
            $FlatDB->load_by('idraft.txt');
        }
        elseif ($source === 'A2') {
            $FlatDB->load_overall();
        }
        else {
            die("CN Internal error: source not recognized\n");
        }

        // Cache Key
        $cache_id  = md5(json_encode(array($cfilter, $ufilter, $tag, $nocat, $date_out, $nlpros, $sort, $dir, $source, $archive_id)));
        $cache_dis = (defined('CACHE_DISABLE') && CACHE_DISABLE) ? 1 : 0;

        if ($cache_dis || $FlatDB->cache_not_exists($cache_id)) {

            // Expand required data
            $FlatDB->load_ext_by(array
            (
                'tg'     => $tag, // title or sort by tags
                'title'  => (strtolower($sort) === 'title'), // sort by title
                'author' => ($sort === 'author'), // sort by author name
                'vcnt'   => ($sort === 'vcnt'), // sort by views count
            ));

            // Filtering data
            // ----------------

            // $cfilter, $ufilter - intersect (one match) filter by category and user_id
            // $nocat   = if has, and $cfilter is empty, stay news withot category only
            // $date_out = '[Y]-[m]-[d]' if present, stay only this date (-,Y,Y-m,Y-m-d)
            // $nlpros  = if present, show prospected (postponed) news

            $FlatDB->filters($cfilter, $ufilter, $tag, $nocat, $date_out, $nlpros);
            $FlatDB->sorting($sort, $dir);
        }

        // Save cache for overall items
        $FlatDB->cache_save($cache_id);

        // Pagination
        // ----------
        $overall = count($FlatDB->list);
        $FlatDB->slicing($st, $per_page);
    }

    // Get news entries
    $entries = $FlatDB->load_entries();

    // Get news structure
    // -------------------------

    $qtree   = array();
    $dirs    = scan_dir(cn_path_construct(SERVDIR, 'cdata', 'news'), '^[\d\-]+\.php$');
    foreach ($dirs as $tc) {
        if (preg_match('/^([\d\-]+)\.php$/i', $tc, $c)) {
            $qtree[$c[1]] = 0;
        }
    }

    // meta-info
    $rev = array(
        'qtree'       => $qtree,
        'overall'     => $overall,
        'cpostponed'  => $FlatDB->_item_postponed
    );

    return array($entries, $rev);
}

// Since 2.0: Show listing for templates
function cn_snippet_show_list_head($head, $id = '')
{
    $head = spsep($head, '|');

    echo '<tr>';
    foreach ($head as $H1) echo '<th>'.$H1.'</th>';

    if ($id) echo '<th><input type="checkbox" name="master_box" onclick="check_uncheck_all(\''.$id.'[]\');"></th></tr>';
    return $id;
}


// Since 2.0: Simple paginate snippet
function cn_snippet_paginate($st, $per_page = 100, $showed = NULL)
{
    echo '<div class="snippet_paginate">';

    echo '<span class="next">';
    if ($st - $per_page < 0) echo '&lt;&lt; Prev';
    else echo '<a href="'.cn_url_modify('st='.($st - $per_page)).'">&lt;&lt; Prev</a> ';
    echo '</span>';

    echo ' <span class="pages">[page <b>'.intval($st / $per_page).'</b>]</span> ';

    echo '<span class="next">';
    if ($showed == $per_page) echo '<a href="'.cn_url_modify('st='.($st + $per_page)).'">Next &gt;&gt;</a>';
    else echo 'Next &gt;&gt;';
    echo '</span>';

    echo '</div>';
}

// Since 2.0: Highlight words
function cn_snippet_search_hl($text, $qhl)
{
    if (!getoption('search_hl')) 
    {
        return $text;
    }

    // ---
    if ($qhl)
    {
        $mhl = preg_split('/\s/', strtolower(urldecode($qhl)));
        $whl = array();
        $ohl = array();

        foreach ($mhl as $qhl) 
        {
            $whl[$qhl] = strlen($qhl); asort($whl);
        }
        foreach ($whl as $wm => $_t1)
        {
            unset($whl[$wm]);

            $cons = FALSE;
            foreach ($whl as $wt => $_t2)
            {
                if (strpos($wt, $wm) !== FALSE)
                {
                    $cons = TRUE;
                    break;
                }
            }

            if ($cons)
            {
                continue;
            }
            $ohl[] = $wm;
        }

        // Replace words
        foreach ($ohl as $wm) 
        {
            $text = str_replace($wm, '<span class="cn_search_hl">'.$wm.'</span>', $text);
        }
    }

    return $text;
}

// Since 2.0.3
function cn_user_email_as_site($user_email, $username)
{
    if (preg_match('/^www\./i', $user_email)) {
        return '<a target="_blank" href="http://'.cn_htmlspecialchars($user_email).'">'.$username.'</a>';
    }
    elseif (preg_match('/^(https?|ftps?):\/\//i', $user_email)) {
        return '<a target="_blank" href="'.cn_htmlspecialchars($user_email).'">'.$username.'</a>';
    }
    else {
        return '<a href="mailto:'.cn_htmlspecialchars($user_email).'">'.$username.'</a>';
    }
}