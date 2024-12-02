<?php	
	$locs = "../../../../";
    include($locs."modules/header.php");
	if(http("addons")->set()){
		$thevehicles = $vehicles->_gets_(http("addons")->val());
		if($thevehicles->height()){
			?>
			<div class="alert alert-warning">
				<h4> are you sure you want to delete the robot (<?php $thevehicles->printi("name") ?>) with serial number(<?php $thevehicles->printi("serial") ?>)</h4>
			</div>
			<button class="btn btn-danger btn-user btn-block" type="button" id="BTN_SUCCESS_FARMS_DELETE_CONTS">
				   Ok
			</button>
			<button class="btn btn-success btn-user btn-block" type="button" id="BTN_SUCCESS_FARMS">
				   Cancel
			</button>
			<script>
				$("#BTN_SUCCESS_FARMS_DELETE_CONTS").click( function(nm){
					nm.preventDefault();
					$("#new_robots_add_well").find("form").html(LOADING_S_);
					$.post("functions/SOP/php/vehicles/delete.php",
					"addonsconts=<?php $thevehicles->printi() ?>",
					function(rets){
						$("#new_robots_add_well").find("form").html(rets);
					})
				})
			</script>
			<?php
		} else {
			?>
			<div class="alert alert-success">
				<h4> the robot you are looking for was removed or have same issues with the system </h4>
			</div>
			<button class="btn btn-success btn-user btn-block" type="button" id="BTN_SUCCESS_FARMS">
				   Ok
			</button>
			<?php
		}
	} else if(http("addonsconts")->set()){
		$vehicles->delete_(http("addonsconts")->val());
		?>
		<script>
			
				$("#KWBOT_ALL_CONTAINER")
					.html(LOADING_S_)
					.load("functions/SOP/php/vehicles/multimedia.php");
		</script>
		<?php
	}?>
	
	<script>
		$("#BTN_SUCCESS_FARMS").click( function(vb){
			vb.preventDefault();
			$("#KWBOT_ALL_CONTAINER")
				.html(LOADING_S_)
				.load("functions/SOP/php/vehicles/multimedia.php");
		})
	</script>