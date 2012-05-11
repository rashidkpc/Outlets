<?php

include('config.php');

if ($_SERVER['PHP_AUTH_USER'] != $username && 
  $_SERVER['PHP_AUTH_PW'] != $password) {
  header('WWW-Authenticate: Basic realm="Outlets"');
  header('HTTP/1.0 401 Unauthorized');
  echo '401 Unauthorized';
  exit;
}

$id_array = explode('_',$_POST['id']);
$edit_name = $id_array[0];
$edit_port = $id_array[1];
$edit_what = $id_array[2];

$edit_template = $apcs[$edit_name]['template'];
$edit_community = $template[$edit_template]['community'];
$edit_nameoid = $template[$edit_template]['nameoid'].
  ($edit_port+$template[$edit_template]['nameoffset']);
$edit_statusoid = $template[$edit_template]['statusoid'].
  ($edit_port+$template[$edit_template]['statusoffset']);

if($edit_what == 'name') {
  if(snmpset(
    $apcs[$edit_name]['host'], 
    $edit_community, $edit_nameoid, 
    "s", 
    trim($_POST['value'])
  )) {
    $hostname = snmpget(
      $apcs[$edit_name]['host'], $edit_community, $edit_nameoid);
    $hostname = preg_replace("/INTEGER|STRING|\"|:/" , "", $hostname);
    $hostname = trim($hostname);
    echo $hostname;
  } else {
    echo trim($_POST['value'])."*";
  }
}

if($edit_what == 'status') {
  $onvalue = 1;
  $offvalue = 2;
  $statustype = 'i';

  if(isset($template[$edit_template]['onvalue']))
    $onvalue = $template[$edit_template]['onvalue'];
  if(isset($template[$edit_template]['offvalue']))
    $offvalue = $template[$edit_template]['offvalue'];
  if(isset($template[$edit_template]['statustype']))
    $statustype = $template[$edit_template]['statustype'];
  if($_POST['value'] == 'On'){
    if(snmpset(
      $apcs[$edit_name]['host'], 
      $edit_community, 
      $edit_statusoid, 
      $statustype, 
      $onvalue
    )) {
      $status = trim(snmpget(
        $apcs[$edit_name]['host'], $edit_community, $edit_statusoid));
      $status = preg_replace("/INTEGER:|STRING:|\"/","",$status);
      if($status == $onvalue) 
        $status = "On";
      if($status == $offvalue) 
        $status = "Off";
      echo $status;
    } else {
      echo trim($_POST['value'])."*";
    }
  }
  if($_POST['value'] == 'Off'){
    if(snmpset(
      $apcs[$edit_name]['host'], 
      $edit_community, 
      $edit_statusoid, 
      $statustype, 
      $offvalue
    )) {
    $status = trim(snmpget(
      $apcs[$edit_name]['host'], $edit_community, $edit_statusoid));
    $status = preg_replace("/INTEGER:|STRING:|\"/","",$status);
    if($status == $onvalue) 
      $status = "On";
    if($status == $offvalue) 
      $status = "Off";
    echo $status;
    } else {
      echo trim($_POST['value'])."*";
    }
  }
  if($_POST['value'] == 'GET'){
    $status = trim(
      snmpget($apcs[$edit_name]['host'], $edit_community, $edit_statusoid));
    $status = preg_replace("/INTEGER:|STRING:|\"/","",$status);
    if($status == $onvalue) $status = "On";
    if($status == $offvalue) $status = "Off";
    echo $status;
  }
}

?>
