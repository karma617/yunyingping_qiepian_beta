/*
 sql安装文件
*/
DROP TABLE IF EXISTS `one_one_api`;
CREATE TABLE `one_one_api` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '名称',
  `secret` varchar(100) NOT NULL DEFAULT '' COMMENT '密钥',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '0禁用 1启用',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;