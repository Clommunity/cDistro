<?php
//gvod.php

$binary_url="http://cloud7.sics.se/new-gvod/gvod.deb";
$binary_deb="gvod.deb";
$install_name="gvod-webservice";
$urlpath='/gvod';

$service_name="gvod-webservice";
$service_log="gvod-webservice.log";
$service_script="/etc/init.d/gvod-webservice";

$config_file="/etc/gvod/application.conf";
$log_file="/var/log/gvod-webservice.log";




function index_get(){

	global $config_file;
	global $urlpath;
    //$disabled = '';

    $page = "";
	$buttons = "";
	$submitButtons = "";
	$stati = "";
	$config_file_content = "";
	$form_fields = "";

	
	// if (isSesionValue('flash')) {
	// 	$flash = getSessionValue('flash');
	// 	unsetSessionValue('flash');
	// 	$flash_class = getSessionValue('flash_class');
	// 	unsetSessionValue('flash_class');
	// 	$page .= "<div class='$flash_class text-center'>$flash</div>\n";
	// }
	

	//$variables = load_conffile($getinconf_file);
	$page .= hlc(t("gvod_title"));
	$page .= hl(t("gvod_subtitle"),4);
    $page .= par(t("gvod_description"));

    $stati .= hl(t('gvod_status'), 4);

    $hasJava = _check_java();

    if ($hasJava['return'] == 0) {
    	$stati .= "<div class='alert alert-success text-center'>".t('gvod_java_installed_true')." (".$hasJava['output'][0].")</div>\n";
    } else {
    	$stati .= "<div class='alert alert-fail text-center'>".t('gvod_java_installed_false')."</div>\n";
	}

    $hasgvod = _check_gvod();

    if ($hasgvod) {
    	$stati .= "<div class='alert alert-success text-center'>".t('gvod_gvod_installed_true')."</div>\n";
    	$buttons .= addButton(array('label'=>t("gvod_button_uninstall"),'class'=>'btn btn-danger', 'href'=>"$urlpath/uninstall", 'divOptions'=>array('class'=>'btn-group')));
    
    	$config_file_content .= hl(t("gvod_config_file"), 4);
    	$config_file_content .= par(t("gvod_config_path")." $config_file: ");
    	$config_file_content .= ptxt(file_get_contents($config_file));

    	$buttons .= addButton(array('label'=>t("gvod_button_log"),'class'=>'btn btn-success', 'href'=>"$urlpath/logfile", 'divOptions'=>array('class'=>'btn-group')));
	
     	$isRunning = _check_running();
     	if ($isRunning) {
			$stati .= "<div class='alert alert-success text-center'>".t('gvod_running_true')."</div>\n";
    		$buttons .= addButton(array('label'=>t("gvod_button_stop"),'class'=>'btn btn-danger', 'href'=>"$urlpath/stop", 'divOptions'=>array('class'=>'btn-group')));
    	} else {
    		$stati .= "<div class='alert alert-fail text-center'>".t('gvod_running_false')."</div>\n";
    		$buttons .= addButton(array('label'=>t("gvod_button_start"),'class'=>'btn btn-success', 'href'=>"$urlpath/start", 'divOptions'=>array('class'=>'btn-group')));
		
    		$variable = _get_config_values();

			$form_fields .= hl(t("gvod_config_edit"), 4);
    		$form_fields .= createForm(array('class'=>'form-horizontal'));
    		$form_fields .= addInput('GVOD_LIB',t('gvod_form_lib'),$variable,array('type'=>'text','required'=>''),false,t('gvod_form_lib_desc'));
    		$form_fields .= addInput('GVOD_LOCAL_ID',t('gvod_form_local_id'),$variable,array('type'=>'number', 'required'=>''),false,t('gvod_form_local_id_desc'));
    		$form_fields .= addInput('GVOD_LOCAL_IP',t('gvod_form_local_ip'),$variable,array('type'=>'text', 'pattern'=>'\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}','required'=>''),false,t('gvod_form_local_ip_desc'));
    		$form_fields .= addInput('GVOD_LOCAL_PORT',t('gvod_form_local_port'),$variable,array('type'=>'number', 'min' => '1024', 'max' => '65535', 'required'=>''),false,t('gvod_form_local_port_desc'));
    		$form_fields .= addInput('GVOD_CARACALDB_IP',t('gvod_form_caracaldb_ip'),$variable,array('type'=>'text', 'pattern'=>'\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}','required'=>''),false,t('gvod_form_caracaldb_ip'));
    		$form_fields .= addInput('GVOD_CARACALDB_PORT',t('gvod_form_caracaldb_port'),$variable,array('type'=>'number', 'min' => '1024', 'max' => '65535', 'required'=>''),false,t('gvod_form_caracaldb_port'));
    		
    		$submitButtons .= addSubmit(array('label'=>t('gvod_button_save')));
		}	
    } else {
    	$stati .= "<div class='alert alert-fail text-center'>".t('gvod_gvod_installed_false')."</div>\n";
    	$buttons .= addButton(array('label'=>t("gvod_button_install"),'class'=>'btn btn-success', 'href'=>"$urlpath/install", 'divOptions'=>array('class'=>'btn-group')));
	}

    $page .= $stati . $config_file_content . $form_fields . $buttons . $submitButtons;
	return(array('type' => 'render','page' => $page));
}

function index_post(){

	global $staticFile, $urlpath;
	global $config_file;

	$config_values = array();
	foreach ($_POST as $key => $value) {
		if (substr_compare($key, "GVOD_", 0, 5, true) == 0) {
			$config_values[$key] = $value;
		}
	}

	file_put_contents($config_file, 'vod.address.id='.$config_values['GVOD_LOCAL_ID'].PHP_EOL); // reset file
	file_put_contents($config_file, 'vod.address.ip="'.$config_values['GVOD_LOCAL_IP'].'"'.PHP_EOL, FILE_APPEND | LOCK_EX);
	file_put_contents($config_file, 'vod.address.port='.$config_values['GVOD_LOCAL_PORT'].PHP_EOL, FILE_APPEND | LOCK_EX);
	file_put_contents($config_file, 'vod.libDir='.$config_values['GVOD_LIB'].PHP_EOL, FILE_APPEND | LOCK_EX);
	file_put_contents($config_file, 'bootstrap.server.address.id='.$config_values['GVOD_LOCAL_ID'].PHP_EOL, FILE_APPEND | LOCK_EX);
	file_put_contents($config_file, 'bootstrap.server.address.ip="'.$config_values['GVOD_LOCAL_IP'].'"'.PHP_EOL, FILE_APPEND | LOCK_EX);
	file_put_contents($config_file, 'bootstrap.server.port='.$config_values['GVOD_LOCAL_PORT'].PHP_EOL, FILE_APPEND | LOCK_EX);
	file_put_contents($config_file, 'caracal.address.ip="'.$config_values['GVOD_CARACALDB_IP'].'"'.PHP_EOL, FILE_APPEND | LOCK_EX);
	file_put_contents($config_file, 'caracal.address.port='.$config_values['GVOD_CARACALDB_PORT'].PHP_EOL, FILE_APPEND | LOCK_EX);

	setFlash(t('gvod_saved_config'),"success");
	return(array('type'=> 'redirect', 'url' => $staticFile.$urlpath));
}

function start() {
	global $urlpath, $staticFile, $service_name, $service_log;

    $gvod_log_clean_cmd = "cd /var/log && rm ".$service_log;
	execute_shell($gvod_log_clean_cmd);

	$ret = execute_shell("service $service_name start");
	if ($ret['return'] != 0) {
		setFlash(t("gvod_start_fail"));
		return(array('type'=> 'redirect', 'url' => $staticFile.$urlpath)); // abort
	}
	setFlash(t("gvod_start_success"), "success");
	return(array('type'=> 'redirect', 'url' => $staticFile.$urlpath));
}

function stop() {
	global $urlpath, $staticFile, $service_name;

	$ret = execute_shell("service $service_name stop");
	if ($ret['return'] != 0) {
		setFlash(t("gvod_stop_fail"));
		return(array('type'=> 'redirect', 'url' => $staticFile.$urlpath)); // abort
	}
	setFlash(t("gvod_stop_success"), "success");
	return(array('type'=> 'redirect', 'url' => $staticFile.$urlpath));
}

function install() {
	global $urlpath, $staticFile, $binary_url, $binary_deb;

	$hasJava = _check_java();
	if ($hasJava['return'] != 0) {
		$java_install_cmd = "apt-get install openjdk-6-jre";
		$ret = execute_shell($java_install_cmd);
		if ($ret['return'] != 0) {
			setFlash(t("gvod_java_install_fail"));
			return(array('type'=> 'redirect', 'url' => $staticFile.$urlpath)); // abort
		}
	}
	$gvod_clean_cmd = "cd /tmp && rm ".$binary_deb;
	execute_shell($gvod_clean_cmd);

	$gvod_get_cmd = "cd /tmp && wget ".$binary_url;
	$ret = execute_shell($gvod_get_cmd);
	if ($ret['return'] != 0) {
		setFlash(t("gvod_download_fail"));
		return(array('type'=> 'redirect', 'url' => $staticFile.$urlpath)); // abort
	}

	$gvod_install_cmd = "cd /tmp && dpkg -i $binary_deb";
	$ret = execute_shell($gvod_install_cmd);
	if ($ret['return'] != 0) {
		setFlash(t("gvod_install_fail"));
		return(array('type'=> 'redirect', 'url' => $staticFile.$urlpath)); // abort
	}

	setFlash(t("gvod_install_success"), "success");
	return(array('type'=> 'redirect', 'url' => $staticFile.$urlpath));
}

function uninstall() {
	global $install_name, $urlpath, $staticFile, $service_name;

	$gvod_uninstall_cmd = "dpkg -r $install_name";
	$ret = execute_program($gvod_uninstall_cmd);
	if ($ret['return'] != 0) {
		setFlash(t($ret['output'][0]));
		return(array('type'=> 'redirect', 'url' => $staticFile.$urlpath)); // abort
	}
	setFlash(t("gvod_uninstall_success"), "success");
	return(array('type'=> 'redirect', 'url' => $staticFile.$urlpath));
}

function logfile() { // nothing fancy...just quick and dirty^^
	global $urlpath, $staticFile, $log_file;
	$page = "";
	$buttons = "";

	$page .= hlc(t("gvod_title"));
	$page .= hl(t("gvod_log"),4);
	$page .= par(t("gvod_log_path")." $log_file: ");
	$page .= '<a href="#bottom">'.t("gvod_scroll_down").'</a>';
    $page .= ptxt(file_get_contents($log_file));
    $page .= '<hr id="bottom" />';


    $buttons .= addButton(array('label'=>t("gvod_button_status"),'class'=>'btn btn-success', 'href'=>"$urlpath", 'divOptions'=>array('class'=>'btn-group')));
	$buttons .= addButton(array('label'=>t("gvod_button_reload"),'class'=>'btn btn-success', 'href'=>"$urlpath/logfile", 'divOptions'=>array('class'=>'btn-group')));
		

    $page .= $buttons;
	return(array('type' => 'render','page' => $page));
}

function _check_java() {
	$ret = execute_program("java -version");
	//print_r($ret);
	return $ret;
}

function _check_gvod() {
	global $service_script;
	$ret = execute_shell("ls $service_script");
	return($ret['return'] == 0);
}

function _check_running() {
	global $service_name;

	$ret = execute_program("service $service_name status");
	// setFlash(t($ret['output'][0]));
	if(strpos($ret['output'][0],'dead') == true) {
		return false;
	}
	if(strpos($ret['output'][0],'not') == true) {
		return false;
	}
	return true; // check if the output contains 'not' (as in "Service not running");
}

function _get_config_values() {
	$variables = array();
	$variables['GVOD_LOCAL_ID'] = "1";
	$variables['GVOD_LOCAL_IP'] = _get_guifi_ip();
	$variables['GVOD_LOCAL_PORT'] = "23456";
	$variables['GVOD_CARACALDB_IP'] = "10.228.207.42";
	$variables['GVOD_CARACALDB_PORT'] = "45678";

	return $variables;
}

function _get_guifi_ip() {
	$ret = execute_program("ifconfig | grep 'inet addr' | awk '{ print $2 }' | awk -F':' '{ print $2 }'");
	foreach ($ret['output'] as $ip) {
		if (substr_compare($ip,"10.",0,3,true) == 0) {
			return $ip;
		}
	}
	return $ret['output'][0];
}

?>