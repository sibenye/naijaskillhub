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

<?php echo $this->routemapping->form_open(ADMIN_USER_ATTRIBUTES, 'edit'); ?>

    <label for="title">Attribute Name</label>
    <input type="input" name="name" value="<?php echo $user_attribute['name'];?>"/><br />
    <input type="hidden" name="id" value="<?php echo $user_attribute['id'];?>" />

    <input type="submit" name="submit" value="Done" /> &nbsp;&nbsp; <a href="<?php echo $this->routemapping->site_url(ADMIN_USER_ATTRIBUTES); ?>">Cancel</a>

</form>
</div>
