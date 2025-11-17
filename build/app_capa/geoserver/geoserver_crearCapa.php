<?php


	//$_POST['capa_ver']='014_v001';
	if(!isset($_POST['capa_ver'])){
		$Log['tx'][]="error al crear feturetype en geoserver";	
	}
	


    // Open log file
    $logfh = fopen("./app_capa/geoserver/GeoserverPHP.log", 'w') or die("can't open log file");
    
	
	$Log['tx'][]='curl de consulta iniciado';
	
	
	$service = "http://190.111.246.33:8080/geoserver/"; // replace with your URL
    $request = "rest/layers.json"; // to add a new workspace
    $url = $service . $request;
	$ch = curl_init($url);
	
// Optional settings for debugging
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //option to return string
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    curl_setopt($ch, CURLOPT_STDERR, $logfh); // logs curl messages
    $passwordStr = "general:mostaza"; // replace with your username:password
    curl_setopt($ch, CURLOPT_USERPWD, $passwordStr);
	$buffer = curl_exec($ch); // Execute the curl request
	curl_close($ch); // free resources if curl handle will not be reused
	fclose($logfh);  // close logfile
	
	$capas=json_decode($buffer, true); //el parametro true fuerza la salida como array, no stdClass
    $Log['tx'][]='curl consulta ejecutado';
	$saltearCreacion='no';
	foreach($capas['layers']['layer'] as $layer){
		if($layer['name']==$_POST['capa_ver']){
			$Log['mg'][]=utf8_encode('la capa ya esta publicada en el servidor wms');
			$Log['data']['creacionWMS']='exito';// fue creada en el pasado pero al parecer no fue registrado
			$saltearCreacion='si';
		}
	}
	
	if($saltearCreacion=='no'){

	    $logfh = fopen("./app_capa/geoserver/GeoserverPHP.log", 'w') or die("can't open log file");
    

	    // Initiate cURL session
	    $service = "http://190.111.246.33:8080/geoserver/"; // replace with your URL
	    $request = "rest/workspaces"; // to add a new workspace
	    $url = $service . $request;
		$url.="/geoGEC/datastores/geogec/featuretypes";
		$ch = curl_init($url);
			
		$Log['tx'][]='curl de crecion iniciado';
		
	    // Optional settings for debugging
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //option to return string
	    curl_setopt($ch, CURLOPT_VERBOSE, true);
	    curl_setopt($ch, CURLOPT_STDERR, $logfh); // logs curl messages
	
	    //Required POST request settings
	    curl_setopt($ch, CURLOPT_POST, True);
	    $passwordStr = "general:mostaza"; // replace with your username:password
	    curl_setopt($ch, CURLOPT_USERPWD, $passwordStr);
	
	    //POST data
	    //crea un workspace llamado test_ws
	    curl_setopt($ch, CURLOPT_HTTPHEADER,
	            array("Content-type: application/xml"));
				  
	
	    $xmlStr = '
<featureType>
  <name>'.$_POST['capa_ver'].'</name>
  <nativeName>'.$_POST['capa_ver'].'</nativeName>
  <namespace>
    <name>UNMgeo</name>
    <atom:link xmlns:atom="http://www.w3.org/2005/Atom" rel="alternate" href="http://170.210.177.36:8080/geoserver/rest/namespaces/UNMgeo.xml" type="application/xml"/>
  </namespace>
  <title>'.$_POST['capa_ver'].'</title>
  <keywords>
    <string>features</string>
    <string>'.$_POST['capa_ver'].'</string>
  </keywords>
  <srs>EPSG:22175</srs>
  <nativeBoundingBox>
    <minx>5525322.0</minx>
    <maxx>5722199.0</maxx>
    <miny>6089103.0</miny>
    <maxy>6247618.5</maxy>
  </nativeBoundingBox>
  <latLonBoundingBox>
    <minx>-59.74787304785044</minx>
    <maxx>-57.53495412215324</maxx>
    <miny>-35.72507630115524</miny>
    <maxy>-33.51215737545804</maxy>
    <crs>GEOGCS[&quot;WGS84(DD)&quot;, 
  DATUM[&quot;WGS84&quot;, 
    SPHEROID[&quot;WGS84&quot;, 6378137.0, 298.257223563]], 
  PRIMEM[&quot;Greenwich&quot;, 0.0], 
  UNIT[&quot;degree&quot;, 0.017453292519943295], 
  AXIS[&quot;Geodetic longitude&quot;, EAST], 
  AXIS[&quot;Geodetic latitude&quot;, NORTH]]</crs>
  </latLonBoundingBox>
  <projectionPolicy>FORCE_DECLARED</projectionPolicy>
  <enabled>true</enabled>
  <metadata>
    <entry key="elevation">
      <dimensionInfo>
        <enabled>false</enabled>
      </dimensionInfo>
    </entry>
    <entry key="time">
      <dimensionInfo>
        <enabled>false</enabled>
        <defaultValue/>
      </dimensionInfo>
    </entry>
    <entry key="cachingEnabled">false</entry>
  </metadata>
  <store class="dataStore">
    <name>UNMgeo:postgis unmgeodata</name>
    <atom:link xmlns:atom="http://www.w3.org/2005/Atom" rel="alternate" href="http://170.210.177.36:8080/geoserver/rest/workspaces/UNMgeo/datastores/postgis+unmgeodata.xml" type="application/xml"/>
  </store>
  <maxFeatures>0</maxFeatures>
  <numDecimals>0</numDecimals>
  <overridingServiceSRS>false</overridingServiceSRS>
  <skipNumberMatched>false</skipNumberMatched>
  <circularArcPresent>true</circularArcPresent>
 
</featureType>
';
		
				
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlStr);
	
	    //POST return code
	    $successCode = 201;
		
		
	    $buffer = curl_exec($ch); // Execute the curl request
	
	    $Log['tx'][]='curl ejecutado';
	    
	    // Check for errors and process results
	    $info = curl_getinfo($ch);
	    
	    $Log['tx'][]=$info;   
	    if ($info['http_code'] != $successCode) {
	
	      $msgStr = "# Unsuccessful cURL request to ";
	      $msgStr .= $url." [". $info['http_code']. "]\n";
	      fwrite($logfh, $msgStr);
			$Log['res']='err';
			$Log['tx'][]='error al publicar en geoserver';
			$Log['tx'][]=$msgStr;
			$Log['tx'][]=$xmlStr;
			terminar($Log);		  
	    } else {
	      $msgStr = "# Successful cURL request to ".$url."\n";
	      fwrite($logfh, $msgStr);
	      $Log['data']['creacionWMS']='exito';
	    }
	    fwrite($logfh, $buffer."\n");
	
		$Log['tx'][]=$buffer;
	    curl_close($ch); // free resources if curl handle will not be reused
	    fclose($logfh);  // close logfile
    }
?>
