<?php
include 'autoload.php';

$head = new twoWayLinkList(1,2,3,4);
// $hero1 = new twoWayLinkList(1,'1111');
// $hero3 = new twoWayLinkList(3,'3333');
// $hero2 = new twoWayLinkList(2,'2222');
// twoWayLinkList::addNode($head,$hero1);
// twoWayLinkList::addNode($head,$hero3);
// twoWayLinkList::addNode($head,$hero2);
// twoWayLinkList::showHero($head);
// twoWayLinkList::delHero($head,2);
// twoWayLinkList::showHero($head);
exit();




$header=new twoWayLoopLinkList();
$header->add();
$header->move(3);



$linklist = new loopLinkList();
$linklist->insert(1,'hello');
$linklist->insert(2,'my');
$linklist->insert(3,'love');
$linklist->insert(4,'haha4');
$linklist->insert(5,'haha5');
$linklist->insert(6,'haha6');
$linklist->insert(7,'haha7');
$linklist->insert(8,'haha8')->insert(9,'haha9')->insert(10,'haha10')->insert(11,'haha11');
 
$linklist->makecircle();
$linklist->findking(4);