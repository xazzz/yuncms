<?php
defined ( 'IN_YUNCMS' ) or exit ( 'No permission resources.' );
return array (
			'code' => 'alipay',
			'name' => '支付宝',
			'desc' => 'aaa',
			'is_cod' => '0',
			'is_online' => '1',
			'version' => '1.0',
			'author' => 'YUNCMS TEAM',
			'website' => 'http://www.tintsoft.com',
			'config' => array (
							'member_id' => array (
												'label' => '支付宝帐户',
												'type' => 'text',
												'value' => '' ),
							'alipay_key' => array (
												'label' => '交易安全校验码',
												'type' => 'text',
												'value' => '' ),
							'alipay_partner' => array (
													'label' => '合作者身份ID',
													'type' => 'text',
													'value' => '' ),
							'alipay_pay_method' => array (
														'label' => '选择接口类型',
														'type' => 'select',
														'value' => '',
														'range' => array (
																		1 => '使用标准双接口',
																		2 => '使用担保交易接口',
																		3 => '使用即时到帐交易接口' ) ) ) );