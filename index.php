<?php
include("config.php");

if ($_SERVER['PHP_AUTH_USER'] != $username && 
  $_SERVER['PHP_AUTH_PW'] != $password) {
  header('WWW-Authenticate: Basic realm="Outlets"');
  header('HTTP/1.0 401 Unauthorized');
  echo '401 Unauthorized';
  exit;
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title>OUTLETS: know thy outlets well</title>
  <meta name="robots" content="index, follow" />
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <meta name="author" content="RapidxHTML" />
  <link href="css/style.css" rel="stylesheet" type="text/css" />
  <link href="css/jquery.alerts.css" rel="stylesheet" type="text/css" />
  <!--[if lte IE 7]><link href="css/iehacks.css" rel="stylesheet" type="text/css" /><![endif]-->
  <script type="text/javascript" src="js/jquery.js"></script>
  <script type="text/javascript" src="js/jeditable.js"></script>
  <script type="text/javascript" src="js/highlight.js"></script>
  <script type="text/javascript" src="js/jquery-impromptu.js"></script>
  <script type="text/javascript">
   $(document).ready(function() {
      $('.edit').editable('backend.php', {
        indicator : 'Saving...',
        width: '220px',
        maxlength: '26',
        placeholder   : '<i class=placeholder>None</i>'
      });
      $('.status').editable(function(value, settings) {
        var allowed;
        var id_array=$(this).attr('id').split("_");
        var device_name = $('#'+id_array[0]+'_'+id_array[1]+'_name').text();
        var element = id_array[0]+'_'+id_array[1]+'_status';
        $.prompt(
          'Are you really sure you want to turn'+
          ' <span style="font-weight: bold;color: #f00">'+
          value.toUpperCase()+
          '</span> <u>'+device_name+
          '</u>?<br>(Port '+id_array[1]+' on '+id_array[0]+')', 
          {   
            buttons: { Ok: element+'_'+value, Cancel: element+'_GET' },
            opacity: 0.4,
            callback: statusChange
          }
        );
      }, 
      {
        data   : "{'On':'On','Off':'Off'}",
        submit : "OK",
        type   : "select",
        indicator : 'Saving...',
        placeholder   : ''
      });
      function statusChange(v,m,f){
        var id_array=v.split("_");
        var element = id_array[0]+'_'+id_array[1]+'_status';
        $('#'+element).load('backend.php', {id: element, value: id_array[3]});
        $('.status').highlight('Off');
        $('.status').highlight(' Off');
      }
      $('.status').highlight('Off');
    });
  </script>
  <!--[if IE 6]>
  <script type="text/javascript" src="js/ie6pngfix.js"></script>
  <script type="text/javascript">
  DD_belatedPNG.fix('img, ul, ol, li, div, p, a, h1, h2, h3, h4, h5, h6, input, span');
  </script>
  <![endif]-->
</head>

<body id="page">

<!-- page setup -->
<div id="pagebg">

  <!-- wrapper -->
  <div class="rapidxwpr floatholder">
  
  <!-- header -->
  <div id="header">
   
    <!-- logo -->
    <a href="index.php"><img id="logo" src="images/logo.png" alt="Home" title="Home" /></a>
    <!-- / logo -->
  
    <!-- tagline -->
    <a href="#" onClick="window.location.reload();$('#reloading').attr('src', 'images/reload-anim.gif');">
      <div id=tagline>
        <span id=pages><img id=reloading src="images/reload.gif"></span>
      </div>
    </a>
<?php
  if(isset($_GET['go'])) {
    foreach($apcs as $pdu) {
      $current = "";
      if($pdu['group'] == $_GET['page']) {
        $current = "_current";
      }
      if($group_name != $pdu['group']) {
        echo "<a onClick=\"$('#reloading').attr('src', 'images/reload-anim.gif');\" href=index.php?go&page=".
          urlencode($pdu['group']).
          "><div id=tagline".$current."><span id=pages>".
          $pdu['group']."</span></div></a>"." ";
      }
      $group_name = $pdu['group'];
    }
  }
?>
    <!-- / tagline -->
  
  </div>
  <!-- / header -->
  
  <!-- main body -->
  <div id="middle">
    <!-- websites -->
    <div id="website" class="clearingfix">
    

    <!-- / websites -->
    
<?php
if(isset($_GET['go'])) {
  foreach($apcs as $name => $pdu) {
    if($_GET['page'] == $pdu['group']) {
      $onvalue = 1;
      $offvalue = 2;
      $community = $template[$pdu['template']]['community'];
      if(isset($template[$pdu['template']]['onvalue']))
        $onvalue = $template[$pdu['template']]['onvalue'];
      if(isset($template[$pdu['template']]['offvalue']))
        $offvalue = $template[$pdu['template']]['offvalue'];
      echo "<div class='website-col'>
        <h2>".$name."</h2>
        Location".preg_replace("/STRING|\"/" , "", snmpget(
          $pdu['host'], 
          $community, 
          $template[$pdu['template']]['locationoid'],
          $template[$pdu['template']]['timeout'])
        ).
        " &nbsp&nbsp <a href=http://".$pdu['host']."> Configure >></a>
        <ul>";
      $ports = $template[$pdu['template']]['ports'];
      for($i=1;$i<=$ports;$i++) {
        $hostname = snmpget(
          $pdu['host'], 
          $community, 
          $template[$pdu['template']]['nameoid'].
            ($i+$template[$pdu['template']]['nameoffset']),
          $template[$pdu['template']]['timeout']
        );
        $status = trim(snmpget(
          $pdu['host'], 
          $community, 
          $template[$pdu['template']]['statusoid'].
            ($i+$template[$pdu['template']]['statusoffset']),
          $template[$pdu['template']]['timeout']
        ));
        $status = preg_replace("/INTEGER:|STRING:|\"/","",$status);
        if($status == $onvalue) 
          $status = "<span id='".$name."_".$i.
            "_status' class=status>On</span>";
        if($status == $offvalue) 
          $status = "<span id='".$name."_".$i.
            "_status' class=status>Off</span>";
        $hostname = preg_replace("/STRING/" , "", $hostname); 
        $hostname = preg_replace("/\"|:/" , "", $hostname);
        $hostname = trim($hostname);
        $span_class = "";
        if($template[$pdu['template']]['writeable'] == 1) {
          $span_class = "class='edit'";
        }
        echo "<li>Port ".$i." ".$status.
          " <span $span_class id='".$name."_".$i."_name'>".
          $hostname."</span></li>\n";
      }
      echo "</ul></div>";
    }
  }
} else {
  $default_page = urlencode($apcs[key($apcs)]['group']);
  echo "<h2>Welcome</h2>This page will attempt to list the name and status of".
        " every outlet on each power distribution unit.<br>I'm going to use ".
        "SNMP to poll them all, which is pretty slow. This may take awhile, ".
        "so please be patient. ".
        "<h4> <a onClick=\"document.getElementById('loading').style.display='inline';\" href=index.php?go&page=".
        $default_page.">Click here to get started</a>".
        "<img id=loading style='display:none;padding-left: 30px' src=images/loading.gif></h4><br>
    ";
}


?>
  </div>
  <!-- / websites -->
  <center>* Change not confirmed by PDU. Wait a few minutes and reload the page 
    to see if your change took effect.
  </center>  
  </div>
  <!-- / main body -->
  
  </div>
  <!-- / wrapper -->

</div>
<!-- / page setup -->

</body>
</html>

