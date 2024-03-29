<?php
/**
 *
 * @author Tongle Xu <xutongle@gmail.com> 2013-2-26
 * @copyright Copyright (c) 2003-2103 yuncms.net
 * @license http://leaps.yuncms.net
 * @version $Id: Update.php 699 2013-07-31 02:23:33Z 85825770@qq.com $
 */
class Update {
	public $applications;
	public $update_url;
	public $http;

	public function __construct() {
		$this->update_url = 'http://www.tintsoft.com/api.php?controller=License';
		$this->http = Loader::lib ( 'HttpClient' );
		$this->uuid = $this->check_uuid ();
	}

	public function check() {
		$url = $this->url ( 'check' );
		$data = $this->http->get ( $url );
		if (! $data) return false;
		return $data;
	}

	function url($action = 'check') {
		$_username = cookie ( '_username' );
		$applications = '';
		$site = S ( 'common/common' );
		$sitename = $site ['site_name'];
		$siturl = SITE_URL;
		$pars = array ('action' => $action,'productid'=>1,'sitename' => $sitename,'siteurl' => $siturl,'charset' => CHARSET,'version' => YUNCMS_VERSION,'release' => YUNCMS_RELEASE,'os' => PHP_OS,'php' => phpversion (),'mysql' => Loader::model ( 'admin_model' )->version (),'browser' => urlencode ( $_SERVER ['HTTP_USER_AGENT'] ),'username' => urlencode ( cookie ( 'admin_username' ) ),'email' => urlencode ( cookie ( 'admin_email' ) ),'uuid' => urlencode ( $this->uuid ) );
		$data = http_build_query ( $pars );
		$verify = md5 ( $this->uuid );
		return $this->update_url . '&' . $data . '&verify=' . $verify;
	}

	function notice() {
		return $this->url ( 'notice' );
	}

	function download() {
		// TODO
	}

	public function check_uuid() {
		$uuid = C ( 'version', 'uuid' );
		if (! empty ( $uuid )) {
			return $uuid;
		} else {
			$uuid = uuid ( C ( 'version', 'product' ) . '-' );
			Base_Config::modify ( 'version', array ('uuid' => $uuid ) );
			return $uuid;
		}
	}
}