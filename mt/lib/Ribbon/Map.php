<?php 

namespace Ribbon;

require_once __DIR__ . "/Component.php";

class Map extends Component implements \IteratorAggregate,\ArrayAccess,\Countable {
	private $_d = array();
	
	public function __construct($data=null) {
		if ($data!=null) {
			$this->copyFrom($data);
		}
	}
	
	public function getIterator() {
		return new \ArrayIterator($this->_d); // TODO
	}
	
	public function count() {
		return $this->getCount();
	}
	
	public function getCount() {
		return count($this->_d);
	}
	
	public function getKeys() {
		return array_keys($this->_d);
	}
	
	public function getValues() {
		return array_values($this->_d);
	}
	
	public function itemAt($key) {
		if (isset($this->_d[$key])) {
			return $this->_d[$key];
		} else {
			return null; // TODO changable
		}
	}
	
	public function add($key,$value) {
		if ($key==null) {
			$this->_d[] = $value;
		} else {
			$this->_d[$key] = $value;
		}
	}
	
	public function remove($key) {
		if (isset($this->_d[$key])) {
			$value = $this->_d[$key];
			unset($this->_d[$key]);
			return $value;
		} else {
			unset($this->_d[$key]);
			return null;
		}
	}
	
	public function clear() {
		foreach (array_keys($this->_d) as $key) {
			$this->remove($key);
		}
	}
	
	public function contains($key) {
		return isset($this->_d[$key]) || array_key_exists($key,$this->_d);
	}
	
	public function toArray() {
		return $this->_d;
	}
	
	public function copyFrom($data) {
		if (is_array($data) || $data instanceof Traversable) {
			if ($this->getCount()>0) {
				$this->clear();
			}
			if ($data instanceof Map) {
				$data=$data->_d;
			}
			foreach($data as $key=>$value) {
				$this->add($key,$value);
			}
		} elseif ($data != null) {
			throw "Error"; // TODO
		}
	}
	
	public function mergeWith($data,$recursive=true) {
		if ( is_array($data) || $data instanceof Traversable) {
			if ($data instanceof Map) {
				$data = $data->_d;
			}
			if ($recursive) {
				if ($data instanceof Traversable) {
					$d = array();
					foreach( $data as $key=>$value ) {
						$d[$key] = $value;
					} 
					$this->_d = self::mergeArray($this->_d,$d);
				} else {
					$this->_d = self::mergeArray($this->_d,$data);
				}
			} else {
				foreach($data as $key=>$value) {
					$this->add($key,$value);
				}
			}
		} elseif ($data!=null) {
			throw "Map Error"; // TODO
		}
	}
	
	public static function mergeArray($a,$b) {
		$args = func_get_args();
		$res = array_shift($args);
		while(!empty($args)) {
			$next = array_shift($args);
			foreach($next as $k => $v) {
				if (is_integer($k)) {
					isset($res[$k]) ? $res[] = $v : $res[$k] = $v;
				} elseif (is_array($v) && isset($res[$k]) && is_array($res[$k])) {
					$res[$k] = self::mergeArray($res[$k],$v);
				} else {
					$res[$k] = $v;
				}
			}
		}
		return $res;
	}
	
	public function offsetExists($offset) {
		return $this->contains($offset);
	}
	
	public function offsetGet($offset) {
		return $this->itemAt($offset);
	}
	
	public function offsetSet($offset,$item) {
		$this->add($offset,$item);
	}
	
	public function offsetUnset($offset) {
		$this->remove($offset);
	}
}

?>