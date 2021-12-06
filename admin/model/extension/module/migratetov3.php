<?php
class ModelExtensionModuleMigrateToV3 extends Model {
    public function setModuleStatus($data) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "setting` WHERE `store_id` = '0' AND `code` = 'module_migratetov3'");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "setting` SET `store_id` = '0', `code` = 'module_migratetov3', `key` = 'module_migratetov3_status', `value` = '" . $data . "'");
    }
	
	public function checkDBs($db_host,$db_user,$db_password,$db_port,$db_name,$db_prefix) {
        $con=mysqli_connect($db_host,$db_user,$db_password,$db_name);

// Check connection
        if (mysqli_connect_errno()) {
			return false;
        } else {
            $sql = "SHOW TABLES LIKE '" . $db_prefix . "address'";
            $exists=mysqli_query($con, $sql);
            if($exists->num_rows == 1)
			{
			    return true;
			} else {
			    return false;
		    }
        }
	}
	
	public function emptyTable($con2,$db_prefix2,$tables) {  
        if (mysqli_connect_errno()) {
			return false;
        } else {
            foreach ($tables as $table)
			{
			    $sql = "TRUNCATE TABLE `".$db_prefix2.$table."`";
                $exists=mysqli_query($con2, $sql);
                if(!$exists == 1)
			    {
			        return false;
		        }
			}
			return true;
        }
    }
	
    public function checkBoth($con1, $con2, $db_prefix1, $db_prefix2, $table) {  
	    $sql1 = "SHOW TABLES LIKE '" . $db_prefix1 .$table."'";
        $exists1=mysqli_query($con1, $sql1);
		if($exists1->num_rows == 0)
		{
		    return false;
		} else {
		    $sql2 = "SHOW TABLES LIKE '" . $db_prefix2 .$table."'";
            $exists2=mysqli_query($con2, $sql2);
		    if($exists2->num_rows == 0)
		    {
		        return false;
		    } else {
		        return true;
		    }
		}
	}
	
	public function rowExists($con, $db_prefix, $table, $row) { 
	    $sql = "SHOW COLUMNS FROM `".$db_prefix.$table."` LIKE '".$row."'";
        $exists=mysqli_query($con, $sql);

		if($exists->num_rows == 0)
		{
		    return false;
		} else {
		    return true;
		}
	}
}
