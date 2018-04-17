<?php
include_once "init.php";
include_once "vendor/autoload.php";
$user_id = 1;

if ($_COOKIE['goal_auth'] != 'goalsohs') {
    die("set cookie!");
}

function calc($p, $deadline = "2018-12-31", $start_date = "2018-01-01") {
    $now = time();
    $deadline = strtotime($deadline . " 23:59");
    $start = strtotime($start_date . " 00:00:00");
    $secondspp = ($deadline - $start) / $p;
    return ($now - $start) / $secondspp;
}

function fetch_data($prefix, $ttl = 3600) {
    global $conn, $user_id, $data;

    $q = mysqli_query($conn, "SELECT `value`, last_update FROM cache WHERE user_id = $user_id and `key` = '$prefix'");
    $row = mysqli_fetch_object($q);

    if (!$row || (time() - $row->last_update) >= $ttl || isset($_GET['ignore-cache'])) {
      $data_glob = $data;
      $data = array();

      include_once $prefix."-data.php";

      $data_spec = $data;

      mysqli_query($conn, "DELETE FROM cache WHERE user_id = $user_id AND `key`='$prefix'");
      mysqli_query($conn, "INSERT INTO cache (user_id, `key`, `value`, last_update) VALUES ($user_id, '$prefix', '".bin2hex(serialize($data_spec))."', ".time().")");
      echo mysqli_error($conn);

      $data = array_merge($data_glob, $data_spec);
    }
    else {
      $data_glob = $data;
      $data = array_merge($data_glob, unserialize(hex2bin($row->value)));
    }
}

$data = array();

fetch_data("books", 86400);
fetch_data("rowing");
fetch_data("weight");
fetch_data("cal");
fetch_data("journey");
fetch_data("skills");
?>
<!doctype html>
<html class="no-js" lang="">

<head>
  <meta charset="utf-8">
  <title>Zsolt's dashboard</title>
  <meta name="description" content="">
  <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1, maximum-scale=1">
  <!-- page stylesheets -->
  <!-- end page stylesheets -->
  <!-- build:css({.tmp,app}) styles/app.min.css -->
  <link rel="stylesheet" href="styles/webfont.css">
  <link rel="stylesheet" href="styles/climacons-font.css">
  <link rel="stylesheet" href="vendor/bootstrap/dist/css/bootstrap.css">
  <link rel="stylesheet" href="styles/font-awesome.css">
  <link rel="stylesheet" href="styles/card.css">
  <link rel="stylesheet" href="styles/sli.css">
  <link rel="stylesheet" href="styles/animate.css">
  <link rel="stylesheet" href="styles/app.css">
  <link rel="stylesheet" href="styles/app.skins.css">
  <!-- endbuild -->
</head>

<body class="page-loading">
  <!-- page loading spinner -->
  <div class="pageload">
    <div class="pageload-inner">
      <div class="sk-rotating-plane"></div>
    </div>
  </div>
  <!-- /page loading spinner -->
  <div class="app layout-fixed-header">
    <!-- content panel -->
    <div class="main-panel">
      <!-- main area -->
      <div class="main-content">
        <div class="row">
          <div class="col-md-6 col-lg-4">
            <div class="card card-block no-border bg-primary" style="background-image:url(images/vision-400.jpg);background-size:cover;background-repeat: no-repeat;background-position: center bottom;">
              <div class="card-title text-center">
                <h5 class="m-a-0 text-uppercase" style="padding-bottom: 20px;">Küldetés</h5>
                Olyan eredményes csapat felépítése, amely a kíváló munkán felül másokat is segít céljaik elérésében.
              </div>
              <div style="height:30px"></div>
            </div>
          </div>
          <div class="col-md-6 col-lg-4">
            <div class="row">
              <div class="col-sm-6">
                <div class="card card-block no-border bg-white row-equal align-middle">
                  <div class="column">
                    <h6 class="m-a-0 text-uppercase"><a href="https://www.goodreads.com/challenges/7501-2018-reading-challenge">books</a></h6>
                    <small class="bold text-muted"><?php echo $data['books_read_2018']?>/25</small>
                  </div>
                  <div class="column">
                    <?php $n = $data['books_read_2018'] - floor(calc(25)) ?>
                    <h3 class="m-a-0 text-<?php echo $n >= 0 ? "success" : "danger" ?>"><?php echo $n > 0 ? "+".$n : $n ?></h3>
                  </div>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="card card-block no-border bg-white row-equal align-middle">
                  <div class="column">
                    <h6 class="m-a-0 text-uppercase">journey</h6>
                    <?php $amount = calc(4007500, "2019-03-13", "2018-03-13"); if ($amount < 0) $amount = 0; ?>
                    <small class="bold text-muted"><?php echo round($data['journey_amount'] - $amount) ?> Ft</small>
                  </div>
                  <div class="column">
                    <h3 class="m-a-0 text-<?php echo ($data['journey_amount'] - $amount) >= 0 ? "success" : "danger" ?>"><?php echo round($data['journey_amount']/1000, 1) ?> eFt</h3>
                  </div>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="card card-block no-border bg-white row-equal align-middle">
                  <div class="column">
                    <h6 class="m-a-0 text-uppercase">Calories</h6>
                    <small class="bold text-muted"><?php echo $data['cal_count'] ?></small>
                  </div>
                  <div class="column">
                    <?php $n = $data['cal_count'] - round(calc(3000, date("Y-m-d", strtotime("Sunday this week")), date("Y-m-d", strtotime("Monday this week")))); ?>
                    <h3 class="m-a-0 text-<?php echo $n >= 0 ? "success" : "danger" ?>"><?php echo $n > 0 ? "+".$n : $n ?></h3>
                  </div>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="card card-block no-border bg-white row-equal align-middle">
                  <div class="column">
                    <h6 class="m-a-0 text-uppercase"><a href="https://dashboard.health.nokia.com/12889548/weight/graph">weight</a></h6>
                    <small class="bold text-muted"><?php echo $data['weight_now'] ?> kg</small>
                  </div>
                  <div class="column">
                    <?php $n = round($data['weight_now'] - ($data['weight_goal'] + 8-calc(8)), 1)?>
                    <h3 class="m-a-0 text-<?php echo $n <= 0 ? "success" : "danger" ?>"><?php echo $n > 0 ? "+".$n : $n ?></h3>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-12 col-lg-4">
            <div class="card card-block no-border bg-white">
              <div class="overflow-hidden" style="margin-top:1px;">
                <h4 class="m-a-0">40 <i>(50)</i></h4>
                <h6 class="m-a-0 text-muted">Presentation goal</h6>
              </div>
            </div>
            <div class="card card-block no-border bg-white">
              <div class="overflow-hidden" style="margin-top:1px;">
                <h4 class="m-a-0"><?php echo $data['skills_count'] ?></h4>
                <h6 class="m-a-0 text-muted">New skills</h6>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-4">
            <div class="card no-border bg-dark text-white" id="map" style="min-height:250px">
            <style>
                #map {
                height: 100%;
                width: 100%;
                }
            </style>

            <?php echo $data['rowing_map'] ?>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card text-white no-border relative" style="min-height:250px">
              <div class="slide absolute tp lt rt bt" data-ride="carousel" data-interval="4000">
                <div class="carousel-inner" role="listbox">
                  <div class="item active" style="background-image:url(images/danube1-400.jpg);background-size:cover;background-repeat: no-repeat;background-position: 50% 50%;width:100%;height:100%;">
                  </div>
                  <div class="item" style="background-image:url(images/danube2-400.jpg);background-size:cover;background-repeat: no-repeat;background-position: 50% 50%;width:100%;height:100%;">
                  </div>
                  <div class="item" style="background-image:url(images/danube3-400.jpg);background-size:cover;background-repeat: no-repeat;background-position: 50% 50%;width:100%;height:100%;">
                  </div>
                </div>
              </div>
              <div class="absolute tp lt rt bt" style="background:rgba(0,0,0,.1)"></div>
              <div class="card-block">
                <div class="block text-right">
                  <i class="icon-action-redo"></i>
                </div>
                <div class="absolute lt rt bt p-a">
                  <h4>Danube <br><?php echo round($data['rowing_all_2018']/1000, 2) ?> kms from source</h4>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card card-block no-border bg-white">
              <div class="overflow-hidden" style="margin-top:1px;">
                <?php $n = $data['rowing_all_2018'] - floor(calc($data['rowing_goal'], "2020-12-31")) ?>
                <h4 class="m-a-0 text-<?php echo $n >= 0 ? "success" : "danger" ?>"><?php echo $data['rowing_all_2018'] ?> m (<?php echo $n > 0 ? "+".$n : $n ?> m)</h4>
                <h6 class="m-a-0 text-muted">Danube challenge</h6>
              </div>
            </div>
            <div class="card card-block no-border bg-white">
              <div class="overflow-hidden" style="margin-top:1px;">
                <h4 class="m-a-0"><?php echo round(($data['rowing_all_2018'] + $data['rowing_before_2018'])/1000)?> kms</h4>
                <h6 class="m-a-0 text-muted"><a href="https://log.concept2.com/log">Lifetime <?php
                $left = 1000000-$data['rowing_all_2018'] - $data['rowing_before_2018'];
                $t = time() - strtotime("2018-01-01");
                $to = $t / $data['rowing_all_2018'];
                $needed = $left*$to;
                echo "(".date("d/M/y", time() + $needed). " to join MMC)";
                ?></a></h6>
              </div>
            </div>
            <div class="card card-block no-border bg-white">
              <div class="overflow-hidden" style="margin-top:1px;">
                <h4 class="m-a-0"><?php echo $data['rowing_max'] ?> m</h4>
                <h6 class="m-a-0 text-muted">Longest 2018</h6>
              </div>
            </div>
          </div>
        </div>
        <div class="row same-height-cards">
          <div class="col-md-4">
            <div class="card bg-primary text-white" style="background-image:url(images/stability-400.jpg);background-size:cover;background-repeat: no-repeat;background-position: center bottom;">
              <div class="p-x p-b">
                <div class="text-center" style="margin:50px 0;">
                  <h2 class="m-a-0 text-uppercase">Stabilitás</h2>
                  <small class="text-uppercase">cég működjön nélkülem 1 hónapig</small>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card bg-primary text-white" style="background-image:url(images/help-others-400.jpg);background-size:cover;background-repeat: no-repeat;">
              <div class="p-x p-b">
                <div class="text-center" style="margin:50px 0;">
                  <h2 class="m-a-0 text-uppercase">Alapítvány</h2>
                  <small class="text-uppercase">aktív részvétel egy alapítvány életében</small>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card bg-primary text-white" style="background-image:url(images/coding-400.jpg);background-size:cover;background-repeat: no-repeat;">
              <div class="p-x p-b">
                <div class="text-center" style="margin:50px 0;">
                  <h2 class="m-a-0 text-uppercase">globális technikai</h2>
                  <small class="text-uppercase">egy másik cég használja a javaslatomat</small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- /chat -->
  </div>
  <!-- build:js({.tmp,app}) scripts/app.min.js -->
  <script src="scripts/helpers/modernizr.js"></script>
  <script src="vendor/jquery/dist/jquery.js"></script>
  <script src="vendor/bootstrap/dist/js/bootstrap.js"></script>
  <script src="vendor/fastclick/lib/fastclick.js"></script>
  <script src="vendor/perfect-scrollbar/js/perfect-scrollbar.jquery.js"></script>
  <script src="scripts/helpers/smartresize.js"></script>
  <script src="scripts/constants.js"></script>
  <script src="scripts/main.js"></script>
  <!-- endbuild -->
  <!-- page scripts -->
  <script src="vendor/flot/jquery.flot.js"></script>
  <script src="vendor/flot/jquery.flot.resize.js"></script>
  <script src="vendor/flot/jquery.flot.categories.js"></script>
  <script src="vendor/flot/jquery.flot.stack.js"></script>
  <script src="vendor/flot/jquery.flot.time.js"></script>
  <script src="vendor/flot/jquery.flot.pie.js"></script>
  <script src="vendor/flot-spline/js/jquery.flot.spline.js"></script>
  <script src="vendor/flot.orderbars/js/jquery.flot.orderBars.js"></script>
  <!-- end page scripts -->
  <!-- initialize page scripts -->
  <script src="scripts/helpers/sameheight.js"></script>
  <script src="scripts/ui/dashboard.js"></script>
  <!-- end initialize page scripts -->
</body>

</html>
