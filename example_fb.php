<?php

/**
 * @desc This is example file for simple cutenews feed with extensions (CN API)
 */

// Include Cutenews API
require_once 'cn_api.php';

    // Get Entry by `id` parameter (externally)
    if ($entry = cn_api_get_entry())
    {
        // Get page title for selected news
        $page_title = $entry['t'];

        // Meta description -- we truncate only 100 words
        $meta_short_desc = clever_truncate( $entry['s'] . $entry['f'], 100 );

        // There we remove all tags for meta-desc
        $meta_short_desc = strip_tags( $meta_short_desc );

        // Optionally: Facebook Open Graph Protocol

        // BEFORE -- Go to CN dashboard, Additional fields, male field. e.g. "og_image" (type image/resource)
        // After, bind image for field "og_image" and save news

        // There appears field "mf", with subfield "og_image"
        $og_image = $entry['mf']['og_image'];
    }
    else
    {
        $page_title = "There's news feed for my site";
        $meta_short_desc = "This page is my news feed... etc";
    }

?><!DOCTYPE HTML><!-- Simple Example for Cutenews -->
<html>
<head>

    <!-- Show title for page name -->
    <title><?php echo $page_title; ?></title>

    <!-- Force UTF-8 -->
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>

    <!-- Insert meta description for SEO -->
    <meta name="description" content="<?php echo $meta_short_desc; ?>" />

    <!-- Example for OpenGraph Protocol (if og_image field present, show it) -->
    <?php if (isset($og_image)) { ?><meta property="og:image" content="<?php echo $og_image; ?>" /><?php } ?>

    <!-- After, we can append any field as shown before -->
    <!-- Also, put there styles/js, etc. -->

</head>
<body>
<?php

    // Use Integration Wizard for this block
    $template = 'default';
    include 'show_news.php';

?>
</body>
</html>