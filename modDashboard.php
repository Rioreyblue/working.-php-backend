<?php

date_default_timezone_set("Asia/Manila");

require "xDBParam.php";
require "xDBConn.php";
$phpFile="modDashboard.php";
require "xToken.php";

$MC= $objEntry->{'MC'}; 

if ($token!=$myToken) {    
    echo "[]";
    exit();
}

$DBConnection = new PDO($dsn,$username, $password);
$DBConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$trailDate=date("Y/m/d h:i:sa");
switch ($MC) {

    case"DASH01":
        try{
            $ActiveCompany = $objEntry->{'ActiveCompany'};

            $sql="SELECT COUNT(*) as total_employee from hrmax.all_employee WHERE company_ = ? ";
            $sth = $DBConnection->prepare($sql);
            $sth->bindValue(1,$ActiveCompany);
            $sth->execute();
            $sth->setFetchMode(PDO::FETCH_ASSOC); 
            $rows=json_encode($sth->fetchall());  
            if ($rows!='[]'){
                echo $rows; 
            }
        }catch(Exception $ex){
            echo $ex;
        }
    break;

    case"DASH02":
        try{
            $sql="SELECT COUNT(*) as total_applicant from hrmax.applicant_profile";
            $sth = $DBConnection->prepare($sql);
            $sth->execute();
            $sth->setFetchMode(PDO::FETCH_ASSOC); 
            $rows=json_encode($sth->fetchall());  
            if ($rows!='[]'){
                echo $rows; 
            }
        }catch(Exception $ex){
            echo $ex;
        }
    break;

    case"DASH03":
        try{
            $varDateNow = $objEntry->{'varDateNow'};

            $sql="SELECT COUNT(*) as total_leave from hrmax.leave_benefits_online where date_leave = ? AND pending=TRUE AND deleted=FALSE;";
            $sth = $DBConnection->prepare($sql);
            $sth->bindValue(1,$varDateNow);
            $sth->execute();
            $sth->setFetchMode(PDO::FETCH_ASSOC); 
            $rows=json_encode($sth->fetchall());  
            if ($rows!='[]'){
                echo $rows; 
            }
        }catch(Exception $ex){
            echo $ex;
        }
    break;

    case"DASH04":
        try{
            $varDateNow = $objEntry->{'varDateNow'};

            $sql="SELECT COUNT(*) as total_travel from hrmax.official_business_travel_online where date_start = ? AND deleted=FALSE;";
            $sth = $DBConnection->prepare($sql);
            $sth->bindValue(1,$varDateNow);
            $sth->execute();
            $sth->setFetchMode(PDO::FETCH_ASSOC); 
            $rows=json_encode($sth->fetchall());  
            if ($rows!='[]'){
                echo $rows; 
            }
        }catch(Exception $ex){
            echo $ex;
        }
    break;
    
	case"DASH05":
        try{
            $varYear = $objEntry->{'varYear'};
            $ActiveUserID = $objEntry->{'ActiveUserID'};

            $sql = "SELECT TO_CHAR(attdate, 'Month') AS Month_, 
                    CASE WHEN remarks = '' THEN 'PRESENT' 
                    ELSE remarks 
                    END AS remarks_, 
                    COUNT(*) AS totalcnt_ 
                    FROM hrmax.daily_attendance_posted 
                    WHERE 
                    EXTRACT(YEAR FROM attdate) = ?
                    AND idno = ?
                    GROUP BY TO_CHAR(attdate, 'Month'), EXTRACT(MONTH FROM attdate), Remarks 
                    ORDER BY EXTRACT(MONTH FROM attdate), Remarks;";
            $sth = $DBConnection->prepare($sql);
            $sth->bindValue(1,$varYear);
            $sth->bindValue(2,$ActiveUserID);
            $sth->execute();
            $sth->setFetchMode(PDO::FETCH_ASSOC); 
            $rows=json_encode($sth->fetchall());  
            if ($rows!='[]'){
                echo $rows; 
            }
        }catch(Exception $ex){
            echo $ex;
        }
    break;
    //rey
case "DASH06":
    try {
        $varDateNow = $objEntry->{'varDateNow'};
        
        $sql = "SELECT 
            (SELECT COUNT(*) FROM hrmax.daily_attendance 
             WHERE date_log = ?) AS total_base,

            (SELECT COUNT(*) FROM hrmax.daily_attendance 
             WHERE remarks = 'ABSENT' AND date_log = ?) AS total_absent,

            (SELECT COUNT(*) FROM hrmax.daily_attendance 
             WHERE (remarks = '' OR remarks = 'LATE' OR remarks = 'UNDERTIME') AND date_log = ?) AS total_present,

            (SELECT COUNT(*) FROM hrmax.daily_attendance 
             WHERE remarks = 'LATE' AND date_log = ?) AS total_late,

            (SELECT COUNT(*) FROM hrmax.daily_attendance 
             WHERE remarks = 'MATERNITY LEAVE' AND date_log = ?) AS total_maternity,

            (SELECT COUNT(*) FROM hrmax.daily_attendance 
             WHERE remarks = 'VACATION LEAVE' AND date_log = ?) AS total_vacation,

            (SELECT COUNT(*) FROM hrmax.daily_attendance 
             WHERE remarks = 'SICK LEAVE' AND date_log = ?) AS total_sick,

            (SELECT COUNT(*) FROM hrmax.daily_attendance 
             WHERE remarks = 'BUSINESS TRAVEL' AND date_log = ?) AS total_business,

            (SELECT COUNT(*) FROM hrmax.daily_attendance 
             WHERE (remarks = 'SPL-HOLIDAY' OR remarks = 'LGL-HOLIDAY') AND date_log = ?) AS total_holiday";

        $sth = $DBConnection->prepare($sql);
        
        $sth->bindValue(1, $varDateNow);
        $sth->bindValue(2, $varDateNow);
        $sth->bindValue(3, $varDateNow);
        $sth->bindValue(4, $varDateNow);
        $sth->bindValue(5, $varDateNow);
        $sth->bindValue(6, $varDateNow);
        $sth->bindValue(7, $varDateNow);
        $sth->bindValue(8, $varDateNow);
        $sth->bindValue(9, $varDateNow);

        $sth->execute();
        $sth->setFetchMode(PDO::FETCH_ASSOC); 
        $rows = json_encode($sth->fetchAll());  
        
        if ($rows != '[]') {
            echo $rows; 
        }
    } catch (Exception $ex) {
        echo $ex->getMessage();
    }
    break;

//rey taks
    case"DASH07":
        try{
            $sql = "SELECT stat.idno,
                    stat.divcode,
                    CONCAT(pr.lname, ', ', pr.fname, ' ', pr.mname) AS full_name,
                    stat.remarks,
                    stat.attdate,
                    stat.trail
                    FROM hrmax.daily_attendance AS stat
                    INNER JOIN 
                    hrmax.profile AS pr ON stat.idno = pr.idno
                    ORDER BY stat.attdate DESC";
            $sth = $DBConnection->prepare($sql);
            $sth->execute();
            $sth->setFetchMode(PDO::FETCH_ASSOC); 
            $rows=json_encode($sth->fetchall());  
            if ($rows!='[]'){
                echo $rows; 
            }
        }catch(Exception $ex){
            echo $ex;
        }
    break;
    }  
$DBConnection=null;

