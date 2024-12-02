<?php	
	$locs = "../../../../";
    include($locs."modules/header.php");
	if(http("serial")->set()){
		if($vehicles->_add_(["serial","name","vehicles_type_id"],["serial","name","vehicles_type_id"])){
			?>
			<div class="alert alert-success">
				<h4> a robot added successfully </h4>
				<p> you check it in the list of robots </p>
			</div>
			<button class="btn btn-success btn-user btn-block" type="button" id="BTN_SUCCESS_FARMS">
				   Ok
			</button>
			<?php
		} else {
			?>
			<div class="alert alert-danger">
				<h4> Failed to add a robot </h4>
				<p><?php echo $vehicles->message ?></p>
			</div>
			<button class="btn btn-success btn-user btn-block" type="button" id="BTN_SUCCESS_FARMS">
				   Ok
			</button>
			<?php
		}
	}
?>

<script>
	$("#BTN_SUCCESS_FARMS").click( function(vb){
		vb.preventDefault();
		$("#KWBOT_ALL_CONTAINER")
            .html(LOADING_S_)
            .load("functions/SOP/php/vehicles/multimedia.php");
	})
</script>