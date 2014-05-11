<!DOCTYPE html>
<html>
    <head>
        <title>Yana Temperature</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="js/libs/twitter-bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <link href="sb-admin/sb-admin.css" rel="stylesheet">
        <link href="js/libs/responsive-calendar/responsive-calendar.css" rel="stylesheet">
        <link href="font-awesome/css/font-awesome.css" rel="stylesheet">
    </head>
    <body>
        <?php
//	    $url=$_SERVER['REQUEST_URI'];
//	    header("Refresh: 60; URL=$url"); 
            setlocale (LC_ALL, 'fr_FR','fra'); 
            $sourceDB = new SQLite3('temp.db');
            if ($_GET['date'] != '') {
                $today = $_GET['date'];
                $date = strtotime ('1 day', strtotime($today));
                $date = date ('Y-m-d' , $date);
            } else {
                $today = "2014-05-03";
                //$today = date("Y-m-d");
                $date = strtotime ('1 day', strtotime($today));
                $date = date ('Y-m-d' , $date);
            }
            $startDate = strtotime ('-6 days', strtotime($date));
            $startDate = date ('Y-m-d' , $startDate);
            $sql = 'SELECT Mesures.Id, Mesures.Mesure, Mesures.DateMesure, Emetteurs.Localisation';
            $sql .= ' FROM Mesures, Emetteurs';
            $sql .= ' WHERE DateMesure > "'.$startDate.'"';
            $sql .= ' AND DateMesure < "'.$date.'"';
            $sql .= ' AND Mesures.Id = Emetteurs.Id';
            $sql .= ' ORDER BY DateMesure;';
            $statement = $sourceDB->prepare($sql);
            $results = $statement->execute();
            $dataArray = array();
            $emetteurArray = array();
            while ($row = $results->fetchArray()) {
                $curTimestamp = 1000*strtotime($row['DateMesure']);
                if (!isset($emetteurArray[$row['Id']])) {
                    $emetteurArray[$row['Id']] = [$row['Localisation'], $curTimestamp, $row['Mesure']];
                }
                $dataArray[$row['Id']] .= '['.$curTimestamp.','.str_replace(",",".",$row['Mesure']).'],';
                if ($curTimestamp > $emetteurArray[$row['Id']][1]) {
                    $emetteurArray[$row['Id']][1] = $curTimestamp;
                    $emetteurArray[$row['Id']][2] = $row['Mesure'];
                }
            }
            foreach ($dataArray as $emetteur => $data) {
                $dataArray[$emetteur] = '[ '.substr($data, 0, strlen($data) - 1).' ]';
            }

        ?>
        <div id="wrapper">
            <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0;z-index:1">
                <div class="navbar-header">
                    <a class="navbar-brand" href="index.php">Yana Temperature</a>
                </div>
            </nav>
            <nav class="navbar-default navbar-static-side" role="navigation">
                <div class="sidebar-collapse">
                    <ul class="nav" id="side-menu">
                        <li id="searchCalendar">                        
                            <div class="responsive-calendar" style='width: 200px;margin-left:25px'>
                                <div class="controls">
                                    <a class="pull-left" data-go="prev"><i class="fa fa-arrow-circle-left fa-fw"></i></a>
                                    <h4><span data-head-year></span> <span data-head-month></span></h4>
                                    <a class="pull-right" data-go="next"><i class="fa fa-arrow-circle-right fa-fw"></i></a>
                                </div>
                                <div class="day-headers">
                                  <div class="day header">L</div>
                                  <div class="day header">M</div>
                                  <div class="day header">M</div>
                                  <div class="day header">J</div>
                                  <div class="day header">V</div>
                                  <div class="day header">S</div>
                                  <div class="day header">D</div>
                                </div>
                                <div class="days" data-group="days"></div>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
            <div id="page-wrapper">
                <div class="row"><div class="col-lg-12" style="height: 10px"></div></div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <i class="fa fa-clock-o fa-fw"></i> Températures
                            </div>
                            <div class="panel-body">
                                <div id="graphRecord" style="height: 300px"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <i class="fa fa-clock-o fa-fw"></i> Ephémeride
                            </div>
                            <div class="panel-body" style='text-align:center'>
                                <?php
                                $sql = "SELECT * FROM Ephemeride WHERE Date='".$today."'";
                                $statement = $sourceDB->prepare($sql);
                                $results = $statement->execute();
                                while($row = $results->fetchArray()){
                                    echo "<h2>".$row['Fete']."</h2>";
                                    echo "<br/>";
                                    echo "<img src='images/soleil.jpg' width=30 height=30/>   ".$row['SolLever']." / ".$row['SolCoucher'];
                                    echo "<br/>";
                                    echo "<br/>";
                                    echo "<img src='images/lune.jpg' width=30 height=30/>    ".$row['LuneLever']." / ".$row['LuneCoucher'];
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <?php
                    foreach ($emetteurArray as $emetteur => $data) {
                    ?>
                        <div class="col-lg-4">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <i class="fa fa-clock-o fa-fw"></i> <?php echo $data[0] ?>
                                </div>
                                <div class="panel-body" style="text-align:center">
                                    <h2><?php echo $emetteurArray[$emetteur][2] ?>°C</h2>
                                    <?php echo strftime("%d %B %Y", $emetteurArray[$emetteur][1]/1000)." ".strftime("%Hh%M", $emetteurArray[$emetteur][1]/1000) ?>
                                    <br/>
                                   
                                </div>
                            </div>
                        </div>
                    <?php
                    }
                    ?>
                </div>                
            </div>
        </div>

        <script src="js/libs/jquery/jquery.js"></script>
        <script src="js/libs/twitter-bootstrap/js/bootstrap.min.js"></script>
        <script src="js/libs/jquery/plugins/metisMenu/jquery.metisMenu.js"></script>
        <script src="sb-admin/sb-admin.js"></script>
        <script src="js/libs/responsive-calendar/responsive-calendar.min.js"></script>
        <script src="js/libs/jquery/plugins/flot/jquery.flot.js"></script>
        <script src="js/libs/jquery/plugins/flot/jquery.flot.tooltip.min.js"></script>
        <script src="js/libs/jquery/plugins/flot/jquery.flot.downsample.js"></script>
        <script src="js/libs/jquery/plugins/flot/jquery.flot.time.js"></script>        
        
        <script type="text/javascript">
            $(document).ready(function () {
                $(".responsive-calendar").responsiveCalendar({
                    time: '<?php echo date("Y-m")?>',
                    translateMonths: ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Aout", "Septembre", "Octobre", "Novembre", "Décembre"],
                    events: {
                        <?php
                        $sql = "SELECT DISTINCT substr(DateMesure,1,10) as recordDay FROM Mesures ORDER BY DateMesure ASC";
                        $statement = $sourceDB->prepare($sql);
                        $results = $statement->execute();
                        $listRecords = '';
                        while ($row = $results->fetchArray()) {
                            $listRecords .= '"'.$row['recordDay'].'":';
                            $listRecords .= '{"url":"javascript:window.location.href = \'index.php?date=';
                            $listRecords .= $row['recordDay'].'\'"},';
                        }
                        $listRecords = substr($listRecords, 0, strlen($listRecords) - 1);
                        echo $listRecords;
                        ?>
                    }
                });
            });
            var dataset = [
                <?php
                    $dataset = '';
                    foreach ($dataArray as $emetteur => $data) {
                        $dataset .= '{ label: "'.$emetteurArray[$emetteur][0].'", data:'.$dataArray[$emetteur].'},';
                    }
                    $dataset = substr($dataset, 0, strlen($dataset) - 1);
                    echo $dataset;
                ?>
            ];
            optionsGraphics = {
                series: {
                    shadowSize: 5,
                    lines: {
                        show: true
                    },
                    points: {
                        show: false
                    }
                },
                grid: {
                    borderWidth:0,
                    hoverable: true
                },
                yaxis: {
                    show: true
                },
                xaxis: { 
                    tickDecimals: 1,
                    mode: "time",
                    timeformat: "%d/%m/%Y %H:%M:%S",
                    show: true
                },
                tooltip: true,
                tooltipOpts: {
                    content: "%y.2 °C (%x)"
                }                
            };
            $.plot("#graphRecord", dataset, optionsGraphics);
        </script>
    </body>
</html>

