<?php
namespace app\core\model;
/**彩票管理*/
class CollectModel extends CommonModel
{
    private $m_lottery;

    public function __construct()
    {
        parent::__construct();
        $this->m_lottery = M('lottery', 'sp_');
    }

    public function update_lottery($post)
    {
        $issue=$origin=null;
        if (!$post) return false;
        extract($post);
        $rt = $this->m_lottery->where(array('issue' => $issue, 'origin' => $origin))->find();//检查是否已存在
        if (!$rt) {
            $data = array(
                "issue" => !empty($issue) ? $issue : null,
                "num" => !empty($num) ? $num : null,
                "origin" => !empty($origin) ? $origin : null,
                "error" => !empty($error) ? $error : null,
                "open_time" => !empty($open_time) ? $open_time : null,
                "create_time" => date("Y-m-d H:i:s")
            );

            return $this->m_lottery->add($data);
        }
        return "-1";
    }
}