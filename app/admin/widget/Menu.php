<?php
namespace app\admin\widget;

use app\admin\model\MenuModel;
use think\Controller;

Class Menu extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    //显示菜单
    public function show_menu()
    {
//        $list = session("menu");
        $m_menu = new MenuModel();
        $get = I('get.');
        if ( is_null($get) ) {
            $param = '';
        } else {
            $param = implode('=',$this->get_key_and_value($get));
        }


        $list = $m_menu->get_user_auth_tree(get_user_id());

        //查询路径
        $path_info =strtolower(MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME);
        $path = $m_menu->get_select_menu($path_info,$param);

        $this->assign('select_path',$path['select_path']);
        $this->assign('open_path',$path['open_path']);
        $this->assign("list", $list);
        return $this->fetch('menu/show_menu');
    }

    private function get_key_and_value($data) {
        $res = array();
        $keys = array_keys($data);
        $res['key'] = $keys[0];
        $res['value'] = $data[$keys[0]];
        return $res;


    }






}