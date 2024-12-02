<?php
    $locs = "../../../";
    include($locs."modules/header.php");
    if(http("namel")->set()){
        if($vehicles_type->exist(http("data_id_conts")->val())){
            if($vehicles_type->editM(["name","description","robots_type_id"],
                                     [http("namel")->val(),
                                      http("descrl")->val(),
                                      http("robots_types")->val()],
                                      http("data_id_conts")->val())){
                ?>
                <script>
                    $("#KWBOT_ALL_CONTAINER").html(LOADING_S_);
                    $.post("functions/SOP/php/vehicles.php",
                           "active=2",
                           function(active){
                                $("#KWBOT_ALL_CONTAINER").html(active);
                                $("body").removeClass("modal-open");
                                $(".modal-backdrop").hide();
                                $("#EDIT_DEL_NEW_CAT_MODAL_close").click();
                                $("#EDIT_DEL_NEW_CAT_MODAL").modal("hide");
                    });
                </script>
                <?php
            } else {
               ?>
                <div class="alert alert-danger alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h4><i class="icon fa fa-ban"></i> the category not edited ! </h4>
                    Sorry there was a system error : <?php echo $vehicles_type->message ?>
                </div>
                <button type="button" class="btn btn-warning retrying_btn" data-dismiss="modal"> Retry </button>
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
    } else {
        ?>
            <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-ban"></i> Editing failed !</h4>
                All input must be provided
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
                    $("#EDIT_DEL_NEW_CAT_MODAL_close").click();
                    $("#EDIT_DEL_NEW_CAT_MODAL").modal("hide");
        });
    });
</script>