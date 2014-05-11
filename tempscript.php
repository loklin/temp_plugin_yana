<?php
/*
Demerdez vous
*/
          setlocale (LC_ALL, 'fr_FR');
date_default_timezone_set('Europe/Paris');
//Acces a la base de yana pour config des sondes.
        $sourceDB = new SQLite3('/var/www/yana/db/.database.db');
        $sql = "SELECT * FROM yana_plugin_temp ORDER BY id";
        $statement = $sourceDB->prepare($sql);
        $results = $statement->execute();
	$capteurs = array();
		while($row = $results->fetchArray()){

			//	$temph = $row['capteur'];
			//	$idi = $row['id'];
			//	$cause = $row['protocol'];

// tri par protocole

			if (!isset($capteurs[$row['protocol']])) {
			$capteurs[$row['protocol']] = array();
			}
  			$capteurs[$row['protocol']][] = $row;
			}


// Port Serie

			if(isset($capteurs['protoserie'])) {
			foreach ($capteurs['protoserie'] as $capteur_serie) {
			$temph = $capteur_serie['capteur'];
			$descri = $capteur_serie['description'];
$number = '';
while (empty($number)){
			$number = serialtemp_get($temph);
}

// Ecriture dans la base du plugin
					$db = new SQLite3('/var/www/yana/plugins/temp/temp.db');
						$date_mesure = strftime("%Y-%m-%d %H:%M:%S.000");
						//$valeur = "INSERT INTO Mesures VALUES('"$idi"', "$number", '"$date_mesure"')";
						//$db->exec($valeur);
						$db->exec('INSERT INTO Mesures VALUES ("'.$capteur_serie['id'].'", "'.$number.'", "'.$date_mesure.'", "'.$descri.'")');
						$db->close();
   }
}



// Sondes DS18B20

			if(isset($capteurs['protodallas'])) {
			foreach ($capteurs['protodallas'] as $capteur_dallas) {
			$temph = $capteur_dallas['capteur'];
			$descri = $capteur_serie['description'];
			$number = dallastemp_get($temph);

// Ecriture dans la base du plugin
					$db = new SQLite3('/var/www/yana/plugins/temp/temp.db');
						$date_mesure = strftime("%Y-%m-%d %H:%M:%S.000");
						//$valeur = "INSERT INTO Mesures VALUES('"$idi"', "$number", '"$date_mesure"')";
						//$db->exec($valeur);
						$db->exec('INSERT INTO Mesures VALUES ("'.$capteur_dallas['id'].'", "'.$number.'", "'.$date_mesure.'", "'.$descri.'")');
						$db->close();


   }
}




//}


//fermeture de la base
$sourceDB->close();


//fonction recuperation de temperature port serie + interogation de la base yana pour la config du port serie

function serialtemp_get($temph){
            $sourceDB = new SQLite3('/var/www/yana/db/.database.db');

$portserie = $sourceDB->querySingle("SELECT value FROM yana_configuration WHERE key = 'plugin_temp_port'");
$currenttemp =''; 				
//$portserie = '/dev/ttyUSB0';
$timeout=microtime(true)+0.5;
exec("/bin/stty -F $portserie 9600 sane raw cs8 hupcl cread clocal -echo -onlcr ");
$fp=fopen($portserie,"c+");
if(!$fp) die("Can't open device");
stream_set_blocking($fp,1);
fwrite($fp,$temph);
stream_set_blocking($fp,0);
do{
  $c=fgetc($fp);
  if($c === false){
      usleep(50000);
      continue;
  } 
  $currenttemp.=$c;
}while($c!="\n" && microtime(true)<$timeout);
 return $currenttemp;
fclose($fp);
$sourceDB->close();	
}

// fonction de recuperation des sondes ds18b20. attention configurer les sondes avant. http://learn.adafruit.com/adafruits-raspberry-pi-lesson-11-ds18b20-temperature-sensing/

function dallastemp_get($temph){
	
   	if ($handle = opendir('/sys/bus/w1/devices')) {
		while (false !== ($entry = readdir($handle))) {
			if(!strncmp($entry, $temph , strlen($temph))) {
				$filename = "/sys/bus/w1/devices/".$entry."/w1_slave" ;
				if (file_exists($filename)) {
					$lines = file($filename);
					$currenttemp = round ( substr($lines[1], strpos($lines[1], "t=")+2) / 1000 , 1) ;
					closedir($handle);
					return $currenttemp;
				}
			}
		}
		closedir($handle);
	}
	return "Error";
}



?>

