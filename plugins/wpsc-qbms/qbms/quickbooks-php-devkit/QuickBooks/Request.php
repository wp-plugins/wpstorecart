<?php

/**
 * QuickBooks request base class
 * 
 * Copyright (c) {2010-04-16} {Keith Palmer / ConsoliBYTE, LLC.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.opensource.org/licenses/eclipse-1.0.php
 * 
 * @author Keith Palmer <keith@consolibyte.com>
 * @license LICENSE.txt
 * 
 * @package QuickBooks
 * @subpackage Client
 */

/**
 * QuickBooks result base class
 */
abstract class QuickBooks_Request
{
	/**
	 * Placeholder constructor method
	 */
	abstract public function __construct($var);
	
	/**
	 * 
	 * 
	 * @param string $key
	 * @param mixed $value
	 * @return void
	 */
	public function set($key, $value)
	{
		$this->$key = $value;
	}
}

