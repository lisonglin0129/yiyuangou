<?php
namespace app\admin\model;

Class RtPresetsModel extends BaseModel
{
    public $m;

    public function __construct()
    {
        $this->m = M('presets','sp_rt_');
    }

    public function get_info_by_id($id,$field='p.*,g.id goods_id,g.name goods_name,g.min_times,g.sum_times,g.max_times')
    {
        $user_info = $this->m
            ->table('sp_rt_presets p')
            ->field($field)
            ->join('sp_goods g ON g.id = p.gid','RIGHT')
            ->where(array("g.id" => $id))->find();
        return $user_info;
    }

    public function update($post)
    {
        $id = $gid = $interval = $num_count = $cheat = $enable = $num_max = $num_min = $loop  = null;
        extract($post);
        //如果全部正确,执行写入数据
        $data = array(
            'id' => !empty($id) ? $id : "",
            'interval' => intval($interval),
            'num_count' => intval($num_count),
            'num_max' => intval($num_max),
            'num_min' => intval($num_min),
            'cheat' => $cheat=='true'?'true':'false',
            'enable' => $enable=='true'?'true':'false',
            'loop' => $loop=='true'?'true':'false',
            'gid' => intval($gid)
        );
        if (!empty($id) && is_numeric($id)) {
            $r = $this->m->where(array("id" => $id))->save($data);
            return $r !== false;
        } else {
            return $this->m->add($data);
        }
    }

    public function update_by_gid($id,$data){
        $r = $this->m->where(array("gid" => $id))->save($data);
        return $r;
    }

    public function del($gid)
    {
        return $this->m->where(array("gid" => $gid))->delete();
    }

    //获取预设列表
    public function presets_list($post){

        $m = M();
        $sql = "SELECT SQL_CALC_FOUND_ROWS
        p.*,g.id goods_id,g.`name` FROM sp_rt_presets p RIGHT JOIN sp_goods g ON p.gid = g.id
        WHERE  g.status <> -1 " . $post->wheresql .
            " ORDER BY id desc,goods_id desc " . $post->limitData;

        $sql_count = "SELECT FOUND_ROWS() as num";
        $info = $m->query($sql);
        $num = $m->query($sql_count);

        $rt["data"] = $info;
        $rt["count"] = $num[0]["num"];
        return $rt;
    }

}