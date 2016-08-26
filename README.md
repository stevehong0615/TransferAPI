# TransferAPI

API規則
API網址：https://lab-stevehong0615.c9users.io/TransferAPI/Transfer.php/API名稱?參數=值

1.新增帳號

api名稱 - addUser

參數1 - username(帳號)


2.取得餘額

api名稱 - getBalance

參數1 - username(帳號)


3.轉帳

api名稱 - transfer

參數1 - username(帳號)

參數2 - transid(轉帳序號)

參數3 - type(轉帳型態)(IN, OUT)

參數4 - amount(轉帳金額)


4.檢查轉帳狀態

api名稱 - checkTransfer

參數1 - username(帳號)

參數2 - transid(轉帳序號)


addUser
https://lab-stevehong0615.c9users.io/TransferAPI/Transfer.php/addUser?username=Steve

getBalance
https://lab-stevehong0615.c9users.io/TransferAPI/Transfer.php/getBalance?username=Steve

transfer 轉出
https://lab-stevehong0615.c9users.io/TransferAPI/Transfer.php/transfer?username=Steve&transid=1&type=OUT&amount=500

transfer 轉入
https://lab-stevehong0615.c9users.io/TransferAPI/Transfer.php/transfer?username=Steve&transid=1&type=IN&amount=500

checkTransfer
https://lab-stevehong0615.c9users.io/TransferAPI/Transfer.php/checkTransfer?username=Steve&transid=7
