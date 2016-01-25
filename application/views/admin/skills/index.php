<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div id="body">
<h2><?php echo $title; ?></h2>
<div><p><?php if ($success) {echo $success;} ?></p></div>
<div class="left"><a href="<?php echo $this->routemapping->site_url(ADMIN_SKILLS, 'create'); ?>">Add Skill</a></div>

<?php if (count($skillsByCategory) > 0):?>

<?php foreach ($skillsByCategory as $cat): ?>
	
	<p><strong>Skill Category - <?php echo $cat['name'];?></strong></p>
	
	<?php $skills = $cat['skills']->toArray(); if (count($skills) > 0):?>
		<table>
		<?php foreach ($cat['skills'] as $skill): ?>		
			<tr>
			<td><img src="<?php echo IMAGE_UPLOAD_LOCATION.$skill['imageName']; ?>" alt="Skill Image" style="width:150px;height:150px;" /></td>
			<td><?php echo $skill['name']; ?></td>
			<td><a href="<?php echo $this->routemapping->site_url(ADMIN_SKILLS, 'edit', $skill['id']); ?>">| Edit </a></td>
			<td><a href="<?php echo $this->routemapping->site_url(ADMIN_SKILLS, 'delete', $skill['id']); ?>">| Delete </a></td>
			</tr>		
		<?php endforeach; ?>
		</table>
	<?php else : ?>
		<p>No Skills found in this category.</p>
	<?php endif; ?>
<?php endforeach; ?>
</table>
<?php else : ?>
<p>No Skills Found.</p>
<?php endif; ?>
</div>