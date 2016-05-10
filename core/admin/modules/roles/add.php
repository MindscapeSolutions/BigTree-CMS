<?	
	$error = "";
	if (isset($_SESSION["bigtree_admin"]["create_role"])) {
		BigTree::globalizeArray($_SESSION["bigtree_admin"]["create_role"],array("htmlspecialchars"));
		unset($_SESSION["bigtree_admin"]["create_role"]);
	} else {
		$role = "";
	}
?>
<div class="container">
	<form class="module" action="<?=ADMIN_ROOT?>roles/create/" method="post">	
		<section>
			<p class="error_message"<? if (!$error) { ?> style="display: none;"<? } ?>>Errors found! Please fix the highlighted fields before submitting.</p>
			<div class="left">
				<fieldset<? if ($error == "role") { ?> class="form_error"<? } ?> style="position: relative;">
					<label class="required">Role Name <? if ($error == "role") { ?><span class="form_error_reason">Already In Use</span><? } ?></label>
					<input type="text" class="required role" name="role" autocomplete="off" value="<?=$role?>" tabindex="1" />
				</fieldset>
			</div>
		</section>
		<footer>
			<input type="submit" class="blue" value="Create" />
		</footer>
	</form>
</div>
<script>
	BigTreeFormValidator("form.module");
</script>
