<?
	$admin->requireLevel(1);
	
	$query = isset($_GET["query"]) ? $_GET["query"] : "";
	$page = isset($_GET["page"]) ? intval($_GET["page"]) : 1;

	// Prevent SQL shenanigans
	$sort_by = "name";
	if (isset($_GET["sort"])) {
		$valid_columns = array("role");
		if (in_array($_GET["sort"],$valid_columns)) {
			$sort_by = $_GET["sort"];
		}
	}
	$sort_dir = (isset($_GET["sort_direction"]) && $_GET["sort_direction"] == "DESC") ? "DESC" : "ASC";

	$pages = $admin->getUsersPageCountForRole($query, $item["id"]);
	$results = $admin->getPageOfUsersForRole($page,"`$sort_by` $sort_dir", $bigtree["commands"][0]);

	foreach ($results as $item) {
?>
<li id="row_<?=$item["id"]?>">
	<section class="view_column users_name"><?=$item["name"]?></section>
    <section class="view_column users_name"></section>
	<section class="view_action">
		<? if ($admin->Level >= $item["level"]) { ?>
		<a href="<?=ADMIN_ROOT?>users/edit/<?=$item["id"]?>/" class="icon_edit"></a>
		<? } else { ?>
		<span class="icon_edit disabled_icon has_tooltip" data-tooltip="<p><strong>Edit Role</strong></p><p>You may not edit this user.</p>"></span>
		<? } ?>
	</section>
</li>
<?
	}
?>
<script>
	BigTree.setPageCount("#view_paging",<?=$pages?>,<?=$page?>);
</script>
