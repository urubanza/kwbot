<?php
    $locs = "../../../";
    include($locs."modules/header.php");
    if(http("bnm")->set())
        $vehicles_logs ->empty();
    $all_vehicles_logs  = $vehicles_logs->_gets_()->reverse();
?>
              <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-bell fa-fw"></i>
                <!-- Counter - Alerts -->
                <span class="badge badge-danger badge-counter"><?php echo $all_vehicles_logs->height() ?></span>
              </a>
              <!-- Dropdown - Alerts -->
              <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
                <h6 class="dropdown-header">
                  Alerts Center
                </h6>
                <?php
                   $end = 3;
                   if($end>$all_vehicles_logs->height())
                      $end =$all_vehicles_logs->height();
                   for($ii=0;$ii<$end;$ii++){
                       $icons = [
                           '<div class="mr-3">
                                <div class="icon-circle bg-warning">
                                  <i class="fas fa-exclamation-triangle text-white"></i>
                                </div>
                            </div>',
                           '<div class="mr-3">
                                <div class="icon-circle bg-primary">
                                  <i class="fas fa-file-alt text-white"></i>
                                </div>
                            </div>'
                       ];
                       
                       ?>
                        <a class="dropdown-item d-flex align-items-center" href="#">
                            <div>
                                <?php echo $icons[$all_vehicles_logs->JS($ii)->type] ?>
                                <div class="small text-gray-500" 
                                     id="PIP_DATE_INLISTS<?php $all_vehicles_logs->printi($ii); ?>"
                                     date-element="day" 
                                     date-data="<?php $all_vehicles_logs->printi(0,"added") ?>"
                                     date-number=""></div>
                                <span class="font-weight-bold"><?php $all_vehicles_logs->printi($ii,"cont"); ?>
                                </span>
                                <script type="text/javascript">
                                    PIP_DATE("#PIP_DATE_INLISTS<?php $all_vehicles_logs->printi($ii); ?>").ego();
                              </script>
                            </div>
                        </a>
                        <?php
                   }   
                ?>
                <a class="dropdown-item text-center small text-gray-500" href="#" id="CLEAR_ALL_NOTS"> Clear all </a>
              </div>
              <script>
                  $("#CLEAR_ALL_NOTS").click( function(bn){
                      bn.preventDefault();
                      $.post("functions/SOP/php/notify.php",
                             "bnm=12",
                             function(rets){
                          $("#NOTIFYS_ALL_DEVICES_ACTS").html(rets);
                      });
                  });
              </script>