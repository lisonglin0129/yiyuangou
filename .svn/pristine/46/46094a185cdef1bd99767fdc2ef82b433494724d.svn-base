<?php
/**
 * Created by PhpStorm.
 * User: lingjiang
 * Date: 2016/6/13
 * Time: 9:55
 */
namespace app\admin\model;
class WechatMenuModel{
    private $m;
    public function __construct(){
        $this->m=M('wechat_menu');
    }
    public function get_all_menu_list(){
      return $this->m->select();
    }
    //获取父级菜单
    public function get_p_menus(){
        return $this->m->alias('w')
            ->field('w.*,count(m.id) as son_count')
            ->join('left join sp_wechat_menu m on w.id=m.pid')
            ->where(['w.pid'=>0])
            ->group('m.pid')
            ->select();
    }
    //树形显示
    public function tree_format($data,$pid=0,$html='└----'){
      static $list=[];
        foreach($data as $k=>$v){
           if($v['pid']==$pid){
               $v['level']==2&&$v['name']=$html.$v['name'];
               $list[]=$v;
               $this->tree_format($data,$v['id'],$html);
           }
        }
        return $list;
    }
    public function get_menu_info_by_id($id){
        return $this->m->where(['id'=>intval($id)])->find();
    }
    //获取子菜单
    public function get_son_menus($id){
        return $this->m->field('id')->where(['pid'=>$id])->count();
    }
    //更新菜单数据
    public function update_menu($post){
        $id=$name=$val=$type=$level=$pid=null;
        extract($post);
        $data=[
            'id'=>!empty($id)?$id:'',
            'name'=>$name,
            'val'=>$val,
            'type'=>$type,
            'level'=>$level,
            'pid'=>!empty($pid)?intval($pid):0
        ];
        if(empty($id)){
            $res=$this->m->add($data);
        }else{
            $res=$this->m->save($data);
        }
        return $res;
    }
    public function menu_del($id){
        if($id){
            if($this->get_son_menus($id)>0){
                return false;
            }
            return $this->m->where(['id'=>intval($id)])->delete();
        }
    }
    //获取菜单平装菜单数据
    public function get_wechat_menus(){
        $p_menus=$this->m->where(['pid'=>0])->select();
        count($p_menus)>3&&die('超出一级菜单数量上限');
        foreach($p_menus as $k=>$v){
            $p_menus[$k]['son_menus']=$this->m->where(['pid'=>$v['id']])->select();
        }
        return $p_menus;
    }
}