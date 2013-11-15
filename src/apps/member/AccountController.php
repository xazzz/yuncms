<?php
defined ( 'IN_YUNCMS' ) or exit ( 'No permission resources.' );
/**
 * 帐户管理
 *
 * @author Tongle Xu <xutongle@gmail.com> 2012-7-3
 * @copyright Copyright (c) 2003-2103 yuncms.net
 * @license http://leaps.yuncms.net
 * @version $Id: AccountController.php 965 2012-07-03 09:37:48Z 85825770@qq.com
 *          $
 */
class AccountController extends Web_Member {
	/**
	 * 账户管理
	 */
	public function init() {
		$memberinfo = $this->memberinfo;
		$avatar = $this->avatar;
		$grouplist = S ( 'member/grouplist' );
		$member_model = S ( 'common/member_model' );
		// 获取用户模型数据
		$this->db->set_model ( $this->memberinfo ['modelid'] );
		$member_modelinfo_arr = $this->db->getby_userid ( $this->memberinfo ['userid'] );
		$model_info = S ( 'member/model_field_' . $this->memberinfo ['modelid'] );
		if (is_array ( $model_info )) {
			foreach ( $model_info as $k => $v ) {
				if ($v ['formtype'] == 'omnipotent') continue;
				if ($v ['formtype'] == 'image') {
					$member_modelinfo [$v ['name']] = "<a href='$member_modelinfo_arr[$k]' target='_blank'><img src='$member_modelinfo_arr[$k]' height='40' widht='40' onerror=\"this.src='" . IMG_PATH . "member/nophoto.gif'\"></a>";
				} elseif ($v ['formtype'] == 'datetime' && $v ['fieldtype'] == 'int') { // 如果为日期字段
					$member_modelinfo [$v ['name']] = Format::date ( $member_modelinfo_arr [$k], $v ['format'] == 'Y-m-d H:i:s' ? 1 : 0 );
				} elseif ($v ['formtype'] == 'images') {
					$tmp = string2array ( $member_modelinfo_arr [$k] );
					$member_modelinfo [$v ['name']] = '';
					if (is_array ( $tmp )) {
						foreach ( $tmp as $tv ) {
							$member_modelinfo [$v ['name']] .= " <a href='$tv[url]' target='_blank'><img src='$tv[url]' height='40' widht='40' onerror=\"this.src='" . IMG_PATH . "member/nophoto.gif'\"></a>";
						}
						unset ( $tmp );
					}
				} elseif ($v ['formtype'] == 'box') { // box字段，获取字段名称和值的数组
					$tmp = explode ( "\n", $v ['options'] );
					if (is_array ( $tmp )) {
						foreach ( $tmp as $boxv ) {
							$box_tmp_arr = explode ( '|', trim ( $boxv ) );
							if (is_array ( $box_tmp_arr ) && isset ( $box_tmp_arr [1] ) && isset ( $box_tmp_arr [0] )) {
								$box_tmp [$box_tmp_arr [1]] = $box_tmp_arr [0];
								$tmp_key = intval ( $member_modelinfo_arr [$k] );
							}
						}
					}
					if (isset ( $box_tmp [$tmp_key] )) {
						$member_modelinfo [$v ['name']] = $box_tmp [$tmp_key];
					} else {
						$member_modelinfo [$v ['name']] = $member_modelinfo_arr [$k];
					}
					unset ( $tmp, $tmp_key, $box_tmp, $box_tmp_arr );
				} elseif ($v ['formtype'] == 'linkage') { // 如果为联动菜单
					$tmp = string2array ( $v ['setting'] );
					$tmpid = $tmp ['linkageid'];
					$linkagelist = S ( 'linkage' . $tmpid );
					$fullname = $this->_get_linkage_fullname ( $member_modelinfo_arr [$k], $linkagelist );
					$member_modelinfo [$v ['name']] = substr ( $fullname, 0, - 1 );
					unset ( $tmp, $tmpid, $linkagelist, $fullname );
				} else {
					$member_modelinfo [$v ['name']] = $member_modelinfo_arr [$k];
				}
			}
		}
		include template ( 'member', 'account_manage' );
	}

	/**
	 * 修改详细信息
	 */
	public function account_manage_info() {
		if (isset ( $_POST ['dosubmit'] )) {
			// 更新用户昵称
			$nickname = isset ( $_POST ['nickname'] ) && trim ( $_POST ['nickname'] ) ? trim ( $_POST ['nickname'] ) : '';
			if ($nickname) {
				$this->db->where(array ('userid' => $this->memberinfo ['userid'] ))->update ( array ('nickname' => $nickname ) );
				if (! isset ( $cookietime )) {
					$get_cookietime = cookie ( 'cookietime' );
				}
				$_cookietime = isset ( $cookietime ) ? intval ( $cookietime ) : ($get_cookietime ? $get_cookietime : 0);
				$cookietime = $_cookietime ? TIME + $_cookietime : 0;
				cookie ( '_nickname', $nickname, $cookietime );
			}
			if (isset ( $_POST ['info'] )) {
				require_once CACHE_MODEL_PATH . 'member_input.php';
				require_once CACHE_MODEL_PATH . 'member_update.php';
				$member_input = new member_input ( $this->memberinfo ['modelid'] );
				$modelinfo = $member_input->get ( $_POST ['info'] );
				$this->db->set_model ( $this->memberinfo ['modelid'] );
				$membermodelinfo = $this->db->getby_userid ( $this->memberinfo ['userid'] );
				if (! empty ( $membermodelinfo )) {
					$this->db->where(array ('userid' => $this->memberinfo ['userid'] ))->update ( $modelinfo );
				} else {
					$modelinfo ['userid'] = $this->memberinfo ['userid'];
					$this->db->insert ( $modelinfo );
				}
			}

			showmessage ( L ( 'operation_success' ), HTTP_REFERER );
		} else {
			$memberinfo = $this->memberinfo;
			// 获取会员模型表单
			require CACHE_MODEL_PATH . 'member_form.php';
			$member_form = new member_form ( $this->memberinfo ['modelid'] );
			$this->db->set_model ( $this->memberinfo ['modelid'] );

			$membermodelinfo = $this->db->getby_userid ( $this->memberinfo ['userid'] );
			$forminfos = $forminfos_arr = $member_form->get ( $membermodelinfo );

			// 万能字段过滤
			foreach ( $forminfos as $field => $info ) {
				if ($info ['isomnipotent']) {
					unset ( $forminfos [$field] );
				} else {
					if ($info ['formtype'] == 'omnipotent') {
						foreach ( $forminfos_arr as $_fm => $_fm_value ) {
							if ($_fm_value ['isomnipotent']) {
								$info ['form'] = str_replace ( '{' . $_fm . '}', $_fm_value ['form'], $info ['form'] );
							}
						}
						$forminfos [$field] ['form'] = $info ['form'];
					}
				}
			}
			$formValidator = $member_form->formValidator;
			include template ( 'member', 'account_manage_info' );
		}
	}

	/**
	 * 修改邮箱密码
	 */
	public function account_manage_password() {
		if (isset ( $_POST ['dosubmit'] )) {
			if (! Validate::is_password ( $_POST ['info'] ['password'] )) {
				showmessage ( L ( 'password_format_incorrect' ), HTTP_REFERER );
			}
			if ($this->memberinfo ['password'] != password ( $_POST ['info'] ['password'], $this->memberinfo ['encrypt'] )) {
				showmessage ( L ( 'old_password_incorrect' ), HTTP_REFERER );
			}
			// 修改会员邮箱
			if ($this->memberinfo ['email'] != $_POST ['info'] ['email'] && Validate::is_email ( $_POST ['info'] ['email'] )) {
				$email = $_POST ['info'] ['email'];
				$updateinfo ['email'] = $_POST ['info'] ['email'];
			} else {
				$email = '';
			}
			$newpassword = password ( $_POST ['info'] ['newpassword'], $this->memberinfo ['encrypt'] );
			$updateinfo ['password'] = $newpassword;

			$this->db->where(array ('userid' => $this->memberinfo ['userid'] ))->update ( $updateinfo );
			if (ucenter_exists ()) {
				$res = Loader::lib ( 'Ucenter' )->uc_user_edit ( $this->memberinfo ['username'], $_POST ['info'] ['password'], $_POST ['info'] ['newpassword'], '', $this->memberinfo ['encrypt'], 1 );
			}
			showmessage ( L ( 'operation_success' ), HTTP_REFERER );
		} else {
			$show_validator = true;
			$memberinfo = $this->memberinfo;
			include template ( 'member', 'account_manage_password' );
		}
	}

	/**
	 * 用户升级
	 */
	public function account_manage_upgrade() {
		$memberinfo = $this->memberinfo;
		$grouplist = S ( 'member/grouplist' );
		if (empty ( $grouplist [$memberinfo ['groupid']] ['allowupgrade'] )) {
			showmessage ( L ( 'deny_upgrade' ), HTTP_REFERER );
		}
		if (isset ( $_POST ['upgrade_type'] ) && intval ( $_POST ['upgrade_type'] ) < 0) {
			showmessage ( L ( 'operation_failure' ), HTTP_REFERER );
		}

		if (isset ( $_POST ['upgrade_date'] ) && intval ( $_POST ['upgrade_date'] ) < 0) {
			showmessage ( L ( 'operation_failure' ), HTTP_REFERER );
		}

		if (isset ( $_POST ['dosubmit'] )) {
			$groupid = isset ( $_POST ['groupid'] ) ? intval ( $_POST ['groupid'] ) : showmessage ( L ( 'operation_failure' ), HTTP_REFERER );

			$upgrade_type = isset ( $_POST ['upgrade_type'] ) ? intval ( $_POST ['upgrade_type'] ) : showmessage ( L ( 'operation_failure' ), HTTP_REFERER );
			$upgrade_date = ! empty ( $_POST ['upgrade_date'] ) ? intval ( $_POST ['upgrade_date'] ) : showmessage ( L ( 'operation_failure' ), HTTP_REFERER );

			// 消费类型，包年、包月、包日，价格
			$typearr = array ($grouplist [$groupid] ['price_y'],$grouplist [$groupid] ['price_m'],$grouplist [$groupid] ['price_d'] );
			// 消费类型，包年、包月、包日，时间
			$typedatearr = array ('366','31','1' );
			// 消费的价格
			$cost = $typearr [$upgrade_type] * $upgrade_date;
			// 购买时间
			$buydate = $typedatearr [$upgrade_type] * $upgrade_date * 86400;
			$overduedate = $memberinfo ['overduedate'] > TIME ? ($memberinfo ['overduedate'] + $buydate) : (TIME + $buydate);

			if ($memberinfo ['amount'] >= $cost) {
				$this->db->where(array ('userid' => $memberinfo ['userid'] ))->update ( array ('groupid' => $groupid,'overduedate' => $overduedate,'vip' => 1 ) );
				// 消费记录
				Loader::lib ( 'pay:spend', false );
				spend::amount ( $cost, L ( 'allowupgrade' ), $memberinfo ['userid'], $memberinfo ['username'] );
				showmessage ( L ( 'operation_success' ), U ( 'member/index/init' ) );
			} else {
				showmessage ( L ( 'operation_failure' ), HTTP_REFERER );
			}
		} else {
			$groupid = isset ( $_GET ['groupid'] ) ? intval ( $_GET ['groupid'] ) : '';
			// 获取头像数组
			$avatar = get_memberavatar ( $this->memberinfo ['userid'], false );
			$memberinfo ['groupname'] = $grouplist [$memberinfo [groupid]] ['name'];
			$memberinfo ['grouppoint'] = $grouplist [$memberinfo [groupid]] ['point'];
			unset ( $grouplist [$memberinfo ['groupid']] );
			include template ( 'member', 'account_manage_upgrade' );
		}
	}
}