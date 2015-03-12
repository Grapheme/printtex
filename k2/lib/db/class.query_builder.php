<?
class QueryBuilder
{
	function __construct()
	{		$this->Operation = 'SELECT';
	}

	function Select($sSelect = false)
	{
		$this->Operation = 'SELECT';
		if($sSelect){
			$this->Select[] = $sSelect;
		}
		return $this;
	}

	function ConcatField($sField, $isNull = false)
	{
		if($isNull){			$this->ConcatField[] = "IFNULL(".$sField.", '')";
		}else{			$this->ConcatField[] = $sField;
		}
		return $this;
	}

	function Update($sTable)
	{
		$this->Operation = 'UPDATE';
		$this->Table = $sTable;
		return $this;
	}

	function From($sTable)
	{
		$this->From[] = $sTable;
		return $this;
	}

	private function AddValue($sValue)
	{		if(is_numeric($sValue)){
        	return $sValue;
        }else{
         	return "'".DBS($sValue)."'";
        }
	}

	function Where($sWhere, $sValue = false)
	{
		if($sValue !== false){
        	$sWhere = str_replace('?', $this->AddValue($sValue), $sWhere);
		}
        $this->Where[] = $sWhere;

		return $this;
	}

	function Set($sSet, $sValue = false)
	{
		if($sValue !== false){
        	$sSet = str_replace('?', $this->AddValue($sValue), $sSet);
		}
        $this->Set[] = $sSet;

		return $this;
	}

	function AndWhere($sWhere, $sValue = false)
	{
		if($sValue !== false){
        	$sWhere = str_replace('?', $this->AddValue($sValue), $sWhere);
		}
		$this->Where[] = 'AND '.$sWhere;

		return $this;
	}

	function OrWhere($sWhere, $sValue = '')
	{
		if($sValue){
        	$sWhere = str_replace('?', $this->AddValue($sValue), $sWhere);
		}
		$this->Where[] = 'OR '.$sWhere;

		return $this;
	}

	function LeftJoin($sJoin)
	{
		$this->LeftJoin[] = 'LEFT JOIN '.$sJoin;
		return $this;
	}

	function OrderBy($sOrderBy)
	{
		$this->OrderBy[] = $sOrderBy;
		return $this;
	}

	function Limit($sLimit)
	{
		$this->Limit = $sLimit;
		return $this;
	}

	function Build()
	{
    	$arSQL = array();
    	if($this->Operation == 'SELECT'){
         	$arSQL[] = $this->Operation;
         	if($this->Num){
         		$arSQL[] = 'SQL_CALC_FOUND_ROWS';
         	}
	    	if($this->Select){
	    		$arSQL[] = implode(', ', $this->Select);
	    	}else{
	    		$arSQL[] = '*';
	    	}

	        $arSQL[] = 'FROM '.implode(', ', $this->From);

			if($this->LeftJoin){
	    		$arSQL[] = implode(' ', $this->LeftJoin);
	    	}
    	}

    	if($this->Operation == 'UPDATE'){
         	$arSQL[] = $this->Operation.' '.$this->Table.' SET ';
         	if($this->Set){
	    		$arSQL[] = implode(', ', $this->Set);
	    	}
    	}

    	if($this->ConcatField && $this->SearchText){
	    	$this->Where[] = ' AND CONCAT('.implode(', ', $this->ConcatField).') LIKE \'%'.DBS($this->SearchText).'%\'';
	    }

        if($this->Where){
    		$arSQL[] = 'WHERE '.implode(' ', $this->Where);
    	}
    	if($this->OrderBy){
    		$arSQL[] = 'ORDER BY '.implode(', ', $this->OrderBy);
    	}
    	if($this->Limit){
    		$arSQL[] = 'LIMIT '.$this->Limit;
    	}
        return implode(' ', $arSQL);
	}
}
?>