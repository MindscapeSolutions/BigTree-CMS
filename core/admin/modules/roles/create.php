<?
	$clean_referer = str_replace(array("http://","https://"),"//",$_SERVER["HTTP_REFERER"]);
	$clean_admin_root = str_replace(array("http://","https://"),"//",ADMIN_ROOT)."roles/add/";

	if ($clean_referer != $clean_admin_root) {
?>
<div class="container">
	<section>
		<p>To create a role, please access the <a href="<?=ADMIN_ROOT?>roles/add/">Add Role</a> page.</p>
	</section>
</div>
<?
	} else {
		$id = $admin->createRole($_POST);	
		if (!$id) {
			$_SESSION["bigtree_admin"]["create_role"] = $_POST;
			$_SESSION["bigtree_admin"]["create_role"]["error"] = "role";
			$admin->growl("Roles","Creation Failed","error");
			BigTree::redirect(ADMIN_ROOT."roles/add/");
		}
	
		$admin->growl("Roles","Added Role");
		BigTree::redirect(ADMIN_ROOT."roles/");
	}
?>
