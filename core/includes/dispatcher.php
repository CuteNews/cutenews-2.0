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
        echo base64_decode('PGRpdiBzdHlsZT0ibWFyZ2luLXRvcDoxNXB4O3dpZHRoOjEwMCU7dGV4dC1hbGlnbjpjZW50ZXI7Zm9udDo5cHggVmVyZGFuYTsiPlBvd2VyZWQgYnkgPGEgaHJlZj0iaHR0cDovL2N1dGVwaHAuY29tLyIgdGl0bGU9IkN1dGVOZXdzIC0gUEhQIE5ld3MgTWFuYWdlbWVudCBTeXN0ZW0iPkN1dGVOZXdzPC9hPjwvZGl2Pg==');
    }
    else
    {
        include(SERVDIR."/cdata/reg.php");
        if ( !preg_match('/\\A(\\w{6})-\\w{6}-\\w{6}\\z/', $reg_site_key, $mmbrid))
        {
            echo base64_decode('PGRpdiBzdHlsZT0ibWFyZ2luLXRvcDoxNXB4O3dpZHRoOjEwMCU7dGV4dC1hbGlnbjpjZW50ZXI7Zm9udDo5cHggVmVyZGFuYTsiPkNvbnRlbnQgTWFuYWdlbWVudCBQb3dlcmVkIGJ5IDxhIGhyZWY9Imh0dHA6Ly9jdXRlcGhwLmNvbS8iIHRpdGxlPSJDdXRlTmV3cyAtIFBIUCBOZXdzIE1hbmFnZW1lbnQgU3lzdGVtIj5DdXRlTmV3czwvYT48L2Rpdj4=');
        }
    }
}

$count_cute_news_includes++;