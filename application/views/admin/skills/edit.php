<?php 
defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div id="body">
<h2><?php echo $title; ?></h2>

<p>
	<?php if ($error && !is_array($error)) {
		echo $error;
	}else {
		echo validation_errors();
	}
	?>
</p>

<?php echo $this->routemapping->form_open_multipart(ADMIN_SKILLS, 'edit'); ?>

    <label for="title">Skill Name</label>
    <input type="input" name="name" value="<?php echo $skill['name'];?>"/><br />
    <input type="hidden" name="id" value="<?php echo $skill['id'];?>" />
    <input type="hidden" name="imageName" value="<?php echo $skill['imageName'];?>" />
    <label for="title">Category</label> 
    <select name="categoryId">
    	<?php foreach ($skillCategories as $cat):?>
    		<option value="<?php echo $cat['id']?>" 
    			<?php if ($cat['id'] == $skill['categoryId']) {echo ' selected="selected"';}?>>
    			<?php echo $cat['name']?>
    		</option>
    	<?php endforeach;?>
    </select><br />
    <label>Current Image</label>
    <img src="<?php echo IMAGE_UPLOAD_LOCATION.$skill['imageName']; ?>" alt="Skill Image" style="width:80px;height:80px;" /><br/>
    <label>Change Image</label>
    <input type="file" name="image_filename"/><br />

    <input type="submit" name="submit" value="Done" /> &nbsp;&nbsp; <a href="<?php echo $this->routemapping->site_url(ADMIN_SKILL_CATEGORIES, 'view', $skill['categoryId']); ?>">Cancel</a>

</form>
</div>
