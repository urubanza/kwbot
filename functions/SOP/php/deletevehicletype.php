<?php
    $locs = "../../../";
    include($locs."modules/header.php");
    if(http("data_id")->set()){
        $the_vehicles_type = $vehicles_type->_gets_(http("data_id")->val());
        if($the_vehicles_type->height()==1){
            {
               ?>
                <div class="alert alert-warning alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h4><i class="icon fa fa-ban"></i> Removing a category !</h4>
                    Are you sure you want to remove <?php echo $the_vehicles_type->JS()->name ?> category? once removed cannot be recovered and all activities related to it will lost
                </div>
                <button type="button" class="btn btn-warning Continue_delete_cats" data-dismiss="modal"> Ok </button>
                <button type="button" class="btn btn-info Cancel_delete_cats" data-dismiss="modal"> Cancel </button>
                <script>
                    $(".Continue_delete_cats").click( function(Continue_delete_cats){
                        Continue_delete_cats.preventDefault();
                        $("#DELETE_DEL_NEW_CAT_MODAL").find(".modal-body").html(l());
                        $.post("functions/SOP/php/deletevehicletype.php",
                               "data_id_conts=<?php echo $the_vehicles_type->id() ?>",
                               function(rets){
                            $("#DELETE_DEL_NEW_CAT_MODAL").find(".modal-body").html(rets);
                        });
                    }); 
                    $(".Cancel_delete_cats").click( function(Continue_delete_cats){
                        Continue_delete_cats.preventDefault();
                        $("#KWBOT_ALL_CONTAINER").html(LOADING_S_);
                        $.post("functions/SOP/php/vehicles.php",
                               "active=2",
                               function(active){
                            $("#KWBOT_ALL_CONTAINER").html(active);
                            $("body").removeClass("modal-open");
                            $(".modal-backdrop").hide();
                            $("#DELETE_DEL_NEW_CAT_MODAL_close").click();
                            $("#DELETE_DEL_NEW_CAT_MODAL").modal("hide");
                        });
                    });
                </script>
            <?php 
            }
        } else {
            ?>
            <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-ban"></i> This categorie is not found !</h4>
                The item you are looking is no longer exist in the system
            </div>
            <button type="button" class="btn btn-warning retrying_btn" data-dismiss="modal"> Retry </button>
        <?php
        }
    } 
    else if(http("data_id_conts")->set()){
        if($vehicles_type->exist(http("data_id_conts")->val())){
            if($vehicles_type->delete_(http("data_id_conts")->val())){
                ?>
                <script>
                    $("#KWBOT_ALL_CONTAINER").html(LOADING_S_);
                    $.post("functions/SOP/php/vehicles.php",
                           "active=2",
                           function(active){
                                $("#KWBOT_ALL_CONTAINER").html(active);
                                $("body").removeClass("modal-open");
                                $(".modal-backdrop").hide();
                                $("#DELETE_DEL_NEW_CAT_MODAL_close").click();
                                $("#DELETE_DEL_NEW_CAT_MODAL").modal("hide");
                    });    
                </script>
                <?php
            } else {
               ?>
                <div class="alert alert-danger alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h4><i class="icon fa fa-ban"></i> This categorie not removed !</h4>
                    There was system error while removing the item <?php echo $vehicles_type->message ?>
                </div>
                <button type="button" class="btn btn-warning retrying_btn" data-dismiss="modal"> Retry </button>
            <?php 
            }
        }
        else {
            ?>
            <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-ban"></i> This categorie is not found !</h4>
                The item you are looking is no longer exist in the system
            </div>
            <button type="button" class="btn btn-warning retrying_btn" data-dismiss="modal"> Retry </button>
        <?php
        }
    }
    else {
        ?>
        <div class="alert alert-danger alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h4><i class="icon fa fa-ban"></i> Adding a new Category failed !</h4>
            All fields are required 
        </div>
        <button type="button" class="btn btn-warning retrying_btn" data-dismiss="modal"> Retry </button>
    <?php
    }
?>
<script>
    $(".retrying_btn").off("click").click(function(retrying_btn){
        retrying_btn.preventDefault();
        $("#KWBOT_ALL_CONTAINER").html(LOADING_S_);
        $.post("functions/SOP/php/vehicles.php",
               "active=2",
               function(active){
                    $("#KWBOT_ALL_CONTAINER").html(active);
                    $("body").removeClass("modal-open");
                    $(".modal-backdrop").hide();
                    $("#DELETE_DEL_NEW_CAT_MODAL_close").click();
                    $("#DELETE_DEL_NEW_CAT_MODAL").modal("hide");
        });
    });
</script>