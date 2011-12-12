<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
  <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
  <title> Test </title>
<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/smoothness/jquery-ui.css" type="text/css" media="all" />
 </head>

 <body>
<div id="tips"></div>
<div class="infos">
    <dl class="worth"><dt>原价：</dt><dd>￥<span style="text-decoration:line-through;">331</span></dd></dl>
    <dl class="discount"><dt>折扣：</dt><dd>3折</dd></dl>
    <dl class="amount"><dt><strong>团购</strong>价：</dt><dd>￥99.9</dd></dl>
    <dl class="district"><dt>地区：</dt><dd>成都</dd></dl>
    <dl class="timeleft"><dt>距团购结束还有</dt><dd class="j-counter"  data-start="1323450000" data-end="1323968400">3天11小时4分</dd></dl>
</div>
<div class="infos">
    <dl class="worth"><dt>原价：</dt><dd>￥<span style="text-decoration:line-through;">331</span></dd></dl>
    <dl class="discount"><dt>折扣：</dt><dd>3折</dd></dl>
    <dl class="amount"><dt><strong>团购</strong>价：</dt><dd>￥99.9</dd></dl>
    <dl class="district"><dt>地区：</dt><dd>成都</dd></dl>
    <dl class="timeleft"><dt>距团购结束还有</dt><dd class="j-counter"  data-start="1323450000" data-end="1323968400">3天11小时4分</dd></dl>
</div>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js" type="text/javascript"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js" type="text/javascript"></script>
<script src="http://jquery-ui.googlecode.com/svn/tags/latest/external/jquery.bgiframe-2.1.2.js" type="text/javascript"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/i18n/jquery-ui-i18n.min.js" type="text/javascript"></script>
<script src="assets/js/et.js" type="text/javascript"></script>
<script type="text/javascript">
<!--
var timestamp = <?php echo time(); ?>;
et.tool.CountDown.init('.infos', '.j-counter', {startTime:timestamp, delay: 1});
et.log('et js loaded');
//et.utils.bookmark('http://zetng.com', '虚谷宅', '#tips')
//-->
</script>
 </body>
</html>
