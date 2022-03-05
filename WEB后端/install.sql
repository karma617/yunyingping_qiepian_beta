/*
 Navicat Premium Data Transfer

 Source Server         : 切片新
 Source Server Type    : MySQL
 Source Server Version : 50562
 Source Host           : 39.101.139.254:3306
 Source Schema         : cut_617kan_cn

 Target Server Type    : MySQL
 Target Server Version : 50562
 File Encoding         : 65001

 Date: 04/03/2022 11:31:18
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for one_album
-- ----------------------------
DROP TABLE IF EXISTS `one_album`;
CREATE TABLE `one_album`  (
  `album_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `site_id` int(11) NOT NULL DEFAULT 0 COMMENT '站点id',
  `site_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '站点名称',
  `album_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '相册,名称',
  `sort` int(11) NOT NULL DEFAULT 0 COMMENT '排序',
  `cover` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '背景图',
  `desc` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '介绍',
  `is_default` tinyint(4) NOT NULL DEFAULT 0 COMMENT '是否默认',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` int(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  `num` int(11) NOT NULL DEFAULT 0 COMMENT '相册图片数',
  PRIMARY KEY (`album_id`) USING BTREE,
  INDEX `IDX_sys_album_is_default`(`is_default`) USING BTREE,
  INDEX `IDX_sys_album_site_id`(`site_id`) USING BTREE,
  INDEX `IDX_sys_album_sort`(`sort`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '[系统] 上传附件分组' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for one_album_pic
-- ----------------------------
DROP TABLE IF EXISTS `one_album_pic`;
CREATE TABLE `one_album_pic`  (
  `pic_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `pic_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '名称',
  `pic_path` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '路径',
  `pic_spec` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '规格',
  `pic_hash` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '文件hash值',
  `site_id` int(11) NOT NULL DEFAULT 0 COMMENT '站点id',
  `drive` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '驱动local qiniu等',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` int(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  `album_id` int(11) NOT NULL DEFAULT 0 COMMENT '相册id',
  `ext` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '后缀',
  PRIMARY KEY (`pic_id`) USING BTREE,
  INDEX `IDX_sys_album_pic_site_id`(`site_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '[系统] 上传附件' ROW_FORMAT = Compact;


-- ----------------------------
-- Table structure for one_goods
-- ----------------------------
DROP TABLE IF EXISTS `one_goods`;
CREATE TABLE `one_goods`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `goods_category_id` int(11) NOT NULL DEFAULT 0 COMMENT '商品分类',
  `goods_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '商品名称',
  `goods_img` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '商品缩略图',
  `goods_des` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '商品简介',
  `goods_content` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '商品信息',
  `price` decimal(10, 2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '商品金额',
  `limit_price` decimal(10, 2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '折扣价',
  `status` int(11) NOT NULL DEFAULT 1 COMMENT '状态 0 禁用 1 启用',
  `create_time` int(11) NOT NULL DEFAULT 0,
  `update_time` int(11) NOT NULL DEFAULT 0,
  `delete_time` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '订单管理' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for one_goods_category
-- ----------------------------
DROP TABLE IF EXISTS `one_goods_category`;
CREATE TABLE `one_goods_category`  (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '分类名',
  `status` tinyint(3) NOT NULL DEFAULT 0 COMMENT '状态 0 正常 1 禁用',
  `create_time` int(11) NOT NULL DEFAULT 0,
  `update_time` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`category_id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '商品分类' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for one_goods_order
-- ----------------------------
DROP TABLE IF EXISTS `one_goods_order`;
CREATE TABLE `one_goods_order`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_sn` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '订单号',
  `member_id` int(11) NOT NULL DEFAULT 0 COMMENT '用户id',
  `goods_id` int(11) NOT NULL DEFAULT 0 COMMENT '商品id',
  `type` tinyint(3) NOT NULL DEFAULT 0 COMMENT '订单类型 0 会员组 1 普通商品',
  `goods_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '商品名',
  `price` decimal(10, 2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '交易金额',
  `number` int(11) UNSIGNED NOT NULL DEFAULT 1 COMMENT '购买数量',
  `remark` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '备注',
  `trade_no` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '支付订单号',
  `status` tinyint(3) NOT NULL DEFAULT 0 COMMENT '订单状态 0 待付款 1 已支付',
  `create_time` int(11) NOT NULL DEFAULT 0,
  `update_time` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `order_sn`(`order_sn`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for one_member
-- ----------------------------
DROP TABLE IF EXISTS `one_member`;
CREATE TABLE `one_member`  (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `username` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '用户名',
  `nickname` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '用户昵称',
  `mobile` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '手机号',
  `email` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '邮箱',
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '用户密码（MD5）',
  `salt` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '用户密码盐',
  `headimg` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '用户头像',
  `level_id` int(11) NOT NULL DEFAULT 0 COMMENT '用户等级',
  `level_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '会员等级名称',
  `qq` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'qq号',
  `qq_openid` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'qq互联id',
  `wx_openid` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '微信用户openid',
  `weapp_openid` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '微信小程序openid',
  `wx_unionid` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '微信unionid',
  `ali_openid` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '支付宝账户id',
  `baidu_openid` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '百度账户id',
  `toutiao_openid` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '头条账号',
  `douyin_openid` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '抖音小程序openid',
  `login_ip` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '当前登录ip',
  `login_type` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'h5' COMMENT '当前登录的操作终端类型',
  `login_time` int(11) NOT NULL DEFAULT 0 COMMENT '当前登录时间',
  `last_login_ip` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '上次登录ip',
  `last_login_type` varchar(11) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'h5' COMMENT '上次登录的操作终端类型',
  `last_login_time` int(11) NOT NULL DEFAULT 0 COMMENT '上次登录时间',
  `login_num` int(11) NOT NULL DEFAULT 0 COMMENT '登录次数',
  `realname` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '真实姓名',
  `idcard` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '身份证号',
  `sex` smallint(6) NOT NULL DEFAULT 0 COMMENT '性别 0保密 1男 2女',
  `location` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '定位地址',
  `birthday` int(11) NOT NULL DEFAULT 0 COMMENT '出生日期',
  `point` int(10) NOT NULL DEFAULT 0 COMMENT '积分',
  `balance` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '余额',
  `growth` int(10) NOT NULL DEFAULT 0 COMMENT '成长值',
  `frozen_balance` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '冻结余额',
  `pay_password` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '交易密码',
  `ext_conifg` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '扩展配置',
  `status` int(11) NOT NULL DEFAULT 1 COMMENT '用户状态  用户状态默认为1',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '删除标记',
  `exp_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '到期时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '会员表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for one_member_account
-- ----------------------------
DROP TABLE IF EXISTS `one_member_account`;
CREATE TABLE `one_member_account`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL DEFAULT 0 COMMENT '用户id',
  `account_type` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'point' COMMENT '账户类型',
  `account_data` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '账户数据',
  `from_type` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '来源类型',
  `type_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '来源类型名称',
  `remark` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '备注信息',
  `username` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '用户名',
  `mobile` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '手机',
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '邮箱',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '账户流水' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for one_member_file
-- ----------------------------
DROP TABLE IF EXISTS `one_member_file`;
CREATE TABLE `one_member_file`  (
  `fileId` int(11) NOT NULL AUTO_INCREMENT COMMENT '文件id',
  `userId` int(11) NOT NULL DEFAULT 0 COMMENT '用户id',
  `doubanId` int(11) NOT NULL DEFAULT 0 COMMENT '豆瓣id',
  `fileType` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '文件类型',
  `secertMd5` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '解析用加密地址索引 md5',
  `fileSortName` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '文件排序名',
  `fileName` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '原视文件名',
  `fileSha` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '文件sha',
  `fileUrl` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '文件url',
  `from` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '接口源',
  `resolute` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '分辨率',
  `num` int(11) NOT NULL DEFAULT 0 COMMENT '播放次数',
  `time` int(11) NOT NULL COMMENT '添加时间',
  `ext_ids` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '共享给其他用户id',
  `create_time` int(11) NOT NULL DEFAULT 0,
  `update_time` int(11) NOT NULL DEFAULT 0,
  `delete_time` int(11) NOT NULL DEFAULT 0 COMMENT '是否删除',
  PRIMARY KEY (`fileId`) USING BTREE,
  UNIQUE INDEX `fileId`(`fileId`) USING BTREE,
  UNIQUE INDEX `unique_all`(`fileSha`) USING BTREE,
  INDEX `index_all`(`userId`, `doubanId`, `fileName`, `fileSortName`, `time`, `secertMd5`, `ext_ids`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for one_member_level
-- ----------------------------
DROP TABLE IF EXISTS `one_member_level`;
CREATE TABLE `one_member_level`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `level_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '等级名称',
  `growth` int(10) NOT NULL DEFAULT 0 COMMENT '所需成长值',
  `is_default` int(11) NOT NULL DEFAULT 0 COMMENT '是否默认，0：否，1：是',
  `send_point` int(11) NOT NULL DEFAULT 0 COMMENT '赠送积分',
  `send_balance` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '赠送红包',
  `remark` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '备注',
  `sort` int(5) UNSIGNED NOT NULL DEFAULT 100 COMMENT '排序',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态(0禁用,1启用)',
  `expire` int(11) NOT NULL DEFAULT 0 COMMENT '有效期 天',
  `price` decimal(10, 2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '金额',
  `limit_price` decimal(10, 2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '折扣价',
  `thumb` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '缩略图',
  `intro` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '描述信息',
  `api_limit` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '允许使用的接口索引',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '会员等级' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of one_member_level
-- ----------------------------
INSERT INTO `one_member_level` VALUES (1, '注册会员', 0, 1, 0, 0.00, '', 100, 1, 0, 0.00, 0.00, '', '', '', 0, 1970);

-- ----------------------------
-- Table structure for one_msg
-- ----------------------------
DROP TABLE IF EXISTS `one_msg`;
CREATE TABLE `one_msg`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '标题',
  `content` varchar(1500) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '内容',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '1 正常 0 停用',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '公告' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for one_news
-- ----------------------------
DROP TABLE IF EXISTS `one_news`;
CREATE TABLE `one_news`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '新闻标题',
  `img` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '缩略图',
  `desc` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '内容简介',
  `content` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '详情',
  `status` tinyint(3) NOT NULL DEFAULT 1 COMMENT '1 启用 0 禁用',
  `create_time` int(11) NOT NULL DEFAULT 0,
  `update_time` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '新闻专题' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for one_one_api
-- ----------------------------
DROP TABLE IF EXISTS `one_one_api`;
CREATE TABLE `one_one_api`  (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '名称',
  `secret` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '密钥',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '0禁用 1启用',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for one_one_pay_log
-- ----------------------------
DROP TABLE IF EXISTS `one_one_pay_log`;
CREATE TABLE `one_one_pay_log`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户标识',
  `type` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '1付款，2退款',
  `product_id` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '产品ID[选填]',
  `order_no` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '商户订单号',
  `refund_no` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '退款单号',
  `trade_no` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '支付平台交易号',
  `method` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '支付方式code',
  `bank` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '支付银行code',
  `money` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '金额',
  `request` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '请求数据',
  `return` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '返回数据',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态(0失败，1待处理，2成功)',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '[pay] 支付日志' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for one_one_pay_payment
-- ----------------------------
DROP TABLE IF EXISTS `one_one_pay_payment`;
CREATE TABLE `one_one_pay_payment`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '支付平台code',
  `abbrev` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '支付平台简称',
  `title` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '支付平台标题',
  `intro` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '支付平台简介',
  `config` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '配置',
  `applies` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'pc' COMMENT '适用环境(pc,wap,wechat,app)',
  `sort` int(10) UNSIGNED NOT NULL DEFAULT 100 COMMENT '排序',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '状态(0停用，1启用)',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '[pay] 支付平台' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for one_system_config
-- ----------------------------
DROP TABLE IF EXISTS `one_system_config`;
CREATE TABLE `one_system_config`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `system` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否为系统配置(1是，0否)',
  `group` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'base' COMMENT '分组',
  `title` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '配置标题',
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '配置名称，由英文字母和下划线组成',
  `value` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '配置值',
  `type` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'input' COMMENT '配置类型()',
  `options` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '配置项(选项名:选项值)',
  `url` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '文件上传接口',
  `tips` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '配置提示',
  `sort` int(10) UNSIGNED NOT NULL DEFAULT 100 COMMENT '排序',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '[系统] 系统配置' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of one_system_config
-- ----------------------------
INSERT INTO `one_system_config` VALUES (1, 1, 'sys', '扩展配置分组', 'config_group', 'app_config:APP配置', 'array', ' ', '', '请按如下格式填写：&lt;br&gt;键值:键名&lt;br&gt;键值:键名&lt;br&gt;&lt;span style=&quot;color:#f00&quot;&gt;键值只能为英文、数字、下划线&lt;/span&gt;', 2, 1, 0, 1638181814);
INSERT INTO `one_system_config` VALUES (2, 1, 'base', '网站域名', 'site_domain', 'http://domain.com', 'input', '', '', '', 2, 1, 0, 0);
INSERT INTO `one_system_config` VALUES (3, 1, 'upload', '图片上传大小限制', 'upload_image_size', '0', 'input', '', '', '单位：KB，0表示不限制大小', 3, 1, 0, 0);
INSERT INTO `one_system_config` VALUES (4, 1, 'upload', '允许上传图片格式', 'upload_image_ext', 'jpg,png,gif,jpeg,ico', 'input', '', '', '多个格式请用英文逗号（,）隔开', 4, 1, 0, 0);
INSERT INTO `one_system_config` VALUES (5, 1, 'upload', '缩略图裁剪方式', 'thumb_type', '2', 'select', '1:等比例缩放\r\n2:缩放后填充\r\n3:居中裁剪\r\n4:左上角裁剪\r\n5:右下角裁剪\r\n6:固定尺寸缩放\r\n', '', '', 5, 1, 0, 0);
INSERT INTO `one_system_config` VALUES (6, 1, 'upload', '图片水印开关', 'image_watermark', '0', 'switch', '0:关闭\r\n1:开启', '', '', 6, 1, 0, 0);
INSERT INTO `one_system_config` VALUES (7, 1, 'upload', '图片水印图', 'image_watermark_pic', '', 'image', '', '', '', 7, 1, 0, 0);
INSERT INTO `one_system_config` VALUES (8, 1, 'upload', '图片水印透明度', 'image_watermark_opacity', '50', 'input', '', '', '可设置值为0~100，数字越小，透明度越高', 8, 1, 0, 0);
INSERT INTO `one_system_config` VALUES (9, 1, 'upload', '图片水印图位置', 'image_watermark_location', '9', 'select', '7:左下角\r\n1:左上角\r\n4:左居中\r\n9:右下角\r\n3:右上角\r\n6:右居中\r\n2:上居中\r\n8:下居中\r\n5:居中', '', '', 9, 1, 0, 0);
INSERT INTO `one_system_config` VALUES (10, 1, 'upload', '文件上传大小限制', 'upload_file_size', '0', 'input', '', '', '单位：KB，0表示不限制大小', 1, 1, 0, 0);
INSERT INTO `one_system_config` VALUES (11, 1, 'upload', '允许上传文件格式', 'upload_file_ext', 'doc,docx,xls,xlsx,ppt,pptx,pdf,wps,txt,rar,zip', 'input', '', '', '多个格式请用英文逗号（,）隔开', 2, 1, 0, 0);
INSERT INTO `one_system_config` VALUES (12, 1, 'upload', '文字水印开关', 'text_watermark', '0', 'switch', '0:关闭\r\n1:开启', '', '', 10, 1, 0, 0);
INSERT INTO `one_system_config` VALUES (13, 1, 'upload', '文字水印内容', 'text_watermark_content', '', 'input', '', '', '', 11, 1, 0, 0);
INSERT INTO `one_system_config` VALUES (14, 1, 'upload', '文字水印字体', 'text_watermark_font', '', 'file', '', '', '不上传将使用系统默认字体', 12, 1, 0, 0);
INSERT INTO `one_system_config` VALUES (15, 1, 'upload', '文字水印字体大小', 'text_watermark_size', '20', 'input', '', '', '单位：px(像素)', 13, 1, 0, 0);
INSERT INTO `one_system_config` VALUES (16, 1, 'upload', '文字水印颜色', 'text_watermark_color', '#000000', 'input', '', '', '文字水印颜色，格式:#000000', 14, 1, 0, 0);
INSERT INTO `one_system_config` VALUES (17, 1, 'upload', '文字水印位置', 'text_watermark_location', '7', 'select', '7:左下角\r\n1:左上角\r\n4:左居中\r\n9:右下角\r\n3:右上角\r\n6:右居中\r\n2:上居中\r\n8:下居中\r\n5:居中', '', '', 11, 1, 0, 0);
INSERT INTO `one_system_config` VALUES (18, 1, 'upload', '缩略图尺寸', 'thumb_size', '', 'input', '', '', '为空则不生成，生成 500x500 的缩略图，则填写 500x500，多个规格填写参考 300x300;500x500;800x800', 4, 1, 0, 0);
INSERT INTO `one_system_config` VALUES (19, 1, 'sys', '开发模式', 'app_debug', '1', 'switch', '0:关闭\r\n1:开启', '', '&lt;strong class=&quot;red&quot;&gt;生产环境下一定要关闭此配置&lt;/strong&gt;', 3, 1, 0, 1607492697);
INSERT INTO `one_system_config` VALUES (20, 1, 'sys', '页面Trace', 'app_trace', '0', 'switch', '0:关闭\r\n1:开启', '', '&lt;strong class=&quot;red&quot;&gt;生产环境下一定要关闭此配置&lt;/strong&gt;', 4, 1, 0, 1607662815);
INSERT INTO `one_system_config` VALUES (21, 1, 'databases', '备份目录', 'backup_path', './backup/database/', 'input', '', '', '数据库备份路径,路径必须以 / 结尾', 0, 1, 0, 0);
INSERT INTO `one_system_config` VALUES (22, 1, 'databases', '备份分卷大小', 'part_size', '20971521', 'input', '', '', '用于限制压缩后的分卷最大长度。单位：B；建议设置20M', 0, 1, 0, 0);
INSERT INTO `one_system_config` VALUES (23, 1, 'databases', '备份压缩开关', 'compress', '1', 'switch', '', '', '压缩备份文件需要PHP环境支持gzopen,gzwrite函数', 0, 1, 0, 0);
INSERT INTO `one_system_config` VALUES (24, 1, 'databases', '备份压缩级别', 'compress_level', '1', 'radio', '最低:1\r\n一般:4\r\n最高:9', '', '数据库备份文件的压缩级别，该配置在开启压缩时生效', 0, 1, 0, 0);
INSERT INTO `one_system_config` VALUES (25, 1, 'base', '网站状态', 'site_status', '1', 'switch', '0:关闭\r\n1:开启', '', '站点关闭后将不能访问，后台可正常登录', 1, 1, 0, 0);
INSERT INTO `one_system_config` VALUES (26, 1, 'sys', '后台管理路径', 'admin_path', 'karma617.php', 'input', '', '', '必须以.php为后缀', 1, 1, 0, 1639929278);
INSERT INTO `one_system_config` VALUES (27, 1, 'base', '网站标题', 'site_title', '网站标题', 'input', '', '', '网站标题是体现一个网站的主旨，要做到主题突出、标题简洁、连贯等特点，建议不超过28个字', 6, 1, 0, 0);
INSERT INTO `one_system_config` VALUES (28, 1, 'base', '网站关键词', 'site_keywords', '网站关键词 ', 'input', '', '', '网页内容所包含的核心搜索关键词，多个关键字请用英文逗号&quot;,&quot;分隔', 7, 1, 0, 0);
INSERT INTO `one_system_config` VALUES (29, 1, 'base', '网站描述', 'site_description', '', 'textarea', '', '', '网页的描述信息，搜索引擎采纳后，作为搜索结果中的页面摘要显示，建议不超过80个字', 8, 1, 0, 0);
INSERT INTO `one_system_config` VALUES (30, 1, 'base', 'ICP备案信息', 'site_icp', '', 'input', '', '', '请填写ICP备案号，用于展示在网站底部，ICP备案官网：&lt;a href=&quot;http://www.miibeian.gov.cn&quot; target=&quot;_blank&quot;&gt;http://www.miibeian.gov.cn&lt;/a&gt;', 9, 1, 0, 0);
INSERT INTO `one_system_config` VALUES (31, 1, 'base', '站点统计代码', 'site_statis', '', 'textarea', '', '', '第三方流量统计代码，前台调用时请先用 htmlspecialchars_decode函数转义输出', 10, 1, 0, 0);
INSERT INTO `one_system_config` VALUES (32, 1, 'base', '网站名称', 'site_name', '网站名称 ', 'input', '', '', '将显示在浏览器窗口标题等位置', 3, 1, 0, 0);
INSERT INTO `one_system_config` VALUES (33, 1, 'base', '网站LOGO', 'site_logo', '', 'image', '', '', '网站LOGO图片', 4, 1, 0, 1607662841);
INSERT INTO `one_system_config` VALUES (34, 1, 'base', '手机网站', 'wap_site_status', '0', 'switch', '0:关闭\r\n1:开启', '', '如果有手机网站，请设置为开启状态，否则只显示PC网站', 2, 1, 0, 1607575916);
INSERT INTO `one_system_config` VALUES (35, 1, 'base', '手机网站域名', 'wap_domain', 'http://m.domain.com', 'input', '', '', '手机访问将自动跳转至此域名，示例：http://m.domain.com', 2, 1, 0, 0);
INSERT INTO `one_system_config` VALUES (36, 1, 'sys', '后台白名单验证', 'admin_whitelist_verify', '0', 'switch', '0:禁用\r\n1:启用', '', '禁用后不存在的菜单节点将不在提示', 7, 1, 0, 0);
INSERT INTO `one_system_config` VALUES (37, 1, 'sys', '系统日志保留', 'system_log_retention', '30', 'input', '', '', '单位天，系统将自动清除 ? 天前的系统日志', 8, 1, 0, 0);
INSERT INTO `one_system_config` VALUES (38, 1, 'upload', '上传驱动', 'upload_driver', 'local', 'select', 'local:本地上传', '', '资源上传驱动设置', 0, 1, 0, 0);
INSERT INTO `one_system_config` VALUES (39, 0, 'app_config', '版本号', 'app_version', '1.4', 'input', '', '', '', 100, 1, 1638181840, 1643271369);
INSERT INTO `one_system_config` VALUES (40, 0, 'app_config', 'APP下载地址', 'app_download', '', 'input', '', '', '', 100, 1, 1638181887, 1643271369);
INSERT INTO `one_system_config` VALUES (41, 0, 'app_config', 'ffmpeg下载地址', 'ffmpeg_download', '', 'input', '', '', '', 100, 1, 1638181912, 1642770663);
INSERT INTO `one_system_config` VALUES (42, 0, 'app_config', 'ffmpeg MD5', 'ffmpeg_md5', '', 'input', '', '', 'ffmpeg文件的md5，用于文件校验', 100, 1, 1639381531, 1642771032);
INSERT INTO `one_system_config` VALUES (43, 0, 'app_config', '放行会员组', 'vip_limit', '', 'input', '', '', '可使用软件的会员组id，多个会员组用英文逗号“,”分割', 100, 1, 1638519463, 1639746047);
INSERT INTO `one_system_config` VALUES (44, 0, 'app_config', '水印字体下载地址', 'water_font', '', 'input', '', '', '', 100, 1, 1642062875, 1642680402);

-- ----------------------------
-- Table structure for one_system_hook
-- ----------------------------
DROP TABLE IF EXISTS `one_system_hook`;
CREATE TABLE `one_system_hook`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `system` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '系统插件',
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '钩子名称',
  `source` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '钩子来源[plugins.插件名，module.模块名]',
  `intro` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '钩子简介',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `name`(`name`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '[系统] 钩子表' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of one_system_hook
-- ----------------------------
INSERT INTO `one_system_hook` VALUES (1, 1, 'system_admin_index', '系统', '后台首页', 1, 0, 0);
INSERT INTO `one_system_hook` VALUES (2, 1, 'system_admin_tips', '系统', '后台所有页面提示', 1, 0, 0);
INSERT INTO `one_system_hook` VALUES (3, 1, 'system_annex_upload', '系统', '附件上传钩子，可扩展上传到第三方存储', 1, 0, 0);

-- ----------------------------
-- Table structure for one_system_hook_plugins
-- ----------------------------
DROP TABLE IF EXISTS `one_system_hook_plugins`;
CREATE TABLE `one_system_hook_plugins`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `hook` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '钩子id',
  `plugins` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '插件标识',
  `sort` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `status` tinyint(2) UNSIGNED NOT NULL DEFAULT 1,
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '[系统] 钩子-插件对应表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for one_system_language
-- ----------------------------
DROP TABLE IF EXISTS `one_system_language`;
CREATE TABLE `one_system_language`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '语言包名称',
  `code` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '编码',
  `locale` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '本地浏览器语言编码',
  `icon` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '图标',
  `pack` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '上传的语言包',
  `sort` tinyint(2) UNSIGNED NOT NULL DEFAULT 100,
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `code`(`code`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '[系统] 语言包' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of one_system_language
-- ----------------------------
INSERT INTO `one_system_language` VALUES (1, '简体中文', 'zh-cn', 'zh-CN,zh-CN.UTF-8,zh-cn', '', '1', 1, 1);

-- ----------------------------
-- Table structure for one_system_log
-- ----------------------------
DROP TABLE IF EXISTS `one_system_log`;
CREATE TABLE `one_system_log`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `title` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `url` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `param` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `remark` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `count` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `ip` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '[系统] 操作日志' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for one_system_menu
-- ----------------------------
DROP TABLE IF EXISTS `one_system_menu`;
CREATE TABLE `one_system_menu`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` int(5) UNSIGNED NOT NULL DEFAULT 0 COMMENT '管理员ID(快捷菜单专用)',
  `pid` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `module` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '模块名或插件名，插件名格式:plugins.插件名',
  `title` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '菜单标题',
  `icon` varchar(80) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '菜单图标',
  `url` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '链接地址(模块/控制器/方法)',
  `param` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '扩展参数',
  `target` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '_self' COMMENT '打开方式(_blank,_self)',
  `sort` int(10) UNSIGNED NOT NULL DEFAULT 100 COMMENT '排序',
  `debug` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '开发模式可见',
  `system` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否为系统菜单，系统菜单不可删除',
  `nav` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否为菜单显示，1显示0不显示',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态1显示，0隐藏',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 149 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '[系统] 管理菜单' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of one_system_menu
-- ----------------------------
INSERT INTO `one_system_menu` VALUES (1, 0, 0, 'system', '首页', 'el-icon-s-home', 'system/index/index', '', '_self', 100, 0, 1, 1, 1, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (2, 0, 0, 'system', '系统', '', 'system/system/index', '', '_self', 100, 0, 1, 1, 1, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (3, 0, 1, 'system', '个人信息', '', 'system/user/info', '', '_self', 100, 0, 1, 0, 1, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (4, 0, 1, 'system', '清空缓存', '', 'system/index/clear', '', '_self', 100, 0, 1, 0, 1, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (5, 0, 1, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (6, 0, 1, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (7, 0, 1, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (8, 0, 2, 'system', '系统设置', '', 'system/system/index', '', '_self', 100, 0, 1, 1, 1, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (9, 0, 2, 'system', '系统扩展', '', 'system/extend/index', '', '_self', 100, 1, 1, 1, 1, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (10, 0, 2, 'system', '系统管理员', '', 'system/user/index', '', '_self', 100, 0, 1, 1, 1, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (11, 0, 2, 'system', '数据库管理', '', 'system/database/index', '', '_self', 100, 0, 1, 1, 1, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (12, 0, 2, 'system', '系统日志', '', 'system/log/index', '', '_self', 100, 0, 1, 1, 1, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (13, 0, 2, 'system', '配置管理', '', 'system/config/index', '', '_self', 100, 1, 1, 1, 1, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (14, 0, 8, 'system', '基础配置', '', 'system/system/index', 'group=base', '_self', 100, 0, 1, 0, 1, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (15, 0, 8, 'system', '系统配置', '', 'system/system/index', 'group=sys', '_self', 100, 0, 1, 0, 1, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (16, 0, 8, 'system', '上传配置', '', 'system/system/index', 'group=upload', '_self', 100, 0, 1, 0, 1, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (17, 0, 8, 'system', '开发配置', '', 'system/system/index', 'group=develop', '_self', 100, 0, 1, 0, 1, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (18, 0, 8, 'system', '数据库配置', '', 'system/system/index', 'group=databases', '_self', 100, 0, 1, 0, 1, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (19, 0, 9, 'system', '本地模块', '', 'system/module/index', '', '_self', 100, 0, 1, 1, 1, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (20, 0, 9, 'system', '模块钩子', '', 'system/hook/index', '', '_self', 100, 0, 1, 1, 1, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (21, 0, 9, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (22, 0, 9, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (23, 0, 9, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (24, 0, 62, 'system', '添加管理员', '', 'system/user/adduser', '', '_self', 100, 0, 1, 0, 1, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (25, 0, 62, 'system', '修改管理员', '', 'system/user/edituser', '', '_self', 100, 0, 1, 0, 1, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (26, 0, 62, 'system', '删除管理员', '', 'system/user/deluser', '', '_self', 100, 0, 1, 0, 1, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (27, 0, 62, 'system', '状态设置', '', 'system/user/status', '', '_self', 100, 0, 1, 0, 1, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (28, 0, 62, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (29, 0, 11, 'system', '备份数据库', '', 'system/database/export', '', '_self', 100, 0, 1, 0, 1, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (30, 0, 11, 'system', '恢复数据库', '', 'system/database/import', '', '_self', 100, 0, 1, 0, 1, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (31, 0, 11, 'system', '优化数据库', '', 'system/database/optimize', '', '_self', 100, 0, 1, 0, 1, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (32, 0, 11, 'system', '删除备份', '', 'system/database/del', '', '_self', 100, 0, 1, 0, 1, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (33, 0, 11, 'system', '修复数据库', '', 'system/database/repair', '', '_self', 100, 0, 1, 0, 1, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (34, 0, 12, 'system', '清空日志', '', 'system/log/clear', '', '_self', 100, 0, 1, 0, 1, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (35, 0, 12, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (36, 0, 12, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (37, 0, 19, 'system', '配置模块', '', 'system/module/editor', '', '_self', 100, 0, 1, 0, 1, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (38, 0, 19, 'system', '生成模块', '', 'system/module/design', '', '_self', 100, 0, 1, 0, 1, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (39, 0, 19, 'system', '安装模块', '', 'system/module/install', '', '_self', 100, 0, 1, 0, 1, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (40, 0, 19, 'system', '卸载模块', '', 'system/module/uninstall', '', '_self', 100, 0, 1, 0, 1, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (41, 0, 19, 'system', '状态设置', '', 'system/module/status', '', '_self', 100, 0, 1, 0, 1, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (42, 0, 19, 'system', '设置默认模块', '', 'system/module/setdefault', '', '_self', 100, 0, 1, 0, 1, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (43, 0, 19, 'system', '删除模块', '', 'system/module/del', '', '_self', 100, 0, 1, 0, 1, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (44, 0, 19, 'system', '重载模块', '', 'system/module/reinstall', '', '_self', 100, 0, 1, 0, 1, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (50, 0, 20, 'system', '添加钩子', '', 'system/hook/add', '', '_self', 100, 0, 1, 0, 1, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (51, 0, 20, 'system', '修改钩子', '', 'system/hook/edit', '', '_self', 100, 0, 1, 0, 1, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (52, 0, 20, 'system', '删除钩子', '', 'system/hook/del', '', '_self', 100, 0, 1, 0, 1, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (53, 0, 20, 'system', '状态设置', '', 'system/hook/status', '', '_self', 100, 0, 1, 0, 1, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (54, 0, 20, 'system', '插件排序', '', 'system/hook/sort', '', '_self', 100, 0, 1, 0, 1, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (55, 0, 13, 'system', '添加配置', '', 'system/config/add', '', '_self', 100, 0, 1, 0, 1, 1490315067, 0);
INSERT INTO `one_system_menu` VALUES (56, 0, 13, 'system', '修改配置', '', 'system/config/edit', '', '_self', 100, 0, 1, 0, 1, 1490315067, 0);
INSERT INTO `one_system_menu` VALUES (57, 0, 13, 'system', '删除配置', '', 'system/config/del', '', '_self', 100, 0, 1, 0, 1, 1490315067, 0);
INSERT INTO `one_system_menu` VALUES (58, 0, 13, 'system', '状态设置', '', 'system/config/status', '', '_self', 100, 0, 1, 0, 1, 1490315067, 0);
INSERT INTO `one_system_menu` VALUES (59, 0, 13, 'system', '排序设置', '', 'system/config/sort', '', '_self', 100, 0, 1, 0, 1, 1490315067, 0);
INSERT INTO `one_system_menu` VALUES (60, 0, 13, 'system', '添加分组', '', 'system/config/addgroup', '', '_self', 100, 0, 1, 0, 1, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (61, 0, 13, 'system', '删除分组', '', 'system/config/delgroup', '', '_self', 100, 0, 1, 0, 1, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (62, 0, 10, 'system', '管理用户', '', 'system/user/index', '', '_self', 100, 0, 1, 0, 1, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (63, 0, 10, 'system', '管理角色', '', 'system/user/role', '', '_self', 100, 0, 1, 0, 1, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (64, 0, 63, 'system', '添加角色', '', 'system/user/addrole', '', '_self', 100, 0, 1, 1, 1, 1490315067, 0);
INSERT INTO `one_system_menu` VALUES (65, 0, 63, 'system', '修改角色', '', 'system/user/editrole', '', '_self', 100, 0, 1, 1, 1, 1490315067, 0);
INSERT INTO `one_system_menu` VALUES (66, 0, 63, 'system', '删除角色', '', 'system/user/delrole', '', '_self', 100, 0, 1, 1, 1, 1490315067, 0);
INSERT INTO `one_system_menu` VALUES (67, 0, 63, 'system', '状态设置', '', 'system/user/statusRole', '', '_self', 100, 0, 1, 1, 1, 1490315067, 0);
INSERT INTO `one_system_menu` VALUES (68, 0, 2, 'system', '附件管理', '', 'system/album/index', '', '_self', 100, 0, 1, 1, 1, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (69, 0, 2, 'system', '驱动管理', '', 'system/album.drive/index', '', '_self', 100, 0, 1, 0, 1, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (70, 0, 0, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (71, 0, 0, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (72, 0, 0, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (73, 0, 0, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (74, 0, 0, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (75, 0, 0, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (76, 0, 0, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (77, 0, 0, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (78, 0, 0, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (79, 0, 0, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (80, 0, 0, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (81, 0, 0, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (82, 0, 0, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (83, 0, 0, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (84, 0, 0, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (85, 0, 0, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (86, 0, 0, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (87, 0, 0, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (88, 0, 0, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (89, 0, 0, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (90, 0, 0, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (91, 0, 0, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (92, 0, 0, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (93, 0, 0, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (94, 0, 0, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (95, 0, 0, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (96, 0, 0, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (97, 0, 0, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (98, 0, 0, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (99, 0, 0, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (100, 0, 0, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0);
INSERT INTO `one_system_menu` VALUES (101, 0, 0, 'goods', '订单管理', 'el-icon-suitcase', 'goods/index/index', '', '_self', 100, 0, 0, 1, 1, 1639537051, 1639537051);
INSERT INTO `one_system_menu` VALUES (102, 0, 101, 'goods', '订单列表', 'fa fa-credit-card', 'goods/order/index', '', '_self', 0, 0, 0, 1, 1, 1639537051, 1639537051);
INSERT INTO `one_system_menu` VALUES (114, 0, 0, 'news', '新闻公告', 'el-icon-postcard', 'news', '', '_self', 100, 0, 0, 1, 1, 1639537053, 1639537053);
INSERT INTO `one_system_menu` VALUES (115, 0, 114, 'news', '新闻列表', 'aicon ai-caidan', 'news/index/index', '', '_self', 0, 0, 0, 1, 1, 1639537053, 1639537053);
INSERT INTO `one_system_menu` VALUES (116, 0, 115, 'news', '增加', 'aicon ai-tianjia', 'news/index/add', '', '_self', 0, 0, 0, 0, 1, 1639537053, 1639537053);
INSERT INTO `one_system_menu` VALUES (117, 0, 115, 'news', '编辑', 'aicon ai-error', 'news/index/edit', '', '_self', 0, 0, 0, 0, 1, 1639537053, 1639537053);
INSERT INTO `one_system_menu` VALUES (118, 0, 115, 'news', '删除', 'aicon ai-jinyong', 'news/index/del', '', '_self', 0, 0, 0, 0, 1, 1639537053, 1639537053);
INSERT INTO `one_system_menu` VALUES (119, 0, 114, 'news', '公告', 'aicon ai-caidan', 'news/msg/index', '', '_self', 0, 0, 0, 1, 1, 1639537053, 1639537053);
INSERT INTO `one_system_menu` VALUES (120, 0, 119, 'news', '增加', 'aicon ai-tianjia', 'news/msg/add', '', '_self', 0, 0, 0, 0, 1, 1639537053, 1639537053);
INSERT INTO `one_system_menu` VALUES (121, 0, 119, 'news', '编辑', 'aicon ai-error', 'news/msg/edit', '', '_self', 0, 0, 0, 0, 1, 1639537053, 1639537053);
INSERT INTO `one_system_menu` VALUES (122, 0, 119, 'news', '删除', 'aicon ai-jinyong', 'news/msg/del', '', '_self', 0, 0, 0, 0, 1, 1639537053, 1639537053);
INSERT INTO `one_system_menu` VALUES (123, 0, 0, 'one_api', 'API授权', 'mdi mdi-artstation', 'one_api/index/index', '', '_self', 100, 0, 0, 1, 1, 1639537054, 1639537054);
INSERT INTO `one_system_menu` VALUES (124, 0, 123, 'one_api', '添加', '', 'one_api/index/add', '', '_self', 100, 0, 0, 0, 1, 1639537054, 1639537054);
INSERT INTO `one_system_menu` VALUES (125, 0, 123, 'one_api', '编辑', '', 'one_api/index/edit', '', '_self', 100, 0, 0, 0, 1, 1639537054, 1639537054);
INSERT INTO `one_system_menu` VALUES (126, 0, 123, 'one_api', '删除', '', 'one_api/index/del', '', '_self', 100, 0, 0, 0, 1, 1639537054, 1639537054);
INSERT INTO `one_system_menu` VALUES (127, 0, 123, 'one_api', '改变状态', '', 'one_api/index/status', '', '_self', 100, 0, 0, 0, 1, 1639537054, 1639537054);
INSERT INTO `one_system_menu` VALUES (128, 0, 0, 'one_pay', '在线支付', 'mdi mdi-wallet-giftcard', 'one_pay/index', '', '_self', 100, 0, 0, 1, 1, 1639537055, 1639537055);
INSERT INTO `one_system_menu` VALUES (129, 0, 128, 'one_pay', '支付管理', '', 'one_pay/index/index', '', '_self', 100, 0, 0, 1, 1, 1639537055, 1639537055);
INSERT INTO `one_system_menu` VALUES (130, 0, 129, 'one_pay', '安装', '', 'one_pay/index/install', '', '_self', 100, 0, 0, 0, 1, 1639537055, 1639537055);
INSERT INTO `one_system_menu` VALUES (131, 0, 129, 'one_pay', '卸载', '', 'one_pay/index/uninstall', '', '_self', 100, 0, 0, 0, 1, 1639537055, 1639537055);
INSERT INTO `one_system_menu` VALUES (132, 0, 129, 'one_pay', '状态设置', '', 'one_pay/index/status', '', '_self', 100, 0, 0, 0, 1, 1639537055, 1639537055);
INSERT INTO `one_system_menu` VALUES (133, 0, 129, 'one_pay', '配置', '', 'one_pay/index/config', '', '_self', 100, 0, 0, 0, 1, 1639537055, 1639537055);
INSERT INTO `one_system_menu` VALUES (134, 0, 129, 'one_pay', '排序', '', 'one_pay/index/sort', '', '_self', 100, 0, 0, 0, 1, 1639537055, 1639537055);
INSERT INTO `one_system_menu` VALUES (135, 0, 128, 'one_pay', '支付日志', '', 'one_pay/logs/index', '', '_self', 100, 0, 0, 1, 1, 1639537055, 1639537055);
INSERT INTO `one_system_menu` VALUES (136, 0, 135, 'one_pay', '删除', '', 'one_pay/index/logsDel', '', '_self', 100, 0, 0, 0, 1, 1639537055, 1639537055);
INSERT INTO `one_system_menu` VALUES (137, 0, 0, 'member', '会员中心', '', 'member/index', '', '_self', 100, 0, 0, 1, 1, 1641877735, 1641877735);
INSERT INTO `one_system_menu` VALUES (138, 0, 137, 'member', '会员列表', 'fa fa-credit-card', 'member/index/index', '', '_self', 0, 0, 0, 1, 1, 1641877735, 1641877735);
INSERT INTO `one_system_menu` VALUES (139, 0, 138, 'member', '编辑', 'fa fa-credit-card', 'member/index/edit', '', '_self', 0, 0, 0, 0, 1, 1641877735, 1641877735);
INSERT INTO `one_system_menu` VALUES (140, 0, 138, 'member', '删除', 'fa fa-credit-card', 'member/index/del', '', '_self', 0, 0, 0, 0, 1, 1641877735, 1641877735);
INSERT INTO `one_system_menu` VALUES (141, 0, 138, 'member', '明细', 'fa fa-credit-card', 'member/account/index', '', '_self', 0, 0, 0, 0, 1, 1641877735, 1641877735);
INSERT INTO `one_system_menu` VALUES (142, 0, 138, 'member', '状态', 'fa fa-credit-card', 'member/index/status', '', '_self', 0, 0, 0, 0, 1, 1641877735, 1641877735);
INSERT INTO `one_system_menu` VALUES (143, 0, 138, 'member', '重置密码', 'fa fa-credit-card', 'member/index/modify_pwd', '', '_self', 0, 0, 0, 0, 1, 1641877735, 1641877735);
INSERT INTO `one_system_menu` VALUES (144, 0, 138, 'member', '修改帐户', 'fa fa-credit-card', 'member/index/modify_account', '', '_self', 0, 0, 0, 0, 1, 1641877735, 1641877735);
INSERT INTO `one_system_menu` VALUES (145, 0, 137, 'member', '会员等级', 'fa fa-credit-card', 'member/level/index', '', '_self', 0, 0, 0, 1, 1, 1641877735, 1641877735);
INSERT INTO `one_system_menu` VALUES (146, 0, 145, 'member', '编辑', 'fa fa-credit-card', 'member/level/edit', '', '_self', 0, 0, 0, 0, 1, 1641877735, 1641877735);
INSERT INTO `one_system_menu` VALUES (147, 0, 145, 'member', '删除', 'fa fa-credit-card', 'member/level/del', '', '_self', 0, 0, 0, 0, 1, 1641877735, 1641877735);
INSERT INTO `one_system_menu` VALUES (148, 0, 137, 'member', '会员文件', 'fa fa-credit-card', 'member/member_file/index', '', '_self', 0, 0, 0, 1, 1, 1641877735, 1641877735);

-- ----------------------------
-- Table structure for one_system_menu_lang
-- ----------------------------
DROP TABLE IF EXISTS `one_system_menu_lang`;
CREATE TABLE `one_system_menu_lang`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `menu_id` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `title` varchar(120) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '标题',
  `lang` tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '语言包',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '[系统] 管理菜单语言包' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for one_system_module
-- ----------------------------
DROP TABLE IF EXISTS `one_system_module`;
CREATE TABLE `one_system_module`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `system` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '系统模块',
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '模块名(英文)',
  `identifier` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '模块标识(模块名(字母).开发者标识.module)',
  `title` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '模块标题',
  `intro` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '模块简介',
  `author` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '作者',
  `icon` varchar(80) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'aicon ai-mokuaiguanli' COMMENT '图标',
  `version` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '版本号',
  `url` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '链接',
  `sort` int(5) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0未安装，1未启用，2已启用',
  `default` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '默认模块(只能有一个)',
  `config` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '配置',
  `app_id` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '应用市场ID(0本地)',
  `app_keys` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '应用秘钥',
  `theme` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'default' COMMENT '主题模板',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `name`(`name`) USING BTREE,
  UNIQUE INDEX `identifier`(`identifier`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '[系统] 模块' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of one_system_module
-- ----------------------------
INSERT INTO `one_system_module` VALUES (2, 0, 'goods', '1f5118bebad16e2cf5515ed38dc308df', '订单管理', '订单管理', 'one', '/static/goods/goods.png', '1.0.0', 'https://www.qdapi.cn/', 0, 2, 0, '', '0', '', 'default', 1639537050, 1639537050);
INSERT INTO `one_system_module` VALUES (3, 0, 'member', 'f694f2920ccac9a036d36fef5b744a53', '会员中心', '', 'one', '/static/member/member.png', '1.0.0', 'https://www.ouenyi.cn/', 0, 2, 0, '', '0', '', 'default', 1639537050, 1639537050);
INSERT INTO `one_system_module` VALUES (4, 0, 'news', 'news.617.module', '新闻公告', '发布相关文章及公告', '617', '', '1.0.0', 'https://www.qdapi.com', 0, 2, 0, '', '0', '', 'default', 1639537050, 1639537050);
INSERT INTO `one_system_module` VALUES (5, 0, 'one_api', 'one', '分层API', '扩展分层模型,对外提供RESTful接口,带基本验证', 'one', '/static/one_api/app.png', '1.0.0', 'https://www.ouenyi.cn/', 0, 2, 0, '', '0', '', 'default', 1639537050, 1639537050);
INSERT INTO `one_system_module` VALUES (6, 0, 'one_pay', 'one_pay', '在线支付', '通用支付模块，可用于管理及拓展各类支付方式', 'one', '/static/one_pay/app.png', '2.0.0', 'https://www.ouenyi.cn/', 0, 2, 0, '', '0', '', 'default', 1639537050, 1639537050);

-- ----------------------------
-- Table structure for one_system_plugins
-- ----------------------------
DROP TABLE IF EXISTS `one_system_plugins`;
CREATE TABLE `one_system_plugins`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `system` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `name` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '插件名称(英文)',
  `title` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '插件标题',
  `icon` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '图标',
  `intro` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '插件简介',
  `author` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '作者',
  `url` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '作者主页',
  `version` varchar(16) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '版本号',
  `identifier` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '插件唯一标识符',
  `config` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '插件配置',
  `app_id` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '来源(0本地)',
  `app_keys` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '应用秘钥',
  `sort` int(10) UNSIGNED NOT NULL DEFAULT 100 COMMENT '排序',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '状态',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '[系统] 插件表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for one_system_role
-- ----------------------------
DROP TABLE IF EXISTS `one_system_role`;
CREATE TABLE `one_system_role`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '角色名称',
  `intro` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '角色简介',
  `auth` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '角色权限',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `name`(`name`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '[系统] 管理角色' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of one_system_role
-- ----------------------------
INSERT INTO `one_system_role` VALUES (1, '超级管理员', '', '', 1, 1606883052, 1606883052);

-- ----------------------------
-- Table structure for one_system_upload
-- ----------------------------
DROP TABLE IF EXISTS `one_system_upload`;
CREATE TABLE `one_system_upload`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '上传平台code',
  `abbrev` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '上传平台简称',
  `title` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '上传平台标题',
  `intro` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '上传平台简介',
  `config` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '配置',
  `applies` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'pc' COMMENT '适用环境(pc,wap,wechat,app)',
  `sort` int(10) UNSIGNED NOT NULL DEFAULT 100 COMMENT '排序',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '状态(0停用，1启用)',
  `default` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '默认',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '[upload] 上传平台' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for one_system_user
-- ----------------------------
DROP TABLE IF EXISTS `one_system_user`;
CREATE TABLE `one_system_user`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '用户名',
  `password` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `nick` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '昵称',
  `mobile` varchar(11) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `email` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '邮箱',
  `auth` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '权限',
  `iframe` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0默认，1框架',
  `theme` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'default' COMMENT '主题',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态',
  `last_login_ip` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '最后登陆IP',
  `last_login_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '最后登陆时间',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '[系统] 管理用户' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of one_system_user
-- ----------------------------
INSERT INTO `one_system_user` VALUES (1, 'admin', '$2y$10$NWVOvKiWzwkxUcRDFV0P/O9Q4evvHZaXHiFc9MXIxr3YKQrwWEB12', '超级管理员', '', '', '', 0, 'default', 1, '127.0.0.1', 1646362688, 1639536384, 1639734726);

-- ----------------------------
-- Table structure for one_system_user_role
-- ----------------------------
DROP TABLE IF EXISTS `one_system_user_role`;
CREATE TABLE `one_system_user_role`  (
  `user_id` int(11) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL DEFAULT 0
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '管理员角色索引' ROW_FORMAT = Compact;

SET FOREIGN_KEY_CHECKS = 1;
