<?php

$urlpath="$staticFile/docker-add";
$returnpath="$staticFile/docker";
$docker_pkg = "docker.io";
$dev = "docker0";
$def_templates_dir=$conf['DOCROOT']."/plug/resources/docker/containers/";
$user_templates_dir="/etc/cloudy/docker/templates/";

function index()
{
    global $title, $urlpath, $docker_pkg, $staticFile, $Parameters, $def_templates_dir, $user_templates_dir;

    $page = "";
    $buttons = "";

    //Capçalera
    $page .= hlc(t("docker_title"));
    $page .= hl(t("docker_add_subtitle"), 4);

    $page .= par(t("docker_add_desc"));

    // Default templates
    $page .= hlc(t("docker_add_subtitle_predefined"), 2);
    $page .= ajaxStr('tableDefaultTemplatesAjax', t("Searching for the default templates, please wait a moment..."));
    $page .= "<div id='tableDefaultTemplates' style='display:none'></div>";
    $page .= "<script>\n";
    $page .= "$('#tableDefaultTemplates').load('".$staticFile."/docker-add/default_templates_table',function(){\n";
    $page .= "	$('#tableDefaultTemplatesAjax').hide();";
    $page .= "	$('#tableDefaultTemplates').css({'display':'block'});";
    $page .= "	$('#tags').tab();\n";
    $page .= "  tservice = $('.table-data');";
    $page .= "});\n";
    $page .= "</script>\n";

    // User-defined templates
    $page .= hlc(t("docker_add_subtitle_user"), 2);
    $page .= ajaxStr('tableUserTemplatesAjax', t("Searching for the user templates, please wait a moment..."));
    $page .= "<div id='tableUserTemplates' style='display:none'></div>";
    $page .= "<script>\n";
    $page .= "$('#tableUserTemplates').load('".$staticFile."/docker-add/user_templates_table',function(){\n";
    $page .= "	$('#tableUserTemplatesAjax').hide();";
    $page .= "	$('#tableUserTemplates').css({'display':'block'});";
    $page .= "	$('#tags').tab();\n";
    $page .= "  tservice = $('.table-data').DataTable( ";
    $page .= '		{ "language": { "url": "/lang/"+LANG+".table.json"} }';
    $page .= "	);";
    $page .= "});\n";
    $page .= "</script>\n";

    $buttons .= addButton(array('label'=>t("default_button_back"),'class'=>'btn btn-default', 'href'=>"$staticFile/docker"));

    $page .= $buttons;

    return array('type' => 'render','page' => $page);
}

function image()
{
    global $title, $urlpath, $docker_pkg, $staticFile, $Parameters, $def_templates_dir, $returnpath;

    $page = "";
    $buttons = "";

    //Capçalera
    $page .= hlc(t("docker_title"));
    $page .= hl(t("docker_add_subtitle"), 4);

    $iimage = _dockerinspectimage($Parameters[0]);

    if (is_null($iimage)) {
        $page .= txt(t('docker_add_error_no_image'));
        $page .= "<div class='alert alert-error text-center'>".t("docker_alert_no_image_pre") . $Parameters[0] . t("docker_alert_no_image_post") . "</div>\n";
        $buttons .= addButton(array('label'=>t("docker_button_back"),'class'=>'btn btn-default', 'href'=>"$returnpath"));
    } else {
        $page .= par(t("docker_add_config_desc"));

        $image = $iimage["RepoTags"][0];
        $name = explode(':', explode('/', $image)[sizeof(explode('/', $image))-1])[0];

        $buttons .= addButton(array('label'=>t("default_button_back"),'class'=>'btn btn-default', 'href'=>"$urlpath"));

        $page .= createForm(array('class'=>'form-horizontal'));
        $page .= addInput('image', t("docker_add_pdform_image"), $image, array('type'=>'text','readonly'=>'true','placeholder'=>$image), "", t("docker_add_pdform_image_tooltip"));
        $page .= addInput('name', t("docker_add_pdform_name"), $name, array('type'=>'text','placeholder'=>$name,'pattern'=>"^[a-z0-9\-]+$"), "", t("docker_add_pdform_name_tooltip"));

        if (isset($iimage["Config"]["ExposedPorts"]) && is_array($iimage["Config"]["ExposedPorts"]) && !empty($iimage["Config"]["ExposedPorts"])) {
            foreach ($iimage["Config"]["ExposedPorts"] as $pvalue => $pkey) {
                $protocol = explode('/', $pvalue)[1];
                $portnumb = explode('/', $pvalue)[0];
                $page .= addInput('port_'.$pvalue, t("docker_add_pdform_port").strtoupper($protocol)." ".$portnumb, $pkey, array('type'=>'number','placeholder'=>rand(49152, 65535),'min'=>0,'max'=>65535), "", t("docker_add_pdform_port_tooltip_pre").strtoupper($protocol)." ".$portnumb.t("docker_add_pdform_port_tooltip_post"));
            }
        }

        if (isset($jcontent["options"])) {
            foreach ($jcontent["options"] as $okey => $ovalue) {
                $page .= addInput('option_'.$okey, t("docker_add_pdform_option").$okey, $ovalue, array('type'=>'text','placeholder'=>$ovalue), "", t("docker_add_pdform_option_tooltip_pre").$okey.t("docker_add_pdform_option_tooltip_post"));
            }
        }

        if (isset($jcontent["links"])) {
            foreach ($jcontent["links"] as $lkey => $lvalue) {
                $page .= addInput('link_'.$lkey, t("docker_add_pdform_link"), $lvalue, array('type'=>'text','placeholder'=>$lvalue), "", t("docker_add_pdform_link_tooltip"));
            }
        }

        $buttons .= addSubmit(array('label'=>t("docker_button_pdform_run"),'class'=>'btn btn-success','divOptions'=>array('class'=>'btn-group')));
    }
    $page .= $buttons;

    return array('type' => 'render','page' => $page);
}

function image_post()
{
    global $title, $returnpath, $docker_pkg, $staticFile, $Parameters, $def_templates_dir;

    $page = "";
    $buttons = "";

    //Capçalera
    $page .= hlc(t("docker_title"));
    $page .= hl(t("docker_add_subtitle"), 4);

    if (isset($_POST) && isset($_POST["image"])) {
        $page .= ptxt(print_r($_POST, 1));

        $image = $_POST["image"];

        $name = (isset($_POST["name"]) ? $_POST["name"] : null);

        $ports = null;
        $options = null;
        $links = null;

        foreach ($_POST as $key => $value) {
            if (startsWith($key, 'port_')) {
                $ports[str_replace("port_", "", $key)] = $value;
            }
            if (startsWith($key, 'option_')) {
                $options[str_replace("option_", "", $key)] = $value;
            }
            if (startsWith($key, 'link_')) {
                $links[] = $value;
            }
        }

        _dockerrun($name, $ports, $options, $links, $image);
        return(array('type'=> 'redirect', 'url' => $returnpath));
    }

    $page .= $buttons;
    return array('type' => 'render','page' => $page);
}

function template()
{
    global $title, $urlpath, $docker_pkg, $staticFile, $Parameters, $def_templates_dir, $user_templates_dir;

    $page = "";
    $buttons = "";


    $templates_dir = $def_templates_dir;
    if ($Parameters[0] == "user")
        $templates_dir = $user_templates_dir;

    //Capçalera
    $page .= hlc(t("docker_title"));
    $page .= hl(t("docker_add_subtitle"), 4);

    if (!file_exists($templates_dir.$Parameters[1])) {
        $page .= txt(t('docker_add_error_no_template'));
        $page .= "<div class='alert alert-error text-center'>".t("docker_alert_no_template_pre") . $Parameters[1] . t("docker_alert_no_template_post") . "</div>\n";
        $buttons .= addButton(array('label'=>t("docker_button_back"),'class'=>'btn btn-default', 'href'=>"$urlpath"));
    } else {
        $page .= par(t("docker_add_config_desc"));

        $fcontent = file_get_contents($templates_dir.$Parameters[1]);
        $jcontent = json_decode($fcontent, true);

        $name = (isset($jcontent["name"]) ? $jcontent["name"] : "");

        $buttons .= addButton(array('label'=>t("default_button_back"),'class'=>'btn btn-default', 'href'=>"$urlpath"));

        $page .= createForm(array('class'=>'form-horizontal'));
        $page .= addInput('image', t("docker_add_pdform_image"), $jcontent["image"], array('type'=>'text','readonly'=>'true','placeholder'=>$jcontent["image"]), "", t("docker_add_pdform_image_tooltip"));
        $page .= addInput('name', t("docker_add_pdform_name"), $name, array('type'=>'text','placeholder'=>$name,'pattern'=>"^[a-z0-9\-]+$"), "", t("docker_add_pdform_name_tooltip"));

        if (isset($jcontent["ports"])) {
            foreach ($jcontent["ports"] as $pkey => $pvalue) {
                $page .= addInput('port_'.$pvalue, t("docker_add_pdform_port").$pvalue, $pkey, array('type'=>'number','placeholder'=>$pkey,'min'=>0,'max'=>65535), "", t("docker_add_pdform_port_tooltip_pre").$pvalue.t("docker_add_pdform_port_tooltip_post"));
            }
        }

        if (isset($jcontent["options"])) {
            foreach ($jcontent["options"] as $okey => $ovalue) {
                $page .= addInput('option_'.$okey, t("docker_add_pdform_option").$okey, $ovalue, array('type'=>'text','placeholder'=>$ovalue), "", t("docker_add_pdform_option_tooltip_pre").$okey.t("docker_add_pdform_option_tooltip_post"));
            }
        }

        if (isset($jcontent["links"])) {
            foreach ($jcontent["links"] as $lkey => $lvalue) {
                $page .= addInput('link_'.$lkey, t("docker_add_pdform_link"), $lvalue, array('type'=>'text','placeholder'=>$lvalue), "", t("docker_add_pdform_link_tooltip"));
            }
        }

        $buttons .= addSubmit(array('label'=>t("docker_button_pdform_run"),'class'=>'btn btn-success','divOptions'=>array('class'=>'btn-group')));
    }
    $page .= $buttons;

    return array('type' => 'render','page' => $page);
}

function template_post()
{
    global $title, $returnpath, $docker_pkg, $staticFile, $Parameters, $def_templates_dir;

    $page = "";
    $buttons = "";

    //Capçalera
    $page .= hlc(t("docker_title"));
    $page .= hl(t("docker_add_subtitle"), 4);

    if (isset($_POST) && isset($_POST["image"])) {
        $page .= ptxt(print_r($_POST, 1));

        $image = $_POST["image"];

        $name = (isset($_POST["name"]) ? $_POST["name"] : null);

        $ports = null;
        $options = null;
        $links = null;

        foreach ($_POST as $key => $value) {
            if (startsWith($key, 'port_')) {
                $ports[str_replace("port_", "", $key)] = $value;
            }
            if (startsWith($key, 'option_')) {
                $options[str_replace("option_", "", $key)] = $value;
            }
            if (startsWith($key, 'link_')) {
                $links[] = $value;
            }
        }

        _dockerrun($name, $ports, $options, $links, $image);
        return(array('type'=> 'redirect', 'url' => $returnpath));
    }

    $page .= $buttons;
    return array('type' => 'render','page' => $page);
}

function launch()
{
    global $title, $urlpath, $docker_pkg, $staticFile, $Parameters, $def_templates_dir, $user_templates_dir, $returnpath;

    $page = "";
    $buttons = "";

    $templates_dir = $def_templates_dir;
    if ($Parameters[0] == "user")
        $templates_dir = $user_templates_dir;

    if (!file_exists($templates_dir.$Parameters[1])) {
        $page .= hlc(t("docker_title"));
        $page .= hl(t("docker_add_subtitle"), 4);
        $page .= txt(t('docker_add_error_no_template'));
        $page .= "<div class='alert alert-error text-center'>".t("docker_alert_no_template_pre") . $Parameters[1] . t("docker_alert_no_template_post") . "</div>\n";
        $buttons .= addButton(array('label'=>t("docker_button_back"),'class'=>'btn btn-default', 'href'=>"$urlpath"));
    } else {
        $fcontent = file_get_contents($templates_dir.$Parameters[1]);
        $jcontent = json_decode($fcontent, true);

        $name = (isset($jcontent["name"]) && $jcontent["name"] !== null && $jcontent["name"] !== "" ? $jcontent["name"] : null);

        if (isset($jcontent["ports"])) {
            foreach ($jcontent["ports"] as $pkey => $pvalue) {
                $ports[$pvalue] = $pkey;
            }
        } else {
            $ports = null;
        }

        if (isset($jcontent["options"])) {
            foreach ($jcontent["options"] as $okey => $ovalue) {
                $options[$okey] = $ovalue;
            }
        } else {
            $options = null;
        }

        if (isset($jcontent["volumes"])) {
            foreach ($jcontent["volumes"] as $vkey => $vvalue) {
                $vols[$vkey] = $vvalue;
            }
        } else {
            $vols = null;
        }

        if (isset($jcontent["misc"])) {
            foreach ($jcontent["misc"] as $mkey => $mvalue) {
                $misc[$mkey] = $mvalue;
            }
        } else {
            $misc = null;
        }

        if (isset($jcontent["links"])) {
            foreach ($jcontent["links"] as $lkey => $lvalue) {
                $links[$lkey] = $lvalue;
            }
        } else {
            $links = null;
        }

        //_dockerrun($name, $ports, $options, $links, $jcontent["image"]);
        _dockerrun2($name, $ports, $options, $misc, $vols, $links, $jcontent["image"]);
        return(array('type'=> 'redirect', 'url' => $returnpath));
    }

    $page .= $buttons;
    return array('type' => 'render','page' => $page);
}

function default_templates_table()
{
    global $def_templates_dir;

    return templates_table("default", $def_templates_dir);
}

function user_templates_table()
{
    global $user_templates_dir;

    return templates_table("user", $user_templates_dir);
}

function templates_table($ttype, $templatesdir)
{
    global $dev, $title, $urlpath, $docker_pkg, $staticFile, $def_templates_dir, $user_templates_dir;

    $page = "";
    $buttons = "";
    $predef_count = 0;

    $allfiles = array_diff(scandir($templatesdir), array('.','..'));

    foreach ($allfiles as $key => $value) {
        if (endsWith($value, '.json')) {
            $files[] = $value;
        }
    }

    if (sizeof($files) > 0) {
        $table = "";
        $headers[] = t('docker_add_header_appname');
        $headers[] = t('docker_add_header_description');
        $headers[] = t('docker_add_header_ports');
        $headers[] = t('docker_add_header_options');
        $headers[] = t('docker_add_header_links');
        $headers[] = t('docker_add_header_actions');
        $headers[] = "";
        $table .= addTableHeader($headers, array('class'=>'table table-striped table-data'));

        foreach ($files as $fkey => $fvalue) {
            $fcontent = file_get_contents($templatesdir . '/' . $fvalue);
            $jcontent = json_decode($fcontent, true);

            if ( isset($jcontent["arch"]) && ($jcontent['arch'] == aptArch() || in_array(aptArch(), $jcontent['arch']))) {
                $fields = "";
                $fields[] = $jcontent["appname"];
                $fields[] = $jcontent["description"];

                $ports = "";
                if (isset($jcontent["ports"])) {
                    foreach ($jcontent["ports"] as $pkey => $pvalue) {
                        if ($ports !== "") {
                            $ports .= "<br>";
                        }
                        $ports .= $pvalue."=>".$pkey;
                    }
                }
                $fields[] = $ports;

                $options = "";
                if (isset($jcontent["options"])) {
                    foreach ($jcontent["options"] as $okey => $ovalue) {
                        if ($options !== "") {
                            $options .= "<br>";
                        }
                        $options .= $okey."=".$ovalue;
                    }
                }
                $fields[] = $options;

                $links = "";
                if (isset($jcontent["links"])) {
                    foreach ($jcontent["links"] as $lkey => $lvalue) {
                        if ($links !== "") {
                            $links .= ", ";
                        }
                        $links .= $lvalue;
                    }
                }
                $fields[] = $links;

                $fields[] = addButton(array('label'=>t("docker_button_pdcontainer_config"),'class'=>'btn btn-info', 'href'=>"$urlpath/template/".$ttype.'/'.$fvalue));
                $fields[] = addButton(array('label'=>t("docker_button_pdcontainer_run"),'class'=>'btn btn-success', 'href'=>"$urlpath/launch/".$ttype.'/'.$fvalue));
                $table .= addTableRow($fields);
                $predef_count++;
            }
        }
        $table .= addTableFooter();
    }

    if ( $predef_count > 0 ) {
        $page .= $table;
    }
    else {
        $page .= "<div class='alert alert-warning text-center'>".t("docker_alert_no_predef_arch_pre") . aptArch() . t("docker_alert_no_predef_arch_post") . "</div>\n";
    }
    return(array('type'=>'ajax','page'=>$page));
}
