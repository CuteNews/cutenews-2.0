<script type="text/javascript">
    /* Dealing with cookies */
    function cn_get_cookie_val(offset)
    {
        var endstr = document.cookie.indexOf (";", offset);
        if (endstr == -1) endstr = document.cookie.length;
        return decodeURIComponent(document.cookie.substring(offset, endstr));
    }

    function cn_get_cookie(name)
    {
        var arg  = name + "=";
        var alen = arg.length;
        var clen = document.cookie.length;
        var i = 0;

        while (i < clen)
        {
            var j = i + alen;
            if (document.cookie.substring(i, j) == arg) return cn_get_cookie_val (j);
            i = document.cookie.indexOf(" ", i) + 1;
            if (i == 0) break;
        }

        return null;
    }

    function cn_set_cookie(name, value)
    {
        var argv    = cn_set_cookie.arguments;
        var argc    = cn_set_cookie.arguments.length;
        var expires = (argc > 2) ? argv[2] : null;
        var domain  = (argc > 3) ? argv[3] : null;
        var secure  = (argc > 4) ? argv[4] : false;
        var path    = '/';

        if (value.length > 0)
        {
            document.cookie = name + "=" + (value ? encodeURIComponent (value) : '') +
                    ((expires == null) ? "" : ("; expires=" + expires.toGMTString())) +
                    ((path == null) ? "" : ("; path=" + path)) +
                    ((domain == null) ? "" : ("; domain=" + domain)) +
                    ((secure == true) ? "; secure" : "");
        }
        else {
            document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT; path=/';
        }
    }

    /* -------------- Get ID in misc browser ------------------ */
    function cn_get_id(id)
    {
        if (document.all) return (document.all[id]);
        else if (document.getElementById) return (document.getElementById(id));
        else if (document.layers) return (document.layers[id]);
        else return null;
    }

    function forget_me()
    {
        var t = document.getElementsByTagName('input');
        for (var i = 0; i < t.length; i++)
        {
            if (t[i].className == 'cn_comm_username' || t[i].className == 'cn_comm_email')
            {
                t[i].value = '';
                t[i].disabled = '';
            }

            if (t[i].name == 'isforgetme') {
                t[i].value = 'true';
            }

            if (t[i].name == '__signature_key' || t[i].name == '__signature_dsi') {
                t[i].value = '';
            }
        }

        cn_set_cookie('session', '');
        cn_set_cookie('CUTENEWS_SESSION', '');

        var c = document.getElementsByTagName('form')[0];
        /* c.submit(); */

        document.location.reload();
    }

    function cn_more_expand(id)
    {
        var dis = cn_get_id(id);
        if (dis.style.display == 'none')
            dis.style.display = 'block';
        else
            dis.style.display = 'none';
    }

    function insertext(text, id)
    {
        cn_get_id(id).value +=" "+ text;
        cn_get_id(id).focus();
    }

</script>
<noscript>Your browser is not Javascript enable or you have turn it off. We recommend you to activate for better security reason</noscript>