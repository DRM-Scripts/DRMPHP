<header id="page-topbar">
    <div class="navbar-header">
        <div class="d-flex">
            <div class="navbar-brand-box">
                <a href="index.php" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="assets/images/logo.png" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="assets/images/logo1.png" alt="" height="17">
                    </span>
                </a>

                <a href="index.php" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="assets/images/logo.png" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="assets/images/logo1.png" alt="" height="19">
                    </span>
                </a>
            </div>

            <button type="button" class="btn btn-sm px-3 font-size-16 header-item waves-effect" id="vertical-menu-btn">
                <i class="fa fa-fw fa-bars"></i>
            </button>

        </div>

        <div class="d-flex">
            <div class="dropdown d-none d-lg-inline-block ms-1">
                <button type="button" class="btn header-item noti-icon waves-effect" data-bs-toggle="fullscreen">
                    <i class="bx bx-fullscreen"></i>
                </button>
            </div>

            <?php
    $Noti = $App->GetNotification("New");
    ?>
            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item noti-icon waves-effect"
                    id="page-header-notifications-dropdown" data-bs-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false">
                    <i class="bx bx-bell <?php
echo count($Noti)>0?"bx-tada":"";
?>"></i>
                    <span class="badge bg-danger rounded-pill"><?php
echo count($Noti)>0?count($Noti):"";
?></span>
                </button>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                    aria-labelledby="page-header-notifications-dropdown">
                    <div class="p-3">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="m-0" key="t-notifications"> Notifications </h6>
                            </div>
                            <div class="col-auto">
                                <!--<a href="#!" class="small" key="t-view-all"> View All</a>-->
                            </div>
                        </div>
                    </div>
                    <div data-simplebar style="max-height: 230px;">
                        <?php 
          if(count($Noti) > 0){
            for($i=0;$i<count($Noti);$i++){
          ?>
                        <a href="javascript: void(0);" class="text-reset notification-item" onclick="SetNotiSeen('<?php
echo $Noti[$i]["ID"];
?>')">
                            <div class="d-flex">
                                <div class="avatar-xs me-3">
                                    <span class="avatar-title bg-primary rounded-circle font-size-16">
                                        <i class="bx bx-cart"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1" key="t-your-order"><?php
echo $Noti[$i]["Title"];
?></h6>
                                    <div class="font-size-12 text-muted">
                                        <p class="mb-1" key="t-grammer"><?php
echo $Noti[$i]["Msg"];
?></p>
                                        <p class="mb-0"><i class="mdi mdi-clock-outline"></i> <span key="t-min-ago"><?php
echo $Noti[$i]["ago"];
?></span></p>
                                    </div>
                                </div>
                            </div>
                        </a>
                        <?                
            }
          }else{
            ?>
                        <a href="javascript: void(0);" class="text-reset notification-item">
                            <div class="d-flex">
                                <div class="avatar-xs me-3">
                                    <span class="avatar-title bg-primary rounded-circle font-size-16">
                                        <i class="bx bx-cart"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1" key="t-your-order">No notification</h6>
                                    <div class="font-size-12 text-muted">
                                        <p class="mb-1" key="t-grammer">No new notifications for now</p>
                                        <p class="mb-0"><i class="mdi mdi-clock-outline"></i> <span key="t-min-ago">0
                                                min ago</span></p>
                                    </div>
                                </div>
                            </div>
                        </a>
                        <?
          }
          ?>
                        <!--
          <div class="p-2 border-top d-grid">
            <a class="btn btn-sm btn-link font-size-14 text-center" href="javascript:void(0)">
              <i class="mdi mdi-arrow-right-circle me-1"></i> <span key="t-view-more">View More..</span> 
            </a>
          </div>
          -->
                    </div>
                </div>





                <div class="dropdown d-inline-block">
                    <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <img class="rounded-circle header-profile-user" src="assets/images/user.jpg"
                            alt="Header Avatar">
                        <span class="d-none d-xl-inline-block ms-1" key="t-henry">Admin</span>
                        <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="change_password.php"><i
                                class="bx bx-user font-size-16 align-middle me-1"></i> <span key="t-profile">Change
                                password</span></a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item text-danger" href="login.php"><i
                                class="bx bx-power-off font-size-16 align-middle me-1 text-danger"></i> <span
                                key="t-logout">Logout</span></a>
                    </div>
                </div>





                <div class="dropdown d-inline-block">
                    <button type="button" class="btn header-item noti-icon right-bar-toggle waves-effect">
                        <i class="bx bxs-moon"></i>
                    </button>
                </div>

            </div>
        </div>
</header>