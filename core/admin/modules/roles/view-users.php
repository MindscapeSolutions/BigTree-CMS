<?
$role = BigTreeAdmin::getRole($bigtree["commands"][0]);
?>

<div class="table">
    <h3>Users in Role: <?= $role["role"] ?></h3>
	<summary>
		<input type="search" name="query" id="query" placeholder="Search" class="form_search" autocomplete="off" />
		<span class="form_search_icon"></span>
		<nav id="view_paging" class="view_paging"></nav>
	</summary>
	<header>
		<span class="view_column users_name"><a class="sort_column asc" href="ASC" name="name">Name <em>&#9650;</em></a></span>
		<span class="view_column users_name"></span>
		<span class="view_action" style="width: 80px;">Actions</span>
	</header>
	<ul id="results">
		<? include BigTree::path("admin/ajax/roles/get-users.php") ?>	
	</ul>
</div>
