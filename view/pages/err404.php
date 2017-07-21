<?php echo "<h1>" . $page->data()[Page::COL_TITLE] . "</h1>"; ?>

<br>
<div class="circle small-circle circle-right"><h1>THE PAGE YOU<br/>TRIED TO OPEN</h1></div>
<div class="circle big-circle"><h1>PAGES THAT EXIST</h1></div>
<div style="clear:both"></div>
<br>
<h1 style="color:#000">It looks like the page you are looking for doesn't exist.
<br>Please try again or return to the <a href="<?php echo Config::get('home'); ?>">homepage</a>.
</h1>