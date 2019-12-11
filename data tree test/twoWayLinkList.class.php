<?php
class twoWayLinkList
{
	public $header;

	public function __construct($id,$name)
	{
		$this->header = new node($id,$name,$id,$id); 
	}

	//判断表是否为空
    public function isEmpty(){
        return $this->header == null;
    }
 
    //清空表
    public function clear(){
        $this->header = null;
    } 
}


if(!isset(get_included_files()[1]))
{
	include_once 'autoload.php';
	$link = new twoWayLinkList(1,'aaa');
	
	var_dump($link);
}

//双向表
class twoWayLinkList2
{
    public $pre=null;
    public $no;
    public $name;
    public $next=null;
    
    public function __construct($no='',$name='')
    {
        $this->no=$no;
        $this->name=$name;
    }
    
    static public function addNode($head,$hero)
    {
        $cur = $head;
        $isExist=false;
        //判断目前这个链表是否为空
        if($cur->next==null)
        {
            $cur->next=$hero;
            $hero->pre=$cur;
        }
        else
        {
            //如果不是空节点，则安排名来添加
            //找到添加的位置
            
            while($cur->next!=null)
            {
                if($cur->next->no > $hero->no)
                {
                    break;
                }
                else if($cur->next->no == $hero->no)
                {
                    $isExist=true;
                    echo "<br>不能添加相同的编号";
                }
                $cur=$cur->next;
            }
            if(!$isExist)
            {
                if($cur->next!=null)
                {
                    $hero->next=$cur->next;
                }
                $hero->pre=$cur;
                if($cur->next!=null)
                {
                    $hero->next->pre=$hero;
                }
                $cur->next=$hero;                    
            }
        }
    }
    
    //遍历
    static public function showHero($head)
    {
        $cur=$head;
        while($cur->next!=null)
        {
            echo "<br>编号：".$cur->next->no."名字：".$cur->next->name;
            $cur=$cur->next;
        }
    }
    
    static public function delHero($head,$herono)
    {
        $cur=$head;
        $isFind=false;
        while($cur!=null)
        {
            if($cur->no==$herono)
            {
                $isFind=true;
                break;
            }
            //继续找
            $cur=$cur->next;
        }
        if($isFind)
        {
            if($cur->next!=null)
            {
                $cur->next_pre=$cur->pre;
            }
            $cur->pre->next=$cur->next;
        }
        else
        {
            echo "<br>没有找到目标";
        }                
    }
}