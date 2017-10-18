<?php
$urlpath="$staticFile/docker";
$dev = "docker0";
$sourceslistdfile="/etc/apt/sources.list.d/docker.list";

function index() {
    global $title, $urlpath, $docker_pkg, $staticFile, $sourceslistdfile;

    $page = "";
    $buttons = "";

    $page .= hlc(t("docker_title"));
    $page .= hl(t("docker_subtitle"),4);
    $page .= par(t("docker_desc"));
    $page .= txt(t("docker_status"));

    if (!validSourceFile($sourceslistdfile)) {
        $page .= "<div class='alert alert-error text-center'>".t("docker_alert_no_sources")."</div>\n";
        $page .= par(t("docker_sources_manual"));
        $page .= addButton(array('label'=>t("docker_button_add_sources"),'class'=>'btn btn-success', 'href'=>"$urlpath/addsources"));
    }
    elseif (!isPackageInstall($docker_pkg)) {
        $page .= "<div class='alert alert-error text-center'>".t("docker_alert_not_installed")."</div>\n";
        $page .= addButton(array('label'=>t("docker_button_install"),'class'=>'btn btn-success', 'href'=>"$urlpath/install"));
    }
    elseif (!isRunning()) {
        $page .= "<div class='alert alert-error text-center'>".t("docker_alert_not_running")."</div>\n";
        $page .= addButton(array('label'=>t("docker_button_start"),'class'=>'btn btn-success', 'href'=>"$urlpath/start"));
        $page .= addButton(array('label'=>t('docker_button_uninstall'),'class'=>'btn btn-danger', 'href'=>$staticFile.'/default/uninstall/'.$docker_pkg));
    }
    else {
        $page .= "<div class='alert alert-success text-center'>".t("docker_alert_running")."</div>\n";
        $buttons .= addButton(array('label'=>t("docker_button_stop"),'class'=>'btn btn-danger', 'href'=>"$urlpath/stop"));
        $buttons .= addButton(array('label'=>t("docker_button_addpd"),'class'=>'btn btn-info', 'href'=>"$staticFile/docker-add"));
        $buttons .= addButton(array('label'=>t("docker_button_search"),'class'=>'btn btn-info', 'href'=>"$staticFile/docker-search"));
        $page .= txt(t("docker_title_containers_running"));
        $page .= docker_ps_running_table()["page"];
        $page .= txt(t("docker_title_containers_stopped"));
        $page .= docker_ps_stopped_table()["page"];
        $page .= docker_img()["page"];
        $page .= docker_volume()["page"];

    }

    $page .= $buttons;

    return array('type' => 'render','page' => $page);
}


function isRunning(){
    $cmd = "/usr/bin/docker ps";
    $ret = execute_program($cmd);

    return ( $ret['return'] ==  0 );
}


function install(){
    global $title, $urlpath, $docker_pkg;

    $page = package_not_install($docker_pkg,t("docker_desc"));

    return array('type' => 'render','page' => $page);
}


function start() {
    global $urlpath;

    execute_program_detached("service docker start");
    setFlash(t('docker_alert_start_message'),"success");

    return(array('type'=> 'redirect', 'url' => $urlpath));
}


function stop() {
    global $urlpath;

    execute_program_detached("service docker stop");
    setFlash(t('docker_alert_stop_message'),"success");

    return(array('type'=> 'redirect', 'url' => $urlpath));
}


function info_docker(){
    global $dev;

    $cmd = "/sbin/ip addr show dev $dev";
    $ret = execute_program($cmd);

    return ( implode("\n", $ret['output']) );
}


function container() {
    global $Parameters, $dev, $title, $urlpath, $docker_pkg, $staticFile;

    switch ($Parameters[0]) {
        case "rm":
            if (isset($Parameters[1]))
                if (isset($Parameters[2]))
                    return _dockercontainerrm($Parameters[1], $Parameters[2]);
                return _dockercontainerrm($Parameters[1]);
            break;

        case "stop":
            if (isset($Parameters[1]))
                if (isset($Parameters[2]))
                    return _dockercontainerstop($Parameters[1], $Parameters[2]);
                return _dockercontainerstop($Parameters[1]);
            break;

        case "publish":
            if (isset($Parameters[1]))
                if (isset($Parameters[2]) && !endsWith($Parameters[2], "_public"))
                {
                    _dockercontainerrename($Parameters[1], $Parameters[2] . "_public");
                    return _dockercontainerpublish($Parameters[1]);
                }
            break;

        case "pull":
            if (isset($Parameters[1]))
                if (isset($Parameters[2]))
                    return _dockercontainerpull($Parameters[1] . "/" . $Parameters[2]);
                return _dockercontainerpull($Parameters[1]);
            break;

        case "restart":
            if (isset($Parameters[1]))
                if (isset($Parameters[2]))
                    return _dockercontainerrestart($Parameters[1], $Parameters[2]);
                return _dockercontainerrestart($Parameters[1]);
            break;

        case "unpublish":
            if (isset($Parameters[1]))
                if (isset($Parameters[2]) && endsWith($Parameters[2], "_public"))
                {
                    _dockercontainerunpublish($Parameters[1]);
                    return _dockercontainerrename($Parameters[1], preg_replace('/_public$/', '', $Parameters[2]));
                }
            break;

        default:
            return(array('type'=> 'redirect', 'url' => $urlpath));
    }
}


function image() {
    global $Parameters, $dev, $title, $urlpath, $docker_pkg, $staticFile;

    switch ($Parameters[0]) {
        case "rmi":
            if (isset($Parameters[1]))
                if (isset($Parameters[2]))
                    return _dockerimagermi($Parameters[1], $Parameters[2]);
                return _dockerimagermi($Parameters[1]);
            break;

        case "run":
            if (isset($Parameters[1]))
                if (isset($Parameters[2]))
                    return _dockerimagerun($Parameters[1], $Parameters[2]);
                return _dockerimagerun($Parameters[1]);
            break;

        default:
            return(array('type'=> 'redirect', 'url' => $urlpath));
    }
}


function volume() {
    global $Parameters, $dev, $title, $urlpath, $docker_pkg, $staticFile;

    switch ($Parameters[0]) {
        case "rm":
            if (isset($Parameters[1]))
                return _dockervolumerm($Parameters[1]);
            break;

        case "inspect":
            return(array('type'=> 'redirect', 'url' => $urlpath));

        default:
            return(array('type'=> 'redirect', 'url' => $urlpath));
    }
}


function addsources(){

  global $title, $urlpath, $docker_pkg, $staticFile, $sourceslistdfile;

  $page = "";
  $buttons = "";

  $page .= hlc(t("docker_title"));
  $page .= hl(t("docker_subtitle"),4);


  $page .= txt(t("docker_addsources_update"));
  $page .= ptxt(execute_program_shell('apt-get update')['output']);
  $page .= txt(t("docker_addsources_install_https"));

  $page .= ptxt(execute_program_shell('apt-get install -y apt-transport-https ca-certificates curl gnupg2 software-properties-common')['output']);

  $docker_list = "deb [arch=".aptArch()."] https://download.docker.com/linux/debian ".aptRelease()." stable";
  addSource($sourceslistdfile, $docker_list);

  $page .= txt(t("docker_addsources_dockerlist_pre").$sourceslistdfile.t("docker_addsources_dockerlist_post"));
  $page .= ptxt(file_get_contents($sourceslistdfile),str_replace(".","",str_replace("/","",$sourceslistdfile)));

  $page .= txt(t("docker_addsources_aptkey"));
  $keycmd = execute_program_shell('curl -fsSL https://download.docker.com/linux/debian/gpg | apt-key add -');
  $page .= ptxt($keycmd['output']);
  $page .= ptxt(execute_program_shell('apt-key list | grep -A1 -B1 -i docker')['output']);

  $page .= txt(t("docker_addsources_update_again"));
  $page .= ptxt(execute_program_shell('apt-get update')['output']);

  $page .= txt(t("docker_addsources_result"));
  $pkgsearch = execute_program_shell('apt-cache search ' . $docker_pkg)['output'];

  if (strpos($pkgsearch, $docker_pkg) !== FALSE) {
    $page .= "<div class='alert alert-success text-center'>".t("docker_addsources_available")."</div>\n";
    $page .= ptxt($pkgsearch);
    $page .= addButton(array('label'=>t("docker_button_back"),'class'=>'btn btn-default', 'href'=>"$urlpath"));
    $page .= addButton(array('label'=>t("docker_button_install"),'class'=>'btn btn-success', 'href'=>"$urlpath/install"));
  }
  else {
    $page .= "<div class='alert alert-error text-center'>".t("docker_addsources_not_available")."</div>\n";
    $page .= addButton(array('label'=>t("docker_button_back"),'class'=>'btn btn-default', 'href'=>"$urlpath"));
    $page .= addButton(array('label'=>t("docker_button_add_sources_retry"),'class'=>'btn btn-warning', 'href'=>"$urlpath/addsources"));
  }


  $page .= $buttons;

  return array('type' => 'render','page' => $page);
}
