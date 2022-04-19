<?php
if (!defined('KAHUKPATH')) {
	die();
}

class kahukconfig
{
	var $id = 0;
	var $var_page = 0;
	var $var_name = 0;
	var $var_value = 0;
	var $var_defaultvalue = 0;
	var $var_optiontext = false;
	var $var_title = 0;
	var $var_desc = '';
	var $EditInPlaceCode = '';

	function showpage()
	{
		global $db;
?>
		<div class="admin_config_content">
	<?php

		$sql = "Select * from " . table_config . " where var_page = '$this->var_page'";
		$configs = $db->get_results($sql);
		if ($configs) {
			global $db, $main_smarty;
			echo '<table class="table table-bordered table-striped">';
			echo '<thead><tr>';
			echo '<th>Title</th>';
			echo '<th>' . $main_smarty->get_config_vars(KAHUK_Visual_Config_Description) . '</th>';
			echo '<th style="min-width:120px">' . $main_smarty->get_config_vars(KAHUK_Visual_Config_Value) . '</th>';
			echo '<th style="width:120px;">' . $main_smarty->get_config_vars(KAHUK_Visual_Config_Default_Value) . '</th>';
			echo '<th style="width:120px;">' . $main_smarty->get_config_vars(KAHUK_Visual_Config_Expected_Values) . '</th>';
			echo '</tr></thead><tbody>';

			foreach ($configs as $config) {
				foreach ($config as $k => $v)
					$this->$k = $v;
				$this->print_summary();
			}
			echo '</tbody></table>';
		} else {
			echo "No Configuration Tables Found";
		}
		echo '</div><div style="clear:both;"> </div>';
	}

	function read()
	{
		global $db;
		$config = $db->get_row("SELECT * FROM " . table_config . " WHERE var_id = $this->var_id");

		$this->var_page = $config->var_page;
		$this->var_name = $config->var_name;
		$this->var_value = htmlentities($config->var_value);
		$this->var_defaultvalue = $config->var_defaultvalue;
		$this->var_optiontext = $config->var_optiontext;
		$this->var_title = $config->var_title;
		$this->var_desc = $config->var_desc;

		return true;
	}

	function store($loud = true)
	{
		global $db;

		if (strtolower(trim($this->var_value)) == 'true') {
			$this->var_value = 'true';
		}

		if (strtolower(trim($this->var_value)) == 'false') {
			$this->var_value = 'false';
		}

		$sql = "UPDATE " . table_config . " set var_value = '" . $db->escape(trim($this->var_value)) . "' where var_id = " . $db->escape(sanitize($this->var_id, 3));
		$db->query($sql);

		$content = trim($this->var_value);

		if ($loud == true) {
			print(htmlspecialchars($content));
		}

		return true;
	}

	function print_summary()
	{
		global $db, $main_smarty;
		/* Redwine: in some instances, the trailing space is left. we want to make sure to trim it.*/
		$this->var_value = trim($this->var_value);
		echo '<span id="var_' . $this->var_id . '_span"><form onsubmit="return false">';
		echo '<tr>';
		echo "<td>" . translate($this->var_title) . "</td>";
		echo "<td>" . translate($this->var_desc) . "</td><td>";

		$gethttphost = $_SERVER["HTTP_HOST"];
		$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http') . '://';
		$port = strpos($gethttphost, ':');
		if ($port !== false) {
			$httphost = substr($gethttphost, 0, $port);
		} else {
			$httphost = $gethttphost;
		}
		$standardport = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 443 : 80);
		$waitTimeoutInSeconds = 1;
		if ($fp = fsockopen($httphost, $standardport, $errCode, $errStr, $waitTimeoutInSeconds)) {
			$expected_base_url = $protocol . $httphost;
		}
		fclose($fp);
		if ($this->var_name == '$my_base_url') {
			echo translate("It looks like this should be set to") . " <strong>" . $expected_base_url . "</strong> ";
		}

		if ($this->var_name == '$my_kahuk_base') {
			$pos = strrpos($_SERVER["SCRIPT_NAME"], "/admin/");
			$path = substr($_SERVER["SCRIPT_NAME"], 0, $pos);
			if ($path == "/" || $path == "") {
				$path = translate("Nothing - Leave it blank");
			}
			echo translate("It looks like this should be set to") . " <strong>" . $path . "</strong><br>";
		}

		echo '<input class="form-control admin_config_input emptytext" id="editme' . $this->var_id . '" onclick="show_edit(' . $this->var_id . ')" value="' . htmlentities($this->var_value, ENT_QUOTES, 'UTF-8') . '">';
		echo '<span class="emptytext" id="showme' . $this->var_id . '" style="display:none;">';
		if (preg_match('/^\s*(.+),\s*(.+) or (.+)\s*$/', $this->var_optiontext, $m)) {
			echo "<select name=\"var_value\" class=\"form-control\">";
			for ($ii = 1; $ii <= 3; $ii++)
				echo "<option value='{$m[$ii]}' " . ($m[$ii] == $this->var_value ? "selected" : "") . ">{$m[$ii]}</option>";
			echo "</select><br />";
		} elseif (
			preg_match('/^\s*(.+[^\/])\s*\/\s*([^\/].+)\s*$/', $this->var_optiontext, $m) ||
			preg_match('/^\s*(.+) or (.+)\s*$/', $this->var_optiontext, $m)
		) {
			if (
				preg_match('/^(\d+)\s*=\s*(.+)$/', $m[1], $m1) &&
				preg_match('/^(\d+)\s*=\s*(.+)$/', $m[2], $m2)
			)
				echo "<select name=\"var_value\" class=\"form-control\"><option value='{$m1[1]}' " . ($m1[1] == $this->var_value ? "selected" : "") . ">{$m1[2]}</option><option value='{$m2[1]}' " . ($m2[1] == $this->var_value ? "selected" : "") . ">{$m2[2]}</option></select><br />";
			else
				echo "<select name=\"var_value\" class=\"form-control\"><option value='{$m[1]}' " . ($m[1] == $this->var_value ? "selected" : "") . ">{$m[1]}</option><option value='{$m[2]}' " . ($m[2] == $this->var_value ? "selected" : "") . ">{$m[2]}</option></select><br />";
		} elseif (preg_match('/^\s*(\d+)\s*-\s*(\d+)\s*$/', $this->var_optiontext, $m)) {
			echo "<select name=\"var_value\" class=\"form-control\">";
			for ($ii = $m[1]; $ii <= $m[2]; $ii++)
				echo "<option value='$ii' " . ($ii == $this->var_value ? "selected" : "") . ">$ii</option>";
			echo "</select><br />";
		} else {
			echo "<input type=\"text\" class='form-control admin_config_input edit_input' name=\"var_value\" value=\"" . htmlentities($this->var_value, ENT_QUOTES, 'UTF-8') . "\" ";
			if (strpos($this->var_optiontext, 'number') === 0) {
				$min = preg_match('/at least (\d+)/', $this->var_optiontext, $m) ? $m[1] : 0;
				echo "size='5' onblur='check_number({$this->var_id},this,$min)'";
			}
			echo '>';
		}
		echo "<input style='margin:4px 4px 0 0;' type=\"submit\" class=\"btn btn-primary\" value=\"Save\" onclick=\"save_changes({$this->var_id},this.form)\">";
		echo "<input style='margin-top:3px;' type=\"reset\" class=\"btn btn-default\" value=\"Cancel\" onclick=\"hide_edit({$this->var_id})\"></span></td>";
		echo "<td>{$this->var_defaultvalue}</td>";
		echo "<td>{$this->var_optiontext}</td>";
		echo '<input type = "hidden" name = "var_id" value = "' . $this->var_id . '">';
		echo "</td></tr></form></span>";
	}
}
