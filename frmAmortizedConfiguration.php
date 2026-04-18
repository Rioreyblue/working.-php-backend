<?php
// rey task
date_default_timezone_set("Asia/Manila");

require 'xDBParam.php';
require "xDBConn.php";
$phpFile = "frmAmortizedConfiguration.php"; 
require "xToken.php";

$MC = $objEntry->{'MC'};

if ($token != $myToken) {
    echo "[]";
    exit();
}

try {
    $DBConnection = new PDO($dsn, $username, $password);
    $DBConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode("Connection failed: " . $e->getMessage());
    exit();
}

$trailDate = date("Y/m/d h:i:sa");

switch ($MC) {

    case "MLOAN01":
        try {
            $sql = "SELECT loan_id, loan_desc, banking, glaccount_no, lr_type FROM hrmax.maintenance_multiloan ORDER BY loan_id ASC;";
            $sth = $DBConnection->prepare($sql);
            $sth->execute();
            $sth->setFetchMode(PDO::FETCH_ASSOC);
            $rows = json_encode($sth->fetchall());
            echo ($rows != '[]') ? $rows : "[]";
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
        break;

    case "MLOAN02": // insrt or update
        try {
            $loan_id      = $objEntry->{'loan_id'};
            $loan_desc    = $objEntry->{'loan_desc'};
            $banking      = $objEntry->{'banking'}; 
            $glaccount_no = $objEntry->{'glaccount_no'};
            $lr_type      = $objEntry->{'lr_type'};
            
            $user         = $objEntry->{'user'};
            $trail        = $user . "-" . $trailDate;
            $IsUpdate     = $objEntry->{'IsUpdate'};

            if ($IsUpdate == "False") {
                $sql = "INSERT INTO hrmax.maintenance_multiloan (
                            loan_id, loan_desc, banking, glaccount_no, lr_type
                        ) VALUES (
                            :loan_id, :loan_desc, :banking, :glaccount_no, :lr_type
                        )";
            } else {
                $sql = "UPDATE hrmax.maintenance_multiloan SET
                            loan_desc    = :loan_desc,
                            banking      = :banking,
                            glaccount_no = :glaccount_no,
                            lr_type      = :lr_type
                        WHERE loan_id = :loan_id";
            }

            $sth = $DBConnection->prepare($sql);
            $sth->bindParam(":loan_id",      $loan_id);
            $sth->bindParam(":loan_desc",    $loan_desc);
            $sth->bindParam(":banking",     $banking, PDO::PARAM_BOOL); 
            $sth->bindParam(":glaccount_no", $glaccount_no);
            $sth->bindParam(":lr_type",      $lr_type);

            if ($sth->execute()) {
                echo json_encode('done');
            } else {
                echo json_encode('error');
            }
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
        break;

    case "MLOAN03":
        try {
            $var_del = $objEntry->{'var_del'};
            $booleany = $objEntry->{'boolean'};
            
            if ($booleany == "True") {
                echo json_encode('sysmaint');
            } else {
                $sql = "DELETE FROM hrmax.maintenance_multiloan WHERE loan_id = ?";
                $sth = $DBConnection->prepare($sql);
                $sth->bindValue(1, $var_del);
                
                if ($sth->execute()) {
                    echo json_encode('done');
                } else {
                    echo json_encode('error');
                }
            }
        } catch (Exception $ex) {
            echo json_encode('Error: ' . $ex->getMessage());
        }
        break;
}

$DBConnection = null;