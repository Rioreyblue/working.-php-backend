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
                 WHERE (remarks IS NULL OR remarks = 'null' OR remarks != 'ABSENT') 
                 AND date_log = ?) AS total_present,
                
                (SELECT COUNT(*) FROM hrmax.daily_attendance_posted 
                 WHERE remarks = 'LATE' AND date_log = ?) AS total_late,
                
                (SELECT COUNT(*) FROM hrmax.official_business_travel_online 
                 WHERE date_start = ? AND deleted = FALSE) AS total_on_business,
                
                (SELECT COUNT(*) FROM hrmax.leave_benefits_online 
                 WHERE ? BETWEEN date_from AND date_to AND deleted = FALSE) AS total_on_leave";

            $sth = $DBConnection->prepare($sql);
            
            // Binding the same date to all 4 subquery placeholders
            $sth->bindValue(1, $varDateNow);
            $sth->bindValue(2, $varDateNow);
            $sth->bindValue(3, $varDateNow);
            $sth->bindValue(4, $varDateNow);
            
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
                    ORDER BY attdate DESC";
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
    // case "DASH07":
    // try {
    //     $varYear = $objEntry->{'varYear'};
    //     $ActiveUserID = $objEntry->{'ActiveUserID'}; 
    //     $ids = is_array($ActiveUserID) ? $ActiveUserID : [$ActiveUserID];
        
    //     $placeholders = implode(',', array_fill(0, count($ids), '?'));

    //     $sql = "SELECT 
    //                 stat.idno, 
    //                 CONCAT(pr.lname, ', ', pr.fname, ' ', pr.mname) AS full_name,
    //                 CASE 
    //                     WHEN stat.remarks IS NULL OR stat.remarks = 'null' OR stat.remarks = '' THEN 'PRESENT'
    //                     ELSE stat.remarks 
    //                 END AS final_remarks,
    //                 stat.attdate
    //             FROM 
    //                 hrmax.daily_attendance_posted AS stat
    //             INNER JOIN 
    //                 hrmax.profile AS pr ON stat.idno = pr.idno
    //             WHERE 
    //                 EXTRACT(YEAR FROM stat.attdate) = ? 
    //                 AND stat.idno IN ($placeholders)";

    //     $sth = $DBConnection->prepare($sql);

        
    //     $sth->bindValue(1, $varYear);

    //     // 4. Bind all IDs starting from Position 2
    //     foreach ($ids as $index => $id) {
    //         $sth->bindValue($index + 2, $id);
    //     }

    //     $sth->execute();
    //     $sth->setFetchMode(PDO::FETCH_ASSOC); 
    //     $rows = json_encode($sth->fetchAll());  
        
    //     if ($rows != '[]') {
    //         echo $rows; 
    //     }
    // } catch(Exception $ex) {
    //     echo $ex->getMessage();
    // }
    // break;
    }  
$DBConnection=null;

