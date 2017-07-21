<?php echo "<h1>" . $page->data()->title . "</h1>"; ?>

<br>
<div class="circle smallcircle circleright"><h1>THE PAGE YOU<br/>TRIED TO OPEN</h1></div>
<div class="circle bigcircle"><h1>PAGES YOU CAN ACCESS</h1></div>
<div style="clear:both"></div>
<br>
<h1 style="color:#000">You do not have permission to access this page
<br>Click <a href="<?php echo Config::get('home'); ?>">here</a> to return to the homepage.
</h1>