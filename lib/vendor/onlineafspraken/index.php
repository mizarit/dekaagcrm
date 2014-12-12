<?php if(!session_id()) session_start(); ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
  <head>
  <title>OnlineAfspraken.nl widget</title>
  <link rel="stylesheet" href="theme/default/css/widget.css" type="text/css" />
	<script type="text/javascript" src="js/prototype.js"></script>
	<script type="text/javascript" src="js/calendarview.js"></script>
	<script type="text/javascript" src="js/widget.js"></script> 
</head>
<body>
<?php 
require_once('lib/widget.php');
Widget::show();
?>
</body>
</html>