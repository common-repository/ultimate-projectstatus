<?php 
class UltimateProjectStatus extends ZO_Basics_UPS {
	//############################
	//! PRIVATE VARS
	//############################
	
	//############################
	//! PUBLIC VARS
	//############################
	public $table;
	
	public $tablestatus;
	
	private $template_projectentry;
	
	private $template_projectlist;
	
	private $plugin_url;
	//############################
	//! CONSTRUCTORS
	//############################
	function __construct() {
		parent::__construct();
		global $wpdb;
		global $_basepath_UPS;
		global $ZO_UPS_config;
		
		$this->table = $wpdb->prefix.'projects';
		$this->tablestatus = $wpdb->prefix.'projectstatus';
		$this->template_projectentry = $_basepath_UPS.'templates/projectentry.html';
		$this->template_projectlist = $_basepath_UPS.'templates/projectlist.html';
		$this->plugin_url = 'http://code.zero-one.ch';
		$this->plugin_url .= '/?productid=' . md5("Ultimate-ProjectStatus");
		//$this->install_tables();
		if($this->install_tables()) {
			$this->adding_installation_stats();
			//print "<p>Databasetables has been installed...</p>";
		}
	}
	
	function __destruct() {}
	
	//############################
	//! PRIVATE
	//############################
	private function show_projectStatus() {
		global $wpdb;
		
		$backval = file_get_contents($this->template_projectlist);
		
		$wpdb->query("SELECT * FROM ".$this->table." as p, ".$this->tablestatus." as s WHERE p.StatusID = s.ID ORDER BY 'p.Name' ");
		$projects = $wpdb->get_results($wpdb->last_query,"ARRAY_A");
		$projects_entrys = "";
		foreach($projects as $project) {
			$projects_entrys .= $this->format_projectentry($project);
		}
		
		// Getting the Date of the last update
		$wpdb->query("SELECT UpdateDate FROM ".$this->table." ORDER BY 'UpdateDate' LIMIT 1");
		$date = $wpdb->get_results($wpdb->last_query,"ARRAY_A");
		$date_string = gmdate("l j F Y", strtotime($date[0]['UpdateDate']));
		
		$backval = str_ireplace('%lastupdate%',$date_string,$backval);
		$backval = str_ireplace('%projectentry%',$projects_entrys,$backval);

		return $backval;
	}
	
	private function adding_installation_stats() {
		global $_GET;
		
		if(!array_key_exists('productid', $_GET)) {		
			$ch = curl_init($this->plugin_url); 
	
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, false); 
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
			curl_setopt($ch, CURLOPT_TIMEOUT, 5);
			curl_exec($ch); 
	
			// Check if any error occured 
			if(curl_errno($ch)) { 
	    		return true; 
			} else { 
	    		return false; 
			} 
	
			// Close handle 
			curl_close($ch); 
		}
		return false;
	}
		
	//http://code.zero-one.ch/?page_id=125&preview=true
	private function format_projectentry($projectdata) {
		require_once(ABSPATH . WPINC . '/registration.php');

		$backval = "";
		
		// Formating the workers
		$workers = explode(",", $projectdata['Workers']);
		//$projectdata['Workers'] = "<ul>";
		$projectdata['Workers'] = "";
		foreach($workers as $worker) {
			if(username_exists( $worker )) {
				$userdata = get_userdatabylogin( $worker );
				$workername = '<a href="mailto:'.$userdata->user_email.'" alt="'.$userdata->description.'">' . $userdata->display_name . '</a>';
			} else {
				$workername = $worker;
			}
		
			//$projectdata['Workers'] .= '<li>'.$workername.'</li>';
			$projectdata['Workers'] .= '<span class="person">'.$workername.'</span>';
		}
		//$projectdata['Workers'] .= '</ul>';
		
		//Formatting the Duration
		$projectdata['Duration'] = round((strtotime($projectdata['ProjectEnd']) - gmmktime()) / 60 / 60 / 24,1);
		if($projectdata['Duration'] <= 0) { $projectdata['Duration'] = 0; }
		
		//Formatting the Title
		if($projectdata['Name'] and $projectdata['CodeName']) {
			$projectdata['Name'] = $projectdata['Name'] . " (".$projectdata['CodeName'].")";
		} elseif(!$projectdata['Name'] and $projectdata['CodeName']) {
			$projectdata['Name'] = $projectdata['CodeName'];
		}
		
		if($projectdata['ProjectURL']) {

			$projectdata['Name'] = '<a href="'.$projectdata['ProjectURL'].'">'.$projectdata['Name'].'</a>';
		}
		// Getting the Template content
		$backval = file_get_contents($this->template_projectentry);
		
		// And put all information in it :)
		$fields = array_keys($projectdata);
		foreach($fields as $field) {
			$backval = str_ireplace('%'.$field.'%',$projectdata[$field],$backval);
		}
		
		return $backval;
	}
	
	private function install_tables() {
		global $wpdb;
		$backval = 0;

		if(strtolower($wpdb->get_var("show tables like '".$this->table."'")) != strtolower($this->table)) {
			// If the table allready exists, return 0 and quit
			// The Table Structure
	   		$structure = "CREATE TABLE IF NOT EXISTS `".$this->table."` (
				`ID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
				`ProjectStart` DATE NOT NULL ,
				`ProjectEnd` DATE NOT NULL ,
				`Name` VARCHAR( 250 ) NOT NULL ,
				`CodeName` VARCHAR( 250 ) NOT NULL ,
				`Description` VARCHAR( 250 ) NOT NULL ,
				`Workers` VARCHAR( 250 ) NOT NULL ,
				`ProjectURL` VARCHAR( 250 ) NOT NULL ,
				`Status` VARCHAR( 250 ) NOT NULL ,
				`StatusID` INT NOT NULL DEFAULT '1',
				`UpdateDate` DATE NOT NULL
				) ENGINE = MYISAM ;";
	    	$wpdb->query($structure);

			// The First Entry
			$firstdata = "INSERT INTO `".$this->table."`( `ID` ,
				`ProjectStart` ,
				`ProjectEnd` ,
				`Name` ,
				`CodeName` ,
				`Description` ,
				`Workers` ,
				`ProjectURL` ,
				`Status` ,
				`StatusID`,
				`UpdateDate`)
				VALUES (NULL , NOW( ) , NOW( ) , 'TestProject', 'Obelix','This is the Description', 'tspycher, John Doe','http://code.zero-one.ch', 'Finishing Evaluation Phase', '1', NOW());";
			$wpdb->query($firstdata);
			$backval++;
		}

		if(strtolower($wpdb->get_var("show tables like '".$this->tablestatus."'")) != strtolower($this->tablestatus)) {
			// If the table allready exists, return 0 and quit
			// The Table Structure
	   		$structure = "CREATE TABLE IF NOT EXISTS `".$this->tablestatus."` (
				`ID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
				`StatusName` VARCHAR( 250 ) NOT NULL ,
				`Color` VARCHAR( 250 ) NOT NULL
				) ENGINE = MYISAM ;";
	    	$wpdb->query($structure);

			// The First Entry
			$firstdata = array("INSERT INTO `".$this->tablestatus."` (`ID`, `StatusName`, `Color`) VALUES (1, 'Full Speed', '00ff00');",
				"INSERT INTO `".$this->tablestatus."` (`ID`, `StatusName`, `Color`) VALUES (2, 'En Route', 'ffff00');",
				"INSERT INTO `".$this->tablestatus."` (`ID`, `StatusName`, `Color`) VALUES (3, 'Grounded', 'ff0000');",
				"INSERT INTO `".$this->tablestatus."` (`ID`, `StatusName`, `Color`) VALUES (4, 'Holding', 'ff0000');",
				"INSERT INTO `".$this->tablestatus."` (`ID`, `StatusName`, `Color`) VALUES (5, 'Landed', 'ffffff');");
			foreach($firstdata as $query) {
				$wpdb->query($query);
			}
			$backval++;
		}
		return $backval;
	}
		
		
	//############################
	//! PUBLIC
	//############################
	public function loadfrontend($content) {
		global $ZO_UPS_config;
		global $wpdb;
		
		$myregex = $ZO_UPS_config['regex'][0];
		$tmp = $content;
		
		if (preg_match_all($myregex, $tmp, $matches)){
			foreach($matches[0] as $match) {
				$functiondata = $match;
				$functiondata = strtolower(str_replace("%","",$functiondata));
				$functiondatadetails = explode(":",$functiondata);
				
				//Get the Repository Details
				switch($functiondatadetails[0]) {
					case "projectstatus":
						$backval = $this->show_projectStatus();
						break;
					default:
						unset($backval);
						break;				}
				
				if($backval) {
					$content = str_replace($match, $backval, $content);
				}
			}
		}
		
		return $content;
	}

}
?>