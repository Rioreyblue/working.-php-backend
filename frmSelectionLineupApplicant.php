<?php

date_default_timezone_set("Asia/Manila");

require "xDBParam.php";
require "xDBConn.php";
$phpFile="frmSelectionLineupApplicant.php";
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

    case"SEL01":
        try{
            $sql = "SELECT a.idno,a.lname,a.fname,a.mname,b.*,c.jobtitle,c.place_of_assignment,c.job_qualifications,
                    c.job_summary,c.job_responsibilities,c.date_needed1,c.date_needed2,a.pincode,d.batch_exam from 
                    hrmax.applicant_profile a
                    inner join hrmax.vacant_position_application b on a.idno = b.idno
                    inner join hrmax.maintenance_job c on b.applied_position = c.jobcode
                    inner join hrmax.applicant_exam_main d on a.idno = d.idno and c.jobcode = d.jobcode";
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

case "SEL02": 
        try {
            $ApplicantID = $objEntry->{'ApplicantID'};
            $ApplicantJob = $objEntry->{'ApplicantJob'};
            $ApplicantAction = $objEntry->{'ApplicantAction'};

            if ($ApplicantAction == 1) {
                $sql = "UPDATE hrmax.vacant_position_application 
                           SET for_examination = 'true',
                               for_requirements = 'false',
                               for_interview = 'false',
                               for_pooling = 'false',
                               hired = 'false',
                               not_qualified = 'false'
                         WHERE idno = :ApplicantID AND applied_position = :ApplicantJob";
            } elseif ($ApplicantAction == 2) {
                $sql = "UPDATE hrmax.vacant_position_application 
                           SET for_examination = 'false',
                               for_requirements = 'true',
                               for_interview = 'false',
                               for_pooling = 'false',
                               hired = 'false',
                               not_qualified = 'false'
                         WHERE idno = :ApplicantID AND applied_position = :ApplicantJob";
            } elseif ($ApplicantAction == 3) {
                $sql = "UPDATE hrmax.vacant_position_application 
                           SET for_examination = 'false',
                               for_requirements = 'false',
                               for_interview = 'true',
                               for_pooling = 'false',
                               hired = 'false',
                               not_qualified = 'false'
                         WHERE idno = :ApplicantID AND applied_position = :ApplicantJob";
            } elseif ($ApplicantAction == 4) {
                $sql = "UPDATE hrmax.vacant_position_application 
                           SET for_examination = 'false',
                               for_requirements = 'false',
                               for_interview = 'false',
                               for_pooling = 'true',
                               hired = 'false',
                               not_qualified = 'false'
                         WHERE idno = :ApplicantID AND applied_position = :ApplicantJob";
            } elseif ($ApplicantAction == 5) {
                $sql = "UPDATE hrmax.vacant_position_application 
                           SET for_examination = 'false',
                               for_requirements = 'false',
                               for_interview = 'false',
                               for_pooling = 'false',
                               hired = 'true',
                               not_qualified = 'false'
                         WHERE idno = :ApplicantID AND applied_position = :ApplicantJob";
            } elseif ($ApplicantAction == 6) {
                $sql = "UPDATE hrmax.vacant_position_application 
                           SET for_examination = 'false',
                               for_requirements = 'false',
                               for_interview = 'false',
                               for_pooling = 'false',
                               hired = 'false',
                               not_qualified = 'true'
                         WHERE idno = :ApplicantID AND applied_position = :ApplicantJob";
            }

            $sth = $DBConnection->prepare($sql);
            $sth->bindParam(":ApplicantID", $ApplicantID);
            $sth->bindParam(":ApplicantJob", $ApplicantJob);

            if ($sth->execute()) {
                echo json_encode('done');
            } else {
                echo json_encode('error');
            }

        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    break;

    }   
$DBConnection=null;