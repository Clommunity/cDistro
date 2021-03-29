<?php
// controllers/serf.php
$dirpath="/opt/serf";
$serfproc="serf";
define('SERFPATH', $dirpath."/".$serfproc);
$serf_files_path="$dirpath/machine";
$serfinit="/etc/init.d/serf";
$avahips_config="/etc/avahi-ps.conf";
$avahipsetc_config="/etc/avahi-ps-serf.conf";
$urlpath='/serf';
$serfgeturl='https://raw.githubusercontent.com/Clommunity/package-serf/master/getgithub';
$serfmenu=dirname(__FILE__)."/../menus/r0101.serf.lookfor.menu.php";
$avahipsetc_data=array(
                    'SERF_RPC_ADDR'=> array('default'=>'127.0.0.1:7373'),
                    'SERF_BIND'=> array('default'=>'5000'),
                  'SERF_JOIN'=> array('default'=>'10.139.40.22:5000')
 );
$serf_deps = "jq";
$serf_deps_desc = t('lightweight and flexible command-line JSON processor');

if (isset($plugs_avahi)) {
    addAvahiFiles($documentPath.$plugs_avahi);
}

function serf_search()
{
    $ret = execute_program("SEARCH_ONLY=serf /usr/sbin/avahi-ps search");
    return($ret['output']);
}

function raw_serf_search()
{
    $ret = execute_program("SEARCH_ONLY=serf /usr/sbin/avahi-ps search");
    $page = ptxt(print_r($ret, 1));
    return(array('type'=>'render','page'=>$page));
}

function search()
{
    global $staticFile,$staticPath;

    $page = "";

    $page .= ajaxStr('tableSerfAjax', t("Searching for published services, please wait a moment..."));
    $page .= "<div id='tableSerf' style='display:none'></div>";
    $page .= "<script>\n";
    $page .= "$('#tableSerf').load('".$staticFile."/serf/ajaxsearch',function(){\n";
    $page .= "	$('#tableSerfAjax').hide();";
    $page .= "	$('#tableSerf').css({'display':'block'});";
    $page .= "	$('#tags').tab();\n";
    $page .= "  tservice = $('.table-data').DataTable( ";
    $page .= '		{ "language": { "url": "/lang/"+LANG+".table.json"} }';
    $page .= "	);";
    $page .= "});\n";
    $page .= "</script>\n";
    $page .=  addButton(array('label'=>t("serf_search_quality"), 'class'=>'btn', 'onclick'=>'SQoS("'.$staticFile.'/serf/ajaxquality")'));


    return(array('type'=>'render','page'=>$page));
}

function ajaxquality()
{
    $cmd = SERFPATH." reachability -json";
    $ret = execute_program_shell($cmd);

    return(array('type'=>'ajax','page'=>$ret['output']));
}

function ajaxsearch()
{
    $aServices = serf_search();

    $gService = json_decode($aServices[0]);

    $nServices = array();

    if (!empty($gService)) {
        foreach ($gService as $dates_machine) {
            $serv_new['type'] = $dates_machine->s;
            $serv_new['description'] = $dates_machine->d;
            $serv_new['host'] = $dates_machine->m;
            $serv_new['ip'] = $dates_machine->i;
            $serv_new['port'] = $dates_machine->p;
            $serv_new['microcloud'] = $dates_machine->e;
            $serv_new['txt'] = $dates_machine->t;
            $serv_new['node_id']= $dates_machine->node_id;
            $serv_new['action'] = checkAvahi($serv_new['type'], array($serv_new));
            unset($serv_new['txt']);
            $type=$serv_new['type'];
            if (!is_array($nServices[$type])) {
                $nServices[$type]=array();
            }
            $nServices[$type][] = $serv_new;
        }
    }
    ksort($nServices);

    $page = "";
    $page .= "<ul id='tabs' class='nav nav-tabs' data-tabs='tabs'>\n";
    $active = "";
    foreach ($nServices as $k => $v) {
        if ($active == "") {
            $active = $k;
        }
        $page .= "	<li";
        if ($active == $k) {
            $page .= " class='active'";
        }
        $page .= "><a href='#".$k."' data-toggle='tab'>".$k."</a></li>\n";
    }
    $page .= "</ul>\n";
    $page .= "<div id='my-tab-content' class='tab-content'>\n";
    $services = "";
    foreach ($nServices as $k => $v) {
        $services .= "	<div class='tab-pane";
        if ($active == $k) {
            $services .= " active";
        }
        $services .= "' id='".$k."'>";

        $services .= addTableHeader(array(t('%'),t('Description'),t('Host'),t('IP'),t('Port'),t('&mu;cloud'),t('Action')), array('class'=>'table table-striped table-data'));
        foreach ($v as $serv) {
            unset($serv['type']);
            $node_id=$serv['node_id'];
            unset($serv['node_id']);
            $servarray=array(0 => '', 1 => $serv['description'], 2 => $serv['host'], 3 => $serv['ip'], 4 => $serv['port'], 5 => $serv['microcloud'], 6 => $serv['action'] );
            $services .= addTableRow($servarray, array('class'=>"node-".$node_id), array( 0 => array('class'=>'scan')));
        }
        $services .= addTableFooter();
        $services .= " 	</div>";
    }
    if ($services == "") {
        $services .=t("No services.");
    }
    $page .= $services;
    $page .= "</div>";
    return(array('type'=>'ajax','page'=>$page));
}

function index()
{
    global $urlpath, $avahips_config, $avahipsetc_config,$avahipsetc_data;
    $is_installed=_isInstalled();

    $page = "";
    $buttons = "";

    $page .= hlc(t("serf_common_title"));
    $page .= hl(t("serf_common_subtitle"), 4);

    if (!_existAvahiConf()) {
        createDefaultAvahiFile();
    }
    $var_avahi = load_conffile($avahips_config);

    $page .= par(t("serf_index_description_1"));
    $page .= par(t("serf_index_description_2"));

    $page .= txt(t("serf_index_status"));

    if (!$is_installed) {
        $page .= "<div class='alert alert-error text-center'>".t("serf_alert_not_installed")."</div>";
        $buttons .= addButton(array('label'=>t("serf_button_install"),'class'=>'btn btn-success', 'href'=>"$urlpath/getprogram", 'divOptions'=>array('class'=>'btn-group')));
    } else {
        if (_isRun()) {
            $page .= "<div class='alert alert-error text-center'>".t("serf_alert_stopped")."</div>";
            $buttons .= addButton(array('label'=>t("serf_button_uninstall"),'class'=>'btn btn-danger', 'href'=>"$urlpath/removeprogram", 'divOptions'=>array('class'=>'btn-group')));
            $buttons .= addButton(array('label'=>t("serf_button_start"),'class'=>'btn btn-success', 'href'=>"$urlpath/runprogram", 'divOptions'=>array('class'=>'btn-group')));
        } else {
            $page .= "<div class='alert alert-success text-center'>".t("serf_alert_running")."</div>\n";
            $buttons .= addButton(array('label'=>t("serf_button_stop"),'class'=>'btn btn-danger', 'href'=>"$urlpath/stopprogram", 'divOptions'=>array('class'=>'btn-group')));
        }

        $page .= txt(t("serf_index_publication"));
        if (! isEnabled()) {
            if (_isRun()) {
                $page .= "<div class='alert alert-warning text-center'>".t("serf_alert_wont_publish")."</div>";
            } else {
                $page .= "<div class='alert alert-warning text-center'>".t("serf_alert_not_publishing")."</div>";
            }
            $buttons .= addButton(array('label'=>t("serf_button_publish"),'class'=>'btn btn-warning', 'href'=>"$urlpath/selectserf", 'divOptions'=>array('class'=>'btn-group')));
        } else {
            if (_isRun()) {
                $page .= "<div class='alert alert-info text-center'>".t("serf_alert_will_publish")."</div>";
            } else {
                $page .= "<div class='alert alert-success text-center'>".t("serf_alert_publishing")."</div>";
            }
            $buttons .= addButton(array('label'=>t("serf_button_unpublish"),'class'=>'btn btn-warning', 'href'=>"$urlpath/removeserf", 'divOptions'=>array('class'=>'btn-group')));
            $page .="</div>";
        }

        $page .= hlc(t("serf_index_subtitle_configuration"), 3);

        if (_isRun()) {
            $page .= par(t("serf_index_change_configuration"));
            $readonly = "";
        } else {
            $page .= par(t("serf_index_current_configuration"));
            $readonly = "readonly";
        }

        $variable = load_conffile($avahipsetc_config, $avahipsetc_data);

        if (isset($_GET['join'])) {
            $variable['SERF_JOIN'] = $_GET['join'];
        }


        $page .= createForm(array('class'=>'form-horizontal'));
        $page .= addInput('SERF_RPC_ADDR', t('serf_index_form_rpc'), $variable, array('type'=>'text', 'pattern'=>'\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\:\d{1,5}','required'=>''), $readonly, t('serf_index_form_rpc_tooltip'));
        $page .= addInput('SERF_BIND', t('serf_index_form_bind'), $variable, array('type'=>'number', 'min' => '1024', 'max' => '65535', 'required'=>''), $readonly, t('serf_index_form_bind_tooltip'));
        $page .= addInput('SERF_JOIN', t('serf_index_form_bootstrap'), $variable, array('type'=>'text', 'pattern'=>'\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\:\d{1,5}'), $readonly, t('serf_index_form_bootstrap_tooltip'));

        if (_isRun()) {
            $buttons .= addSubmit(array('label'=>t("serf_button_save"),'class'=>'btn btn-default','divOptions'=>array('class'=>'btn-group')));
        }
    }

    $page .= $buttons;
    return(array('type' => 'render','page' => $page));
}

function index_post()
{
    global $staticFile, $staticPath, $urlpath;
    global $avahipsetc_config;

    $datesToSave = array();
    foreach ($_POST as $key => $value) {
        $datesToSave[$key] = $value;
    }
    write_conffile($avahipsetc_config, $datesToSave, null, null);

    setFlash(t('serf_flash_saving'), "info");
    return(array('type'=> 'redirect', 'url' => $staticFile.$urlpath));
}

function _isInstalled()
{
    return(is_executable(SERFPATH));
}

function _existAvahiConf()
{
    global $avahips_config;

    return(file_exists($avahips_config));
}
function _existAvahiConfEtc()
{
    global $avahipsetc_config;

    return(file_exists($avahipsetc_config));
}
function createDefaultAvahiFile()
{
    global $avahips_config;

    write_conffile($avahips_config, array('ERRORS_PLUG'=> "errors",'EXECUTE_IN'=>"memory",'SAVE_SERVICE'=>"none",'DATABASE'=>"none"), null, null);
}
function createDefaultAvahiEtcFile()
{
    global $avahipsetc_data,$avahipsetc_config;

    $tmparray=array();
    foreach ($avahipsetc_data as $k=>$v) {
        $tmparray[$k] = $v['default'];
    }
    write_conffile($avahipsetc_config, $tmparray, null, null);
}
function _existSerfConf()
{
    global $avahipsetc_config;

    return(file_exists($avahipsetc_config));
}
function _install_menu()
{
    global $serfmenu;
    rename($serfmenu.".disable", $serfmenu);
}
function _uninstall_menu()
{
    global $serfmenu;
    rename($serfmenu, $serfmenu.".disable");
}

function getprogram()
{
    global $serfproc, $staticFile, $serfgeturl, $urlpath,$serf_deps, $serf_deps_desc;

    if (!isPackageInstall($serf_deps)) {
        $page = package_not_install($serf_deps, $serf_deps_desc);
        return(array('type'=>'render','page'=> $page )) ;
    }
    $cmd = "cd /tmp && curl -k ".$serfgeturl."| sh -";
    $ret = execute_shell($cmd);

    setFlash(t("serf_flash_installed"), "success");
    _install_menu();
    if (!_existAvahiConfEtc()) {
        createDefaultAvahiEtcFile();
    }
    return(array('type'=> 'redirect', 'url' => $staticFile.$urlpath));
}

function removeprogram()
{
    global $dirpath, $staticFile, $urlpath;

    $cmd="rm -rf ".$dirpath;
    execute_shell($cmd);
    setFlash(t('serf_flash_uninstalled'), "info");
    _uninstall_menu();
    return(array('type'=> 'redirect', 'url' => $staticFile.$urlpath));
}

function _isRun()
{
    global $serfproc;
    $ret = execute_shell("pidof -s $serfproc");
    return($ret['return'] !=  0);
}

function runprogram()
{
    global $serfinit, $staticFile, $urlpath;

    $cmd = $serfinit." start";
    execute_program_detached($cmd);

    setFlash(t('serf_flash_starting'), "warning");
    return(array('type'=> 'redirect', 'url' => $staticFile.$urlpath));
}

function stopprogram()
{
    global $serfinit, $staticFile, $urlpath;

    $cmd = $serfinit." stop";
    execute_program_detached($cmd);

    setFlash(t('serf_flash_stopping'), "warning");
    return(array('type'=> 'redirect', 'url' => $staticFile.$urlpath));
}

function removeserf()
{
    global $avahips_config, $urlpath, $staticFile;

    // Load Avahi-PS configuration file
    $aps_cfg = load_conffile($avahips_config);

    // Remove Serf as a backend database for publication
    if (isset($aps_cfg['DATABASE'])) {
        $aps_cfg['DATABASE'] = trim(str_replace("serf", "", $aps_cfg['DATABASE']));
    }

    // If there are no backends remaining, set it to "none"
    if ((isset($aps_cfg['DATABASE']) && trim($aps_cfg['DATABASE']) === "") || !isset($aps_cfg['DATABASE'])) {
        $aps_cfg['DATABASE'] = "none";
    }

    // Save Avahi-PS configuration file
    write_conffile($avahips_config, $aps_cfg, null, null);

    // Check if Serf was actually disabled and set a flash message before return
    if (! isEnabled()) {
        setFlash(t('serf_flash_unpublishing'), "success");
    } else {
        setFlash(t('serf_flash_unpublishing_error'), "error");
    }

    return(array('type'=>'redirect','url'=>$staticFile.$urlpath));
}

function selectserf()
{
    global $avahips_config, $urlpath, $staticFile;

    // Load Avahi-PS configuration file
    $aps_cfg = load_conffile($avahips_config);

    // Add Serf as a backend database for publication
    if (isset($aps_cfg['DATABASE'])) {
        if (strpos($aps_cfg['DATABASE'], 'serf') === false) {
            $aps_cfg['DATABASE'] = trim($aps_cfg['DATABASE']." serf");
        }
        $aps_cfg['DATABASE'] = trim(str_replace("none", "", $aps_cfg['DATABASE']));
    } else {
        $aps_cfg['DATABASE'] = "serf";
    }

    // Save Avahi-PS configuration file
    write_conffile($avahips_config, $aps_cfg, null, null);

    // Check if Serf was actually enabled and set a flash message before return
    if (isEnabled()) {
        setFlash(t('serf_flash_publishing'), "success");
    } else {
        setFlash(t('serf_flash_publishing_error'), "error");
    }

    return(array('type'=>'redirect','url'=>$staticFile.$urlpath));
}

/**
 * function isEnabled(): check if Serf is enabled as a publication mechanism
 *
 * This function checks if Serf is enabled as a mechanism to publish local
 * services to the community cloud.
 *
 * @param none
 *
 * @return bool
 */
function isEnabled()
{
    global $avahips_config;

    // Load Avahi-PS configuration file
    $aps_cfg = load_conffile($avahips_config);

    // Check for Serf as a backend database for publication
    if (isset($aps_cfg['DATABASE']) && strpos($aps_cfg['DATABASE'], 'serf') !== false) {
        return true;
    }
    return false;
}
