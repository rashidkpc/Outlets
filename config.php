<?php
// ---- Configuration ---- //

// --- AUTH --- //

$username = 'admin';
$password = 'admin';

/* --- PDUs ---
* The PDU definitions define the hosts to connect to and the template to use.  
* The best way to add a new PDU is to copy an existing one.
* 
* KEY: Short name of PDU. [A-Z][a-z][0-9] and _. No other characters. 
*    Should not start with a number.
* host: IP or hostname (must resolve) to connect to
* template: Template to use to access SNMP data
* group: 
*
*/

$apcs["apc1"] = array(
  "host"      => "apc1.example.com", 
  "template"  => "apc", 
  "group"     => "Downtown Colo"
);

$apcs["apc2"] = array(
  "host"      => "apc2.example.com", 
  "template"  => "apc", 
  "group"     => "Downtown Colo"
);

$apcs["tl2a"] = array(
  "host"      => "tl2a.example.com", 
  "template"  => "tripplite", 
  "group"     => "Uptown Office"
);


/* --- Templates ---
* Templates describe a standard way of accessing a given PDU type's outlet 
* name and status. The best way to add a new template is to copy an existing 
* one, being sure to change the name.
*
* KEY: Template name
* community: Name of SNMP community to access
* ports: Total number of outlets to scan
* nameoid: Base of the OID containing the names out of the outlets 
* statusoid: Base of the OID containing the status (on/off) of the outlets
* locationoid: OID containing the location of the PDU
* nameoffset: Number to start at when collecting the outlet names (usually 0)
* statusoffset: Number to start at when collecting the outlet status (usually 0)
* timeout: Time in microseconds (1000 microseconds per millisecond) to wait for 
*   snmp response
* writeable: Does the device also SNMP SET, 0 = no, 1 = yes.
* onvalue: Number returned for status "ON" (OPTIONAL - Default: 1)
* offvalue: Number returned for status "OFF" (OPTIONAL - Default: 2)
* statustype: Type of SNMP value returned "i" = integer, "s" = string 
*   (OPTIONAL - Default: "i")
*/

$template["apc"] = array(
  "community" => "myWriteCommunity",
  "ports" => 8,
  "nameoid" => ".1.3.6.1.4.1.318.1.1.12.3.4.1.1.2.",
  "statusoid" => ".1.3.6.1.4.1.318.1.1.12.3.3.1.1.4.",
  "locationoid" => "sysLocation.0",
  "nameoffset" => 0,
  "statusoffset" => 0,
  "timeout" => 100000,
  "writeable" => 1
  );

$template["tripplite"] = array(
  "community" => "myWriteCommunity",
  "ports" => 24,
  "nameoid" => ".1.3.6.1.4.1.850.10.2.3.5.1.5.1.",
  "statusoid" => ".1.3.6.1.4.1.850.10.2.3.1.1.6.1.",
  "locationoid" => ".1.3.6.1.4.1.850.10.2.3.1.1.7.1.1",
  "nameoffset" => 0,
  "statusoffset" => 74,
  "timeout" => 100000,
  "writeable" => 1,
  "onvalue" => 2,
  "offvalue" => 1,
  "statustype" => "s"
  );

?>

