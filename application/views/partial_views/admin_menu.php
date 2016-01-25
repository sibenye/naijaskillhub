<?php

defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div id="nav_header">
<h1 class="fl_left">Naija Skill Hub - Admin Dashboard</h1>
<nav id="mainav" class="fl_right">	
      <ul class="clear">
        <li class="active"><a href="<?php echo $this->routemapping->site_url(ADMIN_USER_ATTRIBUTES); ?>">Attributes</a></li>
        <li><a href="<?php echo $this->routemapping->site_url(ADMIN_SKILL_CATEGORIES); ?>">Categories</a></li>
        <li><a href="<?php echo $this->routemapping->site_url(ADMIN_SKILLS); ?>">Skills</a></li>
      </ul>
    </nav>
</div>
<div class="clear"></div>