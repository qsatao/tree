<?php
namespace Lib\Core;
/**
 * 树型数组处理类
 * @author tao
 * @example
 *
 * $data = array(

				0=>array(
						'id'=>1,
						'pid'=>0,
						'name'=>'分类一'
				),

				1=>array(
						'id'=>2,
						'pid'=>0,
						'name'=>'分类二'
				),

				2=>array(
						'id'=>3,
						'pid'=>2,
						'name'=>'分类三'
				),

				3=>array(
						'id'=>4,
						'pid'=>0,
						'name'=>'分类四'
				),

				4=>array(
						'id'=>5,
						'pid'=>3,
						'name'=>'分类五'
				),
		);
		$tree = new Tree();
		$tree->setData($data);
		$tree->setPidKey('pid');
		print_r($tree->toArray());
 *
 */

class Tree {
	protected $_data = array ();
	protected $_treeData = array();
	protected $_pidKey = 'parent_id';
	protected $_idKey = 'id';
	protected $result_2array = array ();
	protected $result_3array = array();

	public function setData($data=array()) {
		$this->_data = $data;
		return $this;
	}

    public function resetResult(){
        $this->result_2array = array();
        $this->result_3array = array();
    }
	/**
	 * 设置父ID在数组中的KEY
	 *
	 * @param $key string
	 */
	public function setPidKey($key = 'parent_id') {
		$this->_pidKey = $key;
		return $this;
	}

	/**
	 * 设置ID在数组中的KEY
	 *
	 * @param $key string
	 */
	public function setIdKey($key = 'id') {
		$this->_idKey = $key;
		return $this;
	}

	/**
	 * 取得树形结构
	 *
	 * @param $pid int 父ID
	 */
	public function getTree($pid = 0) {
		$result = $this->convert($pid);
		return  $result;
	}

	/**
	 * 转换成树形结构
	 *
	 * @param $pid int 父ID
	 */
	public function convert($pid = 0,$deep = 0){
		$newArr = array ();
		$deep++;
		foreach ( $this->_data as $k => $v ) {
			if ($v [$this->_pidKey] == $pid) {
				$v['deep'] = $deep;
				$tmp = $this->convert ( $v [$this->_idKey] ,$deep);
				if (count ( $tmp ) > 0) {
					$v ['children'] = $tmp;
				} else {
					$v ['islast'] = 1;
				}
				$newArr [] = $v;
			}
		}
		return $newArr;
	}

	/**
	 * 查找父类
	 */
	public function getParents($pid,$arr=null){
		if(is_null($arr)){
			$arr = $this->toArray();
		}

		foreach ( $arr as $k => $v ) {
			if($pid==$v[$this->_idKey]){
				$this->result_2array[] = $v;
				if((int)$v[$this->_pidKey]>0){
					$this->getParents($v[$this->_pidKey],$arr);
				}
			}
		}
		return $this->result_2array;
	}

	/**
	 *查找子类
	*/

	public function getChildrens($child, $data = null){
		if(is_null($data)){
			$data = $this->toArray();
		}

		foreach($data as $dk => $dv){
			if($child == $dv[$this->_pidKey]){
				$this->result_3array[] = $dv;
				if((int)$dv[$this->_idKey]>0){
					$this->getChildrens($dv[$this->_idKey], $data);
				}
			}
		}
		return $this->result_3array;
	}

	/**
	 * 转换成二维数组
	 * @param array $arr
	 */
	public function toArray($arr=null) {
		if(is_null($arr)){
			$arr = $this->getTree();
		}
		foreach ( $arr as $k => $v ) {
			$children = array();
			if(isset($v ['children'])){
				$children = $v ['children'];
				unset ( $v ['children'] );
			}
			$this->result_2array[] = $v;
			if (count ( $children )>0) {
				$this->toArray ( $children );
			}
		}
		return $this->result_2array;
	}

}