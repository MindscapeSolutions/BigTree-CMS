<?
	$id = intval($_POST["id"]);

	$clean_referer = str_replace(array("http://","https://"),"//",$_SERVER["HTTP_REFERER"]);
	$clean_admin_root = str_replace(array("http://","https://"),"//",ADMIN_ROOT)."roles/edit/".$id."/";

	if ($clean_referer != $clean_admin_root) {
?>
<div class="container">
	<section>
		<p>To update a role, please access the <a href="<?=ADMIN_ROOT?>roles/edit/<?=$id?>/">Edit Role</a> page.</p>
	</section>
</div>
<?
	} else {
		$success = $admin->updateRole($id,$_POST);
		
		if (!$success) {
			$_SESSION["bigtree_admin"]["update_role"] = $_POST;
			$_SESSION["bigtree_admin"]["update_role"]["error"] = "role";
			$admin->growl("Roles","Update Failed","error");
			BigTree::redirect(ADMIN_ROOT."roles/edit/$id/");
		}
		
		$admin->growl("Roles","Updated Role");
		
		BigTree::redirect(ADMIN_ROOT."roles/");
	}
?>
