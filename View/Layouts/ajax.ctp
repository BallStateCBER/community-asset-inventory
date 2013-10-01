<?php
	$this->extend('DataCenter.ajax');
	$this->Js->buffer("removeTooltips();");
	echo $this->fetch('content');
	