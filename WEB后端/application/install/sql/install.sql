
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for one_album
-- ----------------------------
DROP TABLE IF EXISTS `one_album`;
CREATE TABLE `one_album` (
  `album_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `site_id` int(11) NOT NULL DEFAULT '0' COMMENT '站点id',
  `site_name` varchar(255) NOT NULL DEFAULT '' COMMENT '站点名称',
  `album_name` varchar(50) NOT NULL DEFAULT '' COMMENT '相册,名称',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `cover` varchar(255) NOT NULL DEFAULT '' COMMENT '背景图',
  `desc` varchar(255) NOT NULL DEFAULT '' COMMENT '介绍',
  `is_default` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否默认',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `delete_time` int(11) NOT NULL DEFAULT '0' COMMENT '删除时间',
  `num` int(11) NOT NULL DEFAULT '0' COMMENT '相册图片数',
  PRIMARY KEY (`album_id`) USING BTREE,
  KEY `IDX_sys_album_is_default` (`is_default`) USING BTREE,
  KEY `IDX_sys_album_site_id` (`site_id`) USING BTREE,
  KEY `IDX_sys_album_sort` (`sort`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='[系统] 上传附件分组';
-- ----------------------------
-- Records of one_album
-- ----------------------------
DROP TABLE IF EXISTS `one_album_pic`;
CREATE TABLE `one_album_pic` (
  `pic_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `pic_name` varchar(255) NOT NULL DEFAULT '' COMMENT '名称',
  `pic_path` varchar(255) NOT NULL DEFAULT '' COMMENT '路径',
  `pic_spec` varchar(255) NOT NULL DEFAULT '' COMMENT '规格',
  `pic_hash` varchar(64) NOT NULL DEFAULT '' COMMENT '文件hash值',
  `site_id` int(11) NOT NULL DEFAULT '0' COMMENT '站点id',
  `drive` varchar(50) NOT NULL DEFAULT '' COMMENT '驱动local qiniu等',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `delete_time` int(11) NOT NULL DEFAULT '0' COMMENT '删除时间',
  `album_id` int(11) NOT NULL DEFAULT '0' COMMENT '相册id',
  `ext` varchar(10) NOT NULL DEFAULT '' COMMENT '后缀',
  PRIMARY KEY (`pic_id`) USING BTREE,
  KEY `IDX_sys_album_pic_site_id` (`site_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='[系统] 上传附件';

DROP TABLE IF EXISTS `one_system_upload`;
CREATE TABLE `one_system_upload` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL DEFAULT '' COMMENT '上传平台code',
  `abbrev` varchar(20) DEFAULT '' COMMENT '上传平台简称',
  `title` varchar(50) NOT NULL COMMENT '上传平台标题',
  `intro` varchar(255) NOT NULL COMMENT '上传平台简介',
  `config` text NOT NULL COMMENT '配置',
  `applies` varchar(10) NOT NULL DEFAULT 'pc' COMMENT '适用环境(pc,wap,wechat,app)',
  `sort` int(10) unsigned NOT NULL DEFAULT '100' COMMENT '排序',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态(0停用，1启用)',
  `default` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '默认',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='[upload] 上传平台';
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
) ENGINE = InnoDB AUTO_INCREMENT = 39 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '[系统] 系统配置' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of one_system_config
-- ----------------------------
INSERT INTO `one_system_config` VALUES (1, 1, 'sys', '扩展配置分组', 'config_group', 'app_config:APP配置', 'array', ' ', '', '请按如下格式填写：&lt;br&gt;键值:键名&lt;br&gt;键值:键名&lt;br&gt;&lt;span style=&quot;color:#f00&quot;&gt;键值只能为英文、数字、下划线&lt;/span&gt;', 2, 1, 0, 1638181814),
(2, 1, 'base', '网站域名', 'site_domain', 'http://domain.com', 'input', '', '', '', 2, 1, 0, 0),
(3, 1, 'upload', '图片上传大小限制', 'upload_image_size', '0', 'input', '', '', '单位：KB，0表示不限制大小', 3, 1, 0, 0),
(4, 1, 'upload', '允许上传图片格式', 'upload_image_ext', 'jpg,png,gif,jpeg,ico', 'input', '', '', '多个格式请用英文逗号（,）隔开', 4, 1, 0, 0),
(5, 1, 'upload', '缩略图裁剪方式', 'thumb_type', '2', 'select', '1:等比例缩放\r\n2:缩放后填充\r\n3:居中裁剪\r\n4:左上角裁剪\r\n5:右下角裁剪\r\n6:固定尺寸缩放\r\n', '', '', 5, 1, 0, 0),
(6, 1, 'upload', '图片水印开关', 'image_watermark', '0', 'switch', '0:关闭\r\n1:开启', '', '', 6, 1, 0, 0),
(7, 1, 'upload', '图片水印图', 'image_watermark_pic', '', 'image', '', '', '', 7, 1, 0, 0),
(8, 1, 'upload', '图片水印透明度', 'image_watermark_opacity', '50', 'input', '', '', '可设置值为0~100，数字越小，透明度越高', 8, 1, 0, 0),
(9, 1, 'upload', '图片水印图位置', 'image_watermark_location', '9', 'select', '7:左下角\r\n1:左上角\r\n4:左居中\r\n9:右下角\r\n3:右上角\r\n6:右居中\r\n2:上居中\r\n8:下居中\r\n5:居中', '', '', 9, 1, 0, 0),
(10, 1, 'upload', '文件上传大小限制', 'upload_file_size', '0', 'input', '', '', '单位：KB，0表示不限制大小', 1, 1, 0, 0),
(11, 1, 'upload', '允许上传文件格式', 'upload_file_ext', 'doc,docx,xls,xlsx,ppt,pptx,pdf,wps,txt,rar,zip', 'input', '', '', '多个格式请用英文逗号（,）隔开', 2, 1, 0, 0),
(12, 1, 'upload', '文字水印开关', 'text_watermark', '0', 'switch', '0:关闭\r\n1:开启', '', '', 10, 1, 0, 0),
(13, 1, 'upload', '文字水印内容', 'text_watermark_content', '', 'input', '', '', '', 11, 1, 0, 0),
(14, 1, 'upload', '文字水印字体', 'text_watermark_font', '', 'file', '', '', '不上传将使用系统默认字体', 12, 1, 0, 0),
(15, 1, 'upload', '文字水印字体大小', 'text_watermark_size', '20', 'input', '', '', '单位：px(像素)', 13, 1, 0, 0),
(16, 1, 'upload', '文字水印颜色', 'text_watermark_color', '#000000', 'input', '', '', '文字水印颜色，格式:#000000', 14, 1, 0, 0),
(17, 1, 'upload', '文字水印位置', 'text_watermark_location', '7', 'select', '7:左下角\r\n1:左上角\r\n4:左居中\r\n9:右下角\r\n3:右上角\r\n6:右居中\r\n2:上居中\r\n8:下居中\r\n5:居中', '', '', 11, 1, 0, 0),
(18, 1, 'upload', '缩略图尺寸', 'thumb_size', '', 'input', '', '', '为空则不生成，生成 500x500 的缩略图，则填写 500x500，多个规格填写参考 300x300;500x500;800x800', 4, 1, 0, 0),
(19, 1, 'sys', '开发模式', 'app_debug', '1', 'switch', '0:关闭\r\n1:开启', '', '&lt;strong class=&quot;red&quot;&gt;生产环境下一定要关闭此配置&lt;/strong&gt;', 3, 1, 0, 1607492697),
(20, 1, 'sys', '页面Trace', 'app_trace', '0', 'switch', '0:关闭\r\n1:开启', '', '&lt;strong class=&quot;red&quot;&gt;生产环境下一定要关闭此配置&lt;/strong&gt;', 4, 1, 0, 1607662815),
(21, 1, 'databases', '备份目录', 'backup_path', './backup/database/', 'input', '', '', '数据库备份路径,路径必须以 / 结尾', 0, 1, 0, 0),
(22, 1, 'databases', '备份分卷大小', 'part_size', '20971521', 'input', '', '', '用于限制压缩后的分卷最大长度。单位：B；建议设置20M', 0, 1, 0, 0),
(23, 1, 'databases', '备份压缩开关', 'compress', '1', 'switch', '', '', '压缩备份文件需要PHP环境支持gzopen,gzwrite函数', 0, 1, 0, 0),
(24, 1, 'databases', '备份压缩级别', 'compress_level', '1', 'radio', '最低:1\r\n一般:4\r\n最高:9', '', '数据库备份文件的压缩级别，该配置在开启压缩时生效', 0, 1, 0, 0),
(25, 1, 'base', '网站状态', 'site_status', '1', 'switch', '0:关闭\r\n1:开启', '', '站点关闭后将不能访问，后台可正常登录', 1, 1, 0, 0),
(26, 1, 'sys', '后台管理路径', 'admin_path', 'admin.php', 'input', '', '', '必须以.php为后缀', 1, 1, 0, 0),
(27, 1, 'base', '网站标题', 'site_title', '网站标题', 'input', '', '', '网站标题是体现一个网站的主旨，要做到主题突出、标题简洁、连贯等特点，建议不超过28个字', 6, 1, 0, 0),
(28, 1, 'base', '网站关键词', 'site_keywords', '网站关键词 ', 'input', '', '', '网页内容所包含的核心搜索关键词，多个关键字请用英文逗号&quot;,&quot;分隔', 7, 1, 0, 0),
(29, 1, 'base', '网站描述', 'site_description', '', 'textarea', '', '', '网页的描述信息，搜索引擎采纳后，作为搜索结果中的页面摘要显示，建议不超过80个字', 8, 1, 0, 0),
(30, 1, 'base', 'ICP备案信息', 'site_icp', '', 'input', '', '', '请填写ICP备案号，用于展示在网站底部，ICP备案官网：&lt;a href=&quot;http://www.miibeian.gov.cn&quot; target=&quot;_blank&quot;&gt;http://www.miibeian.gov.cn&lt;/a&gt;', 9, 1, 0, 0),
(31, 1, 'base', '站点统计代码', 'site_statis', '', 'textarea', '', '', '第三方流量统计代码，前台调用时请先用 htmlspecialchars_decode函数转义输出', 10, 1, 0, 0),
(32, 1, 'base', '网站名称', 'site_name', '网站名称 ', 'input', '', '', '将显示在浏览器窗口标题等位置', 3, 1, 0, 0),
(33, 1, 'base', '网站LOGO', 'site_logo', '', 'image', '', '', '网站LOGO图片', 4, 1, 0, 1607662841),
(34, 1, 'base', '手机网站', 'wap_site_status', '0', 'switch', '0:关闭\r\n1:开启', '', '如果有手机网站，请设置为开启状态，否则只显示PC网站', 2, 1, 0, 1607575916),
(35, 1, 'base', '手机网站域名', 'wap_domain', 'http://m.domain.com', 'input', '', '', '手机访问将自动跳转至此域名，示例：http://m.domain.com', 2, 1, 0, 0),
(36, 1, 'sys', '后台白名单验证', 'admin_whitelist_verify', '0', 'switch', '0:禁用\r\n1:启用', '', '禁用后不存在的菜单节点将不在提示', 7, 1, 0, 0),
(37, 1, 'sys', '系统日志保留', 'system_log_retention', '30', 'input', '', '', '单位天，系统将自动清除 ? 天前的系统日志', 8, 1, 0, 0),
(38, 1, 'upload', '上传驱动', 'upload_driver', 'local', 'select', 'local:本地上传', '', '资源上传驱动设置', 0, 1, 0, 0),
(39, 0, 'app_config', '版本号', 'app_version', '1.0', 'input', '', '', '', 100, 1, 1638181840, 1638181840),
(40, 0, 'app_config', 'APP下载地址', 'app_download', '', 'input', '', '', '', 100, 1, 1638181887, 1638181887),
(41, 0, 'app_config', 'ffmpeg下载地址', 'ffmpeg_download', '', 'input', '', '', '', 100, 1, 1638181912, 1638181912),
(42, 0, 'app_config', 'ffmpeg MD5', 'ffmpeg_md5', '', 'input', '', '', 'ffmpeg文件的md5，用于文件校验', 100, 1, 1639381531, 1639381531),
(43, 0, 'app_config', '放行会员组', 'vip_limit', '', 'input', '', '', '可使用软件的会员组id，多个会员组用英文逗号“,”分割', 100, 1, 1638519463, 1638545664),
(44, 0, 'app_config', '水印字体下载地址', 'water_font', '', 'input', '', '', '', 100, 1, 1642062875, 1642062920);



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
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '[系统] 钩子表' ROW_FORMAT = COMPACT;

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
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '[系统] 钩子-插件对应表' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of one_system_hook_plugins
-- ----------------------------

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
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '[系统] 语言包' ROW_FORMAT = Compact;

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
-- Records of one_system_log
-- ----------------------------

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
) ENGINE = InnoDB AUTO_INCREMENT = 101 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '[系统] 管理菜单' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of one_system_menu
-- ----------------------------
INSERT INTO `one_system_menu` VALUES (1, 0, 0, 'system', '首页', 'el-icon-s-home', 'system/index/index', '', '_self', 100, 0, 1, 1, 1, 1607490932, 0),
(2, 0, 0, 'system', '系统', '', 'system/system/index', '', '_self', 100, 0, 1, 1, 1, 1607490932, 0),
(3, 0, 1, 'system', '个人信息', '', 'system/user/info', '', '_self', 100, 0, 1, 0, 1, 1607490932, 0),
(4, 0, 1, 'system', '清空缓存', '', 'system/index/clear', '', '_self', 100, 0, 1, 0, 1, 1607490932, 0),
(5, 0, 1, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0),
(6, 0, 1, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0),
(7, 0, 1, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0),
(8, 0, 2, 'system', '系统设置', '', 'system/system/index', '', '_self', 100, 0, 1, 1, 1, 1607490932, 0),
(9, 0, 2, 'system', '系统扩展', '', 'system/extend/index', '', '_self', 100, 1, 1, 1, 1, 1607490932, 0),
(10, 0, 2, 'system', '系统管理员', '', 'system/user/index', '', '_self', 100, 0, 1, 1, 1, 1607490932, 0),
(11, 0, 2, 'system', '数据库管理', '', 'system/database/index', '', '_self', 100, 0, 1, 1, 1, 1607490932, 0),
(12, 0, 2, 'system', '系统日志', '', 'system/log/index', '', '_self', 100, 0, 1, 1, 1, 1607490932, 0),
(13, 0, 2, 'system', '配置管理', '', 'system/config/index', '', '_self', 100, 1, 1, 1, 1, 1607490932, 0),
(14, 0, 8, 'system', '基础配置', '', 'system/system/index', 'group=base', '_self', 100, 0, 1, 0, 1, 1607490932, 0),
(15, 0, 8, 'system', '系统配置', '', 'system/system/index', 'group=sys', '_self', 100, 0, 1, 0, 1, 1607490932, 0),
(16, 0, 8, 'system', '上传配置', '', 'system/system/index', 'group=upload', '_self', 100, 0, 1, 0, 1, 1607490932, 0),
(17, 0, 8, 'system', '开发配置', '', 'system/system/index', 'group=develop', '_self', 100, 0, 1, 0, 1, 1607490932, 0),
(18, 0, 8, 'system', '数据库配置', '', 'system/system/index', 'group=databases', '_self', 100, 0, 1, 0, 1, 1607490932, 0),
(19, 0, 9, 'system', '本地模块', '', 'system/module/index', '', '_self', 100, 0, 1, 1, 1, 1607490932, 0),
(20, 0, 9, 'system', '模块钩子', '', 'system/hook/index', '', '_self', 100, 0, 1, 1, 1, 1607490932, 0),
(21, 0, 9, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0),
(22, 0, 9, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0),
(23, 0, 9, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0),
(24, 0, 62, 'system', '添加管理员', '', 'system/user/adduser', '', '_self', 100, 0, 1, 0, 1, 1607490932, 0),
(25, 0, 62, 'system', '修改管理员', '', 'system/user/edituser', '', '_self', 100, 0, 1, 0, 1, 1607490932, 0),
(26, 0, 62, 'system', '删除管理员', '', 'system/user/deluser', '', '_self', 100, 0, 1, 0, 1, 1607490932, 0),
(27, 0, 62, 'system', '状态设置', '', 'system/user/status', '', '_self', 100, 0, 1, 0, 1, 1607490932, 0),
(28, 0, 62, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0),
(29, 0, 11, 'system', '备份数据库', '', 'system/database/export', '', '_self', 100, 0, 1, 0, 1, 1607490932, 0),
(30, 0, 11, 'system', '恢复数据库', '', 'system/database/import', '', '_self', 100, 0, 1, 0, 1, 1607490932, 0),
(31, 0, 11, 'system', '优化数据库', '', 'system/database/optimize', '', '_self', 100, 0, 1, 0, 1, 1607490932, 0),
(32, 0, 11, 'system', '删除备份', '', 'system/database/del', '', '_self', 100, 0, 1, 0, 1, 1607490932, 0),
(33, 0, 11, 'system', '修复数据库', '', 'system/database/repair', '', '_self', 100, 0, 1, 0, 1, 1607490932, 0),
(34, 0, 12, 'system', '清空日志', '', 'system/log/clear', '', '_self', 100, 0, 1, 0, 1, 1607490932, 0),
(35, 0, 12, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0),
(36, 0, 12, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0),
(37, 0, 19, 'system', '配置模块', '', 'system/module/editor', '', '_self', 100, 0, 1, 0, 1, 1607490932, 0),
(38, 0, 19, 'system', '生成模块', '', 'system/module/design', '', '_self', 100, 0, 1, 0, 1, 1607490932, 0),
(39, 0, 19, 'system', '安装模块', '', 'system/module/install', '', '_self', 100, 0, 1, 0, 1, 1607490932, 0),
(40, 0, 19, 'system', '卸载模块', '', 'system/module/uninstall', '', '_self', 100, 0, 1, 0, 1, 1607490932, 0),
(41, 0, 19, 'system', '状态设置', '', 'system/module/status', '', '_self', 100, 0, 1, 0, 1, 1607490932, 0),
(42, 0, 19, 'system', '设置默认模块', '', 'system/module/setdefault', '', '_self', 100, 0, 1, 0, 1, 1607490932, 0),
(43, 0, 19, 'system', '删除模块', '', 'system/module/del', '', '_self', 100, 0, 1, 0, 1, 1607490932, 0),
(44, 0, 19, 'system', '重载模块', '', 'system/module/reinstall', '', '_self', 100, 0, 1, 0, 1, 1607490932, 0),
(50, 0, 20, 'system', '添加钩子', '', 'system/hook/add', '', '_self', 100, 0, 1, 0, 1, 1607490932, 0),
(51, 0, 20, 'system', '修改钩子', '', 'system/hook/edit', '', '_self', 100, 0, 1, 0, 1, 1607490932, 0),
(52, 0, 20, 'system', '删除钩子', '', 'system/hook/del', '', '_self', 100, 0, 1, 0, 1, 1607490932, 0),
(53, 0, 20, 'system', '状态设置', '', 'system/hook/status', '', '_self', 100, 0, 1, 0, 1, 1607490932, 0),
(54, 0, 20, 'system', '插件排序', '', 'system/hook/sort', '', '_self', 100, 0, 1, 0, 1, 1607490932, 0),
(55, 0, 13, 'system', '添加配置', '', 'system/config/add', '', '_self', 100, 0, 1, 0, 1, 1490315067, 0),
(56, 0, 13, 'system', '修改配置', '', 'system/config/edit', '', '_self', 100, 0, 1, 0, 1, 1490315067, 0),
(57, 0, 13, 'system', '删除配置', '', 'system/config/del', '', '_self', 100, 0, 1, 0, 1, 1490315067, 0),
(58, 0, 13, 'system', '状态设置', '', 'system/config/status', '', '_self', 100, 0, 1, 0, 1, 1490315067, 0),
(59, 0, 13, 'system', '排序设置', '', 'system/config/sort', '', '_self', 100, 0, 1, 0, 1, 1490315067, 0),
(60, 0, 13, 'system', '添加分组', '', 'system/config/addgroup', '', '_self', 100, 0, 1, 0, 1, 1607490932, 0),
(61, 0, 13, 'system', '删除分组', '', 'system/config/delgroup', '', '_self', 100, 0, 1, 0, 1, 1607490932, 0),
(62, 0, 10, 'system', '管理用户', '', 'system/user/index', '', '_self', 100, 0, 1, 0, 1, 1607490932, 0),
(63, 0, 10, 'system', '管理角色', '', 'system/user/role', '', '_self', 100, 0, 1, 0, 1, 1607490932, 0),
(64, 0, 63, 'system', '添加角色', '', 'system/user/addrole', '', '_self', 100, 0, 1, 1, 1, 1490315067, 0),
(65, 0, 63, 'system', '修改角色', '', 'system/user/editrole', '', '_self', 100, 0, 1, 1, 1, 1490315067, 0),
(66, 0, 63, 'system', '删除角色', '', 'system/user/delrole', '', '_self', 100, 0, 1, 1, 1, 1490315067, 0),
(67, 0, 63, 'system', '状态设置', '', 'system/user/statusRole', '', '_self', 100, 0, 1, 1, 1, 1490315067, 0),
(68, 0, 2, 'system', '附件管理', '', 'system/album/index', '', '_self', 100, 0, 1, 1, 1, 1607490932, 0),
(69, 0, 2, 'system', '驱动管理', '', 'system/album.drive/index', '', '_self', 100, 0, 1, 0, 1, 1607490932, 0),
(70, 0, 0, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0),
(71, 0, 0, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0),
(72, 0, 0, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0),
(73, 0, 0, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0),
(74, 0, 0, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0),
(75, 0, 0, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0),
(76, 0, 0, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0),
(77, 0, 0, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0),
(78, 0, 0, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0),
(79, 0, 0, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0),
(80, 0, 0, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0),
(81, 0, 0, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0),
(82, 0, 0, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0),
(83, 0, 0, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0),
(84, 0, 0, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0),
(85, 0, 0, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0),
(86, 0, 0, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0),
(87, 0, 0, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0),
(88, 0, 0, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0),
(89, 0, 0, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0),
(90, 0, 0, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0),
(91, 0, 0, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0),
(92, 0, 0, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0),
(93, 0, 0, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0),
(94, 0, 0, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0),
(95, 0, 0, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0),
(96, 0, 0, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0),
(97, 0, 0, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0),
(98, 0, 0, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0),
(99, 0, 0, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0),
(100, 0, 0, 'system', '预留占位', '', '', '', '_self', 100, 0, 1, 0, 0, 1607490932, 0);

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
-- Records of one_system_menu_lang
-- ----------------------------

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
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '[系统] 模块' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of one_system_module
-- ----------------------------

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
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '[系统] 插件表' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of one_system_plugins
-- ----------------------------

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
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '[系统] 管理用户' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for one_system_user_role
-- ----------------------------
DROP TABLE IF EXISTS `one_system_user_role`;
CREATE TABLE `one_system_user_role`  (
  `user_id` int(11) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL DEFAULT 0
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '管理员角色索引' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of one_system_user_role
-- ----------------------------

SET FOREIGN_KEY_CHECKS = 1;
