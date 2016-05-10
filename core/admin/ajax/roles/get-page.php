<?
	$admin->requireLevel(1);
	
	$query = isset($_GET["query"]) ? $_GET["query"] : "";
	$page = isset($_GET["page"]) ? intval($_GET["page"]) : 1;

	// Prevent SQL shenanigans
	$sort_by = "role";
	if (isset($_GET["sort"])) {
		$valid_columns = array("role");
		if (in_array($_GET["sort"],$valid_columns)) {
			$sort_by = $_GET["sort"];
		}
	}
	$sort_dir = (isset($_GET["sort_direction"]) && $_GET["sort_direction"] == "DESC") ? "DESC" : "ASC";

	$pages = $admin->getRolesPageCount($query);
	$results = $admin->getPageOfRoles($page,$query,"`$sort_by` $sort_dir");
	
	foreach ($results as $item) {
?>
<li id="row_<?=$item["id"]?>">
	<section class="view_column users_name"><?=$item["role"]?></section>
    <section class="view_column users_name"></section>
	<section class="view_action">
		<? if ($admin->Level >= $item["level"]) { ?>
		<a href="<?=ADMIN_ROOT?>roles/edit/<?=$item["id"]?>/" class="icon_edit"></a>
		<? } else { ?>
		<span class="icon_edit disabled_icon has_tooltip" data-tooltip="<p><strong>Edit Role</strong></p><p>You may not edit this role.</p>"></span>
		<? } ?>
	</section>
	<section class="view_action">
		<a href="#<?=$item["id"]?>" class="icon_delete"></a>
	</section>
</li>
<?
	}
?>
<script>
	BigTree.setPageCount("#view_paging",<?=$pages?>,<?=$page?>);
</script>
