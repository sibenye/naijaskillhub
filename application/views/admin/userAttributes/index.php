<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div id="body">
<h2><?php echo $title; ?></h2>
<div><p><?php if ($success) {echo $success;} ?></p></div>
<div class="left"><a href="<?php echo $this->routemapping->site_url(ADMIN_USER_ATTRIBUTES, 'create'); ?>">Create Attribute</a></div>
<table>
	<tr><th>Attribute Name</th><th>Date Created</th><th></th><th></th><th></th></tr>
<?php if (count($user_attributes) > 0):?>

<?php foreach ($user_attributes as $user_attribute): ?>

	<tr>
	<td><?php echo $user_attribute['name']; ?></td>
	<td><?php echo $user_attribute['createdDate']; ?></td>
	<td><a href="<?php echo $this->routemapping->site_url(ADMIN_USER_ATTRIBUTES, 'view', $user_attribute['id']); ?>">| View </a></td>
	<td><a href="<?php echo $this->routemapping->site_url(ADMIN_USER_ATTRIBUTES, 'edit', $user_attribute['id']); ?>">| Edit </a></td>
	<td><a href="<?php echo $this->routemapping->site_url(ADMIN_USER_ATTRIBUTES, 'delete', $user_attribute['id']); ?>">| Delete </a></td>
	</tr>

<?php endforeach; ?>
</table>
<?php else : ?>
	<tr><td></td><td></td><td></td></tr>
</table>
<p>No User Attributes Found.</p>
<?php endif; ?>
</div>