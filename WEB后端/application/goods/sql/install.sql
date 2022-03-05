CREATE TABLE `one_goods` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `goods_category_id` int(11) NOT NULL DEFAULT '0' COMMENT '商品分类',
  `goods_name` varchar(255) NOT NULL DEFAULT '' COMMENT '商品名称',
  `goods_img` varchar(255) NOT NULL DEFAULT '' COMMENT '商品缩略图',
  `goods_des` varchar(255) NOT NULL DEFAULT '' COMMENT '商品简介',
  `goods_content` text COMMENT '商品信息',
  `price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '商品金额',
  `limit_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '折扣价',
  `status` int(11) NOT NULL DEFAULT '1' COMMENT '状态 0 禁用 1 启用',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0',
  `delete_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='订单管理';

CREATE TABLE `one_goods_category` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '分类名',
  `status` tinyint(3) NOT NULL DEFAULT '0' COMMENT '状态 0 正常 1 禁用',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`category_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='商品分类';

CREATE TABLE `one_goods_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_sn` varchar(255) NOT NULL DEFAULT '' COMMENT '订单号',
  `member_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
  `goods_id` int(11) NOT NULL DEFAULT '0' COMMENT '商品id',
  `type` tinyint(3) NOT NULL DEFAULT '0' COMMENT '订单类型 0 会员组 1 普通商品',
  `goods_name` varchar(255) NOT NULL DEFAULT '' COMMENT '商品名',
  `price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '交易金额',
  `number` int(11) unsigned NOT NULL DEFAULT '1' COMMENT '购买数量',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `trade_no` varchar(255) NOT NULL DEFAULT '' COMMENT '支付订单号',
  `status` tinyint(3) NOT NULL DEFAULT '0' COMMENT '订单状态 0 待付款 1 已支付',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `order_sn` (`order_sn`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;