<?php
/**
 * Created by PhpStorm.
 * User: lingjiang
 * Date: 2016/6/13
 * Time: 9:44
 */
namespace app\admin\controller;

use app\admin\model\ConfModel;
use app\admin\model\WechatMenuModel;

class Wechat extends Common
{
    private $m;

    public function __construct()
    {
        parent::__construct();
        $this->m = new WechatMenuModel();
    }

    //自定义菜单管理
    public function menus()
    {
        //获取公众号的appid和appsecret
        $c_model=new ConfModel();
        $this->assign('appid',$c_model->get_conf('UNION_WECHAT_MP_APPID'));
        $this->assign('appsecret',$c_model->get_conf('UNION_WECHAT_MP_APPSEC'));
        return $this->display();
    }

    public function show_list()
    {
        $menus = $this->m->get_all_menu_list();
        $menus = $this->m->tree_format($menus);
        $this->assign('list', $menus);
        return $this->fetch();
    }

    //添加微信菜单
    public function exec()
    {
        //查看,编辑
        $id = I("get.id", null);
        $type = I("get.type", null);
        //获取全部菜单
        $menus = $this->m->get_p_menus();
        $this->assign('menus', $menus);
        $this->assign('p_count', count($menus));

        if (!empty($id)) {
            !is_numeric($id) && die('id格式不正确');
            //获取详情
            $info = $this->m->get_menu_info_by_id($id);
            empty($info) && die('获取信息失败');
            $this->assign("info", $info);
            if ($type == 'edit') {
                $this->assign('type', 'edit');//编辑
            } else {
                $this->assign('type', 'see');//查看
            }
        } //新增
        else {
            $this->assign('type', 'add');//新增
        }

        return $this->fetch('form');
    }

    //菜单删除
    public function del()
    {
        $id = I('post.id');
        empty($id) && wrong_return('删除失败');
        $rt = $this->m->menu_del($id);
        $rt && ok_return('删除成功!');
        wrong_return('删除失败');
    }

    //菜单更新
    public function update()
    {
        $rt = $this->m->update_menu(I('post.'));
        $rt && ok_return('恭喜!操作成功!');
        wrong_return('数据写入失败');
    }
    //生成自定义菜单
    public function create_menu(){
        $menus=$this->m->get_wechat_menus();
        $wechat=new \app\lib\Wechat([
            'appid'=>I('post.appid'),
            'appsecret'=>I('post.appsecret')]);
        $res=$wechat->createMenu(['button'=>$this->generate_menu($menus)]);
        $res && ok_return('恭喜!操作成功!');
        wrong_return('操作失败');
    }
    //格式化微信自定义菜单
    private  function  generate_menu($menus){
        $menu=[];
        foreach($menus as $k=>$v){
            if(empty($v['son_menus'])){
               $menu[$k]['name']=$v['name'];
               $menu[$k]['type']=$v['type'];
                $v['type']=='click'&&$menu[$k]['key']=$v['val'];
                $v['type']=='view'&&$menu[$k]['url']=$v['val'];
            }else{
                $menu[]=[
                    'name'=>$v['name'],
                    'sub_button'=>$this->generate_son_menu($v['son_menus'])
                ];
            }
        }
        return $menu;
    }
    //格式化子菜单
    private function generate_son_menu($son_menu){
        $menu=[];
        foreach($son_menu as $k=>$v){
            $menu[$k]['type']=$v['type'];
            $menu[$k]['name']=$v['name'];
            $v['type']=='click'&&$menu[$k]['key']=$v['val'];
            $v['type']=='view'&&$menu[$k]['url']=$v['val'];
        }
        return $menu;
    }
}