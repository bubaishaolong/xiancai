<?php
// +----------------------------------------------------------------------
// | PHP框架 [ ThinkPHP ]
// +----------------------------------------------------------------------
// | 版权所有 为开源做努力
// +----------------------------------------------------------------------
// | 时间: 2018-07-06 09:42:56
// +----------------------------------------------------------------------
// | 开源协议 ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
namespace app\enterprise\admin;

use app\admin\controller\Admin;
use app\common\builder\ZBuilder;

use app\admin\model\Model as ModelModel;
use app\admin\model\Button as ButtonModel;
use app\enterprise\model\Setting as SettingModel;
use app\admin\model\Field as FieldModel;

class  Setting extends Admin
{

    /**
     * 首页
     * @return mixed
     */
    public function index()
    {
    //获取数据
		$order = $this->getOrder();
		// 获取筛选
		$map = $this->getMap();
        //获取数据
        $dataList = SettingModel::where($order)->where($map)->order('id desc')->paginate();
		// 分页数据
		$page = $dataList->render();
        //获取当前所在
        $datamodelID = ModelModel::where(array('table' => 'cj_enterprise_setting','status'=>1))->value('id');
        $datafile = FieldModel::where(array('model' => $datamodelID,'status'=>1,'show'=>1,'list_type'=>['<>','hidden']))->field('id,name,title,list_type')->select();

        foreach ($datafile as $key => $value) {
            $names = $value['name'];
            $title = $value['title'];
            $datavalues = $value['list_type'];
			if($datavalues == 'text' || $datavalues == 'date' || $datavalues == 'time' || $datavalues == 'datetime' || $datavalues == 'textarea'){
				$data_list = $value['list_type'] . '.edit';
			}else{
				$data_list = $value['list_type'];
			}
            if(empty($value['list_type'])){
                $data_type_list = '';
            }else{
                $data_type_list = $data_list;
            }
            $data[] = [$names, $title,$data_type_list];
        }
        //搜索查询可以搜索的字段
        $datafilesea = FieldModel::where(array('model' => $datamodelID,'status'=>1,'show'=>1,'is_search'=>1))->field('id,name,title,is_search')->select();
        if($datafilesea){
            foreach ($datafilesea as $key => $value) {
                $names = $value['name'];
                $title = $value['title'];
                $data_search[$names] = $title;

            }
        }else{
            $data_search = '';
        }
		$topbutton = ModelModel::where(array('id' => $datamodelID, 'status' => 1, 'is_top_button' => 1))->value('top_button_value');
		$rightbutton = ModelModel::where(array('id' => $datamodelID, 'status' => 1, 'is_right_button' => 1))->value('right_button_value');
		$datafilesea = FieldModel::where(array('model' => $datamodelID,'status'=>1,'show'=>1,'is_filter'=>1))->column('id,name');
		if(!$datafilesea){
			$datafilesea = '';
		}
		//顶部和右侧按钮自定义
        $module_id =ButtonModel::where(array('module_id'=>$datamodelID,'status' => 1))->select();
		if($module_id){
            foreach ($module_id as $key=>$value){
                $gatakey['title'] = $value['title'];
                $gatakey['name'] = $value['name'];
                $gatakey['icon'] = $value['icon'];
                if($value['param']){
                    $dataarr= explode(',',$value['param']);
                    //$gatakey['herf'] = url($value['url'],$dataarr);
                    for ($i=0;$i<count($dataarr);$i++){
                        $datapppp[$i] = str_replace('[','',$dataarr[$i]);
                        $datassss[$i] = str_replace(']','',$datapppp[$i]);
                        $dataeee[$i] = str_replace("'",'',$datassss[$i]);
                        $datakey[$i] = explode('=>',$dataeee[$i]);
                        $param[$datakey[$i][0]] = $datakey[$i][1];
                    }
                    $gatakey['href'] = url($value['url'],$param);
                }else{
                    $gatakey['href'] = url($value['url']);
                }
                //tab1 是顶部按钮  tab2是右侧按钮
                if($value['button_type'] == 'tab1'){
                    $datavaluet['custom'.$key] =  $gatakey;
                }elseif ($value['button_type'] == 'tab2'){
                    $datavaluer['custom'.$key] =  $gatakey;
                }

            }

        }else{
            $datavaluet['custom'] =  '';
            $datavaluer['custom'] =  '';
        }
         $width = FieldModel::where(array('model' => $datamodelID, 'status' => 1, 'show' => 1, 'is_filter' => 0))->column('name,width');
         $bind_title = <<<JS
<script>
td = $('.builder-table-body tr td:eq(3)');
td.find('div').attr('title', td.text());
</script>
JS;
        // 使用ZBuilder快速创建数据表格
        return ZBuilder::make('table')
            ->setSearch($data_search)
            ->setPageTips('注意事项:  由于系统未实现自定义按钮,所以还是沿用系统的规定的按钮方法,去对应的文件新增按钮')
            ->addFilter($datafilesea)
            ->addColumn('__INDEX__', '#')
            ->addColumns($data)
            ->addColumn('right_button', '操作', 'btn')
            //->addTopButtons('back,add,delete')
            //->addRightButtons('edit,delete')
            ->addTopButtons($topbutton)
            ->addTopButtons($datavaluet)
			->addRightButtons($rightbutton)
			->addRightButtons($datavaluer)
			 ->setColumnWidth($width)
			 ->setExtraJs($bind_title)
            ->setRowList($dataList)
            ->setPages($page) // 设置分页数据
            ->fetch();
    }
    
    /**
     *新增
     */
     public function add(){
       if($this->request->isPost()){
             $datas = $this->request->Post();
             //判断数据是否重复添加
             $datappp =SettingModel::where($datas)->find();
             if($datappp){
                 $this->error('数据重复');
             }
             $dataadd = SettingModel::create($datas);
             if($dataadd){
                 $this->success('添加成功','index');
             }
         }
         $datamodelID = ModelModel::where(array('table' => 'cj_enterprise_setting','status'=>1))->value('id');
         $datafile = FieldModel::where(array('model' => $datamodelID,'status'=>1,'show'=>1,'new_type'=>['<>','hidden']))->field('type,name,title,tips,new_type')->order('sort asc')->select();
         foreach ($datafile as $key => $value) {
             $names = $value['name'];
             $title = $value['title'];
             $type = $value['type'];
             $tips = $value['tips'];
             $new_type = $value['new_type'];
             $data[] = [$new_type,$names, $title,$tips];
         }
       // 显示添加页面
        return ZBuilder::make('form')
               ->addFormItems($data)
               ->fetch();
     }
     public function edit($id=''){
		if($this->request->isPost()){
			$data= $this->request->post();
			if(isset($data['status']) == 'on'){
				$data['status'] = 1;
			}else{
				$data['status'] = 0;
			}
			SettingModel::update($data);
			// 验证
			//$result = $this->validate($data, 'Setting.edit');
			$this->success('编辑成功', 'index');
		}
		$datamodelID = ModelModel::where(array('table' => 'cj_enterprise_setting', 'status' => 1))->value('id');
		$datafile = FieldModel::where(array('model' => $datamodelID, 'status' => 1, 'show' => 1, 'edit_type' => ['<>', 'hidden']))->field('type,name,title,tips,edit_type')->select();
		foreach ($datafile as $key => $value) {
			$names = $value['name'];
			$title = $value['title'];
			//$type = $value['type'];
			$tips = $value['tips'];
			$edit_type = $value['edit_type'];
			$data[] = [$edit_type, $names, $title, $tips];
		}
		// 模型信息
		$info = SettingModel::get($id);
		// 显示编辑页面
		return ZBuilder::make('form')
			->addFormItem('hidden','id')
			->addFormItems($data)
			->setFormData($info)
			->fetch();
	}
    
}