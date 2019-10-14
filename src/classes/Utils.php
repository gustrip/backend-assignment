<?php

use Spatie\ArrayToXml\ArrayToXml;


 class Utils {
    public function __construct() {
    }

    public static function fixMmsiParams(array $queryParams){
    	$mmsis = array();
    	if(is_array($queryParams)){
	    	foreach ($queryParams as $param) {
	    		if(is_array($param)){
	    			foreach ($param as $value) {
	    				array_push($mmsis, $value);
	    			}
	    		}else{
	    			array_push($mmsis,$param);
	    		}
	    	}
	    }

	    return $mmsis;

    }

    public static function getCorrectArray(array $data){
    	$temp_array = array();
    	if(is_array($data)){
			foreach ($data as $value) {
				array_push($temp_array, $value->getShipArray());
			}
		}else{
			array_push($temp_array, $value->getShipArray());
		}
		return $temp_array;
	}
	public static function fixDateIntervalParams(array $queryParams){
		$finalParams = array();
		if(isset($queryParams['minDate'])){
			$finalParams['minDate'] = $queryParams['minDate'];
		}else{
			$finalParams['minDate'] = 0;
		}
		if(isset($queryParams['maxDate'])){
			$finalParams['maxDate'] = $queryParams['maxDate'];
		}
		else{
			$finalParams['maxDate'] = $_SERVER['REQUEST_TIME'];
		}

		return $finalParams;
	}

	public static function fixCoordinatesParams(array $queryParams){
		$finalParams = array();

		if(isset($queryParams['minLat'])){
			$finalParams['minLat'] = $queryParams['minLat'];
		}else{
			$finalParams['minLat'] = -90;
		}
		if(isset($queryParams['maxLat'])){
			$finalParams['maxLat'] = $queryParams['maxLat'];
		}
		else{
			$finalParams['maxLat'] = 90;
		}
		if(isset($queryParams['minLon'])){
			$finalParams['minLon'] = $queryParams['minLon'];
		}else{
			$finalParams['minLon'] = -180;
		}
		if(isset($queryParams['maxLon'])){
			$finalParams['maxLon'] = $queryParams['maxLon'];
		}else{
			$finalParams['maxLon'] = 180;
		}

		return $finalParams;


	}

	public static function defineContentType($request){
		if(strcasecmp($_SERVER['HTTP_ACCEPT'], "*/*")!=0){
			$accept_headers = $request->getHeaderLine('Accept');
			$accept_headers = explode(",", $accept_headers);
			if(in_array("application/json", $accept_headers) ||
			 in_array("application/ld+json", $accept_headers) || 
			 in_array("application/xml", $accept_headers) ||
			 in_array("text/csv", $accept_headers) ){
				foreach ($accept_headers as $value) {
					if(strcasecmp("application/json",$value) == 0 || 
					strcasecmp("application/json",$value) == 0 || 
					strcasecmp("application/ld+json",$value) == 0 ||
					strcasecmp("application/xml",$value) == 0 ||
					strcasecmp("text/csv",$value) == 0 ){
						return $value;
					}
				}
			}else{
				return false;
			}
		}else{
			return "application/json";
		}
	}


	  /*
    * **** change to return a string to send with the responce body *************
    * Based on (forked from) the work by https://gist.github.com/Kostanos
    *
    * This revision allows the PHP file to be included/required in another PHP file and called as a function, rather than focusing on command line usage.
    * 
    * Convert JSON file to CSV and output it.
    *
    * JSON should be an array of objects, dictionaries with simple data structure
    * and the same keys in each object.
    * The order of keys it took from the first element.
    *
    * Example:
    * json:
    * [
    *  { "key1": "value", "kye2": "value", "key3": "value" },
    *  { "key1": "value", "kye2": "value", "key3": "value" },
    *  { "key1": "value", "kye2": "value", "key3": "value" }
    * ]
    *
    * The csv output: (keys will be used for first row):
    * 1. key1, key2, key3
    * 2. value, value, value
    * 3. value, value, value
    * 4. value, value, value
    *
    * Usage:
    * 
    *     require '/path/to/json-to-csv.php';
    *     
    *     // echo a JSON string as CSV
    *     jsonToCsv($strJson);
    *     
    *     // echo an arrayJSON string as CSV
    *     jsonToCsv($arrJson);
    *     
    *     // save a JSON string as CSV file
    *     jsonToCsv($strJson,"/save/path/csvFile.csv");
    *     
    *     // save a JSON string as CSV file through the browser (no file saved on server)
    *     jsonToCsv($strJson,false,true);
    *     
    *     
  */
  
  public static function jsonToCsv ($json, $csvFilePath = false, $boolOutputFile = false,$firstLineKeys = false) {
    
    // See if the string contains something
    if (empty($json)) { 
      die("The JSON string is empty!");
    }
    
    // If passed a string, turn it into an array
    if (is_array($json) === false) {
      $json = json_decode($json, true);
    }
    
    // If a path is included, open that file for handling. Otherwise, use a temp file (for echoing CSV string)
    if ($csvFilePath !== false) {
      $f = fopen($csvFilePath,'w+');
      if ($f === false) {
        die("Couldn't create the file to store the CSV, or the path is invalid. Make sure you're including the full path, INCLUDING the name of the output file (e.g. '../save/path/csvOutput.csv')");
      }
    }
    else {
      $boolEchoCsv = true;
      if ($boolOutputFile === true) {
        $boolEchoCsv = false;
      }
      $strTempFile = 'csvOutput' . date("U") . ".csv";
      $f = fopen($strTempFile,"w+");
    }
    
    $firstLineKeys = $firstLineKeys;
    foreach ($json as $line) {
      if (empty($firstLineKeys)) {
        $firstLineKeys = array_keys($line);
        fputcsv($f, $firstLineKeys);
        $firstLineKeys = array_flip($firstLineKeys);
      }
      
      // Using array_merge is important to maintain the order of keys acording to the first element
      fputcsv($f, array_merge($firstLineKeys, $line));
    }
    fclose($f);
    
    // Take the file and put it to a string/file for output (if no save path was included in function arguments)
    if ($boolOutputFile === true) {
      if ($csvFilePath !== false) {
        $file = $csvFilePath;
      }
      else {
        $file = $strTempFile;
      }
      
      // Output the file to the browser (for open/save)
      if (file_exists($file)) {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename='.basename($file));
        header('Content-Length: ' . filesize($file));
        readfile($file);
      }
    }
    elseif ($boolEchoCsv === true) {
      if (($handle = fopen($strTempFile, "r")) !== FALSE) {
      	$final_string ="";
        while (($data = fgetcsv($handle)) !== FALSE) {
          $final_string.=implode(",",$data);
          $final_string.="\r\n";
        }
        fclose($handle);
      }
    }
    
    // Delete the temp file
    unlink($strTempFile);
    return $final_string;
  }

  public static function getProperMessage($content_type,$array,$mmsi_results=false){
  	switch ($content_type) {
  		case 'application/json':
  			return json_encode($array);
  			break;
  		case 'application/ld+json':
  			return json_encode($array, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
  			break;
  		
  		case 'text/csv':
  			if($mmsi_results){
  				$message = '';
	    		$csv_headers = false; 
	    		foreach ($array as $value) {
		    		if($csv_headers){
		    			$csv_h = strtok($message, "\r\n");
		    			$temp = Utils::jsonToCsv(json_encode($value));
		    			$message.=str_replace($csv_h, "", $temp);
		    		}else{
		    			$message .= Utils::jsonToCsv(json_encode($value));
		    		}
	    	
	    		$csv_headers = true;
	    		}
	    		return $message;
  			}else{
  				return Utils::jsonToCsv(json_encode($array));
  			}
  			break;
  		case 'application/xml':
  			if($mmsi_results){
  				$temp_array= array();
  				foreach ($array as $value) {
  					foreach ($value as $item) {
  						array_push($temp_array, $item);
  					}
  				}
	    		return ArrayToXml::convert(['__numeric' => $temp_array],'shipsResults');
  			}else{
  				return ArrayToXml::convert(['__numeric' => $array],'shipsResults');
  			}
  			break;
  		default:
  			return json_encode($array);
  			break;
  	}
  }

  public static function correctQueryParams($queryParams,$route){
  	switch ($route) {
  		case 'mmsi':
  			if(is_array($queryParams['mmsi'])){
  				foreach ($queryParams['mmsi'] as $value) {
  					var_dump($value);
  					if(!(strlen($value)==9 && is_numeric($value))){
  						return false;
  					}
  				}
  			}else{
  				if(!(strlen($queryParams)==9 && is_numeric($queryParams))){
  						return false;
  				}
  			}
  			return true;
  			break;
  		
  		case 'coordinates':
  			if(is_array($queryParams)){
  				foreach ($queryParams as $value) {
  					if(!(is_numeric($value))){
  						return false;
  					}
  				}
  			}else{
  				if(!(is_numeric($queryParams))){
  						return false;
  				}
  			}
  			return true;
  			break;
  		case 'time-interval':
  			if(is_array($queryParams)){
  				foreach ($queryParams as $value) {
  					if(!(is_numeric($value))){
  						return false;
  					}
  				}
  			}else{
  				if(!(is_numeric($queryParams))){
  						return false;
  				}
  			}
  			break;
  		default:
  			return true;
  			break;
  	}
  }

}