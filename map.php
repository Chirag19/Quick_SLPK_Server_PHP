<?php
#USER PARAMETER
$slpk_dir    = './slpk';

#SCRIPT
$slpks=array_diff(scandir($slpk_dir), array('.', '..'));
$input=explode("/", $_GET['uri']);
if (in_array($input[0],$slpks)) {
	$slpk=$input[0];
}else{
	$slpk="error";
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Quick SLPK Server - viewer : <?php echo $slpk; ?></title>
  <style>
    html,
    body,
    #viewDiv {
      padding: 0;
      margin: 0;
      height: 100%;
      width: 100%;
    }
  </style>

  <link rel="stylesheet" href="https://js.arcgis.com/4.6/esri/css/main.css">
  <script src="https://js.arcgis.com/4.6/"></script>

  <script>
	var measureWidget, view;
    require([
      "esri/Map",
      "esri/views/SceneView",
      "esri/layers/SceneLayer",
	  "esri/widgets/DirectLineMeasurement3D",
	  "esri/widgets/Legend",

      "dojo/domReady!"
    ], function(Map, SceneView, SceneLayer, DirectLineMeasurement3D,Legend) {

      // Create Map
      var map = new Map({
        basemap: "topo",
        ground: "world-elevation"
      });

      // Create the SceneView
      view = new SceneView({
        container: "viewDiv",
        map: map,
      });

      // Create SceneLayer and add to the map
      var layer = new SceneLayer({
        url:"../server/<?php echo $slpk; ?>/SceneServer/layers/0",
        popupEnabled: true
      });
      map.add(layer);
	  
	  // Automatic zoom to layer extent
	layer.when(function(){
	  view.goTo(layer.fullExtent);
	});
	
	//3d measure
	  dojo.connect(dojo.byId("startmeasure"),"onclick", function(){
			// initialize widget
			measureWidget = new DirectLineMeasurement3D({
				view: view,
				});
			view.ui.add(measureWidget, "top-right");
			});
	  dojo.connect(dojo.byId("stopmeasure"),"onclick", function(){
			view.ui.empty("top-right");
			measureWidget.destroy();
			});
	
	//legend
        view.when(function() {
          // when the resources in the MapView have loaded.
          var legend = new Legend({
            view: view,
            layerInfos: [{
              layer: layer,
              title: "<?php echo $slpk; ?>"
            }]
          });

          // Add widget to the bottom right corner of the view
          view.ui.add(legend, "bottom-right");
        });
    });
  </script>
</head>

<body>
 <a href="../index.php">Retour</a>
 <button id="startmeasure" type="button">Start 3D Measure</button>
 <button id="stopmeasure" type="button">Stop 3D Measure</button>
  <div id="viewDiv"></div>
</body>
</html>