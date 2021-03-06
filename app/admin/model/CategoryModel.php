<?php
namespace app\admin\model;

Class CategoryModel
{
    private $category;
    private $category_list;

    public function __construct()
    {
        $this->category = M('category', "sp_");
        $this->category_list = M('category_list', "sp_");
    }

    public function update($post)
    {
        $id = $allow_del = $status = $name = $tips = $code = $type = null;
        extract($post);
        $data = array(
            "allow_del" => $allow_del,
            "status" => $status,
            "name" => $name,
            "tips" => $tips,
            "code" => $code,
            "type" => $type,
            "create_time" => NOW_TIME
        );

        if (!empty($id) && is_numeric($id)) {
            $info = $this->category->where(array("id" => $id))->save($data);
            return $info !== false;
        } else {
            return $this->category->add($data);
        }

    }

    public function update_child($post)
    {
        $id = $pid = $orders = $mid = $imgid = $code = $status = $name = $url = $desc = $sumclick = null;
        extract($post);
        $data = array(
            "pid" => $pid,
            "orders" => $orders,
            "mid" => $mid,
            "imgid" => $imgid,
            "code" => $code,
            "status" => $status,
            "name" => $name,
            "url" => $url,
            "desc" => $desc,
            "sumclick" => $sumclick
        );

        if (!empty($id) && is_numeric($id)) {
            $info = $this->category_list->where(array("id" => $id))->save($data);
            return $info !== false;
        } else {
            return $this->category_list->add($data);
        }

    }

    //新增分类
    public function category_add($post)
    {
        $data = array(
            "name" => $post['name'],
            "code" => $post['code'],
            "type" => $post['type'],
            "tips" => $post['tips'],
            "createtime" => $post['createtime']);
        return $this->category->add($data);
    }

    //保存分类
    public function category_save($post)
    {
        extract($post);
        $data = array(
            "name" => $name,
            "type" => $type,
            "tips" => $tips);
        return $this->category->where(array("id" => $id))->save($data);
    }

    //根据id返回分类主菜单的详细内容
    public function get_class_info_by_id($id)
    {
        return $this->category->where(array('status'=>"1",'id'=>$id))->find();
    }

    //根据id返回子分类主菜单的详细内容
    public function get_list_info_by_id($id)
    {
        return $this->category_list->where(array("id" => $id))->find();
    }

    //不存在返回false
    public function check_class_code($type)
    {
        return $this->category->where(array("code" => $type))->find();
    }

    //根据id获取当前目录下的菜单
    public function get_category_list($mid)
    {
        return $this->category_list->where(array("status" => "1", "mid" => $mid))->select();
    }

    //根据code获取分类一级列表
    public function get_category_by_code($code)
    {
        //获取父级id
        $m_category = M("category", "sp_");
        $m_category_list = M("category_list", "sp_");
        $p_info = $m_category->where(array("code" => $code))->field("id")->find();

        //获取该父级id下的一级列表
        return $m_category_list->where('status <> -1 AND mid = ' . $p_info['id'])->order('id desc,`orders` desc')->select();
    }

    //删除分类信息
    public function del_category($id)
    {
        return $this->category->where(array("id" => $id))->save(array('status'=>-1));
    }

    //删除子分类信息
    public function del_category_child($id)
    {
        return $this->category_list->where(array("id" => $id))->save(array('status'=>-1));
    }

    //删除分类下全部子分类
    public function del_sub_category($mid)
    {
        return $this->category_list->where(array("mid" => $mid))->delete();
    }

    //检测分类mid是否存在
    public function check_subclass_mid($mid)
    {
        return $this->category->where(array("status" => "1", "mid" => $mid))->find();
    }

    /**+
     *作用：检测父级id是否存在
     *返回：不存在返回false
     */
    public function checkSubclassPid($pid)
    {
        return $this->category_list->where(array("status" => "1", "id" => $pid))->find();
    }

    /**+
     *作用：检测是否存在分类id
     *返回：不存在返回false
     */
    public function checkSubclassId($id)
    {
        return $this->category_list->where(array("status" => "1", "id" => $id))->find();
    }

    /**+
     *作用：修改名称的Service
     *返回：修改失败返回false,成功返回影响的记录数
     */
    public function subclassRename($post)
    {
        $data['name'] = $post['name'];
        return $this->category_list->where(array("status" => "1", "id" => $post["id"]))->save($data);
    }

    /**+
     *作用：新增一条分类
     *返回：成功返回true
     */
    public function subclassAdd($post)
    {
        $data = array(
            'name' => $post['name'],
            'pid' => $post['pid'],
            'mid' => $post['mid'],
            'status' => "1"
        );
        return $this->category_list->add($data);
    }


    /**+
     *作用：删除分类
     *返回：有返回true
     */
    public function subclassDel($id)
    {
        return $this->category_list->where(array("id" => $id))->save(array("status" => "-1"));
    }

    /**+
     *作用：删除id和所属该id的分类
     *返回：成功返回true
     */
    public function checkChild($id)
    {
        return $this->category_list->where(array("status" => "1", "pid" => $id))->find();
    }

    /*
    * 作用: 粘贴分类
    * 参数: 操作id  新pid
    */
    public function subclassPaste($post)
    {
        $data['pid'] = $post['pid'];
        return $this->category_list->where(array("status" => "1", "id" => $post["id"]))->save($data);
    }

    /*
    * 作用: 设置识别码
    * 参数: 操作id  识别码code
    */
    public function setCode($post)
    {
        $data['code'] = $post['code'];
        return $this->category_list->where(array("status" => "1", "id" => $post["id"]))->save($data);
    }

    /*
    * 作用: 设置url
    * 参数: 操作id  url
    */
    public function setUrl($post)
    {
        $data['url'] = $post['url'];
        return $this->category_list->where(array("status" => "1", "id" => $post["id"]))->save($data);
    }

    /*
    * 作用: 设置Img
    * 参数: 操作id  Img

    */
    public function setImg($post)
    {
        $data['imgid'] = $post['imgid'];
        return $this->category_list->where(array("status" => "1", "id" => $post["id"]))->save($data);
    }

    /*
    * 作用: 检测识别码是否已存在
    * 参数: 操作id  识别码code
    */
    public function checkCode($post)
    {
        $getMidList = $this->category_list->where(array("status" => "1", "id" => $post["id"]))->find();
        $mid = $getMidList['mid'];

        return $this->category_list->where(array("status" => "1", "mid" => $mid, "code" => $post["code"]))->find();
    }

    /*
    * 作用: 查看识别码
    * 参数: 操作id
    */
    public function seeCode($id)
    {
        return $this->category_list->where(array("status" => "1", "id" => $post["id"], "id" => $id))->find();
    }

    /*
    * 作用: 检测图片是否存在且为本人上传
    * 参数: 操作id
    */
    public function checkImgId($post)
    {
        return M('images')->where(array("status" => "1", "id" => $post["id"], "memberid" => $post['memberid']))->find();
    }

    /*
    * 作用: 设置描述Desc
    * 参数: 操作id  Desc
    */
    public function setDesc($post)
    {
        $data['desc'] = $post['desc'];

        return $this->category_list->where(array("status" => "1", "id" => $post["id"]))->save($data);
    }

    /*
    * 获取商品列表
    */
    public function get_category($post)
    {
        $sql = "SELECT SQL_CALC_FOUND_ROWS *
        FROM  sp_category
        WHERE  status <> '-1' " . $post->wheresql .
            " ORDER BY id desc " . $post->limitData;

        $sql_count = "SELECT FOUND_ROWS() as num";
        $user_info = $this->category->query($sql);
        $num = $this->category->query($sql_count);

        $rt["data"] = $user_info;
        $rt["count"] = $num[0]["num"];
        return $rt;
    }

    //根据id获取当前目录下的菜单
    public function get_class_list($mid)
    {
        return $this->category_list->where(array("status" => "1", "mid" => $mid))->select();
    }

    public function get_category_list_info_by_code($code)
    {
        return $this->category_list->where(array("code" => $code))->find();
    }

    //根据code获取mid,返回当前mid分类下的全部分类
    public function get_list_by_code_mid($code)
    {
        $info = $this->get_category_info_by_code($code);

        if (!$info) return false;
        return $this->get_list_by_mid($info["id"]);
    }

    public function get_list_by_code($code)
    {
        $cat_info = $this->get_category_list_info_by_code($code);
        return $this->get_list_by_pid($cat_info["id"]);
    }


    public function get_category_info_by_code($code)
    
    {
    	$success  = $this->category->where(array("code" => $code))->find();
    
        return $success;
    }

    public function get_son_category_info($id)
    {
        return $this->category_list->where(['id' => $id])->find();
    }

    public function get_cate_list_root_list_by_mid($mid)
    {
        return $this->category_list->where(array("mid" => $mid, "pid" => 0))->select();
    }

    public function get_list_by_mid($mid)
    {
        return $this->category_list->where(array("mid" => $mid,'status'=>1))->order('orders desc')->select();
    }

    //根据父级id获取列表
    public function get_list_by_pid($pid)
    {
        return $this->category_list->where(array("status" => "1", "pid" => $pid))->select();
    }

    //获取mid下某pid的目录列表
    public function get_mid_pid_list($mid, $pid)
    {
        return $this->category_list->where(array("status" => "1", "mid" => $mid, "pid" => $pid))->select();
    }

    //添加导航URL
    public function nav_add($post)
    {
        extract($post);
        $data = array(
            "rid" => $rid,
            "cid" => $cid,
            "name" => $name,
            "show_img" => $show_img,
            "indexes" => $indexes,
            "target" => $target,
            "url" => $url,
            "img" => $img,
            "status" => '1',
            "order_num" => $order_num,
        );

        $m_nav = M('nav');
        return $m_nav->add($data);
    }


    //保存导航URL
    public function nav_save($post)
    {
        $rid=$cid=$name=$show_img=$indexes=$target=$url=$img=$order_num=$mobile_icon='';
        extract($post);
        $data = array(
            "rid" => $rid,
            "cid" => $cid,
            "name" => $name,
            "show_img" => $show_img,
            "indexes" => $indexes,
            "target" => $target,
            "url" => $url,
            "img" => $img,
            "order_num" => $order_num,
            'mobile_icon' => $mobile_icon
        );
        $m_nav = M('nav');
        return $m_nav->where(array("id" => $post["id"]))->save($data);
    }

    //执行更新操作
    public function update2($post)
    {
        $orders = $status = $name = $tips = $code = $type1 = $mobile_icon = '';
        extract($post);
        $data = array(
            "orders" => $orders,
            "status" => $status,
            "name" => $name,
            "tips" => $tips,
            "code" => $code,
            "type" => $type1,
            'edit_status' => 1,
            'del_status' => 1,
            "createtime" => time()
        );

        if ($post['type'] == "edit") {
            $res = $this->category->where(array("id"=>$post['id']))->save($data);
            return $res !== false;
        } else if ($post['type'] == "add") {
            $res = $this->category->add($data);
        } else {
            return false;
        }
        return $res;
    }

    //获取子分类列表
    public function get_son_cate_list($post)
    {
        $m = M('category_list', 'sp_');
        $sql = "SELECT SQL_CALC_FOUND_ROWS  *
        FROM  sp_category_list
        WHERE  status <> '-1' " . $post->wheresql .
            " ORDER BY id desc " . $post->limitData;

        $sql_count = "SELECT FOUND_ROWS() as num";
        $info = $m->query($sql);
        $num = $m->query($sql_count);

        $rt["data"] = $info;
        $rt["count"] = $num[0]["num"];
        return $rt;

    }

    public function get_son_cate_id_name($pid)
    {
        return $this->category_list->field('id,name')->where(['pid' => $pid, 'status' => 1])->select();
    }

    //删除子分类
    public function son_cat_del($id)
    {
        $res = M('card')->field('id')->where(['cat_id' => $id, 'status' => 1])->count();
        if ($res > 0) {
            return false;
        }
        return $this->category_list->where(['id' => $id])->save(['status' => '-1']);
    }

    //执行更新操作
    public function update3($post)
    {
        $data = array(
            "name" => !empty($post['name']) ? $post['name'] : '',
            "pid" => !empty($post['pid']) ? $post['pid'] : '',
            "orders" => !empty($post['orders']) ? $post['orders'] : '',
            "mid" => !empty($post['mid']) ? $post['mid'] : '',
            "style" => !empty($post['style']) ? $post['style'] : '',
            "code" => !empty($post['code']) ? $post['code'] : '',
            "imgid" => !empty($post['imgid']) ? $post['imgid'] : '',
            "status" => !empty($post['status']) ? $post['status'] : '',
            "url" => !empty($post['url']) ? $post['url'] : '',
            "desc" => !empty($post['desc']) ? $post['desc'] : '',
            "sumclick" => !empty($post['sumclick']) ? $post['sumclick'] : '',
            "img_url" => !empty($post['img_url']) ? $post['img_url'] : '',
            "icon" => !empty($post['icon']) ? $post['icon'] : '',
            'edit_status' => 1,
            'del_status' => 1,
            "mobile_icon" => !empty($post['mobile_icon']) ? $post['mobile_icon'] : ''

        );

        if ($post['type'] == "edit") {
            $res = $this->category_list->where(array("id"=>$post['id']))->save($data);
            return $res !== false;
        } else if ($post['type'] == "add") {
            $res = $this->category_list->add($data);
        } else {
            return false;
        }
        return $res;
    }

    //获取分类类型
    public function get_cate_type($id)
    {
        return $this->category->where(array('id'=>$id ))->find()['type'];
    }

    //验证父级分类选择是否合法
    public function get_son_cates($id)
    {
        $arr = array();
        $data = array();
        $cates = $this->category_list->field('id,name,pid')->where(array('pid' => $id))->select();
        if (!empty($cates)) {
            foreach( $cates as $cate ) {
                $data = $this->get_son_cates($cate['id']);
            }

        }

        return array_merge($arr,$cates,$data);

    }

    public function get_select_data($data) {
        $arr = array();
        foreach ( $data as $item ) {
            $arr[$item['id']] = $item['name'];

        }
        return $arr;

    }



}