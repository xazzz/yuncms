<?php
class content_update {
    public $modelid;
    public $fields;
    public $data;

    public function __construct($modelid, $id) {
        $this->modelid = $modelid;
        $this->fields = S ( 'model/model_field_' . $modelid );
        $this->id = $id;
    }

    public function update($data) {
        $info = array ();
        $this->data = $data;
        foreach ( $data as $field => $value ) {
            if (! isset ( $this->fields [$field] ))
                continue;
            $func = $this->fields [$field] ['formtype'];
            $info [$field] = method_exists ( $this, $func ) ? $this->$func ( $field, $value ) : $value;
        }
    }

    public function posid($field, $value) {
    	if(!empty($value) && is_array($value)) {
    		if($_GET['a']=='add') {
    			$position_data_db = Loader::model('position_data_model');
    			$textcontent = array();
    			foreach($value as $r) {
    				if($r!='-1') {
    					if(empty($textcontent)) {
    						foreach($this->fields AS $_key=>$_value) {
    							if($_value['isposition']) {
    								$textcontent[$_key] = $this->data[$_key];
    							}
    						}
    						$textcontent = array2string($textcontent);
    					}

    					$position_data_db->insert(array('id'=>$this->id,'catid'=>$this->data['catid'],'posid'=>$r,'application'=>'content','modelid'=>$this->modelid,'data'=>$textcontent,'listorder'=>$this->id));
    				}
    			}
    		} else {
    			$posids = array();
    			$catid = $this->data['catid'];
    			$push_api = Loader::lib('admin:push_api');
    			foreach($value as $r) {
    				if($r!='-1') $posids[] = $r;
    			}
    			$textcontent = array();
    			foreach($this->fields AS $_key=>$_value) {
    				if($_value['isposition']) {
    					$textcontent[$_key] = $this->data[$_key];
    				}
    			}
    			//颜色选择为隐藏域 在这里进行取值
    			$textcontent['style'] = isset($_POST['style_color']) ? strip_tags($_POST['style_color']) : '';
    			$textcontent['inputtime'] = strtotime($textcontent['inputtime']);
    			if($_POST['style_font_weight']) $textcontent['style'] = $textcontent['style'].';'.strip_tags($_POST['style_font_weight']);
    			$push_api->position_update($this->id, $this->modelid, $catid, $posids, $textcontent);
    		}
    	}
    }
}?>