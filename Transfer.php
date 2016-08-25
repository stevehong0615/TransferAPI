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

if ($api == "addUser" && isset($userName))
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

        exit(json_encode($user_info));
    } else {
        $user_info = array("massage" => "Repeated Username!");

        exit(json_encode($user_info));
    }
}

if ($api == "getBalance" && isset($userName))
{
    $user = "SELECT `balance` FROM `transfer_data_a` WHERE `user_name` = :user_name";
    $data = $connect->db->prepare($user);
    $data->bindParam(':user_name', $userName);
    $data->execute();
    $result = $data->fetchAll(PDO::FETCH_ASSOC);
    $balance = $result[0]['balance'];

    $user_info = array("user_name" => "$userName", "balance" => "$balance");

    exit(json_encode($user_info));
}

if ($api == "transfer" && isset($userName) && isset($transId) && isset($type) && isset($amount))
{
    $select = "SELECT `trans_id` FROM `transfer_data_a` WHERE `trans_id` = :trans_id";
    $data = $connect->db->prepare($select);
    $data->bindParam(':trans_id', $transId);
    $data->execute();
    $result = $data->fetchAll(PDO::FETCH_ASSOC);
    $trans = $result[0]['trans_id'];

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

        exit(json_encode($user_info));
    } else {
        $user_info = array("message" => "Repeated TransID!");

        exit(json_encode($user_info));
    }
}

if ($api == "checkTransfer" && isset($userName) && isset($transId))
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

        exit(json_encode($user_info));
    } else {
        $user_info = array("massage" => "Not Found Transaction!");

        exit(json_encode($user_info));
    }
}

echo "API網址：https://lab-stevehong0615.c9users.io/TransferAPI/Transfer.php/API名稱?參數=值" . "<br>";
echo "1.新增帳號" . "<br>";
echo "api名稱 - addUser" . "<br>";
echo "參數1 - username(帳號)" . "<br>";
echo "2.取得餘額" . "<br>";
echo "api名稱 - getBalance" . "<br>";
echo "參數1 - username(帳號)" . "<br>";
echo "3.轉帳" . "<br>";
echo "api名稱 - transfer" . "<br>";
echo "參數1 - username(帳號)" . "<br>";
echo "參數2 - transid(轉帳序號)" . "<br>";
echo "參數3 - type(轉帳型態)(IN, OUT)" . "<br>";
echo "參數4 - amount(轉帳金額)" . "<br>";
echo "3.檢查轉帳狀態" . "<br>";
echo "api名稱 - checkTransfer" . "<br>";
echo "參數1 - username(帳號)" . "<br>";
echo "參數2 - transid(轉帳序號)" . "<br>";
