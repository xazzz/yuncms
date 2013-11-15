<?php
/**
 *
 * @author		YUNCMS Dev Team
 * @copyright	Copyright (c) 2008 - 2011, NewsTeng, Inc.
 * @license	http://www.yuncms.net/about/license
 * @link		http://www.yuncms.net
 * $Id: delete.sql.php 675 2013-07-30 03:40:40Z 85825770@qq.com $
 */
defined('IN_YUNCMS') or exit('No permission resources.');
$this->db->execute("ALTER TABLE `$tablename` DROP `$field`");