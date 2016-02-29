<?php if (!defined('EXEC_TIME')) die('Access restricted!');

$current_dir = dirname(__FILE__);

do
{
    // Add Comment -----------------------------------------------------------------------------------------------------
    if (isset($allow_add_comment) && $allow_add_comment)
    {
        $break = include ("$current_dir/add_comment.php");
        if ($break === FALSE) { $CN_HALT = TRUE; break; }
    }

    // Show Full Story -------------------------------------------------------------------------------------------------
    if (isset($allow_full_story) && $allow_full_story)
    {
        $break = include ("$current_dir/full_story.php");
        if ($break === FALSE) { $CN_HALT = TRUE; break; }
    }

    // Show Comments ---------------------------------------------------------------------------------------------------
    if (isset($allow_comments) && $allow_comments)
    {
        $break = include ("$current_dir/comments.php");
        if ($break === FALSE) { $CN_HALT = TRUE; break; }
    }

    // Active News -----------------------------------------------------------------------------------------------------
    if (isset($allow_active_news) && $allow_active_news)
    {
        $break = include ("$current_dir/active_news.php");
        if ($break === FALSE) { $CN_HALT = TRUE; break; }
    }
}
while (FALSE);

// ---------------------------------------------------------------------------------------------------------------------
if ((!isset($count_cute_news_includes) or !$count_cute_news_includes) and $template != 'rss')
{
    /// Removing the "Powered By..." line is NOT allowed by the CuteNews License, only registered users are alowed to do so.
    if (!file_exists(SERVDIR."/cdata/reg.php"))
    {
        echo base64_decode('PGRpdiBzdHlsZT0ibWFyZ2luLXRvcDoxNXB4IWltcG9ydGFudDt3aWR0aDoxMDAlIWltcG9ydGFudDt0ZXh0LWFsaWduOmNlbnRlciFpbXBvcnRhbnQ7Zm9udDo5cHggVmVyZGFuYSFpbXBvcnRhbnQ7ZGlzcGxheTpibG9jayFpbXBvcnRhbnQ7dGV4dC1pbmRlbnQ6IDBweCFpbXBvcnRhbnQ7dmlzaWJpbGl0eTogdmlzaWJsZSFpbXBvcnRhbnQ7Y29sb3I6IzAwMDAwMCFpbXBvcnRhbnQ7Ij5Qb3dlcmVkIGJ5IDxhIGhyZWY9Imh0dHA6Ly9jdXRlcGhwLmNvbS8iIHRpdGxlPSJDdXRlTmV3cyAtIFBIUCBOZXdzIE1hbmFnZW1lbnQgU3lzdGVtIiBzdHlsZT0iZm9udDo5cHggVmVyZGFuYSFpbXBvcnRhbnQ7ZGlzcGxheTppbmxpbmUhaW1wb3J0YW50O3Zpc2liaWxpdHk6dmlzaWJsZSFpbXBvcnRhbnQ7Y29sb3I6IzAwMzM2NiFpbXBvcnRhbnQ7dGV4dC1pbmRlbnQ6IDBweCFpbXBvcnRhbnQ7Ij5DdXRlTmV3czwvYT48L2Rpdj4=');
    }
    else
    {
        include(SERVDIR."/cdata/reg.php");
        if ( !preg_match('/\\A(\\w{6})-\\w{6}-\\w{6}\\z/', $reg_site_key, $mmbrid))
        {
            echo base64_decode('PGRpdiBzdHlsZT0ibWFyZ2luLXRvcDoxNXB4IWltcG9ydGFudDt3aWR0aDoxMDAlIWltcG9ydGFudDt0ZXh0LWFsaWduOmNlbnRlciFpbXBvcnRhbnQ7Zm9udDo5cHggVmVyZGFuYSFpbXBvcnRhbnQ7ZGlzcGxheTpibG9jayFpbXBvcnRhbnQ7dGV4dC1pbmRlbnQ6IDBweCFpbXBvcnRhbnQ7dmlzaWJpbGl0eTogdmlzaWJsZSFpbXBvcnRhbnQ7Y29sb3I6IzAwMDAwMCFpbXBvcnRhbnQ7Ij5Db250ZW50IE1hbmFnZW1lbnQgUG93ZXJlZCBieSA8YSBocmVmPSJodHRwOi8vY3V0ZXBocC5jb20vIiB0aXRsZT0iQ3V0ZU5ld3MgLSBQSFAgTmV3cyBNYW5hZ2VtZW50IFN5c3RlbSIgc3R5bGU9ImZvbnQ6OXB4IFZlcmRhbmEhaW1wb3J0YW50O2Rpc3BsYXk6aW5saW5lIWltcG9ydGFudDt2aXNpYmlsaXR5OnZpc2libGUhaW1wb3J0YW50O2NvbG9yOiMwMDMzNjYhaW1wb3J0YW50O3RleHQtaW5kZW50OjBweCFpbXBvcnRhbnQ7Ij5DdXRlTmV3czwvYT48L2Rpdj4=');
        }
    }
}

if(isset($count_cute_news_includes)) $count_cute_news_includes++;