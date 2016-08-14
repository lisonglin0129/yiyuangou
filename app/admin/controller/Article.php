<?php
namespace app\admin\controller;

use app\admin\model\CategoryModel;
use think\Model;
use app\admin\model\ArticleModel;
use app\lib\Page;
use think\Controller;
use app\lib\Condition;

Class Article extends Common
{

    public function __construct()
    {
        parent::__construct();
        $this->del_model = new ArticleModel();
    }

    //文章管理页index
    public function index(){
        //文章分类
        $m_cat = new CategoryModel();
        $cates = $m_cat->get_list_by_code_mid('article_category');
        $this->assign('cates',$cates);
        return $this->fetch();
    }

    //文章详情列表页
    public function show_list(){
        $type = $keywords = null;
        $post = I("post.", []);
        extract($post);
        //获取列表
        $condition_rule = array(
            array(
                'field' => 'title',
                'operator'=>'LIKE',
                'value' => I('post.keywords')
            ),
            array(
                'field' => 'content',
                'operator'=>'LIKE',
                'value' => I('post.keywords'),
                'relation'=>'OR'
            ),
            array(
                'field' => 'category',
                'value' => $type
            )
        );
        //文章分类
        $m_cat = new CategoryModel();
        $cates = $m_cat->get_list_by_code_mid('article_category');
        $cates = $m_cat->get_select_data($cates);

        $pay_list = new ArticleModel();
        $model = new Condition($condition_rule, $this->page, $this->page_num);
        $model->init();
        $res = $pay_list->get_art_list($model);

        //生成分页
        $my_page = new Page($res["count"], $this->page_num, $this->page, U('show_list'), 5);
        $pages = $my_page->myde_write();
        $this->assign('pages', $pages);
        $this->assign('cates',$cates);
        $this->assign('art_list', $res['data']);
        return $this->fetch();
    }

    //编辑/新增
    public function exec()
    {
        $type = $_GET['type'];
        if($type!=="add"){
            $id = $_GET['id'];
            //获取该ID对应的文章信息
            $m = new ArticleModel();
            $res = $m->get_one_data($id);
            $this->assign('art_data',$res);
        }

        //文章分类
        $m_cat = new CategoryModel();
        $cates = $m_cat->get_list_by_code_mid('article_category');
        $this->assign('cates',$cates);
        $this->assign('type',$type);
        return $this->fetch('form');
    }

    //执行添加/修改
    public function update()
    {
        //获取表单信息
        $post = I("post.", []);
        extract($post);

        //标题
        empty(trim($title)) && wrong_return('文章标题不能为空');
        empty(trim($content)) && wrong_return('文章内容不能为空');

        //添加进库
        $m=new ArticleModel();
        $res = $m->update($post);
        $res && ok_return('恭喜!操作成功!');
        wrong_return('数据写入失败');

    }

    //删除
    public function del()
    {
        $id = I("post.id");
        (empty($id) || !is_numeric($id)) && wrong_return('参数异常,删除失败');
        $m = new ArticleModel();
        $m->del($id) !== false && ok_return('删除成功');
        wrong_return('删除操作失败');
    }
    //获取移动端接口列表进行文章指定
    public function confirm_article(){
        $map=[];
        //$map['admin_tpl']='移动端接口';
        $map['name']=['in','COMMON_PROBLEM,SERVICE_PROVISION,USER_AGGREMENT,HELP_URL,PROTECT_PRIVATE'];
        $list=M('conf')->field('id,name,title')->where($map)->select();
        foreach($list as $k=>$v){
            $list[$k]['action']=strtolower($v['name']);
            $v['name']=='HELP_URL'&&$list[$k]['action']='help';

        }
        $this->assign('list',$list);
        return json_encode([
            'code'=>1,
            'html'=>$this->fetch()
        ]);
    }
    public function do_confirm_article(){
        //文章id
        $aid=I('post.aid');
        //配置id
        $conf_id=I('post.conf_id');
        empty($aid)&&wrong_return('aid不能空');
        $url='http://'.$_SERVER['HTTP_HOST'].U('mobile/article/'.I('post.action'),['id'=>$aid]);
        $res=M('conf')->save(['id'=>$conf_id,'value'=>$url]);
        $res && ok_return('恭喜!操作成功!');
        wrong_return('数据写入失败');

    }
}