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

<?php echo $this->routemapping->form_open(ADMIN_SKILL_CATEGORIES, 'edit'); ?>

    <label for="title">Category Name</label>
    <input type="input" name="name" value="<?php echo $skillCategory['name'];?>"/><br />
    <input type="hidden" name="id" value="<?php echo $skillCategory['id'];?>" />

    <input type="submit" name="submit" value="Done" /> &nbsp;&nbsp; <a href="<?php echo $this->routemapping->site_url(ADMIN_SKILL_CATEGORIES); ?>">Cancel</a>

</form>
</div>
