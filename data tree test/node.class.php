<?php
//链表节点 
class node {
    public $id; 	//节点id 
    public $name; 	//节点名称 
    public $last; 	//上一节点 
    public $next; 	//下一节点 
   
    public function __construct($id,$name,$next = null,$last = null) { 
        $this->id 	= $id; 
        $this->name = $name; 
        $this->last = $last; 
        $this->next = $next;
    }
}