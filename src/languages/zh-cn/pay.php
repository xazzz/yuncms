<?php
/**
 * Language Format:
 * Add a new file(.php) with your app name at /sources/languages/
 * translation save at the array:$LANG
 */
$LANG['trade_sn'] = '支付单号';
$LANG['addtime'] = '订单时间';
$LANG['to'] = '至';
$LANG['confirm_pay'] = '确认并支付';
$LANG['usernote'] = '备注';
$LANG['adminnote'] = '管理员操作';
$LANG['user_balance'] = '用户余额：';
$LANG['yuan'] = '&nbsp;元';
$LANG['dian'] = '&nbsp;点';
$LANG['trade_succ'] = '成功';
$LANG['checking'] = '验证中..';
$LANG['user_not_exist'] = '该用户不存在';

$LANG['input_price_to_change'] = '输入修改数量（资金或者点数）';
$LANG['number'] = '数量 ';
$LANG['must_be_price'] = '必须为金额，最多保留两位小数';
$LANG['reason_of_modify'] = '要修改的理由';

//modify_deposit.php
$LANG['recharge_type'] = '充值类型';
$LANG['capital'] = '资金';
$LANG['point'] = '点数';
$LANG['recharge_quota'] = '充值额度';
$LANG['increase'] = '增加';
$LANG['reduce'] = '减少';
$LANG['trading'] = '交易';
$LANG['op_notice'] = '提醒操作';
$LANG['op_sendsms'] = '发送短消息通知会员';
$LANG['op_sendemail'] = '发送e-mail通知会员';
$LANG['send_account_changes_notice'] = '账户变更通知';
$LANG['background_operation'] = '后台操作';
$LANG['account_changes_notice_tips'] = '尊敬的{username},您好！<br/>您的账户于{time}发生变动,操作：{op},理由:{note},当前余额：{amount}元，{point}积分。';

//payment.php
$LANG['basic_config'] = '基本设置';
$LANG['contact_email'] = '联系邮箱';
$LANG['contact_phone'] = '联系电话';
$LANG['order_info'] = '订单信息';
$LANG['order_sn'] = '支付单号';
$LANG['order_name'] = '名称';
$LANG['order_price'] = '订单价格';
$LANG['order_discount'] = '交易加价/涨价';
$LANG['order_addtime'] = '订单生成时间';
$LANG['order_ip'] = '订单生成IP';
$LANG['payment_type'] = '支付类型';
$LANG['order'] = '订单';
$LANG['disount_notice'] = '要给顾客便宜10元,降价请输入“-10”';

$LANG['discount'] = '订单改价';
$LANG['recharge'] = '在线充值';
$LANG['offline'] = '线下支付';
$LANG['online'] = '在线支付';
$LANG['selfincome'] = '自助获取';

$LANG['order_time'] = '支付时间';
$LANG['business_mode'] = '业务方式';
$LANG['payment_mode'] = '支付方式';
$LANG['deposit_amount'] = '存入金额';
$LANG['pay_status'] = '付款状态';
$LANG['pay_btn'] = '付款';



$LANG['check_confirm'] = '确认要通过订单  {sn} 审核？';
$LANG['check_passed'] = '审核通过';

$LANG['change_price'] = '改价';
$LANG['check'] = '审核';
$LANG['closed'] = '关闭';

$LANG['thispage'] = '本页';
$LANG['finance'] = '财务';
$LANG['totalize'] = '总计';
$LANG['amount'] = '金额';
$LANG['total'] = '总';
$LANG['bi'] = '笔';
$LANG['trade_succ'] = '成功';
$LANG['transactions'] = '交易量';
$LANG['trade'] = '交易';
$LANG['trade_record_del'] = '确认删除该记录？';

/******************error & notice********************/

$LANG['illegal_sign'] = '签名错误';
$LANG['illegal_notice'] = '通知错误';
$LANG['illegal_return'] = '信息返回错误';
$LANG['illegal_pay_method'] = '支付方式错误';
$LANG['illegal_creat_sn'] = '订单号生成错误';


$LANG['pay_success'] = '恭喜您，支付成功';
$LANG['pay_failed'] = '支付失败，请联系管理员';
$LANG['payment_failed'] = '支付方式发生错误';
$LANG['order_closed_or_finish'] = '订单已完成或该已经关闭';
$LANG['state_change_succ'] = '状态修改完成';

$LANG['delete_succ'] = '删除成功';
$LANG['public_discount_succ'] = '操作成功';
$LANG['admin_recharge'] = '后台充值';

/******************pay status********************/
$LANG['all_status'] = '全部状态';
$LANG['succ'] = '<font color="green" class="onCorrect">支付成功</font>';
$LANG['failed'] = '支付失败';
$LANG['cancel'] = '取消取消';
$LANG['error'] = '处理异常';
$LANG['invalid'] = '非法参数';
$LANG['progress'] = '<font color="orange" class="onTime">交易处理中</font>';
$LANG['timeout'] = '超时';
$LANG['ready'] = '<font color="orange" class="onTime">准备中</font>';

$LANG['select']['succ'] = '支付成功';
$LANG['select']['failed'] = '支付失败';
$LANG['select']['cancel'] = '支付取消';
$LANG['select']['error'] = '处理异常';
$LANG['select']['invalid'] = '非法参数';
$LANG['select']['progress'] = '处理中';
$LANG['select']['timeout'] = '超时';
$LANG['select']['ready'] = '准备中';

/*************pay plus language***************/
$LANG['userid'] = '用户ID';
$LANG['op'] = '操作人';
$LANG['expenditure_patterns'] = '消费类型';
$LANG['money'] = '金钱';
$LANG['point'] = '积分';
$LANG['from'] = '从';
$LANG['content_of_consumption'] = '消费内容';
$LANG['empdisposetime'] = '消费时间';
$LANG['consumption_quantity'] = '消费数量';
$LANG['self'] = '自身';
$LANG['wrong_time_over_time_to_time_less_than'] = '错误的时间格式，结束时间小于开始时间！';

$LANG['spend_msg_1'] = '请对消费内容进行描述。';
$LANG['spend_msg_2'] = '请输入消费金额。';
$LANG['spend_msg_3'] = '用户不能为空。';
$LANG['spend_msg_6'] = '账户余额不足。';
$LANG['spend_msg_7'] = '消费类型为空。';
$LANG['spend_msg_8'] = '数据存入数据库时出错。';
$LANG['bank_transfer'] = '银行转账';
$LANG['transfer'] = '银行汇款/转账';
$LANG['dsa'] = 'DSA 签名方法待后续开发，请先使用MD5签名方式';
$LANG['alipay_error'] = '支付宝暂不支持{sign_type}类型的签名方式';
$LANG['execute_date'] = '执行日期';
$LANG['query_stat'] = '查询统计';
$LANG['total_transactions'] = '总交易数';
$LANG['transactions_success'] = '成功交易';
$LANG['pay_tip'] = '我们目前支持的汇款方式，请根据您选择的支付方式来选择银行汇款。汇款以后，请立即通知我们。';
/* End of file zh-cn.lang.php */