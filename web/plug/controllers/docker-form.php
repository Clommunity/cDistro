<?php
$urlpath="$staticFile/docker";
$docker_pkg = "docker.io";
$dev = "docker0";
$form_dir = "/etc/cloudy/docker/templates/";

function index()
{
    global $title, $urlpath, $docker_pkg, $staticFile, $Parameters;

    //Recollim els paràmetres
    $image = $Parameters[0];
    if ($image=="") {
        $con=array();
    } else {
        $con = get_container_info($image);
    }

    //Capçalera
    $page = hlc(t("docker_title"));
    $page .= hl("Docker Container Details", 3);

    //Formulari
    $page .= createForm(array('class'=>'form-horizontal'));
    $page .= addInput('appname', "Appname", $con['appname'], array('type'=>'text','required'=>''), "", "Container name (one word, without spaces)");
    $page .= addInput('description', "Description", $con['description'], array('type'=>'text','size'=>200,'required'=>''), "", "Give a brief explanation");
    $page .= addInput('image', "Image", $con['image'], array('type'=>'text','required'=>''), "", "Container image");
    $page .= addInput('ports', "Port", $con['ports'], array('type'=>'text', 'required'=>''), "", "Indicate port in host network and pport in Docker network, e.g. 8080:80, or with several ports e.g. 8080:80, 3000:3000");
    $page .= addInput('misc', "Misc", $con['misc'], array('type'=>'text', 'optional'=>''), "", "If the container should use directly the host network, i.e. --net host, indicate this by writing in the field net:host");
    $page .= addInput('arch', "Architecture", $con['arch'], array('type'=>'text', 'readonly'=>'', 'required'=>true, 'value'=>aptArch()), "", "CPU architecture");

    // $page .= addInput('ports', "Port", $con['ports'], array('type'=>'number', 'min' => '1024', 'max' => '65535', 'required'=>''), "", "The port on which the App will publish the service");
    // $page .= addInput('porto', "Porto", $con['porto'], array('type'=>'number', 'min' => '1', 'max' => '65535', 'required'=>''), "", "The original port of the App");

    // $datacont['name']=$cont['name'];

    // if ($con['pub']=="Yes") {
    //     $page .= addSelect2('Pub', "Publish", array("Yes","No"), array('type'=>'text','required'=>''), "", "Determine if the App will be published on Serf");
    // } else {
    //     $page .= addSelect2('Pub', "Publish", array("No","Yes"), array('type'=>'text','required'=>''), "", "Determine if the App will be published on Serf");
    // }
    // $page .= addInput('Id', "Container ID", "ndef", array('type'=>'text','size'=>'200'), "readonly", "Container ID. Not Editable");
    // $page .= addInput('SERF_RPC_ADDR',t('serf_index_form_rpc'),$con['name'],array('type'=>'text', 'pattern'=>'\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\:\d{1,5}','required'=>''),$readonly,t('serf_index_form_rpc_tooltip'));
    // $page .= addInput('SERF_BIND',t('serf_index_form_bind'),$variable,array('type'=>'number', 'min' => '1024', 'max' => '65535', 'required'=>''),$readonly,t('serf_index_form_bind_tooltip'));
    // $page .= addInput('SERF_JOIN',t('serf_index_form_bootstrap'),$variable,array('type'=>'text', 'pattern'=>'\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\:\d{1,5}'),$readonly,t('serf_index_form_bootstrap_tooltip'));
    $page .= addSubmit(array('label'=>t("serf_button_save"),'class'=>'btn btn-default','divOptions'=>array('class'=>'btn-group')));

    return array('type' => 'render','page' => $page);
}


function index_post()
{
    global $staticFile, $staticPath, $urlpath, $form_dir;
    global $avahipsetc_config;

    $ext = "json";

    $datesToSave = array();

    foreach ($_POST as $key => $value) {
        $datesToSave[$key] = $value;
        if ($key=="ports") {
            $sizel1=sizeof($datesToSave[$key]);
            $l1 = preg_split("/[\s,]+/", $datesToSave[$key]);
            $sizeoflist = sizeof($l1);
            $arr1 = array();
            for ($i=0 ; $i<$sizeoflist; $i++) {
                $elem = $l1[$i];
                $list = explode(":", $elem);
                $arr1+=array($list[0]=>$list[1]);
            }
            $datesToSave[$key]=$arr1;
        }
        if ($key=="misc" && $datesToSave[$key] != null) {
            $list=explode(":", $datesToSave[$key]);
            $arr2=array($list[0]=>$list[1]);
            $datesToSave[$key]=$arr2;
        }
    }


    // $file="/var/local/cDistro/plug/resources/docker/containers/".$datesToSave['image'];
    // fwrite($fp, json_encode($datesToSave,JSON_FORCE_OBJECT));
    // write_conffile($file, $datesToSave, "", "", '"');


    // $fp = fopen('/var/local/cDistro/plug/resources/docker/containers/resultsff1.json', 'w');
    // fwrite($fp, json_encode($datesToSave,JSON_PRETTY_PRINT));
    // fclose($fp);

    if (! is_dir($form_dir)) {
        mkdir($form_dir, 0755, true);
    }

    $filename = $datesToSave['appname'] . "." . $ext;
    $fp1 = fopen($form_dir . "/" .$filename, 'w');
    // fwrite($fp1, json_encode($datesToSave,JSON_FORCE_OBJECT));
    fwrite($fp1, json_encode($datesToSave, JSON_PRETTY_PRINT));
    fclose($fp1);

    setFlash("Config -> OK", "info");
    return(array('type'=> 'redirect', 'url' => $staticFile."/docker"));
}

//Funció auxiliar per obtenir la informació d'una imatge en concret
//Si existeix el fitxer de configuració, retorna tota la informació
//Si no existeix, retorna 0
function get_container_info($image)
{
    global $dev,$urlpath;
    $containerspath="/var/local/cDistro/plug/resources/docker/containers/";

    //Llegim el fitxer de configuració
    $cmd = "cat ".$containerspath.$image;
    $properties = execute_program($cmd);

    //Existeix el fitxer de configuració?
    if (count($properties['output'])==1) {
        return("0"); //Retornem Error
    } else {
        //Es recuperen les propietats
        foreach ($properties['output'] as $prop) {
            $prop=explode("=", $prop);
            switch ($prop[0]) {
                                case "Name":
                                        $name=$prop[1]; break;
                                case "Run":
                                        $run=$prop[1]; break;
                                case "Id":
                                        $id=$prop[1]; break;
                                case "Port":
                                        $port=$prop[1]; break;
                                case "Img":
                                        $img=$prop[1]; break;
                                case "Pub":
                                        $pub=$prop[1]; break;
                        }
        }
    }
    return(array('name'=>$name,'run'=>$run,'id'=>$id,'port'=>$port,'img'=>$img,'pub'=>$pub));
}

//Redefinim la funció  addSelect de la llibreria lib/form.php
//L'objectiu és canviar el valor de cada option
function addSelect2($name=null, $label= null, $value = null, $options = null, $attributes = null, $tooltip = null, $nobr = null)
{
    if (!is_null($name)) {
        $options['name'] = $name;
    }

    $str = "";
    $str .= "<div class='control-group'>\n";
    $str .="<label class='control-label'>$label:</label>\n";
    $str .="<div class='controls'>\n";
    $str .= "<select ";
    if (is_array($options)) {
        foreach ($options as $k=>$v) {
            $str .= " $k='".$v."'";
        }
    }
    if (!is_null($attributes)) {
        $str .= $attributes;
    }
    $str .= ">\n";

    foreach ($value as $k => $v) {
        //codi de la llibreria
        //$str .= "<option value='".$k."'>".$v."</option>";
        //nou codi
        $str .= "<option value='".$v."'>".$v."</option>";
    }

    $str .= "</select>";

    if (!is_null($tooltip)) {
        if (is_null($nobr)) {
            $str .= '<br/>';
        }
    }
    $str .= '<span style="font-size: smaller;"><span style="font-size: smaller;">'.$tooltip.'</span></span>';
    $str .="</div>\n";
    $str .="</div>\n";

    return $str;
}
