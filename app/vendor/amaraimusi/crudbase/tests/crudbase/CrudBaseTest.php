<?php 
use PHPUnit\Framework\TestCase;
use CrudBase\CrudBase;

class CrudBaseTest extends TestCase{
	
	protected $obj;
	protected function setUp() :void {
		//$this->object = new Neko();
	}
	
	public function testGetVersion(){
		$version = CrudBase::getVersion();
		$this->assertNotEmpty( $version);
	}
	
	
	public function testToThumnailPath(){
	    var_dump('testToThumnailPath');
	    
	    $orig_path = "/img/orig/12345/orig/test.jpg";
	    $thum_path = CrudBase::toThumnailPath($orig_path);
	    
	    $this->assertEquals('/img/orig/12345/thum/test.jpg', $thum_path);
	    
	}
	
	
	public function testRemoveDirectory(){
		var_dump('testRemoveDirectory');
		
		$fp0 = './tmp/neko.txt';
		$fp = './tmp/testRemoveDirectory/neko/neko2/大猫.txt';
		$res = CrudBase::copyEx($fp0, $fp);
		
		$res = CrudBase::removeDirectory('./tmp/testRemoveDirectory');
		
		$res = file_exists($fp);
		
		$this->assertFalse( $res);
	}
	
	
	public function testCopyExDirClear(){
	    
	    var_dump('testCopyExDirClear');
	    
	    $fp = './tmp/neko.txt';
	    $copy_fp = './tmp/testCopyExDirClear/neko.txt';
	    $res = CrudBase::copyEx($fp, $copy_fp);
	    $copy_fp = './tmp/testCopyExDirClear/neko2.txt';
	    $res = CrudBase::copyEx($fp, $copy_fp);
	    $copy_fp = './tmp/testCopyExDirClear/大猫.txt';
	    $res = CrudBase::copyEx($fp, $copy_fp);
	    
	    $res = CrudBase::dirClear('./tmp/testCopyExDirClear');
	    
	    
	    
	    $this->assertTrue( $res);
	}
	
	
	public function testCopy(){
		
		var_dump('testCopy');
		
		$fp = './tmp/neko.txt';
		$copy_fp = './tmp/animal/abc/bbb/neko.txt';
		
		$res = CrudBase::copyEx($fp, $copy_fp);

		$this->assertTrue( $res);
	}

	
	public function testCopyExIsDirEx(){
	    
	    var_dump('testCopyExIsDirEx');
	    
	    $dp1 = './tmp/testCopyExIsDirEx/yagi/';
	    $dp2 = './tmp/testCopyExIsDirEx/山羊/';
	    $dp3 = './tmp/testCopyExIsDirEx/yagi';
	    $dp4 = './tmp/testCopyExIsDirEx/山羊';
	    $dp5 = './tmp/testCopyExIsDirEx/dummy/';
	    $dp6 = './tmp/testCopyExIsDirEx/ダミー/';
	    
	    $res = CrudBase::isDirEx($dp1);
	    $this->assertTrue( $res);
	    
	    $res = CrudBase::isDirEx($dp2);
	    $this->assertTrue( $res);
	    
	    $res = CrudBase::isDirEx($dp3);
	    $this->assertTrue( $res);
	    
	    $res = CrudBase::isDirEx($dp4);
	    $this->assertTrue( $res);
	    
	    $res = CrudBase::isDirEx($dp5);
	    if($res === false) $res = true;
	    $this->assertTrue( $res);
	    
	    $res = CrudBase::isDirEx($dp6);
	    if($res === false) $res = true;
	    $this->assertTrue( $res);
	    
	}
	
	
	public function testMakeFilePath() {

	   $y = date('Y');
	   $field = 'img_fn';
	   $files[$field] = ['name'=> 'neko.jpg'];
	   $ent = [$field => 'neko.jpg'];
	   $path_tmpl = "storage/neko/y%Y/999/%unique/orig/%fn";
	   $path = CrudBase::makeFilePath($files, $path_tmpl, $ent, $field);

	   var_dump('テスト→CrudBase::makeFilePath');
	   var_dump($path);

	   $flg = strpos($path, "storage/neko/y{$y}/999");
	   $res = false;
	   if($flg === 0) $res = true;

	   $this->assertTrue( $res);
	   
	   
	   

	}
	
	public function testFactoryFileUploadK(){
		var_dump('testFactoryFileUploadK');//■■■□□□■■■□□□)
		$obj = CrudBase::factoryFileUploadK();
		
		$this->assertTrue( true);
	}

}