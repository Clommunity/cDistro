<?php
//global.php
$CLOUDY_CONF_DIR = "/etc/cloudy/";
$CLOUDY_CONF_FILE = "cloudy.conf";

$conf = parse_bash_file($CLOUDY_CONF_DIR.$CLOUDY_CONF_FILE);
list($wi_ip, $wi_port) = explode(":", $_SERVER['HTTP_HOST']);
$protocol="http";

if ( isset($conf['PORT_SSL']) && ( ! isset($conf['ALLOWHTTP']) || $conf['ALLOWHTTP'] === "0")) {
    if (($wi_port != $conf['PORT_SSL']) || ($_SERVER['REMOTE_ADDR'] != "127.0.0.1")) {
        header('Location: https://'.$wi_ip.':'.$conf['PORT_SSL']);
    } else {
        $protocol="https";
    }
}

// You can change PATH files.
$staticFile=$_SERVER['SCRIPT_NAME'];
$staticPath=dirname($staticFile);

$documentPath=$_SERVER['DOCUMENT_ROOT'];

// App configure
$appCurrentYear = date('Y');
$appCopyright = "&copy; ".$appCurrentYear.", GPLv2";
$appHost = $_SERVER['HTTP_HOST'];
$appHostname = gethostname();
$appName = 'Cloudy';
$appURL=$protocol."://".$appHost;
$sysCPU=`grep -m1 "model name" /proc/cpuinfo || grep -m1 "Processor" /proc/cpuinfo  | awk -F: '{print $2}'`;
$sysRAMHooman=`free -h | grep Mem | awk '{print $2}'`;
$sysRAMHuman=ltrim(rtrim($sysRAMHooman)).'B';
$sysRAM=`grep -i "MemTotal" /proc/meminfo | awk -F: '{print $2}'`;
$sysStorageFree=`df -h | grep -m 1 -e '/$' | awk '{ print $4 "B"}'`;
$sysStorageTotal=`df -h | grep -m 1 -e '/$' | awk '{ print $2 "B"}'`;
$communityURL="http://guifi.net";
$projectURL="http://clommunity-project.eu";
$LANG="en";

// Dir webapp
$plugs_controllers = "/plug/controllers/";
$plugs_menus = "/plug/menus/";
$plugs_avahi = "/plug/avahi/";
$lang_dir = "/lang/";

// Debug
$debug = false;

// Docker
$docker_pkg = "docker.io";

// Guifi inforamtion
$GUIFI_CONF_DIR = "/etc/cloudy/";
$GUIFI_CONF_FILE = "guifi.conf";
$GUIFI_WEB="https://guifi.net";

$GUIFI_WEB_API=$GUIFI_WEB."/api";
$GUIFI_WEB_API_AUTH=$GUIFI_WEB."/api/auth";

$services_types = array('snpservices' => array('name' => 'SNPgraphs', 'prenick'=>'snp', 'function'=>$staticPath.'guifi-snps/install'),
                        'dnsservices' => array('name' => 'DNS', 'prenick'=>'dns', 'function'=>$staticPath.'guifi-dnss/install'),
                        'guifi-proxy3' => array('name' => 'Proxy', 'prenick'=>'prx', 'function'=>$staticPath.'guifi-proxy3/install')
                );

function parse_bash_file($file)
{
    if (file_exists($file)) {
        $lines = file($file);
        $config = array();

        foreach ($lines as $line_num=>$line) {
            if (! preg_match("/#.*/", $line)) {
                if (preg_match("/\S/", $line)) {
                    list($key, $value) = explode("=", trim($line), 2);
                    $key = trim($key);
                    $value = trim($value);
                    // Remove leading double quotes
                    if (strpos($value, '"')===0) {
                        $value=substr($value, 1, (strlen($value)-1));
                    }
                    // if the last char is a " then remove it
                    if (strrpos($value, '"')===(strlen($value)-1)) {
                        $value=substr($value, 0, -1);
                    }
                    $config[$key] = $value;
                }
            }
        }
        return $config;
    }

    return false;
}
