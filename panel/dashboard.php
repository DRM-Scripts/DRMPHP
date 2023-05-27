<?php
include "_config.php";
if(!$App->LoggedIn())header('location: login.php');
?>
<!doctype html>
<html lang="en">
  <?php include "_htmlhead.php"?>
  <style>
    #container {
      width: 100%;
    }
    p{
      margin-bottom: 0px;

    }
    .description {
      font-weight: bold;
      width:100%;
    }
    #trafficlight {
      float: right;
      margin-top: 15px;
      width: 50px;
      height: 50px;
      border-radius: 50px;
      background: <?php echo $trafficlight; ?>;
      border: 3px solid #333;
    }
    #details {
      font-size: 0.8em;
    }
    hr {
      border: 0;
      height: 1px;
      background-image: linear-gradient(to right, rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0));
    }
    .big {
      font-size: 1.2em;
    }
    .dark {
    }
  </style>
  <body data-sidebar="dark">
    <div id="layout-wrapper">
      <?php include "_header.php"?>
      <?php include "_sidebar.php"?>

      <div class="main-content">

        <div class="page-content">
          <div class="container-fluid">


            <div class="row">
              <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                  <h4 class="mb-sm-0 font-size-18">Dashboard</h4>

                  <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                      <li class="breadcrumb-item"><a href="javascript: void(0);">Main</a></li>
                      <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                  </div>

                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-lg-3">
                <div class="card">
                  <div class="card-body">
                    <div class"row">
                    <div class="col-12">

                      <?php
                      $Stat = $App->GetStat();
                      $server_check_version = '1.0.4';
                      $start_time = microtime(TRUE);

                      $operating_system = PHP_OS_FAMILY;

                      if ($operating_system === 'Windows') {
                        // Win CPU
                        $wmi = new COM('WinMgmts:\\\\.');
                        $cpus = $wmi->InstancesOf('Win32_Processor');
                        $cpuload = 0;
                        $cpu_count = 0;
                        foreach ($cpus as $key => $cpu) {
                          $cpuload += $cpu->LoadPercentage;
                          $cpu_count++;
                        }
                        // WIN MEM
                        $res = $wmi->ExecQuery('SELECT FreePhysicalMemory,FreeVirtualMemory,TotalSwapSpaceSize,TotalVirtualMemorySize,TotalVisibleMemorySize FROM Win32_OperatingSystem');
                        $mem = $res->ItemIndex(0);
                        $memtotal = round($mem->TotalVisibleMemorySize / 1000000,2);
                        $memavailable = round($mem->FreePhysicalMemory / 1000000,2);
                        $memused = round($memtotal-$memavailable,2);
                        // WIN CONNECTIONS
                        $connections = shell_exec('netstat -nt | findstr :80 | findstr ESTABLISHED | find /C /V ""'); 
                        $totalconnections = shell_exec('netstat -nt | findstr :80 | find /C /V ""');
                      } 
                      else {
                        // Linux CPU
                        $load = sys_getloadavg();
                        $cpuload = $load[0];
                        $cpu_count = shell_exec('nproc');
                        // Linux MEM
                        $free = shell_exec('free');
                        $free = (string)trim($free);
                        $free_arr = explode("\n", $free);
                        $mem = explode(" ", $free_arr[1]);
                        $mem = array_filter($mem, function($value) { return ($value !== null && $value !== false && $value !== ''); }); // removes nulls from array
                        $mem = array_merge($mem); // puts arrays back to [0],[1],[2] after 
                        $memtotal = round($mem[1] / 1000000,2);
                        $memused = round($mem[2] / 1000000,2);
                        $memfree = round($mem[3] / 1000000,2);
                        $memshared = round($mem[4] / 1000000,2);
                        $memcached = round($mem[5] / 1000000,2);
                        $memavailable = round($mem[6] / 1000000,2);
                        // Linux Connections
                        $connections = `netstat -ntu | grep :80 | grep ESTABLISHED | grep -v LISTEN | awk '{print $5}' | cut -d: -f1 | sort | uniq -c | sort -rn | grep -v 127.0.0.1 | wc -l`; 
                        $totalconnections = `netstat -ntu | grep :80 | grep -v LISTEN | awk '{print $5}' | cut -d: -f1 | sort | uniq -c | sort -rn | grep -v 127.0.0.1 | wc -l`; 
                      }

                      //$memusage = round(($memavailable/$memtotal)*100);
                      $memusage = round(($memused/$memtotal)*100);    


                      $phpload = round(memory_get_usage() / 1000000,2);

                      $diskfree = round(disk_free_space(".") / 1000000000);
                      $disktotal = round(disk_total_space(".") / 1000000000);
                      $diskused = round($disktotal - $diskfree);

                      $diskusage = round($diskused/$disktotal*100);

                      if ($memusage > 85 || $cpuload > 85 || $diskusage > 85) {
                        $trafficlight = 'red';
                      } elseif ($memusage > 50 || $cpuload > 50 || $diskusage > 50) {
                        $trafficlight = 'orange';
                      } else {
                        $trafficlight = '#2F2';
                      }

                      $end_time = microtime(TRUE);
                      $time_taken = $end_time - $start_time;
                      $total_time = round($time_taken,4);

                      ?>

                      <div id="container" class="dark">
                        <!--<div id="trafficlight" class="nodark"></div>-->

                        <div class="description">🌡️ RAM Usage: <span class="result" style="float:right"><?php echo $memusage; ?>%</span></div>
                        <div class="description">🖥️ CPU Usage: <span class="result" style="float:right"><?php echo $cpuload; ?>%</span></div>
                        <div class="description">💽 Hard Disk Usage:  <span class="result" style="float:right"><?php echo $diskusage; ?>%</span></div>
                        <div class="description">🖧 Established Connections:  <span class="result" style="float:right"><?php echo $connections; ?></span></div>
                        <div class="description">🖧 Total Connections:  <span class="result" style="float:right"><?php echo $totalconnections; ?></span></div>
                        <hr>
                        <div class="description">🖥️ CPU Threads: <span class="result" style="float:right"><?php echo $cpu_count; ?></span></div>
                        <hr>
                        <div class="description">🌡️ RAM Total: <span class="result" style="float:right"><?php echo $memtotal; ?> GB</span></div>
                        <div class="description">🌡️ RAM Used: <span class="result" style="float:right"><?php echo $memused; ?> GB</span></div>
                        <div class="description">🌡️ RAM Available: <span class="result" style="float:right"><?php echo $memavailable; ?> GB</span></div>
                        <hr>
                        <div class="description">💽 Hard Disk Free: <span class="result" style="float:right"><?php echo $diskfree; ?> GB</span></div>
                        <div class="description">💽 Hard Disk Used: <span class="result" style="float:right"><?php echo $diskused; ?> GB</span></div>
                        <div class="description">💽 Hard Disk Total: <span class="result" style="float:right"><?php echo $disktotal; ?> GB</span></div>
                        <hr>
                        <div id="details">
                          <div class="description">📟 Server Name:  <span class="result" style="float:right"><?php echo $_SERVER['SERVER_NAME']; ?></span></div>
                          <div class="description">💻 Server Addr:  <span class="result" style="float:right"><?php echo $_SERVER['SERVER_ADDR']; ?></span></div>
                          <div class="description">🌀 PHP Version:  <span class="result" style="float:right"><?php echo phpversion(); ?></span></div>
                          <div class="description">🏋️ PHP Load:  <span class="result" style="float:right"><?php echo $phpload; ?> GB</span></div>
                        </div>
                      </div>

                    </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-lg-9">
                <div class="row">
                  <div class="col-md-4">
                    <div class="card mini-stats-wid">
                      <div class="card-body">
                        <div class="d-flex">
                          <div class="flex-grow-1">
                            <p class="text-muted fw-medium">Channels</p>
                            <h4 class="mb-0"><?php
echo $Stat["Total"];
?></h4>
                          </div>

                          <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                              <span class="avatar-title">
                                <i class="bx bx-copy-alt font-size-24"></i>
                              </span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="card mini-stats-wid">
                      <div class="card-body">
                        <div class="d-flex">
                          <div class="flex-grow-1">
                            <p class="text-muted fw-medium">Online</p>
                            <h4 class="mb-0"><?php
echo $Stat["Online"];
?></h4>
                          </div>

                          <div class="flex-shrink-0 align-self-center ">
                            <div class="avatar-sm rounded-circle bg-primary mini-stat-icon">
                              <span class="avatar-title rounded-circle bg-primary">
                                <i class="bx bx-archive-in font-size-24"></i>
                              </span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="card mini-stats-wid">
                      <div class="card-body">
                        <div class="d-flex">
                          <div class="flex-grow-1">
                            <p class="text-muted fw-medium">Offline</p>
                            <h4 class="mb-0"><?php
echo $Stat["Offline"];
?></h4>
                          </div>

                          <div class="flex-shrink-0 align-self-center">
                            <div class="avatar-sm rounded-circle bg-primary mini-stat-icon">
                              <span class="avatar-title rounded-circle bg-primary">
                                <i class="bx bx-purchase-tag-alt font-size-24"></i>
                              </span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>                

                <div class="card">
                  <div class="card-body">
                    <div class="d-sm-flex flex-wrap">
                      <h4 class="card-title mb-4">Uptime</h4>
                    </div>

                    <div id="stacked-column-chart" class="apex-charts" dir="ltr"></div>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>

        <?php include "_footer.php"?>
      </div>

    </div>
    <?php include "_rightbar.php"?>

    <div class="rightbar-overlay"></div>

    <script src="assets/libs/jquery/jquery.min.js"></script>
    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/libs/metismenu/metisMenu.min.js"></script>
    <script src="assets/libs/simplebar/simplebar.min.js"></script>
    <script src="assets/libs/node-waves/waves.min.js"></script>
    <script src="assets/libs/apexcharts/apexcharts.min.js"></script>
    <script src="assets/js/app.js"></script>
    <script>
      var options = {
        chart: {
          height: 280,
          type: "bar",
          stacked: !0,
          toolbar: {
            show: !1
          },
          zoom: {
            enabled: !0
          }
        },
        plotOptions: {
          bar: {
            horizontal: !1,
            columnWidth: "15%",
            endingShape: "rounded"
          }
        },
        dataLabels: {
          enabled: !1
        },
        series: [{
          name: "Series A",
          data: [<?php
echo $Stat["Uptime"];
?>]
        }],
        xaxis: {
          categories: [<?php
echo $Stat["Names"];
?>]
        },
        colors: ["#556ee6"],
        legend: {
          position: "bottom"
        },
        fill: {
          opacity: 1
        }
      },
      chart = new ApexCharts(document.querySelector("#stacked-column-chart"), options);
      chart.render();    
    </script>
  </body>

</html>