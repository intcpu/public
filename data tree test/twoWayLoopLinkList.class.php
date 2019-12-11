<?php
//双向循环链表
class twoWayLoopLinkList{
    public $header;
    public function __construct($name=null){
        $this->header=new node($name,null,null);
    }
    public function add(){
        $cur=$this->header;
        for($i=0;$i<24;$i++){
            $q=new node();
            $q->name=$i+1;
            $q->pre=$cur;
            $q->next=$cur->next;
            $cur->next=$q;
            $cur = $q;//每次循环完$cur就成了尾巴
        }  
        //将双向链表变为循环双向链表↓↓↓↓↓↓↓↓↓↓↓
        $cur->next=$this->header->next;
        $this->header->next->pre=$cur;
        //↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
    }
    public function move($i){//正数向右，负数向左
        $cur=$this->header->next;
        if($i>0){
            do{
                $cur=$cur->pre;
            }while(--$i);
        }
        if($i<0){
            do{
                $cur=$cur->next;
            }while(++$i);
        }
        for($i=0;$i<24;$i++){
            echo "-".$cur->name."-";
            $cur=$cur->next;
        }       
    }
}