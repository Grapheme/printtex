<?
class Event
{
	function Add($sEvent, $sFunc)
	{
    	$this->Handler[$sEvent][] = $sFunc;
	}

	function Execute($sEvent, &$arPar)
	{
    	$this->Error = false;

    	if(!$this->Handler[$sEvent]){
    		return false;
    	}

    	foreach($this->Handler[$sEvent] as $sFunc)
    	{
    		if($sResult = call_user_func($sFunc, &$arPar)){
    			return $sResult;
    		}
    	}
	}
}
?>