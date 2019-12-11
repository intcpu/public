<?php
require_once '../Autoload.php';
use Library\PDO\Database;


$dsn = 'mysql:host=127.0.0.1;dbname=test;charset=utf8';
$usr = 'root';
$pwd = '123456';

$pdo = new Database($dsn, $usr, $pwd);


// INSERT INTO users ( id , usr , pwd ) VALUES ( ? , ? , ? )

$stmt = $pdo->insert(array('name', 'tupian', 'url', 'status', 'add_time'))->into('yang_link')->values(array('你好sss', 'my_tupian', 'my_url', '1', time()));
$insert_id = $stmt->execute();
var_dump($insert_id);

// SELECT * FROM users WHERE id = ?

$stmt = $pdo->select(['name','add_time'])->from('yang_link')->where('id', '=', $insert_id);
$exec = $stmt->execute();
$data = $exec->fetch();
var_dump($data);



//UPDATE users SET pwd = ? WHERE id = ?

$stmt = $pdo->update(array('name' => '222'))->table('yang_link')->where('id', '=', $insert_id);
$rows = $stmt->execute();
var_dump($rows);





// DELETE FROM users WHERE id = ?
$stmt = $pdo->delete()
            ->from('yang_link')
            ->where('id', '=', $insert_id);

$rows = $stmt->execute();
var_dump($rows);