<?php
    include ("session.php");
?>


<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Marketing and Product Library.</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/ionicons-2.0.0/ionicons.min.css">
  <!-- Ionicons 
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">-->
 <link rel="stylesheet" href="plugins/fontAwesome/css/font-awesome.min.css">
  <!--<link rel="stylesheet" href="plugins/fontAwesome/font-awesome.min.css">-->
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
  <!-- AdminLTE Skins. We have chosen the skin-blue for this starter
        page. However, you can choose any other skin. Make sure you
        apply the skin class to the body tag so the changes take effect.
  -->
  <link rel="stylesheet" href="dist/css/skins/skin-blue.min.css">
  
  <!-- Smoke -->
  <link rel="stylesheet" href="bootstrap/css/smoke.min.css" >
  
  <link rel="stylesheet" href="bootstraptable/bootstrap-table.min.css" >


  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
  
  <style>

	@media (max-width: @screen-xs) {
		body .content-wrapper{font-size: 12px;}
	}

	@media (max-width: @screen-sm) {
		body .content-wrapper{font-size: 16px;}
	}


	.content-wrapper h5{
		font-size: 1.8em;
	}
	
	input[type=checkbox], input[type=radio]
	{
	  /* Double-sized Checkboxes */
	  -ms-transform: scale(1.5); /* IE */
	  -moz-transform: scale(1.5); /* FF */
	  -webkit-transform: scale(1.5); /* Safari and Chrome */
	  -o-transform: scale(1.5); /* Opera */
	  padding: 10px;
	}

	/* Might want to wrap a span around your checkbox text */
	.checkboxtext
	{
	  /* Checkbox text */
	  font-size: 110%;
	  display: inline;
	}
	</style>  
<style>

@import url(./plugins/fontAwesome/fonts/thsarabunnew.css);

  @font-face {
    font-family: 'THSarabunNew';
    src: url('thsarabunnew-webfont.eot');
    src: url('thsarabunnew-webfont.eot?#iefix') format('embedded-opentype'),
         url('thsarabunnew-webfont.woff') format('woff'),
         url('thsarabunnew-webfont.ttf') format('truetype');
    font-weight: normal;
    font-style: normal;

}

@font-face {
    font-family: 'THSarabunNew';
    src: url('thsarabunnew_bolditalic-webfont.eot');
    src: url('thsarabunnew_bolditalic-webfont.eot?#iefix') format('embedded-opentype'),
         url('thsarabunnew_bolditalic-webfont.woff') format('woff'),
         url('thsarabunnew_bolditalic-webfont.ttf') format('truetype');
    font-weight: bold;
    font-style: italic;

}

@font-face {
    font-family: 'THSarabunNew';
    src: url('thsarabunnew_italic-webfont.eot');
    src: url('thsarabunnew_italic-webfont.eot?#iefix') format('embedded-opentype'),
         url('thsarabunnew_italic-webfont.woff') format('woff'),
         url('thsarabunnew_italic-webfont.ttf') format('truetype');
    font-weight: normal;
    font-style: italic;

}

@font-face {
    font-family: 'THSarabunNew';
    src: url('thsarabunnew_bold-webfont.eot');
    src: url('thsarabunnew_bold-webfont.eot?#iefix') format('embedded-opentype'),
         url('thsarabunnew_bold-webfont.woff') format('woff'),
         url('thsarabunnew_bold-webfont.ttf') format('truetype');
    font-weight: bold;
    font-style: normal;

}
  
body, h1, h2, h3, h4, h5, h6 {
  font-family: 'THSarabunNew', sans-serif;
  font-weight: bold;
}
</style>


	<!-- fancybox CSS -->
  <link rel="stylesheet" type="text/css" href="plugins/fancybox-master/dist/jquery.fancybox.min.css">
</head>
<body class="hold-transition skin-blue sidebar-mini">