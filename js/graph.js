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
                            $listRecords .= '{"url":"javascript:window.location.href = \'index.php?module=temp?date=';
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
