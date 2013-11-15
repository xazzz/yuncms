<?php
class content_input {
	public $modelid;
	public $fields;
	public $data;
	public function __construct($modelid) {
		$this->db = Loader::model ( 'model_field_model' );
		$this->db_pre = $this->db->get_prefix ();
		$this->modelid = $modelid;
		$this->fields = S ( 'model/model_field_' . $modelid );
		$this->attachment = new Attachment ( 'content', '0' );
		$this->userid = cookie ( 'userid' ) ? cookie ( 'userid' ) : 0;
		$this->attachment->set_userid ( $this->userid );
		$this->site_config = S ( 'common/common' );
	}
	public function get($data, $isimport = 0) {
		$this->data = $data;
		$info = array ();
		foreach ( $data as $field => $value ) {
			$name = $this->fields [$field] ['name'];
			$minlength = $this->fields [$field] ['minlength'];
			$maxlength = $this->fields [$field] ['maxlength'];
			$pattern = $this->fields [$field] ['pattern'];
			$errortips = $this->fields [$field] ['errortips'];
			if (empty ( $errortips )) $errortips = $name . ' ' . L ( 'not_meet_the_conditions' );
			$length = strlen ( $value );

			if ($minlength && $length < $minlength) {
				if ($isimport) {
					return false;
				} else {
					showmessage ( $name . ' ' . L ( 'not_less_than' ) . ' ' . $minlength . L ( 'characters' ) );
				}
			}
			if ($maxlength && $length > $maxlength) {
				if ($isimport) {
					$value = str_cut ( $value, $maxlength, '' );
				} else {
					showmessage ( $name . ' ' . L ( 'not_more_than' ) . ' ' . $maxlength . L ( 'characters' ) );
				}
			} elseif ($maxlength) {
				$value = str_cut ( $value, $maxlength, '' );
			}
			if ($pattern && $length && ! preg_match ( $pattern, $value ) && ! $isimport) showmessage ( $errortips );
			$MODEL = S ( 'common/model' );
			$this->db->table_name = $this->fields [$field] ['issystem'] ? $this->db_pre . $MODEL [$this->modelid] ['tablename'] : $this->db_pre . $MODEL [$this->modelid] ['tablename'] . '_data';
			if ($this->fields [$field] ['isunique'] && $this->db->where ( array ($field => $value ) )->field ( $field )->find () && ACTION != 'edit') showmessage ( $name . L ( 'the_value_must_not_repeat' ) );
			$func = $this->fields [$field] ['formtype'];
			if (method_exists ( $this, $func )) $value = $this->$func ( $field, $value );
			if ($this->fields [$field] ['issystem']) {
				$info ['system'] [$field] = $value;
			} else {
				$info ['model'] [$field] = $value;
			}
			// 颜色选择为隐藏域 在这里进行取值
			$info ['system'] ['style'] = isset ( $_POST ['style_color'] ) ? strip_tags ( $_POST ['style_color'] ) : '';
			if (isset ( $_POST ['style_font_weight'] )) $info ['system'] ['style'] = $info ['system'] ['style'] . ';' . strip_tags ( $_POST ['style_font_weight'] );
		}
		return $info;
	}
	public function box($field, $value) {
		if ($this->fields [$field] ['boxtype'] == 'checkbox') {
			if (! is_array ( $value ) || empty ( $value )) return false;
			array_shift ( $value );
			$value = ',' . implode ( ',', $value ) . ',';
			return $value;
		} elseif ($this->fields [$field] ['boxtype'] == 'multiple') {
			if (is_array ( $value ) && count ( $value ) > 0) {
				$value = ',' . implode ( ',', $value ) . ',';
				return $value;
			}
		} else {
			return $value;
		}
	}
	public function copyfrom($field, $value) {
		$field_data = $field . '_data';
		if (isset ( $_POST [$field_data] )) {
			$value .= '|' . $_POST [$field_data];
		}
		return $value;
	}
	public function datetime($field, $value) {
		$setting = string2array ( $this->fields [$field] ['setting'] );
		if ($setting ['fieldtype'] == 'int') {
			$value = strtotime ( $value );
		}
		return $value;
	}
	public function downfile($field, $value) {
		// 取得镜像站点列表
		$result = '';
		$server_list = count ( $_POST [$field . '_servers'] ) > 0 ? implode ( ',', $_POST [$field . '_servers'] ) : '';
		$result = $value . '|' . $server_list;
		return $result;
	}
	public function downfiles($field, $value) {
		$files = $_POST [$field . '_fileurl'];
		$files_alt = $_POST [$field . '_filename'];
		$array = $temp = array ();
		if (! empty ( $files )) {
			foreach ( $files as $key => $file ) {
				$temp ['fileurl'] = $file;
				$temp ['filename'] = $files_alt [$key];
				$array [$key] = $temp;
			}
		}
		$array = array2string ( $array );
		return $array;
	}
	public function editor($field, $value) {
		$setting = string2array ( $this->fields [$field] ['setting'] );
		$enablesaveimage = $setting ['enablesaveimage'];
		if (isset ( $_POST ['spider_img'] )) $enablesaveimage = 0;
		if ($enablesaveimage) {
			$watermark_enable = C ( 'attachment', 'watermark_enable' );
			$value = $this->attachment->download ( 'content', $value, $watermark_enable );
		}
		return $value;
	}
	public function groupid($field, $value) {
		$datas = '';
		if (! empty ( $_POST [$field] ) && is_array ( $_POST [$field] )) {
			$datas = implode ( ',', $_POST [$field] );
		}
		return $datas;
	}
	public function image($field, $value) {
		return trim ( $value );
	}
	public function images($field, $value) {
		// 取得图片列表
		$pictures = $_POST [$field . '_url'];
		// 取得图片说明
		$pictures_alt = isset ( $_POST [$field . '_alt'] ) ? $_POST [$field . '_alt'] : array ();
		$array = $temp = array ();
		if (! empty ( $pictures )) {
			foreach ( $pictures as $key => $pic ) {
				$temp ['url'] = $pic;
				$temp ['alt'] = $pictures_alt [$key];
				$array [$key] = $temp;
			}
		}
		$array = array2string ( $array );
		return $array;
	}
	public function textarea($field, $value) {
		if (! $this->fields [$field] ['enablehtml']) $value = strip_tags ( $value );
		return $value;
	}
	public function posid($field, $value) {
		$number = count ( $value );
		$value = $number == 1 ? 0 : 1;
		return $value;
	}
}?>