<?php
namespace app\admin\model;
use app\pay\controller\Aipay;

Class OrderListParentModel
{
    private $m;

    function __construct()
    {
        $this->m = M("order_list_parent", "sp_");
    }

    public function get_pay_list($post)
    {

        $sql = "SELECT SQL_CALC_FOUND_ROWS o.*,u.username,u.nick_name
        FROM  sp_order_list_parent o
        LEFT JOIN sp_users u  on u.id= o.uid
        WHERE  o.status <> '-1' AND o.bus_type = 'recharge' " . $post->wheresql .
            " ORDER BY o.id desc " . $post->limitData;

        $sql_count = "SELECT FOUND_ROWS() as num";
        $pay_info = $this->m->query($sql);
        $num = $this->m->query($sql_count);

        $rt["data"] = $pay_info;
        $rt["count"] = $num[0]["num"];
        return $rt;
    }

    //微信主动查询订单状态
    private function get_wx_order_status($order_id) {
        //微信统一订单
        import("app.lib.Wxpay.WxPayApi",null,'.php');
        $order = new \WxPayUnifiedOrder();
        $order->SetOut_trade_no($order_id);

        //进行状态查询
        $wxpai = new \WxPayApi();
        return $wxpai->orderQuery($order);

    }

    //根据订单号主动查询订单状态
    public function get_miss_document_detail($order_id,$plat='all') {
        $m_order = $this->m->where(array('order_id'=>$order_id))->find();
        $data = array();

        //code -101 获取订单信息失败
        if ( is_null($m_order) ) {
            return $this->implode_data(-101,'','fail');
        }
        //支付成功,无需进行支付平台订单查询
        if ( $m_order['pay_status'] == 3 || $m_order['pay_status'] == -1 ) {
            return $this->implode_data(-100,'','fail');
        }

        //微信平台支付
        if ( $plat == 'wxpay' || $plat == 'all' ) {
            list($res,$status) = $this->wxpay_detail($order_id);
            if( $status == 'fail' && $plat == 'wxpay' ) {
                //拼接失败数据
                return $this->implode_data($res,'wxpay','fail');
            } else if ( $status == 'success' ) {
                //拼接成功数据
                $data = $this->implode_data($res,'wxpay','success');
                return $data;
            }

        }

        //爱贝支付
        if ( $plat == 'aipay'  || $plat == 'all' ) {
            list($res,$status) = $this->aipay_detail($order_id);
            if( $status == 'fail' && $plat == 'aipay' ) {
                //拼接失败数据
                return $this->implode_data($res,'aipay','fail');
            } else if ( $status == 'success' ) {
                //拼接成功数据
                $data = $this->implode_data($res,'aipay','success');
                return $data;
            }


        }
        //支付宝支付
        if ( $plat == 'alipay' || $plat == 'all' ) {

        }
        $data = $this->implode_data('-202','','fail');
        return $data;

    }

    //拼接返回数据
    private function implode_data($money,$pay_plat,$status) {
        $data = array();
        //失败拼接CODE成功则为消费金额
        if ( $status == 'fail' ) {
            $data['code'] = $money;
        } else {
            $data['money'] = $money;
        }

        $data['pay_plat'] = $pay_plat;
        $data['status'] = $status;

        return $data;

    }

    /**
     * code 100 操作成功
     * code -100 支付成功,无需进行支付平台订单查询
     * code -101 获取订单信息失败
     * code -102 改变订单状态失败
     * code -200 支付平台查不到订单
     * code -201 平台付款不成功
     * code -202 商户号mch_id与appid不匹配
     * code -301 获取用户信息失败
     * code -302 返还用户余额失败
     * code -400 未知错误
     *
     */

    public function give_back_money($order_id,$pay_type) {
        $m_order = $this->m->where(array('order_id' => $order_id))->find();
        //code -101 获取订单信息失败
        if ( is_null($m_order) ) {
            return [-101,'fail'];
        }
        if ( $m_order['pay_status'] == 3 || $m_order['pay_status'] == -1 ) {
            return [-100,'fail'];
        }


        switch ($pay_type) {
            case 'aipay':
                list($money,$status) = $this->aipay_detail($order_id);
                break;
            case 'ailipay' :
                break;
            case 'wxpay' :
                list($money,$status) = $this->wxpay_detail($order_id);
                break;

        }

        if ( $status == 'fail' ) {
            //code -200 支付平台查不到订单
            return [$status,'fail'];
        }

        $uid = $m_order['uid'];
        $user = M('users')->where(array('id'=>$uid,'status'=>1))->find();
        if ( is_null($user) ) {
            //code -301 获取用户信息失败
            return [-301,'fail'];
        }
        $user_money = !is_null($user['money'])?$user['money']:0;
        $user_money = (float) $user_money + (float) $m_order['price'];

        $order_status = $this->m->where(array('order_id'=>$order_id))->save(array('pay_status'=>-1));
        if ( $order_status === false ) {
            //code -102 改变订单状态失败
            return [-102,'fail'];

        }
        //返还用户金额
        $set_user_status = M('users')->where(array('id'=>$uid))->save(array('money'=>$user_money));
        if ( $set_user_status === false ) {
            // code -302 返还用户余额失败
            return [-302,'fail'];
        }

        return [100,'success'];



    }


    //爱贝支付平台主动查询流程
    private function aipay_detail($order_id) {
        $aipay = new Aipay();
        $data = $aipay->ReqData($order_id);
        $order_info = json_decode($data,true);

        if ( !$order_info ) {
            //code -200 支付平台查不到订单
            return [-200,'fail'];

        }

        //code -201 平台付款不成功
        return $order_info['result'] === 0?[$order_info['money'],'success']:[-201,'fail'];

    }

    //微信支付平台主动查询流程
    private function wxpay_detail($order_id) {
        $data = $this->get_wx_order_status($order_id);
        if ( isset($data['return_msg']) && $data['return_msg'] == '商户号mch_id与appid不匹配' ) {
            //code -202 商户号mch_id与appid不匹配
            return [-202,'fail'];
        }

        if ( $data['return_code'] == 'FAIL' && isset($data['err_code']) && $data['err_code'] == 'ORDERNOTEXIST' ) {
            //code -200 支付平台查不到订单
            return [-200,'fail'];
        }

        //查找到订单,未付成功返回错误
        if ( $data['return_code'] == 'SUCCESS' && $data['trade_state'] != 'SUCCESS' ) {
            //code -201 平台付款不成功
            return [-201,'fail'];

        } else if ($data['return_code'] == 'SUCCESS' && $data['trade_state'] == 'SUCCESS') {//找到订单支付成功返还支付金额
            return [$data['total_fee'],'success'];

        }

        //code -400 未知错误
        return [-400,'fail'];

    }



}