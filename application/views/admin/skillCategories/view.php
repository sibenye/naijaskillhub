<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div id="body">
<h2><?php echo $title; ?></h2>
<div><p><?php if ($success) {echo $success;} ?></p></div>
<div class="left"><a href="<?php echo $this->routemapping->site_url(ADMIN_SKILLS, 'create'); ?>">Add Skill</a></div>
<table>
	<tr><th></th><th>Skill Name</th><th></th><th></th><th></th></tr>
<?php if (count($skills) > 0):?>

<?php foreach ($skills as $skill): ?>

	<tr>
	<td><img src="<?php echo IMAGE_UPLOAD_LOCATION.$skill['imageName']; ?>" alt="Skill Image" style="width:150px;height:150px;" /></td>
	<td><?php echo $skill['name']; ?></td>
	<td><a href="<?php echo $this->routemapping->site_url(ADMIN_SKILLS, 'edit', $skill['id']); ?>">| Edit </a></td>
	<td><a href="<?php echo $this->routemapping->site_url(ADMIN_SKILLS, 'delete', $skill['id']); ?>">| Delete </a></td>
	</tr>

<?php endforeach; ?>
</table>
<?php else : ?>
	<tr><td></td><td></td><td></td></tr>
</table>
<p>No Skills in this Category.</p>
<?php endif; ?>
</div>