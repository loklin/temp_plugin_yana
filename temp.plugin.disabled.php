<?php
/*
@name Température
@author Yohanndesbois  <non@pasici.com>
@link http://blog.idleman.fr
@licence CC by nc sa
@version 1.0.0
@description Gestion des capteurs de température via port série (arduino) et gpio (DS18b20)
*/
// basé sur le plugin Temperature de MORAIS Jose <vulcain03@gmail.com>
// Basé sur le plugin Température DS18B20 de Arnaud LESUEUR
// Sévèrement inspiré du plugin wireRelay d'IDLE
// Merci

include('Temp.class.php');


//fonction qui affiche le liens dans la barre du haut

function temp_plugin_menu(&$menuItems){
	global $_;
	$menuItems[] = array('sort'=>10,'content'=>'<a href="index.php?module=temp"><i class="icon-th-large"></i> Températures</a>');
}



//fonction qui affiche la page des temperatures


function temp_plugin_page($_){
	global $_,$conf;


	if(isset($_['module']) && $_['module']=='temp'){

?> 

<div >
<iframe src="plugins/temp/test.php" frameborder="0" scrolling="no"></Iframe> 
</div>
<?php


}
}
//fonction de config

function temp_plugin_setting_page(){
	global $_,$myUser,$conf;
	if(isset($_['section']) && $_['section']=='temp' ){

		if($myUser!=false){
			$tempManager = new Temp();
			$temps = $tempManager->populate();
			$roomManager = new Room();
			$rooms = $roomManager->populate();

			//Si on est en mode modification
			if (isset($_['id'])){
				$id_mod = $_['id'];
				$selected = $tempManager->getById($id_mod);
				$description = $selected->GetName();
				$button = "Modifier";
			}
			//Si on est en mode ajout
			else
			{
				$description =  "Ajout d'une sonde";
				$button = "Ajouter";
			}
			?>

		<div class="span9 userBloc">


		<h1>Température</h1>
		<p>Gestion des capteurs de température</p>  
		
        <p><b>Attention ce plugin nécessite l'activation du plugin [Room] avant utilisation !</b></p>

		
		<form action="action.php?action=temp_add_temp" method="POST">
		<fieldset>
		    <legend>Ajout d'un capteur</legend>

		    <div class="left">
			    <label for="nameTemp">Nom</label>

				<?php  if(isset($selected)){echo '<input type="hidden" name="id" value="'.$id_mod.'">';} ?>
				<input type="text" id="nameTemp" value="<?php  if(isset($selected)){echo $selected->getName();} ?>" onkeyup="$('#vocalCommand').html($(this).val());" name="nameTemp" placeholder="Capteur salon"/>

			<small>Commande vocale associée : "<?php echo $conf->get('VOCAL_ENTITY_NAME'); ?>, température <span id="vocalCommand"></span>"</small>			    

			    <label for="descriptionTemp">Localisation</label>
			    <input type="text" value="<?php if(isset($selected)){echo $selected->getDescription();} ?>" name="descriptionTemp" id="descriptionTemp" placeholder="A droite en entrant" />
		     <div>	    
				<label for="protocolTemp">Protocole</label>
				<select name="protocolTemp" id="protocolTemp">
  				<option value="protoserie">Série</option>
  				<option value="protodallas">DS18B20 par gpio</option>

  				</select> 

    </div>
    <div class="protoserie box">
    <label for="capteurTemp">Commande série du capteur</label>
    <input type="text" value="<?php if(isset($selected)){echo $selected->getCapteur();} ?>"name="capteurTemp" id="capteurTemp" placeholder="commande serie" />
    </div>
    <div class="protodallas box">
		<p>Gestion des capteurs de température DS18B20 par locaux</p>  
		<p>Pour connaitre la mise en oeuvre des capteurs cela se passe <a href="http://learn.adafruit.com/adafruits-raspberry-pi-lesson-11-ds18b20-temperature-sensing/">ici<a>.</p>
    <label for="capteurdallasTempHouse">N° de série du capteur</label>
    <input type="text" value="<?php if(isset($selected)){echo $selected->getCapteur();} ?>"name="capteurdallasTempHouse" id="capteurdallasTempHouse" placeholder="commande dallas" />
    </div>

 </form>



</br>

			    <label for="roomTemp">Pièce</label>
			    <select name="roomTemp" id="roomTemp">
			    	<?php foreach($rooms as $room){ 
									if (isset($selected)){$selected_room = ($selected->getRoom());
									}else if(isset($_['room'])){
										$selected_room = $_['room'];
									}else{
										$selected_room = null;}
?>
			    	<option <?php  if ($selected_room == $room->getId()){echo "selected";} ?> value="<?php echo $room->getId(); ?>"><?php echo $room->getName(); ?></option>
			    	<?php } ?>
			    </select>






			    


			</div>

  			<div class="clear"></div>
		    <br/><button type="submit" class="btn"><?php  echo $button; ?></button>
	  	</fieldset>
		<br/>
	</form>

		<table class="table table-striped table-bordered table-hover">
	    <thead>
	    <tr>
	    	<th>Nom</th>
		    <th>Localisation</th>
		    <th>Commande série du capteur</th>
		    <th>Protocol</th>
		    <th>Pièce</th>
	    </tr>
	    </thead>
	    
	    <?php foreach($temps as $temp){ 

	    	$room = $roomManager->load(array('id'=>$temp->getRoom())); 
	    	?>
	    <tr>
	    	<td><?php echo $temp->getName(); ?></td>
		    <td><?php echo $temp->getDescription(); ?></td>
		    <td><?php echo $temp->getCapteur(); ?></td>
		    <td><?php echo $temp->getProtocol(); ?></td>
		    <td><?php echo $room->getName(); ?></td>
		    <td><a class="btn" href="action.php?action=temp_delete_temp&id=<?php echo $temp->getId(); ?>"><i class="icon-remove"></i></a>
<a class="btn" href="setting.php?section=temp&id=<?php echo $temp->getId(); ?>"><i class="icon-edit"></i></a>

</td>

	    </tr>
	    <?php } ?>
	    </table>
		</div>

<?php }else{ ?>

		<div id="main" class="wrapper clearfix">
			<article>
					<h3>Vous devez être connecté</h3>
			</article>
		</div>
<?php
		}
	}

}


//fonction qui affiche le liens dans config


function temp_plugin_setting_menu(){
	global $_;
	echo '<li '.(isset($_['section']) && $_['section']=='temp'?'class="active"':'').'><a href="setting.php?section=temp"><i class="icon-chevron-right"></i> Temperature</a></li>';
}


//fonction qui affiche les bulles dans le module room

function temp_display($room){
	global $_,$conf;

	$tempManager = new Temp();
	$temps = $tempManager->loadAll(array('room'=>$room->getId()));
	
	foreach ($temps as $temp) {
$temph = $temp->getCapteur();
$number = temp_get($temph);
$color = getProperColor($number);
?>
					<div class="span3">
					<div class="roundBloc <?php echo $color ?>" style="max-width:100%;">
						<h5><center>Température <?php echo $temp->getName() ?></center></h5>	
						<p>Capteur n° : <?php echo $temph ?>
						</p><ul></br>
						<b><center>Il fait <?php echo $number ?> 'C</center></b>
						</br>
						<li><?php echo $temp->getDescription(); ?></li>
					</ul>					
				</div>
			</div>
	<?php
	}
}

//fonction ajout modification suppression synthese vocale

function temp_action_temp(){
	global $_,$conf,$myUser;
			$myUser->loadRight();
	switch($_['action']){
		case 'temp_delete_temp':
			if($myUser->can('capteurs','d')){
				$tempManager = new Temp();
				$tempManager->delete(array('id'=>$_['id']));
			}
			header('location:setting.php?section=temp');
		break;
		
		case 'temp_add_temp':
				$right_toverify = isset($_['id']) ? 'u' : 'c';

				if($myUser->can('capteurs',$right_toverify)){
					$temp = new Temp();
					//Si modification on charge la ligne au lieu de la créer
					if ($right_toverify == "u"){$temp = $temp->load(array("id"=>$_['id']));}
					$temp->setName($_['nameTemp']);
					$temp->setDescription($_['descriptionTemp']);
					$temp->setProtocol($_['protocolTemp']);
					$temp->setCapteur($_['capteurTemp']);
					$temp->setRoom($_['roomTemp']);
					$temp->save();
			header('location:setting.php?section=temp');
				}
				else
				{
					header('location:setting.php?section=temp&error=Vous n\'avez pas le droit de faire ça!');
				}


				break;


		break;
				case 'Temp_plugin_setting':
				$conf->put('plugin_temp_port',$_['port']);
				header('location: setting.php?section=preference&block=Temp');
				break;
		case 'temp_action':
			global $_;
			$TemP = new Temp();
				$TemP = $TemP->getById($_['engine']);
				$temph = $TemP->getCapteur();
				$affirmation = 'Il fait '.temp_get($temph).' degrés '.$TemP->getDescription();
				$response = array('responses'=>array(array('type'=>'talk','sentence'=>$affirmation)));
				$json = json_encode($response);
				echo ($json=='[]'?'{}':$json);
				
		break;
	}
}


//fonction commande vocale

function temp_vocal_command(&$response,$actionUrl){
	global $conf;
	$TemPManager = new Temp();	

	$TemPs = $TemPManager->populate();
	foreach($TemPs as $TemP){
	
	$response['commands'][] = array('command'=>$conf->get('VOCAL_ENTITY_NAME').' temperature '.$TemP->getName(),'url'=>$actionUrl.'?action=temp_action&engine='.$TemP->getId(),'confidence'=>'0.88');
   	}
}


//fonction recuperation de temperature port serie

function temp_get($temph){
	global $_,$conf;
$portserie = $conf->get('plugin_temp_port');
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
}

//fonction recuperation de temperature port serie

function dallastemphouse_get($temph){
	
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






//fonction qui affiche les couleurs en fonction de la temperature

function getProperColor($number)
{
    if ($number <= 0) //1
        return 'temp-1';

    else if ($number >= 1 && $number <= 2)
        return 'temp-2'; 
    else if ($number >= 3 && $number <= 4)
        return 'temp-3'; 
    else if ($number >= 5 && $number <= 6)
        return 'temp-4'; 
    else if ($number >= 7 && $number <= 8)
        return 'temp-5'; 
    else if ($number >= 9 && $number <= 10)
        return 'temp-6'; 
    else if ($number == 11)
        return 'temp-7'; 
    else if ($number == 12)
        return 'temp-8'; 
    else if ($number == 13)
        return 'temp-9'; 
    else if ($number == 14)
        return 'temp-10'; 
    else if ($number == 15)
        return 'temp-11'; 
    else if ($number == 16)
        return 'temp-12'; 
    else if ($number == 17)
        return 'temp-13'; 
    else if ($number == 18)
        return 'temp-14'; 
    else if ($number == 19)
        return 'temp-15'; 
    else if ($number == 20)
        return 'temp-16'; 
    else if ($number == 21)
        return 'temp-17'; 
    else if ($number == 22)
        return 'temp-18'; 
    else if ($number == 23)
        return 'temp-19'; 
    else if ($number == 24)
        return 'temp-20'; 
    else if ($number == 25)
        return 'temp-21'; 
    else if ($number == 26)
        return 'temp-22'; 
    else if ($number == 27)
        return 'temp-23'; 
    else if ($number >= 28 && $number <= 29)
        return 'temp-24'; 
    else if ($number >= 30 && $number <= 34)
        return 'temp-25'; 
    else if ($number >= 35 && $number <= 39)
        return 'temp-26'; 

    else if ($number >= 40) //27
        return 'temp-27';

}

//fonction config du plugin, reglage du port serie

		function temp_plugin_preference_menu(){
			global $_;
			echo '<li '.(@$_['block']=='temp'?'class="active"':'').'><a  href="setting.php?section=preference&block=Temp"><i class="icon-chevron-right"></i> Temperature Serie </a></li>';
		}
		function temp_plugin_preference_page(){
			global $myUser,$_,$conf;
			if((isset($_['section']) && $_['section']=='preference' && @$_['block']=='Temp' )  ){
				if($myUser!=false){
					?>

					<div class="span9 userBloc">
						<form class="form-inline" action="action.php?action=Temp_plugin_setting" method="POST">

							<p>Port série du raspberry PI branché à l'Arduino (ou autre): </p>
							<input type="text" class="input-large" name="port" value="<?php echo $conf->get('plugin_temp_port');?>" placeholder="Port serie...">

						

							<button type="submit" class="btn">Enregistrer</button>
						</form>
					</div>

					<?php }else{ ?>

					<div id="main" class="wrapper clearfix">
						<article>
							<h3>Vous devez être connecté</h3>
						</article>
					</div>
					<?php

				}
			}
		}

// plugin et css graph

//Plugin::addCss("/js/libs/twitter-bootstrap/css/bootstrap.min.css");
//Plugin::addCss("/sb-admin/sb-admin.css");
//Plugin::addCss("/js/libs/responsive-calendar/responsive-calendar.css");
//Plugin::addCss("/font-awesome/css/font-awesome.css");
//Plugin::addJs("/js/graph.js");

//Plugin::addJs("/js/libs/jquery/jquery.js");
//Plugin::addJs("/js/libs/twitter-bootstrap/js/bootstrap.min.js");
//Plugin::addJs("/js/libs/jquery/plugins/metisMenu/jquery.metisMenu.js");
//Plugin::addJs("/sb-admin/sb-admin.js");
//Plugin::addJs("/js/libs/responsive-calendar/responsive-calendar.min.js");
//Plugin::addJs("/js/libs/jquery/plugins/flot/jquery.flot.js");
//Plugin::addJs("/js/libs/jquery/plugins/flot/jquery.flot.tooltip.min.js");
//Plugin::addJs("/js/libs/jquery/plugins/flot/jquery.flot.downsample.js");
//Plugin::addJs("/js/libs/jquery/plugins/flot/jquery.flot.time.js");    


Plugin::addCss("/css/style.css"); 
Plugin::addJs("/js/showhide.js");

Plugin::addHook("preference_menu", "Temp_plugin_preference_menu"); 
Plugin::addHook("preference_content", "Temp_plugin_preference_page"); 

Plugin::addHook("action_post_case", "temp_action_temp"); 
Plugin::addHook("node_display", "temp_display");
Plugin::addHook("setting_bloc", "temp_plugin_setting_page");  
Plugin::addHook("setting_menu", "temp_plugin_setting_menu");  
Plugin::addHook("vocal_command", "temp_vocal_command");

Plugin::addHook("menubar_pre_home", "temp_plugin_menu"); 
Plugin::addHook("home", "temp_plugin_page");
?>
