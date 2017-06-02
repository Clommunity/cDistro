<?php
//controllers/monitor-aas.php

$urlpath="/monitor-aas";
$install_script="https://raw.githubusercontent.com/Clommunity/monitor/master/getgithub";

function index(){
	global $urlpath, $staticFile;
	
	$page .= hlc(t("Monitor as a Service"));
        $page .= hl(t("Monitor/Loggging extended service to Cloudy"),4);
	$page .= "<div class='alert alert-error text-center'>".t("Monitor as a Service not installed yet")."</div>\n";
	$page .= par(t("How to install?<br>Just click Install, and wait till it finishes. It will update Cloudy and services aces accordingly, and restart SERF."));
	$page .= addButton(array('label'=>t("Install"),'class'=>'btn btn-success', 'href'=>"$staticFile$urlpath/install"));

	return(array('type' => 'render','page' => $page));
}

function install() {
	global $install_script,$staticFile,$urlpath;
	//Just to make sure we get the install script error!
	$cmd = "cd /tmp/ && mkdir monitor_inst && cd monitor_inst";
	execute_program_shell($cmd)['output'];

	$cmd = "cd /tmp/monitor_inst/ && curl -k  ".$install_script." | sh - 2>&1";
	$ret = execute_program_shell($cmd)['output'];

	$cmd = "cd /tmp ; rm -rf monitor_inst";
	execute_program_shell($cmd)['output'];
	
	if (strpos($ret,"Not") !== false)
                setFlash(t("Monitor Service Error, msg: ".$ret),"error");
	else
		setFlash(t("Monitor Service installed"),"success");

	return(array('type'=>'redirect','url'=>$staticFile.$urlpath));
}
