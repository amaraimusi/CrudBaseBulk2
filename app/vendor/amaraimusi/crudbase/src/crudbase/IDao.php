<?php
namespace CrudBase;

interface IDao{
	public function sqlExe($sql);
	public function begin();
	public function rollback();
	public function commit();
}