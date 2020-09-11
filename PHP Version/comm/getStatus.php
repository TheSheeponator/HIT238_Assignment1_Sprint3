<?php

class wrapper {
    public $zoneStatus;
}
class zone {
    public $id;
    public $data;
}
class zoneData {
    public $status;
    public $staTime;
    public $finTime;
    public $duration;
    public $title;
}

$_JSONdata = json_decode(file_get_contents('php://input'), true);
if (isset($_JSONdata['loc']))
{
    require '../includes/sdbh.inc.php';


    $sql = 'SELECT id, name, start1, end1, status FROM times WHERE id=?';
    $stmt = mysqli_stmt_init($sconn);

    if (!mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_close($sconn);
        echo json_encode(array('errorpage' => '//errordocs/err002'));
        exit();
    }
    else {
        function GetTimeDiff ($start, $end) {
            $startT = explode(":", $start);
            $endT = explode(":", $end);
            $sMili = ((int)$startT[0] * 3600000) + ((int)$startT[1] * 60000);
            $eMili = ($endT[0] * 3600000) + ($endT[1] * 60000);
            $diff = $eMili - $sMili;
            $hours = floor($diff / 1000 / 60 / 60);
            $diff -= $hours * 1000 * 60 * 60;
            $minutes = floor($diff / 1000 / 60);
            if ($hours < 0 ) { $hours += 24; }
            return ($hours == 0 ? "" : $hours."h ").($hours != 0 && $minutes == 0 ? "" : $minutes." min");
        }

        $location = $_JSONdata['loc'];
        mysqli_stmt_bind_param($stmt, "s", $location);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $sqlId, $sqlname, $sqlstart, $sqlend, $status);
        mysqli_stmt_fetch($stmt);
        $title = $sqlname;
        $staTime = date('h:i', strtotime($sqlstart));
        $finTime = date('h:i', strtotime($sqlend));
        $duration = GetTimeDiff($sqlstart, $sqlend);
        

        if (explode(":", $sqlstart)[0] >= 12) {
            $staTime .= " pm";
        } else {
            $staTime .= " am";
        }
        if (explode(":", $sqlend)[0] >= 12) {
            $finTime .= " pm";
        } else {
            $finTime .= " am";
        }
    }

    mysqli_close($sconn);

    $out = array();

    if ($status == 0) {
        // Blue (stand-by), ON, OFF
        $out = array("status" => "0", "staTime" => $staTime, "finTime" => $finTime, "duration" => $duration, "title" => $title); 
    }
    elseif ($status == 1) {
        // Green (water on), ON, OFF
        $out = array("status" => "1", "staTime" => $staTime, "finTime" => $finTime, "duration" => $duration, "title" => $title); 
    }
    elseif ($status == 2) {
        // Red (water off/manual), ON, AUTO
        $out = array("status" => "2", "staTime" => $staTime, "finTime" => $finTime, "duration" => $duration, "title" => $title); 
    }
    elseif ($status == 3) {
        // Dark-Green (water on/manual), OFF, AUTO
        $out = array("status" => "3", "staTime" => $staTime, "finTime" => $finTime, "duration" => $duration, "title" => $title); 
    }
    elseif ($status == 4) {
        // Orange (water off/weather), ON, OFF
        $out = array("status" => "4", "staTime" => $staTime, "finTime" => $finTime, "duration" => $duration, "title" => $title); 
    }
    else {
        // grey (error - number not expected)
        $out = array("status" => "5", "staTime" => '00:00', "finTime" => '00:00', "duration" => '0', "title" => '--------');
    }
    echo json_encode($out);
    exit();
} elseif (isset($_JSONdata['all'])) {
    require '../includes/sdbh.inc.php';


    $sql = 'SELECT id, name, start1, end1, status FROM times';
    $result = mysqli_query($sconn, $sql);

    if (!$result) {
        mysqli_close($sconn);
        echo json_encode(array('errorpage' => '//errordocs/err002'));
        exit();
    } else {
        function GetTimeDiff ($start, $end) {
            $startT = explode(":", $start);
            $endT = explode(":", $end);
            $sMili = ((int)$startT[0] * 3600000) + ((int)$startT[1] * 60000);
            $eMili = ($endT[0] * 3600000) + ($endT[1] * 60000);
            $diff = $eMili - $sMili;
            $hours = floor($diff / 1000 / 60 / 60);
            $diff -= $hours * 1000 * 60 * 60;
            $minutes = floor($diff / 1000 / 60);
            if ($hours < 0 ) { $hours += 24; }
            return ($hours == 0 ? "" : $hours."h ").($hours != 0 && $minutes == 0 ? "" : $minutes." min");
        }

        $output = array();

        while($row = mysqli_fetch_assoc($result)) {

            $id = $row['id'];
            $title = $row['name'];
            $status = $row['status'];
            
            $sqlstart = $row['start1'];
            $sqlend = $row['end1'];
            
            $staTime = date('h:i', strtotime($sqlstart));
            $finTime = date('h:i', strtotime($sqlend));
            $duration = GetTimeDiff($sqlstart, $sqlend);
            
            
            if (explode(":", $sqlstart)[0] >= 12) {
                $staTime .= " pm";
            } else {
                $staTime .= " am";
            }
            if (explode(":", $sqlend)[0] >= 12) {
                $finTime .= " pm";
            } else {
                $finTime .= " am";
            }
            
            $out = new zoneData();
            
            if ($status == 0) {
                // Blue (stand-by), ON, OFF
                $out->status = '0';
                $out->staTime = $staTime;
                $out->finTime = $finTime;
                $out->duration = $duration;
                $out->title = $title;
            }
            elseif ($status == 1) {
                // Green (water on), ON, OFF
                $out->status = '1';
                $out->staTime = $staTime;
                $out->finTime = $finTime;
                $out->duration = $duration;
                $out->title = $title;
            }
            elseif ($status == 2) {
                // Red (water off/manual), ON, AUTO
                $out->status = '2';
                $out->staTime = $staTime;
                $out->finTime = $finTime;
                $out->duration = $duration;
                $out->title = $title;
            }
            elseif ($status == 3) {
                // Dark-Green (water on/manual), OFF, AUTO
                $out->status = '3';
                $out->staTime = $staTime;
                $out->finTime = $finTime;
                $out->duration = $duration;
                $out->title = $title;
            }
            elseif ($status == 4) {
                // Orange (water off/weather), ON, OFF
                $out->status = '4';
                $out->staTime = $staTime;
                $out->finTime = $finTime;
                $out->duration = $duration;
                $out->title = $title;
            }
            else {
                // grey (error - number not expected)
                $out->status = '5';
                $out->staTime = '00:00';
                $out->finTime = '00:00';
                $out->duration = '0';
                $out->title = '--------';
            }
            $zone = new zone();
            $zone->data = $out;
            $zone->id = $id;
            array_push($output, $zone);
        }
        $wrapper = new wrapper();
        $wrapper->zoneStatus = $output;
        echo json_encode($wrapper);
        mysqli_close($sconn);
        exit();
    }
} else {
    echo json_encode(array('error' => 'Invalid'));
    exit();
}