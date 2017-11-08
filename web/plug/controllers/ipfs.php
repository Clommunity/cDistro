<?PHP
$title="IPFS";
$ipfspath="/var/local/ipfs/";
$ipfsprogram="ipfs";
$ipfsfile="/var/local/ipfs/ipfs.info";
$port="5001";
$avahi_type="ipfs";
$ipfsutils=dirname(__FILE__)."/../resources/ipfs/ipfscontroller";

function index(){
  global $paspath,$title;
  global $staticFile;

  $page=hlc(t($title));
  $page .= hl(t("Distributed filesystem"),4);
  $page .= par(t("Peer-to-peer file distribution protocol."));

  if ( ! isIPFSInstalled() ) {
    $page .= "<div class='alert alert-error text-center'>".t("IPFS is not installed")."</div>\n";
    $page .= par(t("Click on the button to install IPFS"));
    $buttons .= addButton(array('label'=>t("Install IPFS"),'class'=>'btn btn-success', 'href'=>$staticFile.'/ipfs/install'));
    $page .= $buttons;
  } else {
    $page .= "<div class='alert alert-success text-center'>".t("IPFS is installed")."</div>\n";
    if ( isRunning() ) {
      $page .= "<div class='alert alert-success text-center'>".t("IPFS is running")."</div>\n";
      $page .= addButton(array('label'=>t('Go to node'),'href'=>'http://'. getCommunityIP()['output'][0] .':'. $port));
      $page .= addButton(array('label'=>t('Stop node'),'href'=>$staticFile.'/ipfs/stop'));
    } else  {
      $page .= "<div class='alert alert-error text-center'>".t("IPFS is not running")."</div>\n";
    }
    $page .= addButton(array('label'=>t('Start IPFS daemon'),'href'=>$staticFile.'/ipfs/startDaemon'));
  }

  return(array('type' => 'render','page' => $page));
}

function stop() {
  // Stops ipfs server
  global $ipfspath,$ipfsprogram,$title,$ipfsutils,$avahi_type,$port;
  $page = "";
  $cmd = $ipfsutils." stop ";
  execute_program_detached($cmd);
  $temp = avahi_unpublish($avahi_type, $port);
  $flash = ptxt($temp);
  setFlash($flash);
  return(array('type'=>'redirect','url'=>$staticFile.'/ipfs'));
}

function isRunning() {
  // Returns whether ipfs is running or not
  global $ipfsfile;
  return(file_exists($ipfsfile));
}

function publish_get() {
  global $ipfspath, $title;
  global $staticFile;

  $page = hlc(t($title));
  $page .= hlc(t('Publish IPFS server'), 2);
  $page .= par(t("Write the port to publish your IPFS service"));
  $page .= createForm(array('class'=>'form-horizontal'));
  $page .= addInput('description', t('Describe this server'));
  $page .= addSubmit(array('label'=>t('Publish')));
  $page .= addButton(array('label'=>t('Cancel'), 'href'=>$staticFile.'/ipfs'));
  return(array('type' => 'render', 'page' => $page));
}

function publish_post() {
  $description = $_POST['description'];
  $ip = "";
  $page = "<pre>";
  $page .= _ipfssource($description);
  $page .= "</pre>";
  return(array('type' => 'render','page' => $page));
}

function isIPFSInstalled(){
  global $ipfspath;
  return(file_exists($ipfspath) && is_dir($ipfspath));
}

function install(){
  global $ipfsutils,$staticFile;
  $ret = execute_program($ipfsutils." install");
  $output = ptxt(implode("\n",$ret['output']));
  setFlash($output);
  return(array('type'=>'redirect','url'=>$staticFile.'/ipfs'));
}

function initialize(){
  global $ipfsutils,$staticFile;
  $ret = execute_program($ipfsutils." initialize");
  $output = ptxt(implode("\n",$ret['output']));
  setFlash($output);
  return(array('type'=>'redirect','url'=>$staticFile.'/ipfs'));
}

function startDaemon(){
  global $ipfsutils,$staticFile;
  $ret = execute_program($ipfsutils." startDaemon");
  $output = ptxt(implode("\n",$ret['output']));
  setFlash($output);
  return(array('type'=>'redirect','url'=>$staticFile.'/ipfs'));
}
