<div class="row">
	<div class="col-md-12">
<?php
    $locs = "../../../";
    include($locs."modules/header.php");
	if(http("addons")->set()){
		$thevehicles = $vehicles->_gets_(http("addons")->val());
		if($thevehicles->height()){
			?>
			
			<?php
		} else {
			?>
			<div class="alert alert-warning">
				<h4> the robot you are looking for is no longer exist in the system </h4>
			</div>
			<button class="btn btn-danger btn-user btn-block" type="button" id="BTN_SUCCESS_FARMS">
				   Ok
			</button>
			<script>
				$("#BTN_SUCCESS_FARMS").click( function(vb){
					vb.preventDefault();
					$("#KWBOT_ALL_CONTAINER")
						.html(LOADING_S_)
						.load("functions/SOP/php/vehicles/multimedia.php");
				})
			</script>
			<?php
		}
	} else {
		?>
			<div class="alert alert-warning">
				<h4> all information must be provided  </h4>
			</div>
			<button class="btn btn-danger btn-user btn-block" type="button" id="BTN_SUCCESS_FARMS">
				   Ok
			</button>
			<script>
				$("#BTN_SUCCESS_FARMS").click( function(vb){
					vb.preventDefault();
					$("#KWBOT_ALL_CONTAINER")
						.html(LOADING_S_)
						.load("functions/SOP/php/vehicles/multimedia.php");
				})
			</script>
		<?php
	}
?>
	</div>
</div>