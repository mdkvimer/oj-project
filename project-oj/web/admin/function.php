<?
function uploadFile($file,$filetempname) //（文件名，临时文件名）
{
	//自己设置的上传文件存放路径
	$filePath = '../upload/';
	$str = "";
	//包含PHPExcel的相关文件
	require_once '../Classes/PHPExcel.php';
	require_once '../Classes/PHPExcel/IOFactory.php';
	require_once '../Classes/PHPExcel/Reader/Excel5.php';
	$filename=explode(".",$file);//把上传的文件名以“.”号为准做一个数组。
	$time="test";//取当前上传的时间
	$filename[0]=$time;//以上传时间为临时文件文件
	$name=implode(".",$filename); //上传后的文件名
	$uploadfile=$filePath.$name;//上传后的文件名地址
	//move_uploaded_file() 函数将上传的文件移动到新位置。若成功，则返回 true，否则返回 false。
	$result=move_uploaded_file($filetempname,$uploadfile);//假如上传到当前目录下
	if($result){ //如果上传文件成功，就执行导入excel操作
		$objReader = PHPExcel_IOFactory::createReader('Excel5');
		//Excel5支持excel2007格式，office向下支持所以同样支持2003
		$objPHPExcel = $objReader->load($uploadfile); //加载文件
		$sheet = $objPHPExcel->getSheet(0); // 默认读第一张表
		$highestRow = $sheet->getHighestRow(); // 取得总行数
		$highestColumn = $sheet->getHighestColumn(); // 取得总列数
		//循环读取excel文件,读取一条，将其插入数据库
		for($j=2;$j<=$highestRow;$j++)
		{
			for($k='A';$k<=$highestColumn;$k++)
			{
				$str .= iconv("utf-8","utf-8",$objPHPExcel->getActiveSheet()->getCell("$k$j")->getValue()).'\\';//读取单元格
			}
		//explode:函数把字符串分割为数组。
		$strs = explode("\\",$str);
		$sql = "insert into users(`user_id`,`nick`,`email`,`password`,`accesstime`,`reg_time`,`ip`) VALUES('".$strs[0]."','".$strs[1]."','".$strs[2]."','".md5($strs[3])."',NOW(),NOW(),'".$_SERVER['REMOTE_ADDR']."')";
		#$sql = "desc users";	
		//echo "sqlStr=".$sql."<br />";
		mysql_query("set names 'utf8'");//这就是指定数据库字符集，一般放在连接数据库后面就系了
		if(!mysql_query($sql))
		{
		//提示出错信息
			echo "error:".mysql_error();
			echo "successed <span class='red'>".($j-2)."</span>
			lines<br /><span class='red'>Import wrong!!!</span>";
			unlink($uploadfile); //删除上传的excel文件
			return false;
		}
		//$tmpStr="import_".($j-1)." :
		//".$strs[0].",".$strs[1].",".$strs[2].",".$strs[3]."
		//successed.<br />";
		$tmpStr="Register user :
		user_id = ".$strs[0]." password = ".$strs[3]."
		successed.<br />";
		echo $tmpStr;
		$str = "";
		unlink($uploadfile); //删除上传的excel文件
		$msg = "all successed!<br />";
	}}else{//file does not exist
	$msg = "file does not exist!<br>Please first choose file.";
	}
	//整个文件读取成功
	return $msg;
}
?>
