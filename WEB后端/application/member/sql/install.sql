/*
 sql安装文件*/
DROP TABLE IF EXISTS `one_member`;
CREATE TABLE `one_member` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `username` varchar(50) NOT NULL DEFAULT '' COMMENT '用户名',
  `nickname` varchar(50) NOT NULL DEFAULT '' COMMENT '用户昵称',
  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '手机号',
  `email` varchar(50) NOT NULL DEFAULT '' COMMENT '邮箱',
  `password` varchar(255) NOT NULL DEFAULT '' COMMENT '用户密码（MD5）',
  `salt` varchar(255) NOT NULL DEFAULT '' COMMENT '用户密码盐',
  `headimg` varchar(255) NOT NULL DEFAULT '' COMMENT '用户头像',
  `level_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户等级',
  `level_name` varchar(50) NOT NULL DEFAULT '' COMMENT '会员等级名称',
  `qq` varchar(255) NOT NULL DEFAULT '' COMMENT 'qq号',
  `qq_openid` varchar(255) NOT NULL DEFAULT '' COMMENT 'qq互联id',
  `wx_openid` varchar(255) NOT NULL DEFAULT '' COMMENT '微信用户openid',
  `weapp_openid` varchar(255) NOT NULL DEFAULT '' COMMENT '微信小程序openid',
  `wx_unionid` varchar(255) NOT NULL DEFAULT '' COMMENT '微信unionid',
  `ali_openid` varchar(255) NOT NULL DEFAULT '' COMMENT '支付宝账户id',
  `baidu_openid` varchar(255) NOT NULL DEFAULT '' COMMENT '百度账户id',
  `toutiao_openid` varchar(255) NOT NULL DEFAULT '' COMMENT '头条账号',
  `douyin_openid` varchar(255) NOT NULL DEFAULT '' COMMENT '抖音小程序openid',
  `login_ip` varchar(255) NOT NULL DEFAULT '' COMMENT '当前登录ip',
  `login_type` varchar(255) NOT NULL DEFAULT 'h5' COMMENT '当前登录的操作终端类型',
  `login_time` int(11) NOT NULL DEFAULT '0' COMMENT '当前登录时间',
  `last_login_ip` varchar(255) NOT NULL DEFAULT '' COMMENT '上次登录ip',
  `last_login_type` varchar(11) NOT NULL DEFAULT 'h5' COMMENT '上次登录的操作终端类型',
  `last_login_time` int(11) NOT NULL DEFAULT '0' COMMENT '上次登录时间',
  `login_num` int(11) NOT NULL DEFAULT '0' COMMENT '登录次数',
  `realname` varchar(50) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `idcard` varchar(50) NOT NULL DEFAULT '' COMMENT '身份证号',
  `sex` smallint(6) NOT NULL DEFAULT '0' COMMENT '性别 0保密 1男 2女',
  `location` varchar(255) NOT NULL DEFAULT '' COMMENT '定位地址',
  `birthday` int(11) NOT NULL DEFAULT '0' COMMENT '出生日期',
  `point` int(10) NOT NULL DEFAULT '0' COMMENT '积分',
  `balance` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '余额',
  `growth` int(10) NOT NULL DEFAULT '0' COMMENT '成长值',
  `frozen_balance` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '冻结余额',
  `pay_password` varchar(32) NOT NULL DEFAULT '' COMMENT '交易密码',
  `ext_conifg` text COMMENT '扩展配置',
  `status` int(11) NOT NULL DEFAULT '1' COMMENT '用户状态  用户状态默认为1',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `delete_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除标记',
  `exp_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '到期时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COMMENT='会员表';

DROP TABLE IF EXISTS `one_member_account`;
CREATE TABLE `one_member_account` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
  `account_type` varchar(255) NOT NULL DEFAULT 'point' COMMENT '账户类型',
  `account_data` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '账户数据',
  `from_type` varchar(255) NOT NULL DEFAULT '' COMMENT '来源类型',
  `type_name` varchar(50) NOT NULL DEFAULT '' COMMENT '来源类型名称',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注信息',
  `username` varchar(255) NOT NULL DEFAULT '' COMMENT '用户名',
  `mobile` varchar(255) NOT NULL DEFAULT '' COMMENT '手机',
  `email` varchar(255) NOT NULL DEFAULT '' COMMENT '邮箱',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COMMENT='账户流水';

DROP TABLE IF EXISTS `one_member_level`;
CREATE TABLE `one_member_level` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `level_name` varchar(50) NOT NULL DEFAULT '' COMMENT '等级名称',
  `growth` int(10) NOT NULL DEFAULT '0' COMMENT '所需成长值',
  `is_default` int(11) NOT NULL DEFAULT '0' COMMENT '是否默认，0：否，1：是',
  `send_point` int(11) NOT NULL DEFAULT '0' COMMENT '赠送积分',
  `send_balance` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '赠送红包',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `sort` int(5) unsigned NOT NULL DEFAULT '100' COMMENT '排序',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态(0禁用,1启用)',
  `expire` int(11) NOT NULL DEFAULT '0' COMMENT '有效期 天',
  `price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '金额',
  `limit_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '折扣价',
  `thumb` varchar(255) NOT NULL DEFAULT '' COMMENT '缩略图',
  `intro` varchar(255) NOT NULL DEFAULT '' COMMENT '描述信息',
  `api_limit` varchar(255) NOT NULL DEFAULT '' COMMENT '允许使用的接口索引',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='会员等级';

INSERT INTO `one_member_level`(`id`, `level_name`, `growth`, `is_default`, `send_point`, `send_balance`, `remark`, `sort`, `status`, `expire`, `price`, `limit_price`, `thumb`, `intro`, `api_limit`, `create_time`, `update_time`) VALUES (1, '注册会员', 0, 1, 0, 0.00, '', 100, 1, 0, 0.00, 0.00, '', '', '', 0, 0);

DROP TABLE IF EXISTS `one_member_file`;
CREATE TABLE `one_member_file` (
  `fileId` int(11) NOT NULL AUTO_INCREMENT COMMENT '文件id',
  `userId` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
  `doubanId` int(11) NOT NULL DEFAULT '0' COMMENT '豆瓣id',
  `fileType` varchar(10) NOT NULL DEFAULT '' COMMENT '文件类型',
  `secertMd5` varchar(255) NOT NULL DEFAULT '' COMMENT '解析用加密地址索引 md5',
  `fileSortName` varchar(255) NOT NULL DEFAULT '' COMMENT '文件排序名',
  `fileName` varchar(255) NOT NULL DEFAULT '' COMMENT '原视文件名',
  `fileSha` varchar(255) NOT NULL COMMENT '文件sha',
  `fileUrl` mediumtext NOT NULL COMMENT '文件url',
  `from` mediumtext NOT NULL COMMENT '接口源',
  `resolute` mediumtext NOT NULL COMMENT '分辨率',
  `num` int(11) NOT NULL DEFAULT '0' COMMENT '播放次数',
  `time` int(11) NOT NULL COMMENT '添加时间',
  `ext_ids` varchar(255) NOT NULL DEFAULT '' COMMENT '共享给其他用户id',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0',
  `delete_time` int(11) NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`fileId`),
  UNIQUE KEY `fileId` (`fileId`),
  UNIQUE KEY `unique_all` (`fileSha`),
  KEY `index_all` (`userId`,`doubanId`,`fileName`,`fileSortName`,`time`,`secertMd5`,`ext_ids`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;