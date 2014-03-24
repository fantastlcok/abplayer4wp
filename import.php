<?php
function abpwp_import_danmaku($type, $data, $pool = "none"){
	global $wpdb;
	if(!is_uploaded_file($data["tmp_name"])){
		return -1;
	}
	$d = file_get_contents($data["tmp_name"]);
	$count = 0;
	if($type === "importXmlfile"){
		if(preg_match_all("~<d p=[\"'](.+)[\"']>(.+)</d>~iUs",$d, $matches)){
			for($i = 0; $i < count($matches[1]); $i++){
				$params = $matches[1][$i];
				$text = $matches[2][$i];
				$p = split(",",$params);
				$comment = array(
					"text"=>$text,
					"size"=>(int)$p[2],
					"type"=>(int)$p[1],
					"stime"=>(int)((float)$p[0] * 1000),
					"color"=>(int)$p[3],
					"date"=>(int)$p[4],
					"author"=>"import:" . $p[6]
				);
				$wpdb->query("INSERT INTO `" . $wpdb->prefix . "danmaku` (pool, text, type, stime, size, color, author, date, notes) " . 
						"VALUES('" . mysql_real_escape_string($pool) . "', '" . mysql_real_escape_string($comment["text"]) ."',"
						. $comment["type"] . "," . $comment["stime"] . ", " . $comment["size"] . ", " . $comment["color"]. 
						",'" . mysql_real_escape_string($comment["author"]) . "', '" . date("Y-m-d H:i:s", $comment['date']) . 
						"','import');");
				$count++;
			}
		}
	}
	return $count;
}

?>
