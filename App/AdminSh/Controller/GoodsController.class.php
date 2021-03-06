<?php
namespace AdminSh\Controller;
use Think\Controller;
class GoodsController extends CommonController {
	
	
    public function index(){
	echo 123;
    	$this->display();
	}
	
	//出售中
	public function selling(){
	
	$list=$this->goods_commem($str=$_POST["str"],$goods_state=0,$str_f);
		$this->goods=$list; 
		//dump($list);die;
		$this->program_id=$_COOKIE["program_id"];
		//获取商品属性      -------根据小程序id 查询小程序表 在里面得到小程序属于什么类的小程序，再查对应的属性表
		$shuxign_lei=M("muban")->where("program_id='".$_COOKIE["program_id"]."'")->find();
		$this->shuxing_list=M("shuxing")->where("banbenleixing_type='".$shuxign_lei["muban_type"]."'")->select();
			
		$this->display();
	}
	//售罄
	public function haved_selled(){
		$list=$this->goods_commem($str,$goods_state=2,$str_f);
		$this->goods=$list;
		//获取商品属性      -------根据小程序id 查询小程序表 在里面得到小程序属于什么类的小程序，再查对应的属性表
		$shuxign_lei=M("muban")->where("program_id='".$_COOKIE["program_id"]."'")->find();
		$this->shuxing_list=M("shuxing")->where("banbenleixing_type='".$shuxign_lei["muban_type"]."'")->select();
		
		$this->display();
	}
	//仓库中
	public function cangku(){
		$list=$this->goods_commem($str,$goods_state=3,$str_f);
		$this->goods=$list;
		//获取商品属性      -------根据小程序id 查询小程序表 在里面得到小程序属于什么类的小程序，再查对应的属性表
		$shuxign_lei=M("muban")->where("program_id='".$_COOKIE["program_id"]."'")->find();
		$this->shuxing_list=M("shuxing")->where("banbenleixing_type='".$shuxign_lei["muban_type"]."'")->select();
		
		$this->display();
	}
	//回收站
	public function huishou(){
		$list=$this->goods_commem($str,$goods_state=4,$str_f);
		$this->goods=$list;
		//获取商品属性      -------根据小程序id 查询小程序表 在里面得到小程序属于什么类的小程序，再查对应的属性表
		$shuxign_lei=M("muban")->where("program_id='".$_COOKIE["program_id"]."'")->find();
		$this->shuxing_list=M("shuxing")->where("banbenleixing_type='".$shuxign_lei["muban_type"]."'")->select();
		
		$this->display();
	}
	
	//商品公用函数
	function goods_commem($str,$goods_state,$str_f){
		$where["program_id"]=$_COOKIE["program_id"];
		$where["dsh_id"]=$_COOKIE["admin_dsh_id"];//登录的子商户的id
		if($goods_state==0){
			$where["goods_state"]=array("in","1,3");//只显示上架和下架的
		}else{
			$where["goods_state"]=$goods_state;
		}
		
		//两个字段的模糊搜索  str=》id、名称    str_f=》分类
		 if(isset($str)){
				//$str=$str;     //如果接收到搜索关键字,执行模糊查询 返回数据
				
				//模糊查询 id、名称、编号、条码、商户名称
				$where['_string']='
					(id like "%'.$str.'%")  
					OR (goods_title like "%'.$str.'%")
				';
				$list=M("goods")
				->field('id,sort,image,goods_title,goods_state,price_sell,fenlei_1,fenlei_2,is_jingpin')
				->where($where)
				->order("sort")
				->select();//对id、名称的模糊搜索
		
			}else if(isset($str_f)){
			//根据商品分类的id查询 商品列表	
				$list= M("goods")->query("
					select * from 
					goods 
					left outer join  
					( select * from attribute where id='".$str_f."'"."  ) c 
					on goods.id=c.goods_id
					where goods.program_id='".$_COOKIE["program_id"]."'"." and goods.id='".$goods_state."'"." 	
				");
			
			}else{
				$list=M("goods")
				->field('id,sort,image,goods_title,number,goods_state,price_sell,sell_num,fenlei_1,fenlei_2,goods_subtitle01,goods_subtitle02,goods_subtitle03,goods_subtitle04,goods_subtitle05,is_jingpin')
				->where($where)
				->order("sort")
				->select();
			}
	
				for($i=0;$i<count($list);$i++){
					$arr1102=explode(",",$list[$i]['image']);
					$list[$i]["image"]=$arr1102[0];
				}
			return $list;	
	}
	
	
	//商品分类
	public function fenlei(){

		$fl=M("attribute")
		->field('id,uid,title,image_1,is_show,dsh_id')
		->where("dsh_id='".$_COOKIE["admin_dsh_id"]."'". " and program_id='".$_COOKIE["program_id"]."'" )
		->select();
		$all_fenlei=$this->fenlei=$this->bq($fl);
		
		$this->display();
	}
	
	public function fenlei_add(){
		
		$this->display();
	}
	//添加子分类
	public function fenlei_add02(){
		$this->add_child=$_GET["id"];
		
		$this->display('fenlei_add');
	}
	
	public function fenlei_edit(){
		 $a= $this->info=M('attribute')->where("id=".$_GET['id'])->find();
	
		$this->display('fenlei_add');
	}
	
	public function run_fenlei_add(){
		$receive=I();
		$receive["program_id"]=$_COOKIE["program_id"];
			$upload = new \Think\Upload();// 实例化上传类
			    $upload->maxSize   =     3145728 ;// 设置附件上传大小
			    $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
			    $upload->rootPath  =     './Uploads/'; // 设置附件上传根目录

			    // 上传文件 
			    $info   =   $upload->upload();
			    
		 if(empty($_GET['id'])) {
		 	//执行添加操作
			 if($info){
                    $receive['image_1']=$info['image02']['savepath'].$info['image02']['savename'];
					//$receive["uid"]=0;
					//dsh_id='".$_COOKIE["admin_dsh_id"]."'". " and
					$receive["dsh_id"]=$_COOKIE["admin_dsh_id"];
				     if(M('attribute')->add($receive)){
				    	$this->success('添加成功',U('fenlei'));
				    }else{
				    	$this->error('添加失败');	
				    }
			    
			    }else{
			    	$receive["dsh_id"]=$_COOKIE["admin_dsh_id"];
			    	if(M('attribute')->add($receive)){
				    	$this->success('添加成功',U('fenlei'));
				    }else{
				    	$this->error('添加失败');	
				    }
			    }
            
        }else {
        	//执行修改操作
			if($info){
                  $receive['image_1']=$info['image02']['savepath'].$info['image02']['savename'];

			     if(M('attribute')->where(array('id'=>$_GET['id']))->save($receive)){
			    	$this->success('修改成功2',U('fenlei'));
			    }else{
			    	$this->error('修改失败2');	
			    }
			    
			    }else{
			  if(M('attribute')->where(array('id'=>$_GET['id']))->save($receive)){
			    	$this->success('修改成功02',U('fenlei'));
			    }else{
			    	$this->error('修改失败02');	
			    }
			}
       }   
	}
		public function fenlei_delete(){
				 $user_id =$_GET['id'];
			$d_zhu_img=M('attribute')->where("id='".$_GET['id']."'")->find();
	        $d_zi_img=M('attribute')->where("uid='".$_GET['id']."'")->select();
	        	
	        if(M('attribute')->delete($user_id)) {
	        	//删除主类图片
	        	unlink('./Uploads/'.$d_zhu_img["image_1"]);
	        	
	        	
	        	//删除 所属 子类 图片
	        	for($i=0;$i<count($d_zi_img);$i++){
	        		unlink('./Uploads/'.$d_zi_img[$i]["image_1"]);
	        	}
	        	
	        	//删除 所属 子类  数据
	        	$all["uid"]=$_GET['id'];
	        	M('attribute')->delete($all);
	            $this->success('删除成功');
	        } else {
	            $this->error('删除失败');
	        }
		}
		public function fenlei_delete02(){
			
			
				$d_img=M('attribute')->where("id='".$_GET['id']."'")->find();
				 $user_id = $_GET['id'];
	        if(M('attribute')->delete($user_id)) {
	        	//删除对应子类图片
	        	
	        	unlink('./Uploads/'.$d_img["image_1"]);
	        	
	       
	           $this->success('删除成功');
	        } else {
	           $this->error('删除失败');
	        }
		}
	//异步修改属性  =======开始
	//ajax_change_shuxing01
	public function ajax_change_shuxing01(){
		$where["program_id"]=$_COOKIE["program_id"];
		$where["id"]=$_POST["id"];
		
		
		
		$a=M("goods")->where($where)->find();
			
		if($a["goods_subtitle01"]==1){
			$date["goods_subtitle01"]="2";//（1：选中、2：不选）
		}else if(($a["goods_subtitle01"]==2)){
			$date["goods_subtitle01"]="1";//（1：选中、2：不选）
		}
		
		if(M("goods")->where($where)->save($date)){
			$ajax_back=$date["goods_subtitle01"];
		}else{
			$ajax_back=0;
		}
			$this->ajaxReturn($ajax_back);
	}
	//ajax_change_shuxing02
	public function ajax_change_shuxing02(){
		$where["program_id"]=$_COOKIE["program_id"];
		$where["id"]=$_POST["id"];
		
		$a=M("goods")->where($where)->find();
			
			
			if($a["goods_subtitle02"]==1){
				$date["goods_subtitle02"]="2";//（1：选中、2：不选）
			}else if(($a["goods_subtitle02"]==2)){
				$date["goods_subtitle02"]="1";//（1：选中、2：不选）
			}
	
		if(M("goods")->where($where)->save($date)){
			$ajax_back=$date["goods_subtitle02"];
		}else{
			$ajax_back=0;
		}
			$this->ajaxReturn($ajax_back);
	}
	//ajax_change_shuxing03
	public function ajax_change_shuxing03(){
		$where["program_id"]=$_COOKIE["program_id"];
		$where["id"]=$_POST["id"];
		
		$a=M("goods")->where($where)->find();
			
		if($a["goods_subtitle03"]==1){
			$date["goods_subtitle03"]="2";//（1：选中、2：不选）
		}else if(($a["goods_subtitle03"]==2)){
			$date["goods_subtitle03"]="1";//（1：选中、2：不选）
		}
		
		if(M("goods")->where($where)->save($date)){
			$ajax_back=$date["goods_subtitle03"];
		}else{
			$ajax_back=0;
		}
			$this->ajaxReturn($ajax_back);
	}
	//ajax_change_shuxing04
	public function ajax_change_shuxing04(){
		$where["program_id"]=$_COOKIE["program_id"];
		$where["id"]=$_POST["id"];
		
		$a=M("goods")->where($where)->find();
			
		if($a["goods_subtitle04"]==1){
			$date["goods_subtitle04"]="2";//（1：选中、2：不选）
		}else if(($a["goods_subtitle04"]==2)){
			$date["goods_subtitle04"]="1";//（1：选中、2：不选）
		}
		
		if(M("goods")->where($where)->save($date)){
			$ajax_back=$date["goods_subtitle04"];
		}else{
			$ajax_back=0;
		}
			$this->ajaxReturn($ajax_back);
	}
	//ajax_change_shuxing05
	public function ajax_change_shuxing05(){
		$where["program_id"]=$_COOKIE["program_id"];
		$where["id"]=$_POST["id"];
		
		$a=M("goods")->where($where)->find();
			
		if($a["goods_subtitle05"]==1){
			$date["goods_subtitle05"]="2";//（1：选中、2：不选）
		}else if(($a["goods_subtitle05"]==2)){
			$date["goods_subtitle05"]="1";//（1：选中、2：不选）
		}
		
		if(M("goods")->where($where)->save($date)){
			$ajax_back=$date["goods_subtitle05"];
		}else{
			$ajax_back=0;
		}
			$this->ajaxReturn($ajax_back);
	}
	
	 //异步删除图片
	public function ajax_delete_img(){
		//得到那条  好消息 的id、和对应的图片的位置键值，对数据的字符串进行删减
		
		$info=M('goods')->where("id='".$_POST["id"]."'")->find();
		$arr=explode(",",$info["image"]);//字符串转换成数组
		for($i=0;$i<count($arr);$i++){
			if($i != $_POST["key"]){
				$arr_new[]=$arr[$i];
			}
		}
		$str_new["image"]=implode(",",$arr_new);//数组转换成 字符串   这个就是新的需要存的数据
		if($str_new==","){
			$str_new=null;
		}
		if(M("goods")->where("id='".$_POST["id"]."'")->save($str_new)){
			
			unlink('./Uploads/'.$arr[$_POST["key"]]);
			
			$date="1";//保存并删除旧图片成功
		}else{
			$date="2";//操作失败
		}
		$this->ajaxReturn($date);
	}
	
	//异步修改属性 =======结束
	
	
	public function goods_add(){
		 
		 //一级分类名 dsh_id='".$_COOKIE["admin_dsh_id"]."'". " and
		 	$this->fenlei=M("attribute")
		->field('id,uid,title')
		->where("uid=0 and dsh_id='".$_COOKIE["admin_dsh_id"]."'". " and program_id='".$_COOKIE["program_id"]."'")->select();
		$this->display();
	}
	public function goods_edit(){
			$info=M('goods')->where("id=".$_GET['id'])->find();
		 
	 //商品所有图片
			$arr=explode(",",$info["image"]);
			
			$info["image"]=$arr;
		//	dump($info);die;
			//获得商品参数
		$canshu=M("specifications")->where("goods_id='".$_GET['id']."'")->find();		
		$this->canshu=	$arr=$this->str_to_arr($canshu["title"],$canshu["content"]);
		
		 //一级分类名
		 	$fenlei1611=M("attribute")->where("id='".$info["fenlei_1"]."'")->find();
		 	$info["fenlei_1_title"]=$fenlei1611["title"];
		 	//二级分类名
		 	$fenlei1612=M("attribute")->where("id='".$info["fenlei_2"]."'")->find();
		 	$info["fenlei_2_title"]=$fenlei1612["title"];
		 
		 $this->info=$info;
		//遍历商品分类
		$this->fenlei=M("attribute")
		->field('id,uid,title')
		->where("uid=0 and dsh_id='".$_COOKIE["admin_dsh_id"]."'". " and program_id='".$_COOKIE["program_id"]."'")->select();

		$this->is_edit="1";
		$this->display('goods_add');
	}

	//异步获得子类
	public function ajax_fenlei_2(){
		//需要主类 id
		$where01["uid"]="0";
		$where01["title"]=$_POST["title"];
		$where01["program_id"]=$_COOKIE["program_id"];
		
		
		$a=M("attribute")->where($where01)->find();
		$b=M("attribute")->where("uid='".$a["id"]."'")->select();
		 for($i=0;$i<count($b);$i++){
            $arr_road[$i]=$b[$i]["title"];

        }
		$this->ajaxReturn($arr_road);
	}
	public function run_goods_add(){
			$_POST["dsh_id"]=$_COOKIE["admin_dsh_id"];
				
				
			//处理会员价=============
			for($i1130=0;$i1130<count($_POST["lever_id"]);$i1130++){
				if(!empty($_POST["price_mem"][$i1130])){	
					$where1130["program_id"] = $_COOKIE["program_id"];
					$where1130["goods_id"]   = $_GET["id"];
					$where1130["lever_id"]   = $_POST["lever_id"][$i1130];
	
					$date1130["price"]		 = $_POST["price_mem"][$i1130];//默认价格、不是规格的
					$date1130["discount"]	 = $_POST["discount"][$i1130];
					$member_price1130=M("member_price")->where($where1130)->find();
					if(!empty($member_price1130)){
						//修改 fenlei_2_title
						M("member_price")->where($where1130)->save($date1130);	
					}else{
						//添加
							$add1130["program_id"]	= $_COOKIE["program_id"];
							$add1130["goods_id"]	= $_GET["id"];
							$add1130["lever_id"]	= $_POST["lever_id"][$i1130];
							$add1130["price"]	 	= $_POST["price_mem"][$i1130];
							$add1130["discount"]	= $_POST["discount"][$i1130];
						
							M("member_price")->add($add1130);
						}
					}
			}
			
//处理批发价=============
			for($q1216=0;$q1216<count($_POST["wholesale_discount"]);$q1216++){
				if(!empty($_POST["wholesale_discount"][$q1216])){
					$where1216["program_id"]	= $_COOKIE["program_id"];
					$where1216["goods_id"]		= $_GET["id"];
					$where1216["num_start"]		= $_POST["num_start"][$q1216];
					$where1216["num_end"]		= $_POST["num_end"][$q1216];
					
					$date1216["discount"]		= $_POST["wholesale_discount"][$q1216];
					$date1216["price"]			= $_POST["wholesale_price"][$q1216];
					$wholesale_discount=M("wholesale_discount")->where($where1216)->find();
					if(!empty($wholesale_discount)){
						//修改

						M("wholesale_discount")->where($where1216)->save($date1216);	
					}else{
						//添加
							$add1216["program_id"]	= $_COOKIE["program_id"];
							$add1216["goods_id"]	= $_GET["id"];
							$add1216["num_start"]	= $_POST["num_start"][$q1216];
							$add1216["num_end"]		= $_POST["num_end"][$q1216];
							$add1216["discount"]	= $_POST["wholesale_discount"][$q1216];
							$add1216["price"]		= $_POST["wholesale_price"][$q1216];
							$add1216["state"]		= 1;//批发条件（1：件数、2：满额）
							M("wholesale_discount")->add($add1216);
						}
					}
				}
			
			
			$_POST["good_attribute"]	= $_POST["goods_subtitle"];
			
			$_POST["program_id"]		= $_COOKIE["program_id"];
			
			$fenlei_1=M("attribute")->where("uid=0 and attribute_style=1 and title='".$_POST["fenlei_1_title"]."'"."and program_id='".$_COOKIE["program_id"]."'")->find();
			$_POST["fenlei_1"]=$fenlei_1["id"];
			
			$fenlei_2=M("attribute")->where("uid !=0 and title='".$_POST["fenlei_2_title"]."'")->find();
			$_POST["fenlei_2"]=$fenlei_2["id"];
			
		$_POST["goods_subtitle01"]=(empty($_POST["good_attribute01"]))?2:1;
		$_POST["goods_subtitle02"]=(empty($_POST["good_attribute02"]))?2:1;
		$_POST["goods_subtitle03"]=(empty($_POST["good_attribute03"]))?2:1;
		$_POST["goods_subtitle04"]=(empty($_POST["good_attribute04"]))?2:1;
		$_POST["goods_subtitle05"]=(empty($_POST["good_attribute05"]))?2:1;

		$str1026["title"]	 = implode(",",$_POST["canshu_title"]);//参数名数组	
		$str1026["content"]  = implode(",",$_POST["canshu_con"]);//参数值数组	
	
		$_POST["str_guige"]  = implode(",",$_POST["guige"]);//1128 商品规格
		$_POST["str_price"]  = implode(",",$_POST["price"]);//1128 商品价格
		$_POST["str_num"]	 = implode(",",$_POST["num"]);//1128 商品价格
		$_POST["str_remark"] = implode(",",$_POST["remark"]);//1128 商品规格备注
		
		$_POST["str_guige02"]=implode(",",$_POST["guige02"]);//1128 商品规格备注  discount  $_POST["discount"]
		
		$upload = new \Think\Upload();// 实例化上传类
		$upload->maxSize   =  3145728 ;// 设置附件上传大小
		$upload->exts      =  array('jpg', 'gif', 'png', 'jpeg','mp4');// 设置附件上传类型
		$upload->rootPath  =   './Uploads/'; // 设置附件上传根目录
		// 上传文件 
		$info   =   $upload->upload();
		if($info){
	
					//有上传图片的地方进来
		foreach($info as $k=>$v){
			if($info[$k]['key']=="image"){
			    $date['image'].=$info[$k]['savepath'].$info[$k]['savename'].",";
			}else if($info[$k]['key']=="img"){
			    $date['img'].=$info[$k]['savepath'].$info[$k]['savename'].",";
			}
		}
					
		$arr_0302=explode(".",$info["goods_video"]['savename']);
			if($arr_0302[1]=="mp4"){
				$_POST["goods_video"]=$info["goods_video"]['savepath'].$info["goods_video"]['savename'];
			}
					
			$_POST['image']   = substr($date['image'],0,strlen($date['image'])-1); //去掉最后一个逗号
			$_POST['str_img'] = substr($date['img'],0,strlen($date['img'])-1); //去掉最后一个逗号
		}else{
			//无上传文件
				
		}
		
		if(empty($_GET["id"])){
			//执行添加
			$_POST["time_set"]			= time();
			$only=M("goods")->where("time_set='".$_POST["time_set"]."'")->find();
				if(empty($only)){
				if(empty($_POST['image'])) $this->error('必须上传商品图片');
					if(M("goods")->add($_POST)){
						$set_img=M("goods")->where("time_set='".$_POST["time_set"]."'")->find();
							//1026...22.24 多条数据改写成一条
							$str1026["goods_id"]=$set_img["id"];
							M("specifications")->add($str1026);//商品参数
						$this->success('添加成功',U('selling'));
				    }else{
				    	$this->error('添加失败');	
				    }
				}
		}else{
			//执行修改
			 		$goods1103=M("goods")->where("id='".$_GET["id"]."'")->find();	
			 		
			 		if(!empty($goods1103["image"]) ){
			 			
			 			$_POST["image"]=(empty($_POST["image"]))?$goods1103["image"]:$goods1103["image"].",".$_POST["image"];
			 			
			 		}	
			 		if(!empty($goods1103["str_img"])){
			 			$_POST["str_img"]=(empty($_POST["str_img"]))?$goods1103["str_img"]:$goods1103["str_img"].",".$_POST["str_img"];
			 		}		
			 		if(M("goods")->where("id='".$_GET["id"]."'")->save($_POST)){
			 			
					//删除这个商品的全部参数，然后重新赋值     1026...22.25 改写，保存字符串就好
					//删除这个商品的全部参数，然后重新赋值  1026...22.27改写
					 		$is_null1107=M("specifications")->where("goods_id='".$_GET["id"]."'")->find();
					 		if(empty($is_null1107)){
					 			$str1026["goods_id"]=$_GET["id"];
					 			M("specifications")->add($str1026);
					 		}else{
					 			M("specifications")->where("goods_id='".$_GET["id"]."'")->save($str1026);
					 		}
			 		$this->success('修改成功',U('selling'));
				    }else{
				    	$this->error('修改失败');	
				    }
		}
	}
	
		public function goods_delete(){
				 $user_id = $_GET['id'];
	        if(M('goods')->delete($user_id)) {
	            $this->success('删除成功');
	        } else {
	            $this->error('删除失败');
	        }
		}
		
		public function goods_huishou(){
			$date["goods_state"]="4";
			if(M('goods')->where("id='".$_GET["id"]."'")->save($date)) {
	            $this->success('回收成功');
	        } else {
	            $this->error('回收失败');
	        }
		}
		public function goods_huishou_back(){
			$date["goods_state"]="3";
			if(M('goods')->where("id='".$_GET["id"]."'")->save($date)) {
	            $this->success('回收成功');
	        } else {
	            $this->error('回收失败');
	        }
		}
		//递归
	function bq($arr,$p=0){
		$ar=array();
		foreach($arr as $v){ 
				if($v["uid"]==$p){ 
					$v["child"]=$this->bq($arr,$v["id"]);
						$ar[]=$v;
						}
			} return $ar;
	}
	//筛选区间
	public function shanxuan(){
		echo 777;
		$this->display();
	}
	//标签管理
	public function biaoqian(){
		echo 888;
		$this->display();
	}
	
	//商品异步上下架
	function ajax_change_ok(){
	$state=M("goods")->where("id='".$_POST["id"]."'")->find();
		//  goods_state  商品状态（1：在售、2：售罄、3：仓库中、4：回收站）
			if($state["goods_state"]==1){
				$val["goods_state"]="3";
			}else if($state["goods_state"]==3){
				$val["goods_state"]="1";
			}
			
			if(M("goods")->where("id='".$_POST["id"]."'")->save($val)){
					$date="1";
			}else{
					$date="2";
			}
			$this->ajaxReturn($date);
	}
	
	//回收站商品恢复到仓库里
	function back_cangku(){
		$val["goods_state"]="3";
		
		if(M("goods")->where("id='".$_POST["id"]."'")->save($val)){
				$this->success('恢复成功',U('cangku'));
			}else{
				$this->error('恢复失败');
			}
	}
	//异步修改商品的排序
	function ajax_sort_goods(){
		$save["sort"]=$_POST["sort_val"];
		if(M("goods")->where("id='".$_POST["id"]."'")->save($save)){
				$date="1";
			}else{
				$date="2";
			}
		$this->ajaxReturn($date);
	}
	
}