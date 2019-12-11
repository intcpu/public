<?php
include_once 'autoload.php';
class singelLink{
	private static $header;


	private static function init(Node $node)
	{
		self::$header = $node;
		self::$header->last = null;
		self::$header->next = null;
	}

	public static function addNode(Node $node)
	{
		if(self::$header == null)
		{
			self::init($node);
		}
		else
		{
			$index = self::$header;
			while($index->next) {
				if($index->next->id > $node->id)
				{
					break;
				}
				$index = $index->next;
			}
			$node->next = $index->next;
			$index->next = $node;
		}
		return true;
	}

	public static function delNode($id)
	{
		$index = self::$header;
		$last  = null;
		while (isset($index->id)) {
			if($index->id == $id)
			{
				if($last)
				{
					$last->next = $index->next;
				}
				else
				{
					self::$header = $index->next;
				}
				return true;
				break;
			}
			$last  = $index;
			$index = $index->next;
		}
		return false;
	}

	public static function isEmpty()
	{
		return self::$header == null;
	}

    //清空表
    public static function clear()
	{
        self::$header = null;
        return true;
    }

    public static function getLength()
    {
    	$index = self::$header;
    	if(empty($index)) return 0;

    	$i = 1;
    	while ($index->next) {
    		$i++;
    		$index = $index->next;
    	}
    	return $i;
    }

    public static function getNodeById($id)
    {
    	$index = self::$header;

    	if(empty($index)) return [];

    	while ($index->next) {
    		if ($index->id == $id) {
    			return $index->name;
    			break;
    		}
    		$index = $index->next;
    	}

    	return [];
    }

	public static function getList()
	{
		if(empty(self::$header)) return [];

		$lists = [];

		$index = self::$header;
		while ($index->next) {
			$lists[$index->id] = $index->name;
			$index = $index->next;
		}
		$lists[$index->id] = $index->name;

		return $lists;
	}

	//反转表
	public static function reversalList()
	{
		$index = self::$header;
		$last  = null;
		while ($index->next) {

			$last  = $index;
			$index = $index->next;
		}
	}
}

if(!isset(get_included_files()[1]))
{
	singelLink::addNode(new node(1111,'aaaaaa'));
	singelLink::addNode(new node(4444,'ddddd'));
	singelLink::addNode(new node(5555,'eeeeee'));
	singelLink::addNode(new node(3333,'ccccc'));
	singelLink::addNode(new node(2222,'bbbbbb'));

	singelLink::delNode(2222);
	singelLink::delNode(5555);
	singelLink::delNode(1111);

	var_dump(singelLink::isEmpty());
	var_dump(singelLink::getNodeById(1111));
	var_dump(singelLink::getList());
	var_dump(singelLink::getLength());
}

//单链表 
class singelLinkList {
    private $header; //表头节点 
   
    //构造方法 
    public function __construct($id = null, $name = null) { 
        $this->header = new node($id, $name); 
        var_dump($this->header);
    }
 
    //获取表长度 
    public function getLinkLength() {
        $i = 0; 
        $current = $this->header; 
        while ( $current->next != null ) { 
            $i ++; 
            $current = $current->next; 
        }
        return $i; 
    }
 
    //添加节点数据 
    public function addLink($node) {
        $current = $this->header; 
        while ( $current->next != null ) { 
            if ($current->next->id > $node->id) { 
                break; 
            }
            $current = $current->next; 
        }
        $node->next = $current->next; 
        $current->next = $node;
        return true;
    } 
 
    //删除表节点 
    public function delLink($id) { 
        $current = $this->header; 
        $flag = false; 
        while ( $current->next != null ) { 
            if ($current->next->id == $id) { 
                $flag = true; 
                break; 
            }
            $current = $current->next; 
        } 
        if ($flag) {
            $current->next = $current->next->next; 
            return true;
        } else {
            return false;
        } 
    }
 
    //判断表是否为空
    public function isEmpty(){
        return $this->header == null;
    }
 
    //清空表
    public function clear(){
        $this->header = null;
    }
 
    //获取表 
    public function getLinkList() { 
        $current = $this->header; 
        if ($current->next == null) {
            return []; 
        }
        $links = [];
        while ($current->next != null ) {
            $links[$current->next->id] = $current->next->name;
            if ($current->next->next == null) { 
                break; 
            } 
            $current = $current->next; 
        }
        return $links;
    }
 
    //获取节点名字 
    public function getLinkNameById($id) { 
        $current = $this->header; 
        if ($current->next == null) {  
            return []; 
        } 
        while ( $current->next != null ) { 
            if ($current->id == $id) {
                break; 
            } 
            $current = $current->next; 
        }
        return $current->name; 
    }
 
    //更新节点名称 
    public function updateLink($id, $name) { 
        $current = $this->header; 
        if ($current->next == null) { 
            return []; 
        } 
        while ( $current->next != null ) { 
            if ($current->id == $id) { 
                break; 
            } 
            $current = $current->next; 
        } 
        return $current->name = $name; 
    } 
}

if(!isset(get_included_files()[1]))
{
	include_once 'autoload.php';
	$lists = new singelLinkList ();
	$lists->addLink ( new node ( 5, 'eeeeee' ) ); 
	$lists->addLink ( new node ( 1, 'aaaaaa' ) ); 
	$lists->addLink ( new node ( 6, 'ffffff' ) ); 
	$lists->addLink ( new node ( 4, 'dddddd' ) ); 
	$lists->addLink ( new node ( 3, 'cccccc' ) ); 
	$lists->addLink ( new node ( 2, 'bbbbbb' ) ); 


	var_dump($lists->getLinkList ());


	$lists->delLink ( 5 ); 
	var_dump($lists->getLinkList ());
	$lists->updateLink ( 3, "222222" ); 
	var_dump($lists->getLinkList ());
	$lists->delLink ( 5 ); 
	var_dump($lists->getLinkList ());
	var_dump($lists->getLinkNameById (5));
	var_dump($lists->getLinkLength ()); 
}