<?php
// rey task
date_default_timezone_set("Asia/Manila");

require 'xDBParam.php';
require "xDBConn.php";
$phpFile = "frmDeductionConfiguration.php"; 
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

    case "DEDUCT01":
        try {
            $sql = "SELECT ddctcode, description, nontaxable, formulated, formula, banking, glaccount_no, plotted, lr_type, deduct_to_grossall 
                    FROM hrmax.maintenance_deduction
                    ORDER BY ddctcode ASC";
            $sth = $DBConnection->prepare($sql);
            $sth->execute();
            $sth->setFetchMode(PDO::FETCH_ASSOC);
            $rows = json_encode($sth->fetchall());
            
            if ($rows != '[]') {
                echo $rows;
            }
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
        break;

    case "DEDUCT02":
        try {
            $ddctcode           = $objEntry->{'ddctcode'};
            $old_ddctcode       = $objEntry->{'old_ddctcode'}; 
            $description        = $objEntry->{'description'};
            $nontaxable         = $objEntry->{'nontaxable'};
            $formulated         = $objEntry->{'formulated'};
            $formula            = $objEntry->{'formula'};
            $banking            = $objEntry->{'banking'};
            $glaccount_no       = $objEntry->{'glaccount_no'};
            $plotted            = $objEntry->{'plotted'};
            $lr_type            = $objEntry->{'lr_type'};
            $deduct_to_grossall = $objEntry->{'deduct_to_grossall'};
            $user               = $objEntry->{'user'};
            $trail              = $user . "-" . $trailDate;
            $IsUpdate           = $objEntry->{'IsUpdate'};

            $DBConnection->beginTransaction();

            if ($IsUpdate == "False") {
                $sql = "INSERT INTO hrmax.maintenance_deduction (ddctcode, description, nontaxable, formulated, formula, banking, glaccount_no, plotted, lr_type, deduct_to_grossall, trail) 
                        VALUES (:ddctcode, :description, :nontaxable, :formulated, :formula, :banking, :glaccount_no, :plotted, :lr_type, :deduct_to_grossall, :trail)";
                $sth = $DBConnection->prepare($sql);
                $sth->bindParam(":ddctcode", $ddctcode);
                $sth->bindParam(":description", $description);
                $sth->bindParam(":nontaxable", $nontaxable);
                $sth->bindParam(":formulated", $formulated);
                $sth->bindParam(":formula", $formula);
                $sth->bindParam(":banking", $banking);
                $sth->bindParam(":glaccount_no", $glaccount_no);
                $sth->bindParam(":plotted", $plotted);
                $sth->bindParam(":lr_type", $lr_type);
                $sth->bindParam(":deduct_to_grossall", $deduct_to_grossall);
                $sth->bindParam(":trail", $trail);
                $sth->execute();


            } else {
                $sql = "UPDATE hrmax.maintenance_deduction SET ddctcode = :ddctcode, description = :description, nontaxable = :nontaxable, formulated = :formulated, formula = :formula, banking = :banking, glaccount_no = :glaccount_no, plotted = :plotted, lr_type = :lr_type, deduct_to_grossall = :deduct_to_grossall, trail = :trail WHERE ddctcode = :old_ddctcode";
                $sth = $DBConnection->prepare($sql);
                $sth->bindParam(":ddctcode", $ddctcode);
                $sth->bindParam(":description", $description);
                $sth->bindParam(":nontaxable", $nontaxable);
                $sth->bindParam(":formulated", $formulated);
                $sth->bindParam(":formula", $formula);
                $sth->bindParam(":banking", $banking);
                $sth->bindParam(":glaccount_no", $glaccount_no);
                $sth->bindParam(":plotted", $plotted);
                $sth->bindParam(":lr_type", $lr_type);
                $sth->bindParam(":deduct_to_grossall", $deduct_to_grossall);
                $sth->bindParam(":trail", $trail);
                $sth->bindParam(":old_ddctcode", $old_ddctcode);
                $sth->execute();
            }

            $DBConnection->commit();
            echo json_encode('done');

        } catch (Exception $ex) {
            if ($DBConnection->inTransaction()) { $DBConnection->rollBack(); }
            echo json_encode('error: ' . $ex->getMessage());
        }
        break;

    case "DEDUCT03":
        try {
            $var_del = $objEntry->{'var_del'};
            $booleany = $objEntry->{'boolean'};
            if ($booleany == "True") {
                echo json_encode('sysmaint');
            } else {
                $sql = "DELETE FROM hrmax.maintenance_deduction WHERE ddctcode = :ddctcode";
                $sth = $DBConnection->prepare($sql);
                $sth->bindParam(":ddctcode", $var_del);
                if ($sth->execute()) {
                    echo json_encode('done');
                } else {
                    echo json_encode('error');
                }
            }
        } catch (Exception $ex) {
            echo json_encode('Error');
        }
        break;
}

$DBConnection = null; 
?>