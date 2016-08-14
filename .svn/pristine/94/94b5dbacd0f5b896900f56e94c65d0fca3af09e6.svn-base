<?php
/**
 * Created by PhpStorm.
 * User: lingjiang
 * Date: 2016/6/6
 * Time: 9:31
 */
namespace app\admin\controller;

use app\admin\model\CardModel;
use app\admin\model\CategoryModel;
use app\core\lib\Condition;
use app\core\lib\Page;
use think\Exception;

class Card extends Common{
    private  $c_model;
    public function __construct(){
        parent::__construct();
        $this->c_model=new CategoryModel();
    }
    //添加卡密分类
    public function category_form(){
        $type='add';
        if(I('get.id')){
            $m_card = new CardModel();
            $cate_data=$m_card->category_detail(I('get.id'));
            $this->assign('cate_data',$cate_data);
            $type='edit';
        }
        $this->assign('type',$type);
        return $this->display();
    }
    //更新分类
    public function cat_update(){
        $post=I('post.');
        //$post['pid']=$this->c_model->get_category_info_by_code('kami')['id'];
        $card_model=new CardModel();
        $res=$card_model->category_update($post);
        $res && ok_return('恭喜!操作成功!');
        wrong_return('数据写入失败');
    }
    //卡密分类列表
    public function category_list(){
        return $this->display();
    }
    public function category_show_list(){
        //获取列表
        $m_card = new CardModel();
        $condition_rule = array(
        );
        $model = new Condition($condition_rule, $this->page, $this->page_num);
        $model->init();
        $res = $m_card->category_list($model);
        //统计卡密数量
//        $card_model=M('card');
//        foreach($res['data'] as &$v){
//            $v['npf_count']=$card_model->field('id')->where(['cat_id'=>$v['id'],'status'=>1])->count();
//            $v['pf_count']=$card_model->field('id')->where(['cat_id'=>$v['id'],'status'=>2])->count();
//            $v['total']=$v['npf_count']+$v['pf_count'];
//        }
        //生成分页
        $my_page = new Page($res["count"], $this->page_num, $this->page, U('category_show_list'), 5);
        $pages = $my_page->myde_write();
        $this->assign('pages', $pages);
        $this->assign('cate_list', $res['data']);
        return $this->fetch();
    }
    //分类删除
    public function cat_del(){
        $m_card = new CardModel();
        $res=$m_card->category_del(I('post.id'));
        $res && ok_return('删除成功!');
        wrong_return('删除失败');
    }
    //添加卡密
    public function card_form(){
        $card_model=new CardModel();
        $type='add';
        if(!$cats=$card_model->category_list_simple()){
            $this->redirect('category_form');
            exit;
        }
        if(I('get.id')){
          $this->assign('card_data',$card_model->get_card_info(I('get.id')));
            $type='edit';
        }
        $this->assign('cats',$cats);
        $this->assign('type',$type);
        return $this->display();
    }
    //卡密添加
    public function card_update(){
        $card_model=new CardModel();
        $res=$card_model->card_update(I('post.'));
        $res && ok_return('恭喜!操作成功!');
        wrong_return('数据写入失败');
    }
    //卡密列表
    public function card_list(){
        $card_model=new CardModel();
        if(!$cats=$card_model->category_list_simple()){
            $this->redirect('category_form');
            exit;
        }
        $this->assign('cats',$cats);
        return $this->display();
    }
    //卡密删除
    public function card_del(){
        $card_model=new CardModel();
        $res=$card_model->card_del(I('post.id'));
        $res && ok_return('删除成功!');
        wrong_return('删除失败');
    }
    public function card_show_list(){
        $card_model=new CardModel();
        $model = new Condition([], $this->page, $this->page_num);
        $model->init();
        $res = $card_model->get_cards($model);
        //生成分页
        $my_page = new Page($res["count"], $this->page_num, $this->page, U('card_show_list'), 5);
        $pages = $my_page->myde_write();
        $this->assign('pages', $pages);
        $this->assign('card_list', $res['data']);
        return $this->fetch();
    }
    public function card_import(){
        $card_model=new CardModel();
        if(!$cats=$card_model->category_list_simple()){
            $this->redirect('category_form');
            exit;
        }
        $this->assign('cats',$cats);
        return $this->fetch();
    }
    public function card_import_do(){
        $category = I('post.category',0,'trim,intval');
        if ($category<=0) {
            echo '请选择分类！';
            exit;
        }
        $filename = $_FILES['file']['tmp_name'];
        if (empty ($filename)) {
            echo '请选择要导入的CSV文件！';
            exit;
        }
        $handle = fopen($filename, 'r');
        try{
            $data = $this->input_csv($handle);
        }catch(Exception $ex){
            echo '解析CSV失败，请检查格式；<hr/>'.$ex->getMessage();
            exit;
        }
        $c = count($data);
        if($c==0){
            echo '没有任何数据';
            exit;
        }

        try{
            $m_card =new CardModel();
            for($i=0;$i<$c;$i+=100){
                $frag100 = [];
                for($j=$i;$j<$i+100&&$j<$c;$j++){
                    if($i==0 && $j ==0 )continue;
                    array_push($frag100,[
                        'category'=>$category,
                        'num'=>$data[$j][0],
                        'sec'=>$data[$j][1],
                        'stat'=>0,
                        'used'=>0,
                    ]);
                }
                $m_card->card_import($frag100);
            }
        }catch(Exception $ex){
            echo '导入失败；<hr/>'.$ex->getMessage().$ex->getLine();
            exit;
        }
        echo '导入完成.';
    }
    private function input_csv($handle) {
    $out = array ();
    $n = 0;
    while ($data = fgetcsv($handle)) {
        $num = count($data);
        //i=1 忽略表头(第一行)
        for ($i = 0; $i < $num; $i++) {
            $out[$n][$i] = $data[$i];
        }
        $n++;
    }
    return $out;
}

    //下载demo
    public function download() {
        $content = "#号码#,#密码#,\"(此行为说明,正式导入文件,不含有此行.)\"111,22222,(此行为正式文件第一行)";
        if( empty( $file_name ) )
        {
            $file_name = date("Ymd").".csv";
        }
        header( "Cache-Control: public" );
        header( "Pragma: public" );
        header( "Content-type: text/csv" ) ;
        header( "Content-Disposition: attchment; filename={$file_name}" ) ;
        header( "Content-Length: ". strlen( $content ) );
        echo $content;
        exit;

    }

}
