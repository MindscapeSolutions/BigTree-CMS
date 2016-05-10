<?
	// We set this header so that when the user reloads the page form element changes don't stick (since we're only tracking explicit changes back to the JSON objects for Alerts and Permissions)
	header("Cache-Control: no-store");
	$role = $admin->getRole(end($bigtree["commands"]));

	// Stop if this is a 404 or the user is editing someone higher than them.
	if (!$role) {
?>
<div class="container">
	<section>
		<h3>Error</h3>
		<p>The role you are trying to edit no longer exists.</p>
	</section>
</div>
<?
		$admin->stop();
	}

?>
<div class="container">
	<form class="module" action="<?=ADMIN_ROOT?>roles/update/" method="post">
		<input type="hidden" name="id" value="<?=$role["id"]?>" />
		<section>
			<p class="error_message"<? if (!$error) { ?> style="display: none;"<? } ?>>Errors found! Please fix the highlighted fields before submitting.</p>
			<div class="left">
				<fieldset<? if ($error == "role") { ?> class="form_error"<? } ?> style="position: relative;">
					<label class="required">Role Name <? if ($error == "role") { ?><span class="form_error_reason">Already In Use By Another Role</span><? } ?></label>
					<input type="text" class="required role" name="role" autocomplete="off" value="<?=htmlspecialchars($role["role"])?>" tabindex="1" />
				</fieldset>
			</div>
		</section>
		<footer>
			<input id="edit_role_submit" type="submit" class="blue" value="Update" />
		</footer>
	</form>
</div>

<script>
	BigTreeFormValidator("form.module");
	
	$("form.module").submit(function(ev) {
	});
</script>
