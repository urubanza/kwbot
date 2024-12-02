<div class="row">

</div>
<div class="row LIST_OF_RECENT_PATH">
<?php
    $locs = "../../../";
    include($locs."modules/header.php");
    $all_path = $path->last(5);
    for($ii = 0; $ii<$all_path->height();$ii++){
        ?>
        <div class="col-md-4">
            <div class="recent-path-list" style="padding-bottom:10px">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted card-subtitle mb-2"><?php $all_path->printi($ii,"path_name")?></h6>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-danger btn-sm THE_REMOVER_BTN pull-right" data-id="<?php $all_path->printi($ii)?>">
                            <i class="fa fa-trash"></i>
                        </button>
                        <button class="btn btn-info btn-sm THE_PLAYER_BTN pull-right" data-id="<?php $all_path->printi($ii)?>">
                            <i class="fa fa-play"></i>
                        </button>
                        <button class="btn btn-success btn-sm THE_MAP_BTN pull-right" data-id="<?php $all_path->printi($ii)?>">
                            <i class="fa fa-map"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    ?>
    <div class="col-md-4">
            <div class="recent-path-list" style="padding-bottom:10px">
                <a href="#">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="text-muted card-subtitle mb-2"> All </h6>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-danger btn-sm THE_REMOVER_BTN pull-right" data-id="<?php $all_path->printi($ii)?>">
                                <?php echo $path->counts(); ?>
                            </button>
                        </div>
                    </div>
                </a>
            </div>
        </div>
</div>
<script>
    $(".LIST_OF_RECENT_PATH .THE_PLAYER_BTN").off("click").click( function(THE_PLAYER_BTN){
        THE_PLAYER_BTN.preventDefault();
        socket.emit('playPathsQuik',$(this).attr("data-id"));
    })
</script>