<?php 

namespace Ribbon;

require_once __DIR__ . "/Component.php";

class Vector extends Component implements \IteratorAggregate,\ArrayAccess,\Countable {
	public $_d = array();

	public function __construct($data=null) {
		if ($data!=null) {
			$this->copyFrom($data);
		}
	}
	
	public function getIterator() {
		return new ArrayIterator($this->_d); // TODO
	}
	
	public function count() {
		return count($this->_d);
	}

	public function itemAt($index) {
		if ( isset($this->_d[$index]) ) {
			return $this->_d[$index];
		} else if ($index>=0 && $index<$this->count()) {
			return $this->_d[$index];
		} else {
			throw "error"; // TODO Error
		}
	}
	
	public function add($item) {
		return $this->insertAt($this->count(),$item);
	}
	
	public function insertAt($index,$item) {
		if ($index===$this->count()) {
			$this->_d[] = $item;
		} else if ($index>=0 && $index<$this->count()) {
			array_splice($this->_d,$index,0,array($item));
		} else {
			throw "error";
		}
		return $item;
	}
	
	public function remove($item) {
		$index=$this->indexOf($item);
		if ($index>0) {
			$this->removeAt($index);
		} else {
			return false;
		}
	}
	
	public function removeAt($index) {
		if ($index>=0 && $index<$this->count()) {
			if ($index === $this->count() - 1) {
				return array_pop($this->_d);
			} else {
				$item = $this->_d[$index];
				array_splice($this->_d,$index,1);
				return $item;
			}
		} else {
			throw "Error"; // TODO
		}
	}
	
	public function clear() {
		for($i=$this->count()-1;$i>=0;--$i) {
			$this->removeAt($i);
		}
	}
	
	public function contains($item) {
		return $this->indexOf($item) >= 0;
	}
	
	public function indexOf($item) {
		if (($index=array_search($item,$this->_d,true))!==false) {
			return $index;
		} else {
			return -1;
		}
	}
	
	public function toArray() {
		return $this->_d;
	}
	
	public function copyFrom($data) {
		if (is_array($data) || ($data instanceof Traversable)) {
			if ($this->count()>0) {
				$this->clear();
			}
			if ($data instanceof Vector) {
				$data=$data->_d;
			}
			foreach($data as $item) {
				$this->add($item);
			}
		} else {
			throw "Error"; // TODO
		}
	}
	
	public function mergeWith($data) {
		if (is_array($data) || ($data instanceof Traversable)) {
			if ($data instanceof Vector) {
				$data - $data->_d;
			}
			foreach($data as $item) {
				$this->add($item);
			}
		} else if ($data!=null) {
			throw "Error"; // TODO
		}
	}
	
	public function offsetExists($offset) {
		return ($offset>=0 && $offset<$this->count());
	}
	
	public function offsetGet($offset) {
		return $this->itemAt($offset);
	}
	
	public function offsetSet($offset,$item) {
		if($offset===null || $offset===$this->count()) {
			$this->insertAt($this->count(),$item);
		} else {
			$this->removeAt($offset);
			$this->insertAt($offset,$item);
		}
	}
	
	public function offsetUnset($offset) {
		$this->removeAt($offset);
	}
}

?>