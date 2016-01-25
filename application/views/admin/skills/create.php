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

<?php echo $this->routemapping->form_open_multipart(ADMIN_SKILLS, 'create'); ?>

    <label for="title">Skill Name</label>
    <input type="input" name="name" /><br />
    <label for="title">Category</label> 
    <select name="categoryId">
    	<option>--Select Category--</option>
    	<?php foreach ($skillCategories as $cat):?>
    		<option value="<?php echo $cat['id']?>"><?php echo $cat['name']?></option>
    	<?php endforeach;?>
    </select><br />
    
    <label>Upload Image</label>
    <input type="file" name="image_filename" /><br />

    <input type="submit" name="submit" value="Add Skill" />&nbsp;&nbsp; <a href="<?php echo $this->routemapping->site_url(ADMIN_SKILLS); ?>">Cancel</a>

</form>
</div>