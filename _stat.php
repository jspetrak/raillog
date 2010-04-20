<?php

	/**
	 *	@author 	Josef Petrak (jspetrak@gmail.com)
	 */
	interface StatComparable {
		public static function compare(&$first, &$second);
        public function increment();
	}

	/**
	 *	<p>An object used for statistics. It creates a tuple of an instance and 
	 * 	number of appearance.</p>
	 *
	 *	@author		Josef Petrak (jspetrak@gmail.com)
	 *	@version	1st December 2005 ("KEG" Edition)
	 */
	class StatCountTuple implements StatComparable {  
    	private $count = 0;
    	private $object = null;
  
    	/**
	     *	<p>Constructor.</p>
    	 *
	     *	@param IdObject $object
    	 */
    	public function __construct(IdObject $object) {
            if (is_null($object)) throw new StatBadParameterException();
            
      		$this->object = $object;
      		$this->count = 1;
    	}

        /**
         *  <p>Returns the number of appearances of the object.</p>
         *
         *  @return int Count of appearances
         */
    	public function getCount() { return $this->count; }
    	
    	/**
    	 *	<p>Return the object which are the statistics made about.</p>
    	 */
    	public function getObject() { return $this->object; }

        /**
         *  <p>Retrusn the textual representation of the statisticly investigated object</p>
         *
         *  @return string Txtual representation for the stat object
         */
    	public function getObjectLabel() { return $this->object->__toString(); }
        
        /**
         *  <p>Increments the counter.</p>
         */
    	public function increment() { $this->count++; }

        /**
         *  <p>Method which compares two statistical objects - StatCountTuple. It is used to sort them
         *  in the collection.</p>
         *  @return int 0, 1, -1 accourting to the result of comparism of the objects
         */
    	public static function compare(&$first, &$second) {
      		if ($first === $second) return 0;
      		if ($first->getCount() == $second->getCount()) {
        		return strnatcmp($first->getObjectLabel(), $second->getObjectLabel());
      		} else {
        		if ($first->getCount() > $second->getCount()) {
          			return -1;
        		} else {
          			return 1;
        		}
      		}
    	}
  	}
  
  	class StatCountTriple implements StatComparable {  
    	private $count = 0;
    	private $aObject = null;
    	private $bObject = null;
    
    	public function __construct(IdObject $aObject, IdObject $bObject) {
      		if (is_null($aObject) || is_null($bObject)) throw new StatBadParametersException();
    
      		$this->count = 1;
      		$this->aObject = $aObject;
      		$this->bObject = $bObject;
    	}
    
    	public function getCount() { return $this->count; }
    	public function getAObject() { return $this->aObject; }
    	public function getBObject() { return $this->bObject; }
    	public function getAObjectLabel() { return $this->aObject->__toString(); }
    	public function getBObjectLabel() { return $this->bObject->__toString(); }
    
    	public function increment() { $this->count++; }
  
    	public static function compare(&$first, &$second) {
      		if ($first === $second) return 0;
      		if ($first->getCount() == $second->getCount()) {
        		if ($first->getAObject()->equals($second->getAObject())) {
          			return strnatcmp($first->getBObjectLabel(), $second->getBObjectLabel());
        		} else {
          			return strnatcmp($first->getAObjectLabel(), $second->getAObjectLabel());
        		}
      		} else {
        		return ($first->getCount() > $second->getCount()) ? -1 : 1;
      		}
    	}
  	}
  
  	/**
  	 *	<p>Exception which occurs when the new created statistics object receives
  	 * 	bad initial data - null object.</p>
  	 */
  	class StatBadParametersException extends Exception {}

?>