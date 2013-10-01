<?php
	$this->extend('DataCenter.print');
	
	$this->start('css');
		echo '<link rel="stylesheet" type="text/css" href="/css/print.css" media="screen, print" />';
	$this->end();
	
	echo $this->fetch('content');