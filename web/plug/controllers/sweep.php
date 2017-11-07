<?php
//sweep.php

$binary_url="http://cloud7.sics.se/new-gvod/sweep.deb";
$binary_deb="sweep.deb";
$install_name="sweep";
$urlpath='/sweep';

$service_name="sweep-webservice";
$service_log="sweep-webservice.log";
$service_script="/etc/init.d/sweep-webservice";

$config_file="/etc/sweep/application.conf";
$log_file="/var/log/sweep-webservice.log";




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
	$page .= hlc(t("sweep_title"));
	$page .= hl(t("sweep_subtitle"),4);
    $page .= par(t("sweep_description"));

    $stati .= hl(t('sweep_status'), 4);

    $hasJava = _check_java();

    if ($hasJava['return'] == 0) {
    	$stati .= "<div class='alert alert-success text-center'>".t('sweep_java_installed_true')." (".$hasJava['output'][0].")</div>\n";
    } else {
    	$stati .= "<div class='alert alert-fail text-center'>".t('sweep_java_installed_false')."</div>\n";
	}

    $hassweep = _check_sweep();

    if ($hassweep) {
    	$stati .= "<div class='alert alert-success text-center'>".t('sweep_sweep_installed_true')."</div>\n";
    	$buttons .= addButton(array('label'=>t("sweep_button_uninstall"),'class'=>'btn btn-danger', 'href'=>"$urlpath/uninstall", 'divOptions'=>array('class'=>'btn-group')));
    
    	$config_file_content .= hl(t("sweep_config_file"), 4);
    	$config_file_content .= par(t("sweep_config_path")." $config_file: ");
    	$config_file_content .= ptxt(file_get_contents($config_file));

    	$buttons .= addButton(array('label'=>t("sweep_button_log"),'class'=>'btn btn-success', 'href'=>"$urlpath/logfile", 'divOptions'=>array('class'=>'btn-group')));
	
     	$isRunning = _check_running();
     	if ($isRunning) {
			$stati .= "<div class='alert alert-success text-center'>".t('sweep_running_true')."</div>\n";
    		$buttons .= addButton(array('label'=>t("sweep_button_stop"),'class'=>'btn btn-danger', 'href'=>"$urlpath/stop", 'divOptions'=>array('class'=>'btn-group')));
    	} else {
    		$stati .= "<div class='alert alert-fail text-center'>".t('sweep_running_false')."</div>\n";
    		$buttons .= addButton(array('label'=>t("sweep_button_start"),'class'=>'btn btn-success', 'href'=>"$urlpath/start", 'divOptions'=>array('class'=>'btn-group')));
		
    		$variable = _get_config_values();

			$form_fields .= hl(t("sweep_config_edit"), 4);
    		$form_fields .= createForm(array('class'=>'form-horizontal'));
    		$form_fields .= addInput('SWEEP_LOCAL_SEED',t('sweep_form_local_seed'),$variable,array('type'=>'number', 'min' => '0', 'max' => '65535', 'required'=>''),false,t('sweep_form_local_seed_desc'));
    		$form_fields .= addInput('SWEEP_LOCAL_IP',t('sweep_form_local_ip'),$variable,array('type'=>'text', 'pattern'=>'\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}','required'=>''),false,t('sweep_form_local_ip_desc'));
    		$form_fields .= addInput('SWEEP_LOCAL_PORT',t('sweep_form_local_port'),$variable,array('type'=>'number', 'min' => '1024', 'max' => '65535', 'required'=>''),false,t('sweep_form_local_port_desc'));
			$form_fields .= addInput('SWEEP_BOOTSTRAP_IP',t('sweep_form_bootstrap_ip'),$variable,array('type'=>'text', 'pattern'=>'\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}','required'=>''),false,t('sweep_form_bootstrap_ip_desc'));
    		$form_fields .= addInput('SWEEP_BOOTSTRAP_PORT',t('sweep_form_bootstrap_port'),$variable,array('type'=>'number', 'min' => '1024', 'max' => '65535', 'required'=>''),false,t('sweep_form_bootstrap_port_desc'));    		
    		$submitButtons .= addSubmit(array('label'=>t('sweep_button_save')));
		}	
    } else {
    	$stati .= "<div class='alert alert-fail text-center'>".t('sweep_sweep_installed_false')."</div>\n";
    	$buttons .= addButton(array('label'=>t("sweep_button_install"),'class'=>'btn btn-success', 'href'=>"$urlpath/install", 'divOptions'=>array('class'=>'btn-group')));
	}

    $page .= $stati . $config_file_content . $form_fields . $buttons . $submitButtons;
	return(array('type' => 'render','page' => $page));
}

function index_post(){

	global $staticFile, $urlpath;
	global $config_file;

	$config_values = array();
	foreach ($_POST as $key => $value) {
		if (substr_compare($key, "SWEEP_", 0, 5, true) == 0) {
			$config_values[$key] = $value;
		}
	}

	file_put_contents($config_file, 'system.seed='.$config_values['SWEEP_LOCAL_SEED'].PHP_EOL);// reset file
	file_put_contents($config_file, 'system.self.ip="'.$config_values['SWEEP_LOCAL_IP'].'"'.PHP_EOL, FILE_APPEND | LOCK_EX); 
	file_put_contents($config_file, 'system.self.port='.$config_values['SWEEP_LOCAL_PORT'].PHP_EOL, FILE_APPEND | LOCK_EX); 
	if ($config_values['SWEEP_LOCAL_IP'] == $config_values['SWEEP_BOOTSTRAP_IP']) {
		if ($config_values['SWEEP_LOCAL_PORT'] == $config_values['SWEEP_BOOTSTRAP_PORT']) {
			file_put_contents($config_file, 'system.self.id=0'.PHP_EOL, FILE_APPEND | LOCK_EX); 
		}
	}
	file_put_contents($config_file, 'system.bootstrap.nodes=["boot"]'.PHP_EOL, FILE_APPEND | LOCK_EX); 
	file_put_contents($config_file, 'system.bootstrap.boot.ip="'.$config_values['SWEEP_BOOTSTRAP_IP'].'"'.PHP_EOL, FILE_APPEND | LOCK_EX);
	file_put_contents($config_file, 'system.bootstrap.boot.port='.$config_values['SWEEP_BOOTSTRAP_PORT'].PHP_EOL, FILE_APPEND | LOCK_EX);
	file_put_contents($config_file, 'system.bootstrap.boot.id=0'.PHP_EOL, FILE_APPEND | LOCK_EX);
	file_put_contents($config_file, 'webservice.server="/etc/sweep/config.yml"'.PHP_EOL, FILE_APPEND | LOCK_EX);
	
	setFlash(t('sweep_saved_config'),"success");
	return(array('type'=> 'redirect', 'url' => $staticFile.$urlpath));
}

function start() {
	global $urlpath, $staticFile, $service_name, $service_log;

	$sweep_log_clean_cmd = "cd /var/log && rm ".$service_log;
	execute_shell($sweep_log_clean_cmd);

	$ret = execute_shell("service $service_name start");
	if ($ret['return'] != 0) {
		setFlash(t("sweep_start_fail"));
		return(array('type'=> 'redirect', 'url' => $staticFile.$urlpath)); // abort
	}
	setFlash(t("sweep_start_success"), "success");
	return(array('type'=> 'redirect', 'url' => $staticFile.$urlpath));
}

function stop() {
	global $urlpath, $staticFile, $service_name;

	$ret = execute_shell("service $service_name stop");
	if ($ret['return'] != 0) {
		setFlash(t("sweep_stop_fail"));
		return(array('type'=> 'redirect', 'url' => $staticFile.$urlpath)); // abort
	}
	setFlash(t("sweep_stop_success"), "success");
	return(array('type'=> 'redirect', 'url' => $staticFile.$urlpath));
}

function install() {
	global $urlpath, $staticFile, $binary_url, $binary_deb;

	$hasJava = _check_java();
	if ($hasJava['return'] != 0) {
		$java_install_cmd = "apt-get install openjdk-6-jre";
		$ret = execute_shell($java_install_cmd);
		if ($ret['return'] != 0) {
			setFlash(t("sweep_java_install_fail"));
			return(array('type'=> 'redirect', 'url' => $staticFile.$urlpath)); // abort
		}
	}
	$sweep_clean_cmd = "cd /tmp && rm ".$binary_deb;
	execute_shell($sweep_clean_cmd);

	$sweep_get_cmd = "cd /tmp && wget ".$binary_url;
	$ret = execute_shell($sweep_get_cmd);
	if ($ret['return'] != 0) {
		setFlash(t("sweep_download_fail"));
		return(array('type'=> 'redirect', 'url' => $staticFile.$urlpath)); // abort
	}

	$sweep_install_cmd = "cd /tmp && dpkg -i $binary_deb";
	$ret = execute_shell($sweep_install_cmd);
	if ($ret['return'] != 0) {
		setFlash(t("sweep_install_fail"));
		return(array('type'=> 'redirect', 'url' => $staticFile.$urlpath)); // abort
	}

	setFlash(t("sweep_install_success"), "success");
	return(array('type'=> 'redirect', 'url' => $staticFile.$urlpath));
}

function uninstall() {
	global $install_name, $urlpath, $staticFile, $service_name;

	$sweep_uninstall_cmd = "dpkg -r $install_name";
	$ret = execute_program($sweep_uninstall_cmd);
	if ($ret['return'] != 0) {
		setFlash(t($ret['output'][0]));
		return(array('type'=> 'redirect', 'url' => $staticFile.$urlpath)); // abort
	}
	setFlash(t("sweep_uninstall_success"), "success");
	return(array('type'=> 'redirect', 'url' => $staticFile.$urlpath));
}

function logfile() { // nothing fancy...just quick and dirty^^
	global $urlpath, $staticFile, $log_file;
	$page = "";
	$buttons = "";

	$page .= hlc(t("sweep_title"));
	$page .= hl(t("sweep_log"),4);
	$page .= par(t("sweep_log_path")." $log_file: ");
	$page .= '<a href="#bottom">'.t("sweep_scroll_down").'</a>';
    $page .= ptxt(file_get_contents($log_file));
    $page .= '<hr id="bottom" />';


    $buttons .= addButton(array('label'=>t("sweep_button_status"),'class'=>'btn btn-success', 'href'=>"$urlpath", 'divOptions'=>array('class'=>'btn-group')));
	$buttons .= addButton(array('label'=>t("sweep_button_reload"),'class'=>'btn btn-success', 'href'=>"$urlpath/logfile", 'divOptions'=>array('class'=>'btn-group')));
		

    $page .= $buttons;
	return(array('type' => 'render','page' => $page));
}

function _check_java() {
	$ret = execute_program("java -version");
	//print_r($ret);
	return $ret;
}

function _check_sweep() {
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
	$variables['SWEEP_LOCAL_IP'] = _get_guifi_ip();
	$variables['SWEEP_LOCAL_PORT'] = "34567";
	$variables['SWEEP_BOOTSTRAP_IP'] = _get_guifi_ip();
	$variables['SWEEP_BOOTSTRAP_PORT'] = "34567";

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