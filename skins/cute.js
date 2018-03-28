// Get ID in misc browser
function getId(id)
{
    if (document.all) return (document.all[id]);
    else if (document.getElementById) return (document.getElementById(id));
    else if (document.layers) return (document.layers[id]);
    else return null;
}

function Help(section)
{
    q=window.open('index.php?mod=help&section=' + section, 'Help', 'scrollbars=1,resizable=1,width=550,height=500,left=100,top=100');
}

function ShowOrHide(d1, d2)
{
  if (d1 != '') DoDiv(d1);
  if (d2 != '') DoDiv(d2);
}

function CheckPreview()
{
    var c=document.getElementById('chkPreview');
    c.setAttribute('value','true');
}

function DoDiv(id)
{
    var item = getId(id);

    if (!item) {
    }
    else if (item.style)
    {
        if (arguments.length == 2)
        {
            if (arguments[1] == true)  item.style.display = "";
            else item.style.display = "none";
        }
        else
        {
            if (item.style.display == "none") item.style.display = "";
            else item.style.display = "none";
        }
    }
    else item.visibility = "show";
}

function Show_Only(id)
{
    for (var i = 1; i < arguments.length; i++) DoDiv(arguments[i], 0);
    DoDiv(id, 1);
}

function password_strength()
{
    var cv;
    var doc  = getId('regpassword').value;
    var pst  = getId('password_strength');
    var pid  = getId('pass_msg');

    var ln   = doc.length;
    var pv   = doc.charCodeAt(0);
    var disp = 0;

    if (ln > 2)
        for (var i = 0; i < ln; i++)
        {
            cv    = doc.charCodeAt(i);
            disp += (cv-pv) * (cv-pv);
            pv    = cv;
        }

    if (disp) disp = Math.log( ln*(2.72 + disp) );

    // Password strong level
    if (ln == 0)
    {
        pid.value = 'Enter password';
        pst.style.backgroundColor = 'red';
    }
    else if (disp < 5)
    {
        pid.value = 'Very poor';
        pst.style.backgroundColor = 'red';
    }
    else if (disp < 9)
    {
        pid.value = 'Weak';
        pst.style.backgroundColor = '#c08000';
    }
    else if (disp < 11)
    {
        pid.value = 'Normal';
        pst.style.backgroundColor = '#f0e080';
    }
    else
    {
        pid.value = 'Strong password';
        pst.style.backgroundColor = '#008000';
    }

}

function greeting()
{
    datetoday   = new Date();
    timenow     = datetoday.getTime();
    datetoday.setTime(timenow);
    thehour = datetoday.getHours();

    if (thehour < 9 )      display = "Morning";
    else if (thehour < 12) display = "Day";
    else if (thehour < 17) display = "Afternoon";
    else if (thehour < 20) display = "Evening";
    else display = "Night";

    var greeting = ("Good " + display);
    document.write(greeting);

}

/*
 * A JavaScript implementation of the RSA Data Security, Inc. MD5 Message
 * Digest Algorithm, as defined in RFC 1321.
 * Copyright (C) Paul Johnston 1999 - 2000.
 * Updated by Greg Holt 2000 - 2001.
 * See http://pajhome.org.uk/site/legal.html for details.
 */

/*
 * Convert a 32-bit number to a hex string with ls-byte first
 */
var hex_chr = "0123456789abcdef";
function rhex(num)
{
  str = "";
  for(j = 0; j <= 3; j++)
    str += hex_chr.charAt((num >> (j * 8 + 4)) & 0x0F) +
           hex_chr.charAt((num >> (j * 8)) & 0x0F);
  return str;
}

/*
 * Convert a string to a sequence of 16-word blocks, stored as an array.
 * Append padding bits and the length, as described in the MD5 standard.
 */
function str2blks_MD5(str)
{
  nblk = ((str.length + 8) >> 6) + 1;
  blks = new Array(nblk * 16);
  for(i = 0; i < nblk * 16; i++) blks[i] = 0;
  for(i = 0; i < str.length; i++)
    blks[i >> 2] |= str.charCodeAt(i) << ((i % 4) * 8);
  blks[i >> 2] |= 0x80 << ((i % 4) * 8);
  blks[nblk * 16 - 2] = str.length * 8;
  return blks;
}

/*
 * Add integers, wrapping at 2^32. This uses 16-bit operations internally
 * to work around bugs in some JS interpreters.
 */
function add(x, y)
{
  var lsw = (x & 0xFFFF) + (y & 0xFFFF);
  var msw = (x >> 16) + (y >> 16) + (lsw >> 16);
  return (msw << 16) | (lsw & 0xFFFF);
}

/*
 * Bitwise rotate a 32-bit number to the left
 */
function rol(num, cnt)
{
  return (num << cnt) | (num >>> (32 - cnt));
}

/*
 * These functions implement the basic operation for each round of the
 * algorithm.
 */
function cmn(q, a, b, x, s, t) { return add(rol(add(add(a, q), add(x, t)), s), b); }
function ff(a, b, c, d, x, s, t) { return cmn((b & c) | ((~b) & d), a, b, x, s, t); }
function gg(a, b, c, d, x, s, t) { return cmn((b & d) | (c & (~d)), a, b, x, s, t); }
function hh(a, b, c, d, x, s, t) { return cmn(b ^ c ^ d, a, b, x, s, t); }
function ii(a, b, c, d, x, s, t) { return cmn(c ^ (b | (~d)), a, b, x, s, t); }

/*
 * Take a string and return the hex representation of its MD5.
 */
function calcMD5(str)
{
  x = str2blks_MD5(str);
  a =  1732584193; b = -271733879; c = -1732584194; d =  271733878;
  for(i = 0; i < x.length; i += 16)
  {
    olda = a; oldb = b; oldc = c;  oldd = d;
    a = ff(a, b, c, d, x[i+ 0], 7 , -680876936); d = ff(d, a, b, c, x[i+ 1], 12, -389564586); c = ff(c, d, a, b, x[i+ 2], 17,  606105819); b = ff(b, c, d, a, x[i+ 3], 22, -1044525330);
    a = ff(a, b, c, d, x[i+ 4], 7 , -176418897); d = ff(d, a, b, c, x[i+ 5], 12,  1200080426); c = ff(c, d, a, b, x[i+ 6], 17, -1473231341); b = ff(b, c, d, a, x[i+ 7], 22, -45705983);
    a = ff(a, b, c, d, x[i+ 8], 7 ,  1770035416); d = ff(d, a, b, c, x[i+ 9], 12, -1958414417); c = ff(c, d, a, b, x[i+10], 17, -42063); b = ff(b, c, d, a, x[i+11], 22, -1990404162);
    a = ff(a, b, c, d, x[i+12], 7 ,  1804603682); d = ff(d, a, b, c, x[i+13], 12, -40341101); c = ff(c, d, a, b, x[i+14], 17, -1502002290); b = ff(b, c, d, a, x[i+15], 22,  1236535329);
    a = gg(a, b, c, d, x[i+ 1], 5 , -165796510); d = gg(d, a, b, c, x[i+ 6], 9 , -1069501632); c = gg(c, d, a, b, x[i+11], 14,  643717713); b = gg(b, c, d, a, x[i+ 0], 20, -373897302);
    a = gg(a, b, c, d, x[i+ 5], 5 , -701558691); d = gg(d, a, b, c, x[i+10], 9 ,  38016083); c = gg(c, d, a, b, x[i+15], 14, -660478335); b = gg(b, c, d, a, x[i+ 4], 20, -405537848);
    a = gg(a, b, c, d, x[i+ 9], 5 ,  568446438); d = gg(d, a, b, c, x[i+14], 9 , -1019803690); c = gg(c, d, a, b, x[i+ 3], 14, -187363961); b = gg(b, c, d, a, x[i+ 8], 20,  1163531501);
    a = gg(a, b, c, d, x[i+13], 5 , -1444681467); d = gg(d, a, b, c, x[i+ 2], 9 , -51403784); c = gg(c, d, a, b, x[i+ 7], 14,  1735328473); b = gg(b, c, d, a, x[i+12], 20, -1926607734);
    a = hh(a, b, c, d, x[i+ 5], 4 , -378558); d = hh(d, a, b, c, x[i+ 8], 11, -2022574463); c = hh(c, d, a, b, x[i+11], 16,  1839030562); b = hh(b, c, d, a, x[i+14], 23, -35309556);
    a = hh(a, b, c, d, x[i+ 1], 4 , -1530992060); d = hh(d, a, b, c, x[i+ 4], 11,  1272893353); c = hh(c, d, a, b, x[i+ 7], 16, -155497632); b = hh(b, c, d, a, x[i+10], 23, -1094730640);
    a = hh(a, b, c, d, x[i+13], 4 ,  681279174); d = hh(d, a, b, c, x[i+ 0], 11, -358537222); c = hh(c, d, a, b, x[i+ 3], 16, -722521979); b = hh(b, c, d, a, x[i+ 6], 23,  76029189);
    a = hh(a, b, c, d, x[i+ 9], 4 , -640364487); d = hh(d, a, b, c, x[i+12], 11, -421815835); c = hh(c, d, a, b, x[i+15], 16,  530742520); b = hh(b, c, d, a, x[i+ 2], 23, -995338651);
    a = ii(a, b, c, d, x[i+ 0], 6 , -198630844); d = ii(d, a, b, c, x[i+ 7], 10,  1126891415); c = ii(c, d, a, b, x[i+14], 15, -1416354905); b = ii(b, c, d, a, x[i+ 5], 21, -57434055);
    a = ii(a, b, c, d, x[i+12], 6 ,  1700485571); d = ii(d, a, b, c, x[i+ 3], 10, -1894986606); c = ii(c, d, a, b, x[i+10], 15, -1051523); b = ii(b, c, d, a, x[i+ 1], 21, -2054922799);
    a = ii(a, b, c, d, x[i+ 8], 6 ,  1873313359); d = ii(d, a, b, c, x[i+15], 10, -30611744); c = ii(c, d, a, b, x[i+ 6], 15, -1560198380); b = ii(b, c, d, a, x[i+13], 21,  1309151649);
    a = ii(a, b, c, d, x[i+ 4], 6 , -145523070); d = ii(d, a, b, c, x[i+11], 10, -1120210379); c = ii(c, d, a, b, x[i+ 2], 15,  718787259); b = ii(b, c, d, a, x[i+ 9], 21, -343485551);
    a = add(a, olda); b = add(b, oldb); c = add(c, oldc); d = add(d, oldd);
  }
  return rhex(a) + rhex(b) + rhex(c) + rhex(d);
}

function check_uncheck_all(name)
{
    var e;
    var main_el = document.getElementsByName('master_box')[0];
    var frm     = document.getElementsByName(name);
    var checked = main_el.checked;

    for (var i = 0; i < frm.length; i++)
    {
        e = frm[i];
        if (e.type == 'checkbox') e.checked = checked;
    }
}

function insertAtCursor(myField, myValue)
{
    // IE support
    if (document.selection) {

        myField.focus();
        var sel = document.selection.createRange();
        sel.text = myValue;

    }
    // MOZILLA and others
    else if (myField.selectionStart || myField.selectionStart == '0') {
        var startPos  = myField.selectionStart;
        var endPos    = myField.selectionEnd;
        myField.value = myField.value.substring(0, startPos) + myValue + myField.value.substring(endPos, myField.value.length);

    } else {
        myField.value += myValue;
    }

    myField.focus();
}

function bb_wrap(id, wrp)
{
    var W;
    var HW = false;
    var src = null;

    // Has inner wrapper
    if (arguments.length == 4) {
        src = arguments[2];
        HW  = arguments[3];
        W   = src.getElementById(id);
    } else {
        src = document;
        W = getId(id);
    }

    // ----
    if (src.selection) {

        W.focus();
        var sel = src.selection.createRange();
        if (sel.text == '') return false;

        sel.text = '[' + wrp + (HW? '=' + HW : '') + ']' + sel.text + '[/'+wrp+']';

    } else if (W.selectionStart || W.selectionStart == '0') {

        var startPos  = W.selectionStart;
        var endPos    = W.selectionEnd;

        if (startPos < endPos) {
            var txt = W.value.substring(startPos, endPos);
            W.value = W.value.substring(0, startPos) + '[' + wrp + (HW? '=' + HW : '') +']' + txt + '[/' + wrp + ']' + W.value.substring(endPos);
        }
    }

    return false;
}

function notify_auto_hide(id, delay) {
    setTimeout(function() { getId(id).remove(); }, delay);
}

function tiny_msg(object)  {

    alert(object.title);
    return false;
}
