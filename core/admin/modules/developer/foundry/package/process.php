<?
	$breadcrumb[] = array("title" => "Download Package", "link" => "#");
	
	// Function used for template directory inclusion:
	function _local_recurseFileDirectory($directory) {
		global $x,$index,$tname,$dir;
		$o = opendir($directory);
		while ($r = readdir($o)) {
			if ($r != "." && $r != "..") {
				if (is_dir($directory.$r)) {
					_local_recurseFileDirectory($directory.$r."/");
				} else {
					$x++;
					$index .= "File::||BTX||::$x.part.btx::||BTX||::".str_replace(SERVER_ROOT,"",$directory)."$r::||BTX||::Template\n";
					copy($directory.$r,$dir."$x.part.btx");
				}
			}
		}
	}
	
	// If someone accidentally added something twice, remove the duplicates.
	foreach ($_POST as $key => $val) {
		if (is_array($val)) {
			$_POST[$key] = array_unique($val);
		}
	}
	
	BigTree::globalizePOSTVars();
	
	// First we need to package the file so they can download it manually if they wish.
	if (!is_writable(SERVER_ROOT."cache/")) {
		die("Please make the cache/ directory writable.");
	}
	
	$index = $package_name."\n";
	$index .= "Packaged for BigTree ".BIGTREE_VERSION." by ".$created_by."\n";
	
	if ($module) {
		$modules = array($module_details);
	} elseif ($group) {
		$group_details = $admin->getModuleGroup($group);
		$modules = $admin->getModulesByGroup($group_details["id"]);
		$index .= "Group::||BTX||::".json_encode($group_details)."\n";
	}
	
	// Clear the cache area to build the package.
	$dir = SERVER_ROOT."cache/packager/";
	exec("rm -rf ".$dir);
	@unlink(SERVER_ROOT."cache/package.tar.gz");
	mkdir(SERVER_ROOT."cache/packager");
	$x = 0;
	
	if (is_array($modules)) {
		foreach ($modules as $item) {
			// Do stuff to dump databases here.
			$index .= "Module::||BTX||::".json_encode($item)."\n";
			// Find the actions for the module
			$actions = $admin->getModuleActions($item["id"]);
			foreach ($actions as $a) {
				// If there's an auto module, include it as well.
				if ($a["form"]) {
					$form = $autoModule->getForm($a["form"]);
					$index .= "ModuleForm::||BTX||::".json_encode($form)."\n";
				}
				if ($a["view"]) {
					$view = $autoModule->getView($a["view"]);
					$index .= "ModuleView::||BTX||::".json_encode($view)."\n";
				}
				// Draw Action after the form/view since we'll need to know the form/view ID to create the action.
				$index .= "Action::||BTX||::".json_encode($a)."\n";
			}
		}
	}
	
	// Get the templates we're passing in.
	if (is_array($templates)) {
		foreach ($templates as $template) {
			$item = $cms->getTemplate($template);
			$index .= "Template::||BTX||::".json_encode($item)."\n";
			
			// If we're bringing over a module template, copy the whole darn folder.
			if ($item["routed"]) {
				_local_recurseFileDirectory(SERVER_ROOT."templates/routed/$template/"); 
			} else {
				$x++;
				copy(SERVER_ROOT."templates/basic/$template.php",$dir."$x.part.btx");
				$index .= "File::||BTX||::$x.part.btx::||BTX||::templates/basic/$template.php::||BTX||::Template\n";
			}
		}
	}
	
	// Get the callouts we're passing in.
	if (is_array($callouts)) {
		foreach ($callouts as $callout) {
			$item = $cms->getCallout($callout);
			$index .= "Callout::||BTX||::".json_encode($item)."\n";
			$x++;
			$index .= "File::||BTX||::$x.part.btx::||BTX||::templates/callouts/$callout.php\n";
			copy(SERVER_ROOT."templates/callouts/$callout.php",$dir."$x.part.btx");
		}
	}
	
	// Get the feeds
	if (is_array($feeds)) {
		foreach ($feeds as $feed) {
			$item = $cms->getFeed($feed);
			$index .= "Feed::||BTX||::".json_encode($item)."\n";
		}
	}
	
	// Get the settings
	if (is_array($settings)) {
		foreach ($settings as $setting) {
			$item = $admin->getSetting($setting);
			$index .= "Setting::||BTX||::".json_encode($item)."\n";
		}
	}
	
	// Get the field types
	if (is_array($field_types)) {
		foreach ($field_types as $type) {
			$item = $admin->getFieldType($type);
			$index .= "FieldType::||BTX||::".json_encode($item)."\n";
		}
	}
	
	// Get the included tables now... yep.
	if (is_array($tables)) {
		foreach ($tables as $t) {
			$x++;
			list($table,$type) = explode("#",$t);
			$f = sqlfetch(sqlquery("SHOW CREATE TABLE `$table`"));
			$create = str_replace(array("\r","\n")," ",end($f)).";\n";
			if ($type == "structure") {
				if (strpos($create,"AUTO_INCREMENT=") !== false) {
					$pos = strpos($create,"AUTO_INCREMENT=");
					$part1 = substr($create,0,$pos);
					$part2 = substr($create,strpos($create," ",$pos)+1);
					$create = $part1.$part2;
				}
			} else {
				$q = sqlquery("select * from `$table` order by id asc");
				while ($f = sqlfetch($q)) {
					$fields = array();
					$values = array();
					foreach ($f as $key => $val) {
						$fields[] = "`$key`";
						$values[] = '"'.mysql_real_escape_string(str_replace(array("\r","\n")," ",$val)).'"';
					}
					$create .= "INSERT INTO `$table` (".implode(",",$fields).") VALUES (".implode(",",$values).");\n";
				}
			}
			file_put_contents($dir."$x.part.btx",$create);
			$index .= "SQL::||BTX||::$table::||BTX||::$x.part.btx\n";
		}
	}
	
	
	// Copy all the class files over...
	if (is_array($class_files)) {
		foreach ($class_files as $file) {
			$x++;
			// This is a module class, replace var $Module = "[num]";
			$module_for_file = false;
			foreach ($class_files as $mid => $cfn) {
				if ($cfn == $file)
					$module_for_file = $mid;
			}
			$index .= "ClassFile::||BTX||::$x.part.btx::||BTX||::$file::||BTX||::$module_for_file\n";
			copy(SERVER_ROOT.$file,$dir."$x.part.btx");	
		}
	}
	
	// Copy all the required files over...
	if (is_array($required_files)) {
		foreach ($required_files as $file) {
			$x++;
			$index .= "File::||BTX||::$x.part.btx::||BTX||::$file::||BTX||::Required\n";
			copy(SERVER_ROOT.$file,$dir."$x.part.btx");	
		}
	}
	
	// Copy all the other files over...
	if (is_array($other_files)) {
		foreach ($other_files as $file) {
			$x++;
			$index .= "File::||BTX||::$x.part.btx::||BTX||::$file::||BTX||::Other\n";
			copy(SERVER_ROOT.$file,$dir."$x.part.btx");	
		}
	}
	
	file_put_contents($dir."index.btx",$index);
	exec("cd $dir; tar -zcf SERVER_ROOT"."cache/package.tar.gz *");
	
	// Create the saved copy of this creation.
	BigTree::globalizePOSTVars(array("mysql_real_escape_string"));
	
	$package_file = BigTree::getAvailableFileName(SITE_ROOT."files/",$cms->urlify($package_name).".tgz");
	
	// Move the file into place.
	BigTree::moveFile(SERVER_ROOT."cache/package.tar.gz",SITE_ROOT."/files/".$package_file);
?>
<h1><span class="foundry"></span>Download Package</h1>
<div class="form_container">
	<section>
		<p>Package created successfully.  You may download it <a href="<?=WWW_ROOT?>files/<?=$package_file?>">by clicking here</a>.</p>
	</section>
</form>