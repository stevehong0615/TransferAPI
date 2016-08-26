<?php
header("content-type: text/html; charset=utf-8");

require_once 'Connect.php';

$url = $_SERVER['REQUEST_URI'];
$url = explode('/', $url);
$url = explode('?', $url[3]);
$api = $url[0];
$userName = $_GET['username'];
$transId = $_GET['transid'];
$type = $_GET['type'];
$amount = $_GET['amount'];
$user_info = array();

$connect = new Connect;

function decodeUnicode($str){
    return preg_replace_callback('/\\\\u([0-9a-f]{4})/i',
        create_function(
            '$matches',
            'return mb_convert_encoding(pack("H*", $matches[1]), "UTF-8", "UCS-2BE");'
        ),
    $str);
}

if ($api == "addUser" && isset($userName) && $userName != null)
{
    $user = "SELECT `user_name` FROM `transfer_data_a` WHERE `user_name` = :user_name";
    $data = $connect->db->prepare($user);
    $data->bindParam(':user_name', $userName);
    $data->execute();
    $result = $data->fetchAll(PDO::FETCH_ASSOC);
    $userSelect = $result[0]['user_name'];

    if ($userSelect == null) {
        $user = "INSERT INTO `transfer_data_a` (`user_name`, `balance`)
            VALUES (:user_name, '100000')";
        $data = $connect->db->prepare($user);
        $data->bindParam(':user_name', $userName);
        $data->execute();

        $user_info = array("user_name" => "$userName", "massage" => "Create Finished!");

        $user_info = json_encode($user_info);
        echo decodeUnicode($user_info);
        exit;
    }
    if ($userSelect != null) {
        $user_info = array("massage" => "Repeated Username!");

        exit(json_encode($user_info));
    }
}


if ($api == "getBalance" && isset($userName) && $userName != null)
{

    $user = "SELECT `balance`, `user_name` FROM `transfer_data_a` WHERE `user_name` = :user_name ORDER BY `id` DESC";
    $data = $connect->db->prepare($user);
    $data->bindParam(':user_name', $userName);
    $data->execute();
    $result = $data->fetchAll(PDO::FETCH_ASSOC);

    if ($userName == $result[0]['user_name']) {
        $balance = $result[0]['balance'];

        $user_info = array("user_name" => "$userName", "balance" => "$balance");
        $user_info = json_encode($user_info);
        echo decodeUnicode($user_info);
        exit;
    } else {
        $user_info = array("message" => "User Not Found!");
        exit(json_encode($user_info));
    }
}

if ($api == "transfer" && isset($userName) && isset($transId) && isset($type) && isset($amount) && $transId != null && $userName != null && $amount != null && $amount >= 0)
{
    $user = "SELECT `user_name` FROM `transfer_data_a` WHERE `user_name` = :user_name";
    $data = $connect->db->prepare($user);
    $data->bindParam(':user_name', $userName);
    $data->execute();
    $result = $data->fetchAll(PDO::FETCH_ASSOC);
    $userSelect = $result[0]['user_name'];

    $selectB = "SELECT `balance` FROM `transfer_data_a` WHERE `user_name` = :user_name ORDER BY `id` DESC";
    $dataB = $connect->db->prepare($selectB);
    $dataB->bindParam(':user_name', $userName);
    $dataB->execute();
    $resultB = $dataB->fetchAll(PDO::FETCH_ASSOC);
    $bResult = $resultB[0]['balance'];

    if ($userName != $userSelect) {
        $user_info = array("message" => "User Not Found!");

        exit(json_encode($user_info));
    }

    if ($bResult < $amount) {
        $user_info = array("message" => "餘額不足!");

        $user_info = json_encode($user_info);
        echo decodeUnicode($user_info);
        exit;
    }

    if ($userName == $userSelect && $bResult >= $amount) {
        $select = "SELECT `trans_id` FROM `transfer_data_a` WHERE `user_name` = :user_name AND `trans_id` = :trans_id";
        $data = $connect->db->prepare($select);
        $data->bindParam(':trans_id', $transId);
        $data->bindParam(':user_name', $userName);
        $data->execute();
        $result = $data->fetchAll(PDO::FETCH_ASSOC);
        $trans = $result[0]['trans_id'];
        if ($type == "OUT" or $type == "IN"){
            if ($trans == null) {
                $select = "SELECT `balance` FROM `transfer_data_a` WHERE `user_name` = :user_name ORDER BY `id` DESC";
                $data = $connect->db->prepare($select);
                $data->bindParam(':user_name', $userName);
                $data->execute();
                $result = $data->fetchAll(PDO::FETCH_ASSOC);
                $balance = $result[0]['balance'];

                if ($type == "OUT") {
                    $balance = $balance - $amount;
                }
                if ($type == "IN") {
                    $balance = $balance + $amount;
                }

                $user = "INSERT INTO `transfer_data_a` (`user_name`, `trans_id`, `type`, `amount`, `balance`)
                    VALUES (:user_name, :trans_id, :type, :amount, :balance)";
                $insert = $connect->db->prepare($user);
                $insert->bindParam(':user_name', $userName);
                $insert->bindParam(':trans_id', $transId);
                $insert->bindParam(':type', $type);
                $insert->bindParam(':amount', $amount);
                $insert->bindParam(':balance', $balance);
                $insert->execute();

                $user_info = array("user_name" => "$userName", "message" => "Successful!");

                $user_info = json_encode($user_info);
                echo decodeUnicode($user_info);
                exit;
            } else {
                $user_info = array("message" => "Repeated TransID!");

                exit(json_encode($user_info));
            }
        } else {
            $user_info = array("message" => "Type Error!");

            exit(json_encode($user_info));
        }
    }
}

if ($api == "checkTransfer" && isset($userName) && isset($transId) && $userName != null && $transId != null)
{
    $user = "SELECT `trans_id` FROM `transfer_data_a` WHERE `user_name` = :user_name AND `trans_id` = :trans_id";
    $data = $connect->db->prepare($user);
    $data->bindParam(':user_name', $userName);
    $data->bindParam(':trans_id', $transId);
    $data->execute();
    $result = $data->fetchAll(PDO::FETCH_ASSOC);
    $transSelect = $result[0]['trans_id'];

    if ($transId == $transSelect) {
        $user_info = array("user_name" => "$userName", "massage" => "Successful!");

        $user_info = json_encode($user_info);
        echo decodeUnicode($user_info);
        exit;
    } else {
        $user_info = array("massage" => "Not Found Transaction!");

        exit(json_encode($user_info));
    }
}

$user_info = array("massage" => "Input Error!");

exit(json_encode($user_info));
