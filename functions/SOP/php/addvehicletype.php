<?php
    $locs = "../../../";
    include($locs."modules/header.php");
    if(http("namel")->set()){
        $new_icon = image("type_icon");
        if($new_icon->upload($locs."img/uploads/",90000000,2000,1333)){
            if($new_cats = $vehicles_type->_add_(["name","description","icon","cover","robots_type_id"],
                                                 [http("namel")->val(),http("descrl")->val(),
                                                $new_icon->newName,"",http("robots_types")->val()])){
                ?>
                <div class="alert alert-info alert-dismissable">
                    <h4><i class="icon fa fa-ban"></i> Adding a new Category was success</h4>
                    This category now can be used for classifying robots
                </div>
                <button type="button" class="btn btn-info retrying_btn" data-dismiss="modal"> O.k </button>
            <?php
                
            }
            else {
                ?>
                <div class="alert alert-warning alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h4><i class="icon fa fa-ban"></i> Adding a new Category failed!</h4>
                    Sorry something gone Wrong with the main database <?php echo $vehicles_type->message ?>
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
                    Sorry something gone Wrong with the Server <?php echo $new_prof->error ?>
                </div>
                <button type="button" class="btn btn-warning retrying_btn" data-dismiss="modal"> Retry </button>
            <?php
        }
    } else {
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
    $(".retrying_btn").off("click").click( function(retrying_btn){
        retrying_btn.preventDefault();
        $("#KWBOT_ALL_CONTAINER").html(LOADING_S_);
        $.post("functions/SOP/php/vehicles.php",
               "active=2",
               function(active){
            $("#KWBOT_ALL_CONTAINER").html(active);
            $("#ADD_NEW_CAT_MODAL_close").click();
            $("#ADD_NEW_CAT_MODAL").modal("hide");
            $("body").removeClass("modal-open");
            $(".modal-backdrop").hide();
        })
    })
</script>