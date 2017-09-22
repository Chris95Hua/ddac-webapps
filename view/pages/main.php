<!-- Content start -->
<div id="content">

	<?php require_once('/view/components/sidebar.php'); ?>

	<div class="off-canvas-content" data-off-canvas-content="true" style="height: 100%; overflow-y: auto;">
		<div class="row" style="padding-top: 15px">

			<div class="small-12 columns">
				<div class="callout">
					<h4 class="float-left">
						<i class="fi-info"></i>
						 Booking History
					</h4>
					<a href="dashboard.php?page=search" class="button float-right">
						<i class="fi-plus"></i>
						 New
					</a>
                    
                    <div id="content-load">
                    <!-- Content fetch from resource/Shipment -->
                    </div>

                    <div class='pagination centralise'>
                        <div id="page-selection"></div>
                    </div>
				</div>
			</div>

		</div>
	</div>

</div>
<!-- Content end -->

<!-- JQuery -->
<script src="js/jquery.min.js"></script>
<!-- Foundation -->
<script src="js/foundation.min.js"></script>
<script src="js/app.js"></script>
<script src="js/jquery.bootpag.min.js"></script>
<script>
    $(document).ready(function() {
            getContent(1);
    });

    $('#page-selection').bootpag({
        total: <?php echo ceil($booking->getUserBookingCount($_SESSION['ID']) / Config::get('max_booking_display')); ?>,
        page: 1,
        maxVisible: <?php echo Config::get('max_booking_display'); ?>,
        leaps: false,
        first: '&laquo;',
        last: '&raquo;',
        activeClass: 'current',
        nextClass: 'arrow',
        prevClass: 'arrow'
    }).on("page", function(event, num){
        loader();
        getContent(num);
    });

    function getContent(num) {
        $.ajax({
          method: "GET",
          url: "REST/booking.php",
          data: {view:num}
        })
        .done(function(data) {
            $("#content-load").html( data );
        })
        .fail(function(error) {
            alert("Failed to fetch content");
        });
    }

    function loader() {
        $('#content-load').html('<tr><td class="col-xs-2"></td><td class="col-xs-8" style="padding:0 20px"><img id="loader" src="images/loader.gif" style="display: block;margin: 40px auto 40px auto;"/></td><td class="col-xs-2"></td></tr>');
    }
</script>