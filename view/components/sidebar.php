<?php
$bookingActive = false;

if($page->data()[Page::COL_SLUG] == 'main' || $page->data()[Page::COL_SLUG] == 'detail') {
	$bookingActive = true;
}

?>

<div class="off-canvas position-left reveal-for-large dashboard-off-canvas is-transition-push" id="offCanvas" data-off-canvas="true" aria-hidden="false">
	<ul class="menu vertical">
		<li<?php if($bookingActive){ echo " class='active' "; } ?>>
			<a href="dashboard.php?page=main">
				<i class="fi-results"></i>
				<span>Booking</span>
			</a>
		</li>
		<li<?php if(!$bookingActive){ echo " class='active' "; } ?>>
			<a href="dashboard.php?page=search">
				<i class="fi-plus"></i>
				<span>New</span>
			</a>
		</li>
	</ul>
</div>