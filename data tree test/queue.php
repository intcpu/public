<?php
class queue
{

	private $head;
	private $tail;
	private $num;
	private $count;
	private $team = [];

	public function __construct(int $num)
	{
		$this->num  	= $num;
		$this->head 	= 0;
		$this->tail  	= 0;
		$this->count  	= 0;
	}

	public function push($data)
	{
		if($this->tail >= $this->num)
		{
			if($this->head == 0)
			{
				return false;
			}

			$j = 0;
			for ($i=$this->head; $i < $this->tail; $i++) { 
				$this->team[$j] = $this->team[$i];
				$j++;
			}
			$this->head = 0;
			$this->tail = $j;
		}
		
		$this->team[$this->tail++] = $data;
	}

	public function pop()
	{
		if($this->head == $this->tail) return null;

		$data = $this->team[$this->head];
		unset($this->team[$this->head++]);
		return $data;
	}

	public function loopPush($data)
	{
		//空占一个位置
		//if((($this->tail+1)%$this->num) == $this->head)
		//满的
		if($this->count == $this->num)
		{
			
			return false;
		}

		$this->team[$this->tail] = $data;

		$this->tail = ($this->tail+1)%$this->num;

		$this->count++;
	}

	public function loopPop()
	{
		if($this->head == $this->tail)
		{
			var_dump($this->head,$this->tail);
			return null;
		}

		$data = $this->team[$this->head];

		$this->team[$this->head] = null;

		$this->head = ($this->head+1)%$this->num;

		$this->count--;

		var_dump($this->head);
		return $data;
	}

	public function get()
	{
		return $this->team;
	}
}

if(!isset(get_included_files()[1]))
{
	$test = new queue(15);
	$test->loopPush(11);
	$test->loopPush(12);
	$test->loopPush(13);
	$test->loopPush(14);
	$test->loopPush(15);
	$test->loopPush(16);
	$test->loopPush(17);
	$test->loopPush(18);
	$test->loopPush(19);
	$test->loopPush(20);
	$test->loopPush(21);
	$test->loopPush(22);
	$test->loopPush(23);

	var_dump($test->get());

	var_dump($test->loopPop());
	var_dump($test->loopPop());
	var_dump($test->loopPop());
	var_dump($test->loopPop());
	var_dump($test->loopPop());

	var_dump($test->get());


	$test->loopPush(24);
	$test->loopPush(25);
	$test->loopPush(26);
	$test->loopPush(27);
	$test->loopPush(28);
	$test->loopPush(29);
	$test->loopPush(30);
	
	$test->loopPush(31);
	$test->loopPush(32);
	$test->loopPush(33);

	var_dump($test->get());
}