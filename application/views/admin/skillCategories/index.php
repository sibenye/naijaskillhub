<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div id="body">
<h2><?php echo $title; ?></h2>
<div><p><?php if ($success) {echo $success;} ?></p></div>
<div class="left"><a href="<?php echo $this->routemapping->site_url(ADMIN_SKILL_CATEGORIES, 'create'); ?>">Create Category</a></div>
<table>
	<tr><th>Category Name</th><th></th><th></th><th></th></tr>
<?php if (count($skillCategories) > 0):?>

<?php foreach ($skillCategories as $skillCategory): ?>

	<tr>
	<td><?php echo $skillCategory['name']; ?></td>
	<td><a href="<?php echo $this->routemapping->site_url(ADMIN_SKILL_CATEGORIES, 'view', $skillCategory['id']); ?>">| View </a></td>
	<td><a href="<?php echo $this->routemapping->site_url(ADMIN_SKILL_CATEGORIES, 'edit', $skillCategory['id']); ?>">| Edit </a></td>
	<td><a href="<?php echo $this->routemapping->site_url(ADMIN_SKILL_CATEGORIES, 'delete', $skillCategory['id']); ?>">| Delete </a></td>
	</tr>

<?php endforeach; ?>
</table>
<?php else : ?>
	<tr><td></td><td></td><td></td></tr>
</table>
<p>No Skill Categories Found.</p>
<?php endif; ?>
</div>