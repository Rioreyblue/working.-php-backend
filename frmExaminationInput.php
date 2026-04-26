<?php

date_default_timezone_set("Asia/Manila");

require 'xDBParam.php';
require "xDBConn.php";
$phpFile = "frmExaminationInput.php";
require "xToken.php";

$MC = $objEntry->{'MC'};

if ($token != $myToken) {
    echo "[]";
    exit();
}

$DBConnection = new PDO($dsn, $username, $password);
$DBConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$trailDate = date("Y/m/d h:i:sa");
switch ($MC) {

    case "INPUT01":
        try {
            $sql = "SELECT inp_keyctr, with_situation, sit_keyctr, is_quespic, question_, quespic , ques_main, correct_answer, pts, jobcode, image_filename FROM hrmax.maintenance_exam_input ORDER BY inp_keyctr ASC;";
            $sth = $DBConnection->prepare($sql);
            $sth->execute();
            $sth->setFetchMode(PDO::FETCH_ASSOC);
            $rows = json_encode($sth->fetchall());
            if ($rows != '[]') {
                echo $rows;
            }
        } catch (Exception $ex) {
            echo $ex;
        }
        break;

    case "INPUT02":
        try {
            $jobcode        = $objEntry->{'jobcode'};
            $image_filename = $objEntry->{'image_filename'};
            $inp_keyctr     = $objEntry->{'inp_keyctr'};
            $with_situation = $objEntry->{'with_situation'};
            $sit_keyctr     = $objEntry->{'sit_keyctr'};
            $is_quespic     = $objEntry->{'is_quespic'};
            $question_      = $objEntry->{'question_'};
            $quespic        = $objEntry->{'quespic'};
            $ques_main      = $objEntry->{'ques_main'};
            $correct_answer = $objEntry->{'correct_answer'};
            $pts            = $objEntry->{'pts'};
            $user           = $objEntry->{'user'};
            $trail          = $user . "-" . $trailDate;
            $IsUpdate       = $objEntry->{'IsUpdate'};

            $fileStr = $objEntry->{'filestr'};
            $file_folder = "Examination";
            $filepath = "Questions/";
            $filewithextension = $objEntry->{'image_filename'};

            $directoryfolder = $filepath;
            $directoryName =  $directoryfolder . $file_folder . '/';

            if (!is_dir($directoryName)) {
                mkdir($directoryName, 0777, true);
            }

            if (!empty($fileStr)) {
                $image_base64 = base64_decode($fileStr);
                $file = $directoryName . $filewithextension;
                file_put_contents($file, $image_base64);
            }

            if ($IsUpdate == "False") {
                $sql = "INSERT INTO hrmax.maintenance_exam_input (
                        jobcode, image_filename, inp_keyctr, with_situation, sit_keyctr, is_quespic, 
                        question_, quespic, ques_main, correct_answer, pts, trail
                    ) values (
                        :jobcode, :image_filename, :inp_keyctr, :with_situation, :sit_keyctr, :is_quespic, 
                        :question_, :quespic, :ques_main, :correct_answer, :pts, :trail
                    )";
            } else {
                $sql = "UPDATE hrmax.maintenance_exam_input SET
                        jobcode        = :jobcode,
                        image_filename = :image_filename, 
                        with_situation = :with_situation, 
                        sit_keyctr     = :sit_keyctr, 
                        is_quespic     = :is_quespic, 
                        question_      = :question_, 
                        quespic        = :quespic, 
                        ques_main      = :ques_main,
                        correct_answer = :correct_answer, 
                        pts            = :pts, 
                        trail          = :trail
                    WHERE inp_keyctr = :inp_keyctr";
            }

            $sth = $DBConnection->prepare($sql);
            $sth->bindParam(":jobcode",   $jobcode);
            $sth->bindParam(":image_filename",   $image_filename);
            $sth->bindParam(":inp_keyctr",   $inp_keyctr);
            $sth->bindParam(":with_situation", $with_situation);
            $sth->bindParam(":sit_keyctr",     $sit_keyctr);
            $sth->bindParam(":is_quespic",     $is_quespic);
            $sth->bindParam(":question_",      $question_);
            $sth->bindParam(":quespic",        $quespic);
            $sth->bindParam(":ques_main",      $ques_main);
            $sth->bindParam(":correct_answer", $correct_answer);
            $sth->bindParam(":pts",            $pts);
            $sth->bindParam(":trail",          $trail);

            if ($sth->execute()) {
                echo json_encode('done');
            } else {
                echo json_encode('error');
            }
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
        break;

    case "INPUT03":
        try {

            $var_del = $objEntry->{'var_del'};
            $var_img = $objEntry->{'var_img'};
            $booleany = $objEntry->{'boolean'};
            $trues = "True";
            if ($booleany == $trues) {
                echo json_encode('sysmaint');
            } else {

                if (!empty($var_img)) {
                    $filePath = "Questions/Examination/" . $var_img;
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }

                $sql = "DELETE FROM hrmax.maintenance_exam_input WHERE inp_keyctr = ?";
                $sth = $DBConnection->prepare($sql);
                $sth->bindValue(1, $var_del);
                if ($sth->execute()) {
                    echo json_encode('done');
                } else {
                    echo json_encode('error');
                }
            }
        } catch (Exception $ex) {
            echo json_encode('Error Oi');
        }
        break;
}

$DBConnection = null;
