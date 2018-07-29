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
namespace app\shop\admin;

use app\admin\controller\Admin;
use app\admin\model\Button as ButtonModel;
use app\common\builder\ZBuilder;

use app\admin\model\Model as ModelModel;
use app\shop\model\Menber as MenberModel;
use app\admin\model\Field as FieldModel;

class  Menber extends Admin
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
		$dataList = MenberModel::where($order)->where($map)->paginate();
		// 分页数据
		$page = $dataList->render();
		//获取当前所在
		$datamodelID = ModelModel::where(array('table' => 'cj_shop_menber', 'status' => 1))->value('id');
		$datafile = FieldModel::where(array('model' => $datamodelID, 'status' => 1, 'show' => 1, 'list_type' => ['<>', 'hidden']))->field('id,name,title,list_type')->order('sort asc')->select();
		//排除带有[]主观的数据
		foreach ($datafile as $key => $value) {
			$names = $value['name'];
			$title = $value['title'];//date time  datetime
			$datavalues = $value['list_type'];
			if($datavalues == 'text' || $datavalues == 'date' || $datavalues == 'time' || $datavalues == 'datetime' || $datavalues == 'textarea'){
				$data_list = $value['list_type'] . '.edit';
			}else{
				$data_list = $value['list_type'];
			}
			if (empty($value['list_type'])) {
				$data_type_list = '';
			} else {
				$data_type_list = $data_list;
			}
			$data[] = [$names, $title, $data_type_list];
		}
		//搜索查询可以搜索的字段
		$datafilesea = FieldModel::where(array('model' => $datamodelID, 'status' => 1, 'show' => 1, 'is_search' => 1))->field('id,name,title,is_search')->select();
		if ($datafilesea) {
			foreach ($datafilesea as $key => $value) {
				$names = $value['name'];
				$title = $value['title'];
				$data_search[$names] = $title;

			}
		} else {
			$data_search = '';
		}

		$topbutton = ModelModel::where(array('id' => $datamodelID, 'status' => 1, 'is_top_button' => 1))->value('top_button_value');
		$rightbutton = ModelModel::where(array('id' => $datamodelID, 'status' => 1, 'is_right_button' => 1))->value('right_button_value');
		$datafilesea = FieldModel::where(array('model' => $datamodelID, 'status' => 1, 'show' => 1, 'is_filter' => 1))->column('id,name');
		if (!$datafilesea) {
			$datafilesea = '';
		}
//		$topbuttonID = ModelModel::where(array('id' => $datamodelID, 'status' => 1, 'is_top_button' => 1))->value('id');
//		//$rightbuttonID =  ModelModel::where(array('id' => $datamodelID, 'status' => 1, 'is_right_button' => 1))->value('id');
//		$datassss = ButtonModel::where(array('module_id' => $topbuttonID, 'status' => 1))->select();
//		foreach ($datassss as $key => $v) {
//			$dataone['title'] = $v['title'];
//			$dataone['icon'] = $v['icon'];
//			$ddd = explode('=>',$v['param']);
//			$dataone['href'] = url($v['url'], $v['param']);
//		}
//

		// 使用ZBuilder快速创建数据表格
		return ZBuilder::make('table')
			->setSearch($data_search)
			->addFilter($datafilesea)
			->addColumn('__INDEX__', '#')
			->addColumns($data)
			->addColumn('right_button', '操作', 'btn')
//			->addRightButtons(['edit','delete'])
			->addTopButtons($topbutton)
			->addRightButtons($rightbutton)
			->setRowList($dataList)
			->setPages($page)// 设置分页数据
			->fetch();
	}

	/**
	 *新增
	 */
	public function add()
	{
		if ($this->request->isPost()) {
			$datas = $this->request->Post();
			//判断数据是否重复添加
			$datappp = MenberModel::where($datas)->find();
			if ($datappp) {
				$this->error('数据重复');
			}
			$dataadd = MenberModel::create($datas);
			if ($dataadd) {
				$this->success('添加成功','index');
			}
		}
		$datamodelID = ModelModel::where(array('table' => 'cj_shop_menber', 'status' => 1))->value('id');
		$datafile = FieldModel::where(array('model' => $datamodelID, 'status' => 1, 'show' => 1, 'new_type' => ['<>', 'hidden']))->field('type,name,title,tips,new_type')->order('sort asc')->select();
		foreach ($datafile as $key => $value) {
			$names = $value['name'];
			$title = $value['title'];
			$type = $value['type'];
			$tips = $value['tips'];
			$new_type = $value['new_type'];
			$data[] = [$new_type, $names, $title, $tips];
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
			MenberModel::update($data);
			// 验证
			//$result = $this->validate($data, 'Menber.edit');
			$this->success('编辑成功', 'index');
		}
		$datamodelID = ModelModel::where(array('table' => 'cj_shop_menber', 'status' => 1))->value('id');
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
		$info = MenberModel::get($id);
		// 显示编辑页面
		return ZBuilder::make('form')
			->addFormItem('hidden','id')
			->addFormItems($data)
			->setFormData($info)
			->fetch();
	}

}