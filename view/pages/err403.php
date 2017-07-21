<?php echo "<h1>" . $page->data()[Page::COL_TITLE] . "</h1>"; ?>

<br>
<div class="circle small-circle circle-right"><h1>THE PAGE YOU<br/>TRIED TO OPEN</h1></div>
<div class="circle big-circle"><h1>PAGES YOU CAN ACCESS</h1></div>
<div style="clear:both"></div>
<br>
<h1 style="color:#000">You do not have permission to access this page
<br>Click <a href="<?php echo Config::get('home'); ?>">here</a> to return to the homepage.
</h1>