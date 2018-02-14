<?php
#USER PARAMETER
$slpk_dir    = './slpk';

#FUNCTIONS

function list_services($slpk_dir){
	$slpks=array_diff(scandir($slpk_dir), array('.', '..'));
	$list = "<UL>";
	if (sizeof($slpks) > 0) {
		foreach ($slpks as $slpk){
			if (strtolower(substr($slpk, strrpos($slpk, '.') + 1)) == 'slpk'){
				$list.= '<LI><a href="./server/'.$slpk.'/SceneServer">'.$slpk.'</a><a href="./map/'.$slpk.'">  [Visualize]</a></LI>';
			}
		};
	};
	$list .= "</UL>";
	echo $list;
};

function read($f,$slpk, $slpk_dir){
	//read gz compressed file from slpk (=zip archive) and output result
	$f = ltrim($f, '\\'); #Remove first \ if exists
	$f= str_replace("\\", "/", $f); #replace "\" by "/"  => zip standard sep
	$slpk_dir= str_replace("\\", "/", $slpk_dir); #replace "\" by "/"  => zip standard sep
	//Read ZIP (slpk is a zip archive)
	$zip = new ZipArchive;
	$res = $zip->open(join("/",array($slpk_dir,$slpk)));
	if ($res){
		$fp = $zip->getFromName($f);
		$zip->close();
		if ($fp){
			if (strtolower(substr($f, strrpos($f, '.') + 1)) == 'gz'){ # decode if gz file
				return gzdecode($fp);
			}else{  #other file (binary/image)
				return $fp;
			};
		}else{return "SLPK UNZIP ERROR [1]";};
	}else{return "SLPK UNZIP ERROR [2]";};

};

function show_sceneInfo($slpk , $slpk_dir){   // <slpk>/SceneServer/
	$obj = new stdClass();
	$obj->serviceName=$slpk;
	$obj->serviceName=$slpk;
	$obj->name=$slpk;
	$obj->currentVersion=10.6;
	$obj->serviceVersion="1.6";
	$obj->supportedBindings=array("REST");
	$obj->layers= array(json_decode(read("3dSceneLayer.json.gz",$slpk,$slpk_dir)));
	header('Content-Type: application/json');
	echo json_encode($obj);
};

function show_layerInfo($slpk,$layer,$slpk_dir){ //<slpk>/SceneServer/layers/0
	header('Content-Type: application/json');
	echo read("3dSceneLayer.json.gz",$slpk,$slpk_dir);
};

function show_nodeInfo($slpk,$layer,$node,$slpk_dir){ //<slpk>/SceneServer/layers/0/nodes/<node>
	header('Content-Type: application/json');
	echo read("nodes/".$node."/3dNodeIndexDocument.json.gz",$slpk,$slpk_dir);
};

function show_nodeSharedInfo($slpk,$layer,$node,$slpk_dir){ //<slpk>/SceneServer/layers/0/nodes/<node>/shared
	header('Content-Type: application/json');
	echo read("nodes/".$node."/shared/sharedResource.json.gz",$slpk,$slpk_dir);
};

function show_nodeFeaturesInfo($slpk,$layer,$node,$slpk_dir){ //<slpk>/SceneServer/layers/0/nodes/<node>/features/0
	header('Content-Type: application/json');
	echo read("nodes/".$node."/features/0.json.gz",$slpk,$slpk_dir);
};

function show_nodeAttributesInfo($slpk,$layer,$node,$attributes,$slpk_dir){ //<slpk>/SceneServer/layers/0/nodes/<node>/attributes/<attribute>/0
	echo read("nodes/".$node."/attributes/".$attributes."/0.bin.gz",$slpk,$slpk_dir);
};

function show_nodeGeometriesInfo($slpk,$layer,$node,$slpk_dir){ //<slpk>/SceneServer/layers/0/nodes/<node>/geometries/0
	echo read("nodes/".$node."/geometries/0.bin.gz",$slpk,$slpk_dir);
};

function show_nodetexturesInfo($slpk,$layer,$node,$slpk_dir){ //<slpk>/SceneServer/layers/0/nodes/<node>/textures/0_0
	echo read("nodes/".$node."/textures/0_0.jpg",$slpk,$slpk_dir);
};

function show_nodeCtexturesInfo($slpk,$layer,$node,$slpk_dir){ //<slpk>/SceneServer/layers/0/nodes/<node>/textures/0_0
	echo read("nodes/".$node."/textures/0_0_1.bin.ddz.gz",$slpk,$slpk_dir);
};


#SCRIPT
if (isset($_GET['uri'])) { #ANALYSE URI FROM GET PARAMETER
	#clean and secure GET PARAMETER
	$uri = ltrim($_GET['uri'], '/'); #Remove first / if exists
	$uri = preg_replace('/\/?\?.*/', '', $_GET['uri']);   #remove trailing get parameters send from client (ex: /?f=json)
	$uri = rtrim($uri, '/'); #Remove last / if exists
	$uri = strtolower ($uri);
	$uris = explode("/", $uri);
	#print_r($uris);
	#Analyse parameter and send response
	switch (count($uris)){
		case 2: #SceneLayerInfo / map viewer
			if ($uris[1]=="sceneserver"){
				show_sceneInfo($uris[0],$slpk_dir);
			};
			break;
		case 4: #3DLayerInfo  <slpk>/SceneServer/layers/0
			if ($uris[1]=="sceneserver" and $uris[2]=="layers"){
				show_layerInfo($uris[0],$uris[3],$slpk_dir);
			};
			break;
		case 6: #3DNodeInfo   <slpk>/SceneServer/layers/0/nodes/<node>
			if ($uris[1]=="sceneserver" and $uris[2]=="layers" and $uris[4]=="nodes"){
				show_nodeInfo($uris[0],$uris[3],$uris[5],$slpk_dir);
			};
			break;
		case 7: #sharedinfo:  <slpk>/SceneServer/layers/0/nodes/<node>/shared
			if ($uris[1]=="sceneserver" and $uris[2]=="layers" and $uris[4]=="nodes" and $uris[6]=="shared"){
				show_nodeSharedInfo($uris[0],$uris[3],$uris[5],$slpk_dir);
			};
			break;
		case 8: #Geometrie, texture, feature: /<slpk>/SceneServer/layers/0/nodes/<node>/features/0
			if ($uris[1]=="sceneserver" and $uris[2]=="layers" and $uris[4]=="nodes" and $uris[6]=="features"){
				show_nodeFeaturesInfo($uris[0],$uris[3],$uris[5],$slpk_dir);
			// /<slpk>/SceneServer/layers/0/nodes/<node>/geometries/0
			}elseif ($uris[1]=="sceneserver" and $uris[2]=="layers" and $uris[4]=="nodes" and $uris[6]=="geometries"){
				show_nodeGeometriesInfo($uris[0],$uris[3],$uris[5],$slpk_dir);
			// /<slpk>/SceneServer/layers/0/nodes/<node>/textures/0_0
			}elseif ($uris[1]=="sceneserver" and $uris[2]=="layers" and $uris[4]=="nodes" and $uris[6]=="textures" and $uris[7]=="0_0"){
				show_nodetexturesInfo($uris[0],$uris[3],$uris[5],$slpk_dir);
			// /<slpk>/SceneServer/layers/0/nodes/<node>/textures/0_0_1
			}elseif ($uris[1]=="sceneserver" and $uris[2]=="layers" and $uris[4]=="nodes" and $uris[6]=="geometries" and $uris[7]=="0_0_1"){
				show_nodeCtexturesInfo($uris[0],$uris[3],$uris[5],$slpk_dir);
			};
			break;
		case 9: #attributes:  <slpk>/SceneServer/layers/0/nodes/<node>/attributes/<attribute>/0/
			if ($uris[1]=="sceneserver" and $uris[2]=="layers" and $uris[4]=="nodes" and $uris[6]=="attributes"){
				show_nodeAttributesInfo($uris[0],$uris[3],$uris[5],$uris[7],$slpk_dir);
			};
			break;
		default:
			list_services($slpk_dir);
	}

} else { #DEFAULT: LIST SERVICES
	list_services($slpk_dir);
};

?>
