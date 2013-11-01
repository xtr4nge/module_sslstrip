<?

include "../../../config/config.php";
include "../../../functions.php";

// Checking POST & GET variables...
if ($regex == 1) {
	regex_standard($_POST['type'], "../../../msg.php", $regex_extra);
	regex_standard($_POST['tempname'], "../../../msg.php", $regex_extra);
	regex_standard($_POST['action'], "../../../msg.php", $regex_extra);
	regex_standard($_GET['mod_action'], "../../../msg.php", $regex_extra);
	regex_standard($_GET['mod_service'], "../../../msg.php", $regex_extra);
	regex_standard($_POST['new_rename'], "../../../msg.php", $regex_extra);
	regex_standard($_POST['new_rename_file'], "../../../msg.php", $regex_extra);
}

$type = $_POST['type'];
$tempname = $_POST['tempname'];
$action = $_POST['action'];
$mod_action = $_GET['mod_action'];
$mod_service = $_GET['mod_service'];
$newdata = html_entity_decode(trim($_POST["newdata"]));
$newdata = base64_encode($newdata);
$new_rename = $_POST["new_rename"];
$new_rename_file = $_POST["new_rename_file"];

if ($type == "inject") {

    if ($newdata != "") { $newdata = ereg_replace(13,  "", $newdata);
        $exec = "/bin/echo '$newdata' | base64 --decode > /usr/share/FruityWifi/www/modules/sslstrip/includes/inject.txt";
        exec("/usr/share/FruityWifi/bin/danger \"" . $exec . "\"", $output);
    }

    header('Location: ../index.php?tab=2');
    exit;

}

if ($type == "tamperer") {

    if ($newdata != "") { $newdata = ereg_replace(13,  "", $newdata);
        $exec = "/bin/echo '$newdata' | base64 --decode > /usr/share/FruityWifi/www/modules/sslstrip/includes/app_cache_poison/config.ini";
        exec("/usr/share/FruityWifi/bin/danger \"" . $exec . "\"", $output);
    }

    header('Location: ../index.php?tab=3');
    exit;

}

if ($type == "templates") {
	if ($action == "save") {
		
		if ($tempname != "0") {
			// SAVE TAMPLATE
			if ($newdata != "") { $newdata = ereg_replace(13,  "", $newdata);
				$template_path = "/usr/share/FruityWifi/www/modules/sslstrip/includes/app_cache_poison/templates";
        		$exec = "/bin/echo '$newdata' | base64 --decode > $template_path/$tempname";
        		exec("/usr/share/FruityWifi/bin/danger \"" . $exec . "\"", $output);
    		}
    	}
    	
	} else if ($action == "add_rename") {
	
		if ($new_rename == "0") {
			//CREATE NEW TEMPLATE
			if ($new_rename_file != "") {
				$template_path = "/usr/share/FruityWifi/www/modules/sslstrip/includes/app_cache_poison/templates";
				$exec = "/bin/touch $template_path/$new_rename_file";
				exec("/usr/share/FruityWifi/bin/danger \"" . $exec . "\"", $output);

				$tempname=$new_rename_file;
			}
		} else {
			//RENAME TEMPLATE
			$template_path = "/usr/share/FruityWifi/www/modules/sslstrip/includes/app_cache_poison/templates";
			$exec = "/bin/mv $template_path/$new_rename $template_path/$new_rename_file";
			exec("/usr/share/FruityWifi/bin/danger \"" . $exec . "\"", $output);

			$tempname=$new_rename_file;
		}
		
	} else if ($action == "delete") {
		if ($new_rename != "0") {
			//DELETE TEMPLATE
			$template_path = "/usr/share/FruityWifi/www/modules/sslstrip/includes/app_cache_poison/templates";
			$exec = "/bin/rm $template_path/$new_rename";
			exec("/usr/share/FruityWifi/bin/danger \"" . $exec . "\"", $output);	
		}
	}
	header("Location: ../index.php?tab=4&tempname=$tempname");
	exit;
}

if($mod_service == "mod_sslstrip_inject") {
    $exec = "/bin/sed -i 's/mod_sslstrip_inject=.*/mod_sslstrip_inject=".$mod_action.";/g' ../_info_.php";
    exec("/usr/share/FruityWifi/bin/danger \"" . $exec . "\"" );
}

if($mod_service == "mod_sslstrip_tamperer") {
    $exec = "/bin/sed -i 's/mod_sslstrip_tamperer=.*/mod_sslstrip_tamperer=".$mod_action.";/g' ../_info_.php";
    exec("/usr/share/FruityWifi/bin/danger \"" . $exec . "\"" );
}

header('Location: ../index.php');

?>