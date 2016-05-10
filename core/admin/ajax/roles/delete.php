<?
	header("Content-type: text/javascript");
	$id = intval($_POST["id"]);
	$admin->requireLevel(1);
	$result = $admin->deleteRole($id);

    if ($result) {
?>
$("#row_<?=$id?>").remove();
BigTree.growl("Roles","Deleted Role");
<?
    }
    else {
?>
BigTree.growl("Roles","Unable: Users Still Assigned");
<?
    }
?>
