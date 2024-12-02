<?php
    $locs = "../../../../";
    include($locs."modules/header.php");
	$all_vehicles_type = $vehicles_type->_gets_();
	$all_vehicles = $vehicles->_gets_();
?>
<div class="row">
	<div class="col-md-12" id="control_robots_add_well">
		<button class="btn btn-info">
			add a robot
		</button>
	</div>
	<div class="col-md-6" id="new_robots_add_well" style="display:none;">
		<form>
			<div class="row">
				<div class="col-md-12">
					<input class="form-control form-control-user" name="serial" placeholder="Serial number" required/>
				</div>
				<hr>
				<br>
				<br>
				<div class="col-md-6">
					<input class="form-control form-control-user" name="name" placeholder="enter a name" required/>
				</div>
				<div class="col-md-6">
					<select name="vehicles_type_id" class="form-control form-control-user">
					
						<option value="0">
							select type 
						</option>
						<?php
							for($ii=0;$ii<$all_vehicles_type->height();$ii++){
						?>
						<option value="<?php $all_vehicles_type->printi($ii) ;?>">
							<?php $all_vehicles_type->printi($ii,"name") ;?>
						</option>
						<?php
							}
						?>
					</select>
				</div>
				<hr>
				<br>
				<br>
				<div class="col-md-12">
					<button class="btn btn-info" type="submit"> Save </button>
					<button class="btn btn-danger form_canceling" type="reset"> Cancel </button>
				</div>
			</div>
		</form>
		<script>
			$("#new_robots_add_well").find("form").submit( function(bn){
				bn.preventDefault();
				var vars = $(this).serialize();
				$(this).html(LOADING_S_);
				$.post("functions/SOP/php/vehicles/add.php",
				       vars,
					    function(rets){
							$("#new_robots_add_well").find("form").html(rets);
						})
			});
			
			$("#control_robots_add_well").find("button").click( function(evt){
				evt.preventDefault();
				$("#new_robots_add_well").slideDown();
				$("#List_of_robots_well").slideUp();
				$("#control_robots_add_well").slideUp();
			});
			$("#new_robots_add_well").find("form .form_canceling").click( function(evt){
				$("#new_robots_add_well").slideUp();
				$("#List_of_robots_well").slideDown();
				$("#control_robots_add_well").slideDown();
			});
		</script>
	</div>
	<div class="col-md-12" id="List_of_robots_well">
	   <div class="row">
	   <?php
			for($ii=0;$ii<$all_vehicles->height();$ii++){
				?>
				<div class="col-md-3">
				  <div class="card border-left-primary shadow h-100 py-2">
					<div class="card-body">
					  <div class="row no-gutters align-items-center">
						<div class="col mr-2">
						  <div class="text-xs font-weight-bold text-primary text-uppercase mb-1"><?php $all_vehicles->printi($ii,"name") ?></div>
						  <div class="h5 mb-0 font-weight-bold text-gray-800"><?php $all_vehicles->printi($ii,"serial") ?></div>
						  <button  class="btn btn-danger btn-circle btn-sm" id="REMOVELISTROBOTS<?php $all_vehicles->printi($ii) ?>">
							<i class="fas fa-trash"></i>
						  </button>
						  <a href="#" class="btn btn-info btn-circle btn-sm" id="MAPINGREPOSRT<?php $all_vehicles->printi($ii) ?>">
							<i class="fas fa-info-circle"></i>
						  </a>
						</div>
						<div class="col-auto">
						  <i class="fas fa-circle fa-2x text-gray-300"></i>
						</div>
					  </div>
					</div>
				 </div>
			  </div>
			  <script>
				$("#REMOVELISTROBOTS<?php $all_vehicles->printi($ii) ?>").click( function(vb){
					vb.preventDefault();
					$("#new_robots_add_well").find("form").html(LOADING_S_);
					$("#new_robots_add_well").slideDown();
					$("#List_of_robots_well").slideUp();
					$("#control_robots_add_well").slideUp();
					$.post("functions/SOP/php/vehicles/delete.php",
					        "addons=<?php $all_vehicles->printi($ii) ?>",
							function(rets){
								$("#new_robots_add_well").find("form").html(rets);
							})
				})
				$("#MAPINGREPOSRT<?php $all_vehicles->printi($ii) ?>").click( function(vb){
					vb.preventDefault();
					$("#KWBOT_ALL_CONTAINER").html(LOADING_S_)
					$.post("functions/SOP/php/maping.php",
					        "addons=<?php $all_vehicles->printi($ii) ?>",
							function(rets){
								$("#KWBOT_ALL_CONTAINER").html(rets);
							})
				})
			  </script>
				<?php
			}
	   ?>
		  
	   </div>
		
	</div>
</div>