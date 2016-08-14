<?php
/**
 * Created by PhpStorm.
 * User: lingjiang
 * Date: 2016/5/23
 * Time: 17:29
 */
namespace app\core\model;
class AccountModel {
    private $m;
    public function __construct(){
        $this->m=M('account','sp_');
    }
    //账号添加
    public function account_add($post){
      $identify=$type=$name=$bank_no=$is_default=$uid=$account_type=null;
        extract($post);
        if(!$uid){
           return false;
        }
        //获取账户列表
        $data=[
            'identify'=>$identify,
            'type'=>!empty($type)?$type:'',
            'name'=>$name,
            'bank_no'=>$bank_no,
            'uid'=>$uid,
            'account_type'=>$account_type,
            'bank_name'=>!empty($bank_name)?$bank_name:''

        ];
        $accounts=$this->get_accounts();
        if(empty($is_default)&&empty($accounts)){
          $data['is_default']=1;
        }elseif(!empty($is_default)&&$accounts){
            $this->reset_default();
            $data['is_default']=1;
        }

        return $this->m->add($data);
    }
    public function account_del($id){
        return $this->m->where(['id'=>$id])->delete();
    }
    //获取银行卡列表
    public function  get_accounts($uid="",$type=""){
        if(empty($uid)&&empty($type)){
            $where['uid'] = get_user_id();
        }else{
            $where['uid'] = $uid;
            $where['account_type'] = $type;
        }

        return $this->m->where($where)->select();
    }
    //重置默认账号
    public function reset_default(){
        return $this->m->where(['uid'=>get_user_id(),'account_type'=>1,'is_default'=>1])->save(['is_default'=>0]);
    }
}