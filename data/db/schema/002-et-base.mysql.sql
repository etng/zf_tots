# --------------------------------------------------------
# Host:                         127.0.0.1
# Server version:               5.5.18-log
# Server OS:                    Win32
# HeidiSQL version:             6.0.0.3603
# Date/time:                    2011-12-16 15:01:55
# --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

# Dumping structure for table zf_tots.et_node
DROP TABLE IF EXISTS `et_node`;
CREATE TABLE IF NOT EXISTS `et_node` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '编号',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户',
  `type` varchar(12) NOT NULL COMMENT '类型',
  `lang` varchar(12) NOT NULL COMMENT '语言',
  `code` varchar(32) NOT NULL COMMENT '缩略',
  `name` varchar(255) NOT NULL COMMENT '英文名称',
  `description` varchar(512) NOT NULL COMMENT '英文描述',
  `ds_name` varchar(32) NOT NULL COMMENT '数据源名称',
  `ds_table` varchar(32) NOT NULL COMMENT '数据源表名',
  `ds_column` varchar(32) NOT NULL COMMENT '数据源字段名',
  `ds_type` varchar(32) NOT NULL COMMENT '数据源数据类型',
  `ds_pre_hook` varchar(100) NOT NULL COMMENT '数据源预处理钩子',
  `ds_post_hook` varchar(100) NOT NULL COMMENT '数据源后处理钩子',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='实体节点';

# Data exporting was unselected.


# Dumping structure for table zf_tots.et_tree
DROP TABLE IF EXISTS `et_tree`;
CREATE TABLE IF NOT EXISTS `et_tree` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '编号',
  `parent_id` int(10) unsigned NOT NULL COMMENT '节点Id',
  `type` varchar(12) NOT NULL COMMENT '孩子类型',
  `node_id` int(10) unsigned NOT NULL COMMENT '孩子ID',
  PRIMARY KEY (`id`),
  KEY `type_parent_id` (`parent_id`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='树形结构';

# Data exporting was unselected.


# Dumping structure for table zf_tots.et_user
DROP TABLE IF EXISTS `et_user`;
CREATE TABLE IF NOT EXISTS `et_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '编号',
  `name` varchar(50) NOT NULL COMMENT '姓名',
  `title` varchar(50) NOT NULL COMMENT '职位',
  `pass` char(32) NOT NULL COMMENT '密码',
  `avatar` varchar(255) NOT NULL COMMENT '头像',
  `signature` varchar(255) NOT NULL COMMENT '签名',
  `email` varchar(100) NOT NULL COMMENT 'email',
  `timezone` varchar(50) NOT NULL COMMENT '时区',
  `language` varchar(50) NOT NULL COMMENT '语言',
  `created` datetime NOT NULL COMMENT '加入时间',
  `updated` datetime NOT NULL COMMENT '更新时间',
  `activated` datetime NOT NULL COMMENT '激活时间',
  `logined` datetime NOT NULL COMMENT '登录时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户';

# Data exporting was unselected.
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
