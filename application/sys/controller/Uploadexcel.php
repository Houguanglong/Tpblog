<?php

namespace app\sys\controller;

use think\Controller;
use think\Db;
use think\Session;
use app\sys\transfer\Excel as CommonExcel;
use app\sys\controller\Upload as ExcelUpload;

class Uploadexcel extends Controller{
	 public function upload_data(){
	 	  $ExcelUpload=new ExcelUpload();
	 	  $my=$ExcelUpload->upload();
	 	  $my=str_replace('\/', '/', $my);
	 	  $my=json_decode($my,true);
	 	  if(!$my['error']){
		 	  $Excel=new CommonExcel('Excel5');
		 	  $file=str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'].$my['url']);
		 	  $var=$Excel->import($file);
		 	    $is_upload_ok=0;
				Db::startTrans();
				try{

					$tamp_question_data=$temp_answer_data=array();

					foreach ($var[0] as $key => $value) {
						$question_data['stem']=$value['stem'];//问题
						$question_data['stem_image']='';//题目图片
						$question_data['analysis']=$value['analysis'];//解析
						$question_data['errorcount']='';//错误次数
						$question_data['subject_type_id']='1';//科目id
						$question_data['topic_type']=$value['topic_type'];//题目类型（0、1）
						$question_data['chapter_id']=$value['chapter_id'];//章节id（第几章节）
						$question_data['chapter_title']=$value['chapter_title'];//章节标题
						$question_data['certificates_id']=1;//证件类型（无用）
						$question_data['subject_id']=0;//科目id（无用）
						$question_data['add_time']=time();//创建时间
						$question_data['models_id']=0;//（无用）
						$question_data['city_id']=$_POST['city'];//城市id
						$question_data['status']=1;//试题状态
					    $get_question_id=Db::name('driving_question')->insertGetId($question_data);
					    /****************************答案*****************************************/
					    $answer_arr=explode(',', $value['option']);
					    foreach ($answer_arr as $k => $v) {
					    	$answer_data['question_id']=$get_question_id;
					    	$answer_data['answer']=$v;
					    	if(($k+1)==$value['correct']){
					    		$answer_data['correct']=1;
					    	}
					    	else{
					    		$answer_data['correct']=0;
					    	}
					    	$get_answer_id=Db::name('driving_answer')->insertGetId($answer_data);
					    	$answer_data['answer_id']=$get_answer_id;
					    	array_push($temp_answer_data,$answer_data);
					    	unset($answer_data);
					    }
					    /****************************答案*****************************************/
					    $question_data['question_id']=$get_question_id;
					    $question_data['answer']=$temp_answer_data;
					    array_push($tamp_question_data,$question_data);
						/********格式化问题答案json存放在服务器******/
						$save_path=$_SERVER['DOCUMENT_ROOT'].'data/question/';
						$myfile = fopen($save_path.$get_question_id.".json", "w") or die("Unable to open file!");
						fwrite($myfile, json_encode($question_data));
						fclose($myfile);
						/********格式化问题答案json存放在服务器******/
					    unset($question_data);
					}
				    Db::commit();
				    $is_upload_ok=1;
				} catch (\Exception $e) {
				    Db::rollback();
				}
				if($is_upload_ok){
					$this->success('导入成功',U('Uploadexcel/upload_yemian'));
				}
				else{
					$this->success('导入失败',U('Uploadexcel/upload_yemian'));
				}
	 	  }
	 	  else{
	 	  	$this->success('上传失败',U('Uploadexcel/upload_yemian'));
	 	  }
	 }
	 public function upload_yemian(){
	 	return $this->fetch();
	 }


}