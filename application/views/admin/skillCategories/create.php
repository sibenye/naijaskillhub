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

<?php echo $this->routemapping->form_open(ADMIN_SKILL_CATEGORIES, 'create'); ?>

    <label for="title">Category Name</label>
    <input type="input" name="name" /><br />

    <input type="submit" name="submit" value="Create Category" />&nbsp;&nbsp; <a href="<?php echo $this->routemapping->site_url(ADMIN_SKILL_CATEGORIES); ?>">Cancel</a>

</form>
</div>