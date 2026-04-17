<?php
// rey task
date_default_timezone_set("Asia/Manila");

require 'xDBParam.php';
require "xDBConn.php";
$phpFile = "frmEarningsConfiguration.php"; 
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

    case "EARN01":
        try {
            $sql = "SELECT inccode, description, nontaxable, trail, formulated, formula, ceiling_amount, deductible, include_in_alphalisting,
                           payroll_type, automatic_gross, incl_based_on, automatic_contra_acct, lr_type, ceiling_config
                    FROM hrmax.maintenance_earning 
                    ORDER BY inccode ASC";
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

    case "EARN02":
        try {
            $inccode                 = $objEntry->{'inccode'};
            $old_inccode             = $objEntry->{'old_inccode'}; 
            $description             = $objEntry->{'description'};
            $nontaxable              = $objEntry->{'nontaxable'};
            $formulated              = $objEntry->{'formulated'};
            $formula                 = $objEntry->{'formula'};
            $ceiling_amount          = $objEntry->{'ceiling_amount'};
            $deductible              = $objEntry->{'deductible'};
            $include_in_alphalisting = $objEntry->{'include_in_alphalisting'};
            $payroll_type            = $objEntry->{'payroll_type'};
            $automatic_gross         = $objEntry->{'automatic_gross'};
            $incl_based_on           = $objEntry->{'incl_based_on'};
            $automatic_contra_acct   = $objEntry->{'automatic_contra_acct'};
            $lr_type                 = $objEntry->{'lr_type'};
            $ceiling_config          = $objEntry->{'ceiling_config'};
            $user                    = $objEntry->{'user'};
            $trail                   = $user . "-" . $trailDate;
            $IsUpdate                = $objEntry->{'IsUpdate'};

            $DBConnection->beginTransaction();

            if ($IsUpdate == "False") {
                $sql = "INSERT INTO hrmax.maintenance_earning (inccode, description, nontaxable, formulated, formula, ceiling_amount, deductible, include_in_alphalisting, payroll_type, automatic_gross, incl_based_on, automatic_contra_acct, lr_type, ceiling_config, trail) 
                        VALUES (:inccode, :description, :nontaxable, :formulated, :formula, :ceiling_amount, :deductible, :include_in_alphalisting, :payroll_type, :automatic_gross, :incl_based_on, :automatic_contra_acct, :lr_type, :ceiling_config, :trail)";
                $sth = $DBConnection->prepare($sql);
                $sth->bindParam(":inccode", $inccode);
                $sth->bindParam(":description", $description);
                $sth->bindParam(":nontaxable", $nontaxable);
                $sth->bindParam(":formulated", $formulated);
                $sth->bindParam(":formula", $formula);
                $sth->bindParam(":ceiling_amount", $ceiling_amount);
                $sth->bindParam(":deductible", $deductible);
                $sth->bindParam(":include_in_alphalisting", $include_in_alphalisting);
                $sth->bindParam(":payroll_type", $payroll_type);
                $sth->bindParam(":automatic_gross", $automatic_gross);
                $sth->bindParam(":incl_based_on", $incl_based_on);
                $sth->bindParam(":automatic_contra_acct", $automatic_contra_acct);
                $sth->bindParam(":lr_type", $lr_type);
                $sth->bindParam(":ceiling_config", $ceiling_config);
                $sth->bindParam(":trail", $trail);
                $sth->execute();

                $sql = "UPDATE hrmax.fixearnings SET formula = :formula, nontaxable = :nontaxable, deductible = :deductible, payroll_type = :payroll_type, incl_based_on = :incl_based_on WHERE inccode = :inccode AND automatic_gross = :automatic_gross";
                $sth = $DBConnection->prepare($sql);
                $sth->bindParam(":formula", $formula);
                $sth->bindParam(":nontaxable", $nontaxable);
                $sth->bindParam(":deductible", $deductible);
                $sth->bindParam(":payroll_type", $payroll_type);
                $sth->bindParam(":incl_based_on", $incl_based_on);
                $sth->bindParam(":inccode", $inccode);
                $sth->bindParam(":automatic_gross", $automatic_gross);
                $sth->execute();

                $sql = "UPDATE hrmax.otherearning SET formula = :formula, nontaxable = :nontaxable, deductible = :deductible, incl_based_on = :incl_based_on, automatic_contra_acct = :automatic_contra_acct WHERE inccode = :inccode AND automatic_gross = :automatic_gross";
                $sth = $DBConnection->prepare($sql);
                $sth->bindParam(":formula", $formula);
                $sth->bindParam(":nontaxable", $nontaxable);
                $sth->bindParam(":deductible", $deductible);
                $sth->bindParam(":incl_based_on", $incl_based_on);
                $sth->bindParam(":automatic_contra_acct", $automatic_contra_acct);
                $sth->bindParam(":inccode", $inccode);
                $sth->bindParam(":automatic_gross", $automatic_gross);
                $sth->execute();

            } else {
                $sql = "UPDATE hrmax.maintenance_earning SET inccode = :inccode, description = :description, nontaxable = :nontaxable, formulated = :formulated, formula = :formula, ceiling_amount = :ceiling_amount, deductible = :deductible, include_in_alphalisting = :include_in_alphalisting, payroll_type = :payroll_type, automatic_gross = :automatic_gross, incl_based_on = :incl_based_on, automatic_contra_acct = :automatic_contra_acct, lr_type = :lr_type, ceiling_config = :ceiling_config, trail = :trail WHERE inccode = :old_inccode";
                $sth = $DBConnection->prepare($sql);
                $sth->bindParam(":inccode", $inccode);
                $sth->bindParam(":description", $description);
                $sth->bindParam(":nontaxable", $nontaxable);
                $sth->bindParam(":formulated", $formulated);
                $sth->bindParam(":formula", $formula);
                $sth->bindParam(":ceiling_amount", $ceiling_amount);
                $sth->bindParam(":deductible", $deductible);
                $sth->bindParam(":include_in_alphalisting", $include_in_alphalisting);
                $sth->bindParam(":payroll_type", $payroll_type);
                $sth->bindParam(":automatic_gross", $automatic_gross);
                $sth->bindParam(":incl_based_on", $incl_based_on);
                $sth->bindParam(":automatic_contra_acct", $automatic_contra_acct);
                $sth->bindParam(":lr_type", $lr_type);
                $sth->bindParam(":ceiling_config", $ceiling_config);
                $sth->bindParam(":trail", $trail);
                $sth->bindParam(":old_inccode", $old_inccode);
                $sth->execute();

                $sql = "UPDATE hrmax.fixearnings SET formula = :formula, nontaxable = :nontaxable, deductible = :deductible, payroll_type = :payroll_type, automatic_gross = :automatic_gross, incl_based_on = :incl_based_on WHERE inccode = :old_inccode";
                $sth = $DBConnection->prepare($sql);
                $sth->bindParam(":formula", $formula);
                $sth->bindParam(":nontaxable", $nontaxable);
                $sth->bindParam(":deductible", $deductible);
                $sth->bindParam(":payroll_type", $payroll_type);
                $sth->bindParam(":automatic_gross", $automatic_gross);
                $sth->bindParam(":incl_based_on", $incl_based_on);
                $sth->bindParam(":old_inccode", $old_inccode);
                $sth->execute();

                $sql = "UPDATE hrmax.otherearning SET formula = :formula, nontaxable = :nontaxable, deductible = :deductible, automatic_gross = :automatic_gross, incl_based_on = :incl_based_on, automatic_contra_acct = :automatic_contra_acct WHERE inccode = :old_inccode";
                $sth = $DBConnection->prepare($sql);
                $sth->bindParam(":formula", $formula);
                $sth->bindParam(":nontaxable", $nontaxable);
                $sth->bindParam(":deductible", $deductible);
                $sth->bindParam(":automatic_gross", $automatic_gross);
                $sth->bindParam(":incl_based_on", $incl_based_on);
                $sth->bindParam(":automatic_contra_acct", $automatic_contra_acct);
                $sth->bindParam(":old_inccode", $old_inccode);
                $sth->execute();

                $sql = "UPDATE hrmax.fixearnings_payroll SET inccode = :inccode WHERE inccode = :old_inccode";
                $sth = $DBConnection->prepare($sql);
                $sth->bindParam(":inccode", $inccode);
                $sth->bindParam(":old_inccode", $old_inccode);
                $sth->execute();

                $sql = "UPDATE hrmax.otherearning_payroll SET inccode = :inccode WHERE inccode = :old_inccode";
                $sth = $DBConnection->prepare($sql);
                $sth->bindParam(":inccode", $inccode);
                $sth->bindParam(":old_inccode", $old_inccode);
                $sth->execute();
            }

            $DBConnection->commit();
            echo json_encode('done');

        } catch (Exception $ex) {
            if ($DBConnection->inTransaction()) { $DBConnection->rollBack(); }
            echo json_encode('error: ' . $ex->getMessage());
        }
        break;

    case "EARN03":
        try {
            $var_del = $objEntry->{'var_del'};
            $booleany = $objEntry->{'boolean'};
            if ($booleany == "True") {
                echo json_encode('sysmaint');
            } else {
                $sql = "DELETE FROM hrmax.maintenance_earning WHERE inccode = :inccode";
                $sth = $DBConnection->prepare($sql);
                $sth->bindParam(":inccode", $var_del);
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