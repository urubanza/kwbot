<?php
    $locs = "../../../";
    include($locs."modules/header.php");
    $all_vehicles_type = $vehicles_type->_gets_();
    $all_robots_type = $robots_type->_gets_();

    $actives = 2;
    if(http("active")->set()){
        $actives = http("active")->val();
        //echo "why this shit are set to $active and i can't find any solution to this problem";
    }
    
    $all_robots_type->script("all_robots_type",1);
    $all_vehicles_type->script("all_vehicles_type",1);
    $all_vehicles_type = $all_vehicles_type->CR();
    $all_robots_type = $all_robots_type->CR();
?>

<style>
    .robots-type-image {
        width:100%;
        height: 200px;
        background-size: cover;
        background-position: center;
    }
    .robots-type-image .robots-type-image-overlay{
        background-color: rgba(23,100,233,.4);
        width: 100%;
        height: 100%;
        color: #fff;
        transition: all .3s;
        overflow: hidden;
        padding: 1em;
    }
    
    .robots-type-image .robots-type-image-overlay:hover{
        background-color: rgba(23,100,233,.7);
    }
    
    .robots-type-image .robots-type-image-overlay .robots-type-image-footer{
        width:100%;
        height: auto;
        position: relative;
        top: 100%;
        display: none;
        transition: all 2s;
    }
    .robots-type-image .robots-type-image-overlay .robots-type-image-footer a{
        color: black;
    }
    .robots-type-image .robots-type-image-overlay:hover .robots-type-image-footer{
        display: block;
        top:20%;
    }
</style>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand" href="#"><i class="fas fa-fw fa-car"></i> Vehicles </a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav">
      <li class="nav-item active catego-tabs-nav" data-id="all_devices">
        <a class="nav-link" href="#">
           <i class="fa fa-asterisk"></i> All <span class="sr-only"></span>
        </a>
      </li>
      <li class="nav-item active catego-tabs-nav" data-id="features">
        <a class="nav-link" href="#">
            <i class="fa fa-book"></i> Features </a>
      </li>
      <li class="nav-item active catego-tabs-nav" data-id="categories">
        <a class="nav-link" href="#">
            <i class="fa fa-bars"></i> Categories </a>
      </li>
    </ul>
  </div>
</nav>
<div class="row">
    <div class="col-md-12 catego-tabs-div<?php if($actives==0) echo " active"; ?>" id="all_devices">
        aaa
    </div>
    <div class="col-md-12 catego-tabs-div <?php if($actives==1) echo " active"; ?>" id="features">
        bbb
    </div>
    <div class="col-md-12 catego-tabs-div <?php if($actives==2) echo " active"; ?>" id="categories">
        <div class="row">
            <div class="col-md-12">
                <br>
                <button class="btn btn-info" data-toggle="modal" data-target="#ADD_NEW_CAT_MODAL"> add new </button>
                <!-- Modal -->
                <div class="modal fade" id="ADD_NEW_CAT_MODAL" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="ADD_NEW_CAT_MODALTIT">Add a new vehicle category </h5>
                        <button type="button" class="close" id="ADD_NEW_CAT_MODAL_close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <div class="modal-body">
                        <form>
                            <label> Name: </label>
                            <input type="text" name="namel" class="form-control" placeholder="category name" required>
                            <label> Description: </label>
                            <textarea class="form-control" name="descrl" required>
                                
                            </textarea>
                            <label> Select a Robot Type: </label>
                            <select name="robots_types" class="form-control" required>
                                <option value="0"> -- Select a robot type -- </option>
                                <?php
                                    
                                    while($all_robots_type->next()){
                                ?>
                                    <option value="<?php $all_robots_type->printi() ?>">
                                        <?php $all_robots_type->printi("name") ?>
                                    </option>
                                <?php
                                    }
                                ?>
                            </select>
                            <label> Choose an icon </label>
                            <br>
                            <input type="file" name="type_icon" required>
                            <br>
                            <hr>
                            <button type="submit" class="btn btn-success"> Save </button>
                        </form>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                      </div>
                    </div>
                    <script>
                        $("#ADD_NEW_CAT_MODAL").find("form").off("submit").on("submit", function(ADD_NEW_CAT_MODAL){
                            ADD_NEW_CAT_MODAL.preventDefault();
                            var allDD = $(this).serialize();
                            var personal_information = new FormData(this);
                            $(this).html(LOADING_S_);
                            $.ajax({
                                 url : "functions/SOP/php/addvehicletype.php",
                                 type : 'POST',
                                 data : personal_information,
                                 processData: false,  // tell jQuery not to process the data
                                 contentType: false,  // tell jQuery not to set contentType
                                 enctype: 'multipart/form-data',
                                 success : function(retsDats) {
                                      $("#ADD_NEW_CAT_MODAL").find("form").html(retsDats);
                                 }
                            });
                        });
                    </script>
                  </div>
                </div>
            </div>
            
        </div>
        <hr>
        <div class="row">
        <?php
            while($all_vehicles_type->next()){
        ?>
            <div class="col-md-4">
                <div class="card border-0 card-body">
                    <figure class="itemside">
                        <figcaption class="text-wrap align-self-center">
                            <div class="robots-type-image"
                                 style="background-image:url('img/uploads/<?php $all_vehicles_type->printi("icon") ?>')">
                                <div class="robots-type-image-overlay">
                                    <h3><?php $all_vehicles_type->printi("name") ?></h3>
                                    <div class="robots-type-image-footer">
                                    <p>
                                        <?php echo pipStr($all_vehicles_type->JS()->description)->sub(0,30) ?><a href="#">...</a>
                                    </p>
                                    <?php
                                        echo template::image(
                                            sys_img($all_robots_type->_gets_($all_vehicles_type->JS()->robots_type_id)->first()->icon),"pip_small_icon"
                                        );
                                    ?>
                                    <small>
                                        <?php 
                                            $all_robots_type->_gets_($all_vehicles_type->JS()->robots_type_id)->first()->printi("name") 
                                        ?>
                                    </small>
                                    <br>
                                    <small>
                                        <small>
                                            <i class="fa fa-calendar"></i>
                                            <?php
                                                echo template::pipDate($all_vehicles_type->JS()->added_date,
                                                                       "VEHICLES_IDS".$all_vehicles_type->id());
                                            ?>
                                        </small>
                                    </small>
                                        <button class="btn btn-warning btn-circle btn-sm">
                                            <i class="fa fa-pie-chart"></i>
                                        </button>
                                        <button class="btn btn-warning btn-circle btn-sm">
                                            <i class="fa fa-car"></i>
                                        </button>
                                        <sup>
                                            <span class="badge badge-danger badge-counter">0</span>
                                        </sup>
                                        <button class="btn btn-warning btn-circle btn-sm">
                                            <i class="fa fa-map"></i>
                                        </button>
                                        <sup>
                                            <span class="badge badge-danger badge-counter">0</span>
                                        </sup>
                                    </div>
                                </div>
                            </div>
                        </figcaption>
                    </figure>
                    <div class="card-footer">
                        <button class="btn btn-danger btn-circle btn-sm REMOVE_CATEGORIES_OFF"
                            id="REMOVE_CATEGORIES_OFF<?php $all_vehicles_type->printi() ?>"
                            data-id="<?php $all_vehicles_type->printi() ?>"
                            data-toggle="modal" 
                            data-target="#DELETE_DEL_NEW_CAT_MODAL">
                            <i class="fas fa-trash"></i>
                        </button>
                        <button class="btn btn-info btn-circle btn-sm EDIT_CATEGORIES_OFF"
                                id="EDIT_CATEGORIES_OFF<?php $all_vehicles_type->printi() ?>"
                                data-id="<?php $all_vehicles_type->printi() ?>"
                                data-toggle="modal" 
                                data-target="#EDIT_DEL_NEW_CAT_MODAL">
                            <i class="fa fa-pencil"></i>
                        </button>
                    </div>
                </div> <!-- card.// -->
                
            </div>
            <script>
                
            </script>
        <?php
            }
        ?>
        </div>
    </div>
</div>
<script>
    $(".catego-tabs-nav").off().click( function(bb){
            bb.preventDefault();
            var ids = $(this).attr("data-id");
            $(".catego-tabs-div").removeClass("active");
            $("#"+ids).addClass("active");
    })
    $(".REMOVE_CATEGORIES_OFF").click( function(REMOVE_CATEGORIES_OFF){
        REMOVE_CATEGORIES_OFF.preventDefault();
        var data_id = $(this).attr("data-id");
        $("#DELETE_DEL_NEW_CAT_MODAL").find(".modal-body").html(l());
        $.post("functions/SOP/php/deletevehicletype.php",
               "data_id="+data_id,
               function(rets){
            $("#DELETE_DEL_NEW_CAT_MODAL").find(".modal-body").html(rets);
        })
    });
    $(".EDIT_CATEGORIES_OFF").off("click").click( function(EDIT_CATEGORIES_OFF){
        EDIT_CATEGORIES_OFF.preventDefault();
        var data_id = $(this).attr("data-id");
        if(all_vehicles_type._gets_(data_id).height()==1){
            $("#continuiing_form").show();
            $("#the_local_error_display").hide();
            $("#the_local_error_display").html("");
            $("#namel").val(all_vehicles_type._gets_(data_id).JS().name);
            $("#descrl").html(all_vehicles_type._gets_(data_id).JS().description);
            $("#robots_types").val(all_vehicles_type._gets_(data_id).JS().robots_type_id);
            $("#data_id_conts").val(all_vehicles_type._gets_(data_id).id());
        } else {
            $("#continuiing_form").hide();
            $("#the_local_error_display").show();
            $("#the_local_error_display").html(all_vehicles_type._gets_(data_id).message);
        }
    });
</script>
<div class="modal fade" id="EDIT_DEL_NEW_CAT_MODAL" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="ADD_NEW_CAT_MODALTIT"> </h5>
        <button type="button" class="close" id="EDIT_DEL_NEW_CAT_MODAL_close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="continuiing_form">
            <label> Name: </label>
            <input type="text" name="namel" id="namel" class="form-control" placeholder="category name" required>
            <label> Description: </label>
            <textarea class="form-control" name="descrl" id="descrl" required>

            </textarea>
            <label> Select a Robot Type: </label>
            <select name="robots_types" class="form-control" id="robots_types" required>
                <option value="0"> -- Select a robot type -- </option>
                <?php
                    $all_robots_type->start();
                    while($all_robots_type->next()){
                ?>
                <option value="<?php $all_robots_type->printi() ?>">
                    <?php $all_robots_type->printi("name") ?>
                </option>
                <?php
                    }
                ?>
            </select>
            <input type="number" id="data_id_conts" name="data_id_conts" style="display:none">
            <br>
            <hr>
            <button type="submit" class="btn btn-success"> Save </button>
        </form>
        <div class="alert alert-danger alert-dismissable" id="the_local_error_display">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h4><i class="icon fa fa-ban"></i> there is a problem with this item</h4>
            this item is not found! may be it is sytem error or was removed by someone 
            <span class="problemRepoter">
                
            </span>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
    <script>
        $("#EDIT_DEL_NEW_CAT_MODAL").find("form").off("submit").on("submit", function(ADD_NEW_CAT_MODAL){
            ADD_NEW_CAT_MODAL.preventDefault();
            var allDD = $(this).serialize();
            var personal_information = new FormData(this);
            $(this).html(LOADING_S_);
            $.ajax({
                 url : "functions/SOP/php/editvehicletype.php",
                 type : 'POST',
                 data : personal_information,
                 processData: false,  // tell jQuery not to process the data
                 contentType: false,  // tell jQuery not to set contentType
                 enctype: 'multipart/form-data',
                 success : function(retsDats) {
                      $("#EDIT_DEL_NEW_CAT_MODAL").find("form").html(retsDats);
                 }
            });
        });
    </script>
  </div>
</div>

<div class="modal fade" id="DELETE_DEL_NEW_CAT_MODAL" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="ADD_NEW_CAT_MODALTIT"> Delete a vehicle type</h5>
        <button type="button" class="close" id="DELETE_DEL_NEW_CAT_MODAL_close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>