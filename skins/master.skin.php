<?PHP

global $skin_header, $skin_menu, $skin_footer, $skin_prefix;

$skin_prefix = "";
// ********************************************************************************
// Skin MENU
// ********************************************************************************
$skin_menu = cn_get_menu();// . '<div style="clear:both;"></div>';

// ***********
// Skin HEADER
// ***********
$skin_header = <<<HTML
<!DOCTYPE html>
<html lang="en">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>{title}</title>

    <link href="skins/images/favicon.ico" rel="shortcut icon" type="image/ico" />
    <link href="libs/css/{theme}.min.css" rel="stylesheet" type="text/css" />
    <link href="libs/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="skins/custom.css" rel="stylesheet" type="text/css" />
    <link href="libs/sweet-alert2/sweet-alert2.css" rel="stylesheet" type="text/css" >
    <link href="libs/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" type="text/css" >
    <link href="libs/codemirror/lib/codemirror.css" rel="stylesheet" type="text/css" >

    <script src="libs/js/jquery.js"></script>
    <script src="libs/js/bootstrap.min.js"></script>
    <script src="libs/bootbox/bootbox.min.js"></script>
    <script src="libs/bootstrap-select/js/bootstrap-select.min.js"></script>
    <script src="libs/sweet-alert2/sweet-alert2.min.js"></script>
    
    <script src="libs/codemirror/lib/codemirror.js"></script>
    <script src="libs/codemirror/addon/selection/selection-pointer.js"></script>
    <script src="libs/codemirror/mode/htmlmixed/htmlmixed.js"></script>
    <script src="libs/codemirror/mode/xml/xml.js"></script>
    <script src="libs/codemirror/mode/javascript/javascript.js"></script>
    <script src="libs/codemirror/mode/css/css.js"></script>
    <script src="libs/codemirror/mode/vbscript/vbscript.js"></script>
    
    <script type="text/javascript" src="skins/cute.js"></script>
    {CustomJS}
    <script></script>
    <style type='text/css'></style>
</head>
<body>
<header>
<nav class="navbar navbar-inverse" role="banner">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="index.php">CuteNews <small>news management system</small></a>
        </div>
        <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav navbar-right">
                {menu}
            </ul>
        </div>
    </div>
</nav>
</header>

<!--SELF-->
<!--MAIN area-->
<section>
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				<!--{header-text} - {header-time}-->
				{breadcrumbs}
			</div>
		</div>
	</div>
</section>

<!--
<div class="row">
  <div class="col-xs-12"> -->

HTML;

// ***********
// Skin FOOTER
// ***********
$skin_footer = <<<HTML
	<!--/div>
</div-->
	
<!--FIN MAIN area-->
<section>
	<div class="container">
		<div class="row">
			<div class="col-sm-12 text-center" style="padding: 0 0 16px 0">
                <!-- Execution time: {exec-time} -->
                {copyrights}
			</div>
		</div>
	</div>
</section>
</body></html>

HTML;
?>