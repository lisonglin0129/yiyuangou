<?php
namespace app\admin\model;

Class CommonModel
{
    public $m_model;

    public function __construct()
    {
        $this->m_model = M(null, null);
    }

    //根据code获取分类一级列表
    public function get_category_by_code($code,$order=NULL)
    {
        empty($order) && $order = 'id';
        //获取父级id
        $m_category = M("category", "sp_");
        $m_category_list = M("category_list", "sp_");
        $p_info = $m_category->where(array("code" => $code))->field("id")->find();

        //获取该父级id下的一级列表
        return $m_category_list->where(array("status" => "1", "mid" => $p_info['id']))->order($order)->select();
    }


    //获取配置信息
    public function get_conf($name)
    {
        $m_conf = M('conf', 'sp_');
        $info = $m_conf->where(array('name' => strtoupper($name)))->find();
        return $info["value"];
    }

    //设置配置信息
    public function set_conf($name, $value)
    {
        $m_conf = M('conf', 'sp_');
        return $m_conf->where(array('name' => strtoupper($name)))->save(array('value' => $value));
    }

    /**
     * 获得机器人位晒单的未读消息通知
     * @param $type
     * @return array | string
     */
    function get_rt_show_order($type)
    {
        $sql = "SELECT SQL_CALC_FOUND_ROWS  w.*
        FROM  sp_win_record w
        LEFT JOIN sp_users u ON u.id=w.luck_uid
        LEFT JOIN sp_show_order s ON s.nper_id=w.nper_id
        WHERE u.type=-1 AND ISNULL(w.read_flag) AND u.type=-1 ORDER BY id desc LIMIT 5";
        $m_win = M('win_record', 'sp_');
        $sql_count = "SELECT FOUND_ROWS() as num";
        $info = $m_win->query($sql);
        $num = $m_win->query($sql_count);
        $num = $num[0]['num'];
        $data = $num;
        if ( $type=='info') {
            $data = $info;
        }
        return $data;

    }
    //获取配置信息remark
    function get_conf_remark($name)
    {
        $m_conf = M('conf','sp_');
        $info =$m_conf-> where(array('name'=>strtoupper($name)))->find();
        return $info['remark'];
    }
}