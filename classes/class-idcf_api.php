<?php
class IDCF_API {
	/*
	1. Enable sending stats
	2. When to send stats (project create, update, delete, end, sale)
	3. What to send
	*/
	var $data;
	var $methods;

	function __construct(
		$data = null,
		$methods
		) 
	{
		$this->data = $data;
		$this->method = $method;
		if (is_array($methods)) {
			foreach ($methods as $method) {

			}
		}
		else {
			// fire method
		}
	}
}
?>