<?php
$urlpath="$staticFile/docker-search";
$docker_pkg = "docker.io";
$dev = "docker0";

function index()
{
    global $title, $urlpath, $docker_pkg, $staticFile, $Parameters;

    //Capçalera
    $page = hlc(t("docker_title"));
    $page .= hl(t("docker_search_subtitle"), 4);

    $page .= par(t("docker_search_desc"));

    $buttons .= addButton(array('label'=>t("default_button_back"),'class'=>'btn btn-default', 'href'=>"$staticFile/docker"));


    //Formulari
    $page .= createForm(array('class'=>'form-horizontal'));
    $page .= addInput('search', t("docker_search_form_search"), $con['search'], array('type'=>'text','required'=>true,'placeholder'=>'etherpad','pattern'=>"^[a-z0-9\-]+$"), "", t("docker_search_form_search_tooltip"));
    $page .= addCheckbox('automated', t("docker_search_form_automated"), $con['automated'], array('type'=>'checkbox'), "", t("docker_search_form_automated_tooltip"));
    $buttons .= addSubmit(array('label'=>t("docker_button_search"),'class'=>'btn btn-success','divOptions'=>array('class'=>'btn-group')));

    $page .= $buttons;

    return array('type' => 'render','page' => $page);
}

function index_post()
{
    global $staticFile, $staticPath, $urlpath;

    //Capçalera
    $page = hlc(t("docker_title"));
    $page .= hl(t("docker_search_subtitle"), 4);

    $POSTdata = array();
    foreach ($_POST as $key => $value) {
        $POSTdata[$key] = $value;
    }

    $eps_options = '--no-trunc ';

    if ($POSTdata['automated']) {
        $eps_options .= '--filter is-automated=true ';
    }

    $ret = execute_program_shell('docker search ' . $eps_options . $POSTdata['search']);

    //$page .= ptxt(print_r($ret['output'], true));

    $retarray = explode(PHP_EOL, $ret['output']);

    //$page .= ptxt(print_r($retarray, true));

    foreach ($retarray as $key => $value) {
//    $page .= txt($key . ': ' . $value);
    }

    $headers = get_fancyheaders_from_string($retarray[0]);
    $headers[] = t('docker_search_header_action');
    $headerspos = get_headers_position_in_string($retarray[0]);


    $table = "";

    $table .= addTableHeader($headers);
    foreach ($retarray as $key => $value) {
        $value = str_replace('[OK]', t("docker_search_yes"), $value);
        if ($key > 0) {
            $fields = get_fields_in_string($value, $headerspos);

            if ($fields[0] != "") {
                $fields[0] = preg_replace('/\s+/', '', $fields[0]);
                $fields[] = addButton(array('label'=>t("docker_button_container_pull"),'class'=>'btn btn-primary', 'href'=>"docker/container/pull/".$fields[0]));
                $table .= addTableRow($fields);
            }
        }
    }
    $table .= addTableFooter();

    $page .= $table;


    return array('type' => 'render','page' => $page);
}
