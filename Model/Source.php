<?php
App::uses('AppModel', 'Model');
class Source extends AppModel {
    var $source = 'Datum';
    var $displayField = 'name';
    var $actsAs = array('Containable');
	var $hasAndBelongsToMany = array('DataCategory');
}