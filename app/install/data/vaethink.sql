/*
Navicat MySQL Data Transfer

Source Server         : 本地
Source Server Version : 80012
Source Host           : 127.0.0.1:3306
Source Database       : pwtp6

Target Server Type    : MYSQL
Target Server Version : 80012
File Encoding         : 65001

Date: 2020-03-31 18:00:21
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for vae_admin
-- ----------------------------
DROP TABLE IF EXISTS `vae_admin`;
CREATE TABLE `vae_admin` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL DEFAULT '',
  `pwd` varchar(255) NOT NULL DEFAULT '',
  `salt` varchar(50) NOT NULL DEFAULT '',
  `status` int(1) NOT NULL DEFAULT '1' COMMENT '1正常-1禁止登陆',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0',
  `last_login_time` int(11) NOT NULL DEFAULT '0',
  `last_login_ip` varchar(100) NOT NULL DEFAULT '',
  `nickname` varchar(255) DEFAULT '',
  `desc` text COMMENT '备注',
  `thumb` varchar(200) DEFAULT NULL,
  `groups` varchar(255) NOT NULL DEFAULT '' COMMENT '权限组,隔开',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`id`,`username`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='管理员';

-- ----------------------------
-- Records of vae_admin
-- ----------------------------

-- ----------------------------
-- Table structure for vae_admin_group
-- ----------------------------
DROP TABLE IF EXISTS `vae_admin_group`;
CREATE TABLE `vae_admin_group` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `status` int(1) NOT NULL DEFAULT '1',
  `rules` text CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT '用户组拥有的规则id， 多个规则","隔开',
  `desc` text COMMENT '备注',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='权限分组';

-- ----------------------------
-- Records of vae_admin_group
-- ----------------------------

-- ----------------------------
-- Table structure for vae_admin_rule
-- ----------------------------
DROP TABLE IF EXISTS `vae_admin_rule`;
CREATE TABLE `vae_admin_rule` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(11) unsigned NOT NULL DEFAULT '0',
  `src` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '规则',
  `title` varchar(255) NOT NULL DEFAULT '',
  `is_menu` int(1) NOT NULL DEFAULT '1' COMMENT '1是菜单2不是',
  `font_family` varchar(50) DEFAULT '' COMMENT '图标来源',
  `icon` varchar(100) DEFAULT NULL COMMENT '图标',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序，越大越靠前',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL COMMENT '0',
  PRIMARY KEY (`id`),
  KEY `name` (`src`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8 COMMENT='权限节点';

-- ----------------------------
-- Records of vae_admin_rule
-- ----------------------------
INSERT INTO `vae_admin_rule` VALUES ('1', '0', '', '系统', '1', '', 'layui-icon-windows', '0', '0', '0');
INSERT INTO `vae_admin_rule` VALUES ('2', '1', 'menu/index', '菜单', '1', '', 'layui-icon-tree', '0', '0', '0');
INSERT INTO `vae_admin_rule` VALUES ('3', '2', 'menu/add', '添加菜单', '0', '', '', '0', '0', '0');
INSERT INTO `vae_admin_rule` VALUES ('4', '2', 'menu/edit', '修改菜单', '0', '', '', '0', '0', '0');
INSERT INTO `vae_admin_rule` VALUES ('5', '2', 'menu/delete', '删除菜单', '0', '', '', '0', '0', '0');
INSERT INTO `vae_admin_rule` VALUES ('6', '14', 'admin/index', '管理员', '1', '', 'layui-icon-friends', '0', '0', '0');
INSERT INTO `vae_admin_rule` VALUES ('7', '6', 'admin/add', '添加管理员', '0', '', '', '0', '0', '0');
INSERT INTO `vae_admin_rule` VALUES ('8', '6', 'admin/edit', '修改管理员', '0', '', '', '0', '0', '0');
INSERT INTO `vae_admin_rule` VALUES ('9', '6', 'admin/delete', '删除管理员', '0', '', '', '0', '0', '0');
INSERT INTO `vae_admin_rule` VALUES ('10', '14', 'group/index', '管理组', '1', '', 'layui-icon-user', '0', '0', '0');
INSERT INTO `vae_admin_rule` VALUES ('11', '10', 'group/add', '添加权限组', '0', '', '', '0', '0', '0');
INSERT INTO `vae_admin_rule` VALUES ('12', '10', 'group/edit', '修改权限组', '0', '', '', '0', '0', '0');
INSERT INTO `vae_admin_rule` VALUES ('13', '10', 'group/delete', '删除权限组', '0', '', '', '0', '0', '0');
INSERT INTO `vae_admin_rule` VALUES ('14', '1', '', '安全', '1', '', 'layui-icon-auz', '-1', '0', '0');
INSERT INTO `vae_admin_rule` VALUES ('15', '1', 'conf/index', '配置', '1', '', 'layui-icon-set', '0', '0', '0');
INSERT INTO `vae_admin_rule` VALUES ('16', '15', 'conf/confsubmit', '提交配置', '0', '', '', '0', '0', '0');
INSERT INTO `vae_admin_rule` VALUES ('17', '0', '', '应用', '1', '', 'layui-icon-app', '0', '0', '0');
INSERT INTO `vae_admin_rule` VALUES ('18', '17', 'nav/index', '导航', '1', '', 'layui-icon-component', '0', '0', '0');
INSERT INTO `vae_admin_rule` VALUES ('19', '18', 'nav/addgroup', '添加导航组', '0', '', '', '0', '0', '0');
INSERT INTO `vae_admin_rule` VALUES ('20', '18', 'nav/editgroup', '修改导航组', '0', '', '', '0', '0', '0');
INSERT INTO `vae_admin_rule` VALUES ('21', '18', 'nav/deletegroup', '删除导航组', '0', '', '', '0', '0', '0');
INSERT INTO `vae_admin_rule` VALUES ('22', '18', 'nav/navindex', '导航集', '0', '', '', '0', '0', '0');
INSERT INTO `vae_admin_rule` VALUES ('23', '18', 'nav/addnav', '添加导航', '0', '', '', '0', '0', '0');
INSERT INTO `vae_admin_rule` VALUES ('24', '18', 'nav/editnav', '修改导航', '0', '', '', '0', '0', '0');
INSERT INTO `vae_admin_rule` VALUES ('25', '18', 'nav/deletenav', '删除导航', '0', '', '', '0', '0', '0');
INSERT INTO `vae_admin_rule` VALUES ('26', '1', 'route/index', '路由', '1', '', 'layui-icon-link', '0', '0', '0');
INSERT INTO `vae_admin_rule` VALUES ('27', '26', 'route/add', '添加美化', '0', '', '', '0', '0', '0');
INSERT INTO `vae_admin_rule` VALUES ('28', '26', 'route/edit', '修改美化', '0', '', '', '0', '0', '0');
INSERT INTO `vae_admin_rule` VALUES ('29', '26', 'route/delete', '删除美化', '0', '', '', '0', '0', '0');
INSERT INTO `vae_admin_rule` VALUES ('31', '17', 'slide/index', '轮播', '1', '', 'layui-icon-carousel', '0', '0', '0');
INSERT INTO `vae_admin_rule` VALUES ('32', '17', 'cate/index', '分类', '1', '', 'layui-icon-tree', '0', '0', '0');
INSERT INTO `vae_admin_rule` VALUES ('34', '31', 'slide/addgroup', '添加轮播组', '0', '', '', '0', '0', '0');
INSERT INTO `vae_admin_rule` VALUES ('35', '31', 'slide/editgroup', '修改轮播组', '0', '', '', '0', '0', '0');
INSERT INTO `vae_admin_rule` VALUES ('36', '31', 'slide/deletegroup', '删除轮播组', '0', '', '', '0', '0', '0');
INSERT INTO `vae_admin_rule` VALUES ('37', '31', 'slide/slideindex', '轮播集', '0', '', '', '0', '0', '0');
INSERT INTO `vae_admin_rule` VALUES ('38', '31', 'slide/addslide', '添加轮播图', '0', '', '', '0', '0', '0');
INSERT INTO `vae_admin_rule` VALUES ('39', '31', 'slide/editslide', '修改轮播图', '0', '', '', '0', '0', '0');
INSERT INTO `vae_admin_rule` VALUES ('40', '31', 'slide/deleteslide', '删除轮播图', '0', '', '', '0', '0', '0');
INSERT INTO `vae_admin_rule` VALUES ('41', '32', 'cate/addgroup', '添加分类组', '0', '', '', '0', '0', '0');
INSERT INTO `vae_admin_rule` VALUES ('42', '32', 'cate/editgroup', '修改分类组', '0', '', '', '0', '0', '0');
INSERT INTO `vae_admin_rule` VALUES ('43', '32', 'cate/deletegroup', '删除分类组', '0', '', '', '0', '0', '0');
INSERT INTO `vae_admin_rule` VALUES ('44', '32', 'cate/cateindex', '分类集', '0', '', '', '0', '0', '0');
INSERT INTO `vae_admin_rule` VALUES ('45', '32', 'cate/addcate', '添加分类', '0', '', '', '0', '0', '0');
INSERT INTO `vae_admin_rule` VALUES ('46', '32', 'cate/editcate', '修改分类', '0', '', '', '0', '0', '0');
INSERT INTO `vae_admin_rule` VALUES ('47', '32', 'cate/deletecate', '删除分类', '0', '', '', '0', '0', '0');
INSERT INTO `vae_admin_rule` VALUES ('48', '17', 'content/index', '内容', '1', '', 'layui-icon-template-1', '0', '0', '0');
INSERT INTO `vae_admin_rule` VALUES ('49', '48', 'content/addgroup', '添加内容组', '0', '', '', '0', '0', '0');
INSERT INTO `vae_admin_rule` VALUES ('50', '48', 'content/editgroup', '修改内容组', '0', '', '', '0', '0', '0');
INSERT INTO `vae_admin_rule` VALUES ('51', '48', 'content/deletegroup', '删除内容组', '0', '', '', '0', '0', '0');
INSERT INTO `vae_admin_rule` VALUES ('52', '48', 'content/addcontent', '添加内容', '0', '', '', '0', '0', '0');
INSERT INTO `vae_admin_rule` VALUES ('53', '48', 'content/editcontent', '修改内容', '0', '', '', '0', '0', '0');
INSERT INTO `vae_admin_rule` VALUES ('54', '48', 'content/deletecontent', '删除内容', '0', '', '', '0', '0', '0');

-- ----------------------------
-- Table structure for vae_cate
-- ----------------------------
DROP TABLE IF EXISTS `vae_cate`;
CREATE TABLE `vae_cate` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `icon` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `pid` int(11) NOT NULL DEFAULT '0',
  `status` int(1) NOT NULL DEFAULT '1' COMMENT '1正常0下架',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序，值越大越靠前',
  `cate_group_id` int(11) NOT NULL DEFAULT '0',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='分类';

-- ----------------------------
-- Records of vae_cate
-- ----------------------------

-- ----------------------------
-- Table structure for vae_cate_group
-- ----------------------------
DROP TABLE IF EXISTS `vae_cate_group`;
CREATE TABLE `vae_cate_group` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `key` varchar(50) NOT NULL,
  `desc` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='分类分组';

-- ----------------------------
-- Records of vae_cate_group
-- ----------------------------

-- ----------------------------
-- Table structure for vae_content
-- ----------------------------
DROP TABLE IF EXISTS `vae_content`;
CREATE TABLE `vae_content` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `img` text CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT '图片，多图用,隔开',
  `status` int(1) NOT NULL DEFAULT '1' COMMENT '1正常0下架',
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `desc` varchar(500) DEFAULT NULL COMMENT '概要',
  `content` text COMMENT '详情',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序，值越大越靠前',
  `content_group_id` int(11) NOT NULL DEFAULT '0',
  `cate_id` int(11) NOT NULL DEFAULT '0',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='内容';

-- ----------------------------
-- Records of vae_content
-- ----------------------------

-- ----------------------------
-- Table structure for vae_content_group
-- ----------------------------
DROP TABLE IF EXISTS `vae_content_group`;
CREATE TABLE `vae_content_group` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `key` varchar(50) NOT NULL,
  `cate_group_id` int(11) NOT NULL DEFAULT '0' COMMENT '分类组id',
  `desc` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='内容分组';

-- ----------------------------
-- Records of vae_content_group
-- ----------------------------

-- ----------------------------
-- Table structure for vae_nav
-- ----------------------------
DROP TABLE IF EXISTS `vae_nav`;
CREATE TABLE `vae_nav` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `icon` varchar(255) NOT NULL,
  `src` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `status` int(1) NOT NULL DEFAULT '1' COMMENT '1正常0下架',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序，值越大越靠前',
  `nav_group_id` int(11) NOT NULL DEFAULT '0',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='导航';

-- ----------------------------
-- Records of vae_nav
-- ----------------------------

-- ----------------------------
-- Table structure for vae_nav_group
-- ----------------------------
DROP TABLE IF EXISTS `vae_nav_group`;
CREATE TABLE `vae_nav_group` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `key` varchar(50) NOT NULL,
  `desc` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='导航分组';

-- ----------------------------
-- Records of vae_nav_group
-- ----------------------------

-- ----------------------------
-- Table structure for vae_route
-- ----------------------------
DROP TABLE IF EXISTS `vae_route`;
CREATE TABLE `vae_route` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `module` varchar(255) NOT NULL DEFAULT '' COMMENT '应用名',
  `full_url` varchar(255) NOT NULL DEFAULT '',
  `url` varchar(255) NOT NULL DEFAULT '',
  `status` int(1) NOT NULL DEFAULT '1' COMMENT '1启用-1禁用',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='路由设置';

-- ----------------------------
-- Records of vae_route
-- ----------------------------

-- ----------------------------
-- Table structure for vae_slide
-- ----------------------------
DROP TABLE IF EXISTS `vae_slide`;
CREATE TABLE `vae_slide` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `icon` varchar(255) NOT NULL,
  `status` int(1) NOT NULL DEFAULT '1' COMMENT '1正常0下架',
  `src` varchar(255) DEFAULT NULL,
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序，值越大越靠前',
  `slide_group_id` int(11) NOT NULL DEFAULT '0',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='轮播';

-- ----------------------------
-- Records of vae_slide
-- ----------------------------

-- ----------------------------
-- Table structure for vae_slide_group
-- ----------------------------
DROP TABLE IF EXISTS `vae_slide_group`;
CREATE TABLE `vae_slide_group` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `key` varchar(50) NOT NULL,
  `desc` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='轮播分组';

-- ----------------------------
-- Records of vae_slide_group
-- ----------------------------
