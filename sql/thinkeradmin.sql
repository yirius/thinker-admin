/*
 Navicat Premium Data Transfer

 Source Server         : localhost_3306
 Source Server Type    : MySQL
 Source Server Version : 100134
 Source Host           : localhost:3306
 Source Schema         : thinkeradmin

 Target Server Type    : MySQL
 Target Server Version : 100134
 File Encoding         : 65001

 Date: 28/02/2019 21:22:55
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for ices_admin_group
-- ----------------------------
DROP TABLE IF EXISTS `ices_admin_group`;
CREATE TABLE `ices_admin_group` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `title` char(100) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `rules` longtext CHARACTER SET utf8,
  `createtime` datetime DEFAULT NULL,
  `updatetime` datetime DEFAULT NULL,
  `deletetime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of ices_admin_group
-- ----------------------------
BEGIN;
INSERT INTO `ices_admin_group` VALUES (1, '超级管理员', 1, '1,2,3,4,5,6,7,8,9,10,11,12,13', '2019-02-28 16:39:11', '2019-02-28 16:39:14', NULL);
INSERT INTO `ices_admin_group` VALUES (2, '系统管理员', 1, '1,2,3,4,5,6,7,8,9,10,11,12,13', NULL, NULL, NULL);
COMMIT;

-- ----------------------------
-- Table structure for ices_admin_group_access
-- ----------------------------
DROP TABLE IF EXISTS `ices_admin_group_access`;
CREATE TABLE `ices_admin_group_access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) unsigned NOT NULL,
  `group_id` mediumint(8) unsigned NOT NULL,
  `type` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uid_group_id` (`uid`,`group_id`,`type`) USING BTREE,
  KEY `uid` (`uid`),
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of ices_admin_group_access
-- ----------------------------
BEGIN;
INSERT INTO `ices_admin_group_access` VALUES (1, 1, 1, 0);
COMMIT;

-- ----------------------------
-- Table structure for ices_admin_member
-- ----------------------------
DROP TABLE IF EXISTS `ices_admin_member`;
CREATE TABLE `ices_admin_member` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `realname` varchar(100) DEFAULT NULL,
  `password` varchar(41) NOT NULL,
  `salt` varchar(10) DEFAULT '',
  `createtime` datetime DEFAULT NULL,
  `updatetime` datetime DEFAULT NULL,
  `deletetime` datetime DEFAULT NULL,
  `status` int(1) NOT NULL DEFAULT '0',
  `theme` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of ices_admin_member
-- ----------------------------
BEGIN;
INSERT INTO `ices_admin_member` VALUES (1, 'admin', '11111111111', '超级管理员', '874437cefaca98162131470e51cd31c7e2bd311f', '740143', '2018-06-28 15:27:50', '2018-06-29 17:07:09', NULL, 1, NULL);
COMMIT;

-- ----------------------------
-- Table structure for ices_admin_menu
-- ----------------------------
DROP TABLE IF EXISTS `ices_admin_menu`;
CREATE TABLE `ices_admin_menu` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `pid` smallint(6) NOT NULL DEFAULT '0',
  `name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `title` varchar(20) CHARACTER SET utf8 NOT NULL COMMENT '菜单名称',
  `jump` varchar(50) CHARACTER SET utf8 DEFAULT NULL COMMENT '链接',
  `icon` varchar(50) CHARACTER SET utf8 DEFAULT NULL COMMENT '字体图标',
  `sort` smallint(6) DEFAULT NULL,
  `createtime` datetime DEFAULT NULL,
  `updatetime` datetime DEFAULT NULL,
  `deletetime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COMMENT='菜单表';

-- ----------------------------
-- Records of ices_admin_menu
-- ----------------------------
BEGIN;
INSERT INTO `ices_admin_menu` VALUES (1, 0, 'thinkersystem', 'Admin管理', 'thinkersystem', 'layui-icon-set', 0, NULL, NULL, NULL);
INSERT INTO `ices_admin_menu` VALUES (2, 1, 'thinkersystem-rules', 'Rules管理', '/thinkersystem/rules', NULL, 0, NULL, NULL, NULL);
INSERT INTO `ices_admin_menu` VALUES (3, 1, 'thinkersystem-menu', 'Menus管理', '/thinkersystem/menus', NULL, 0, NULL, NULL, NULL);
INSERT INTO `ices_admin_menu` VALUES (4, 1, 'thinkersystem-role', 'Roles管理', '/thinkersystem/roles', NULL, 0, NULL, NULL, NULL);
INSERT INTO `ices_admin_menu` VALUES (5, 1, 'thinkersystem-member', 'Members管理', '/thinkersystem/members', NULL, 0, NULL, NULL, NULL);
COMMIT;

-- ----------------------------
-- Table structure for ices_admin_rule
-- ----------------------------
DROP TABLE IF EXISTS `ices_admin_rule`;
CREATE TABLE `ices_admin_rule` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(80) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `title` char(20) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `condition` char(100) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `mid` int(8) NOT NULL,
  `type` tinyint(2) NOT NULL DEFAULT '1',
  `createtime` datetime DEFAULT NULL,
  `updatetime` datetime DEFAULT NULL,
  `deletetime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of ices_admin_rule
-- ----------------------------
BEGIN;
INSERT INTO `ices_admin_rule` VALUES (1, 'thinkersystem', 'Admin管理', 1, '', 0, 1, NULL, NULL, NULL);
INSERT INTO `ices_admin_rule` VALUES (2, '/thinkersystem/rules', 'Rules管理', 1, '', 1, 1, NULL, NULL, NULL);
INSERT INTO `ices_admin_rule` VALUES (3, '/restful/adminrule', 'Rules管理Restful', 1, '', 2, 1, NULL, NULL, NULL);
INSERT INTO `ices_admin_rule` VALUES (4, '/thinkersystem/rulesEdit', 'Rules管理Edit界面', 1, '', 2, 1, NULL, NULL, NULL);
INSERT INTO `ices_admin_rule` VALUES (5, '/thinkersystem/menus', 'Menus管理', 1, '', 1, 1, NULL, NULL, NULL);
INSERT INTO `ices_admin_rule` VALUES (6, '/restful/adminmenu', 'Menus管理Restful', 1, '', 5, 1, NULL, NULL, NULL);
INSERT INTO `ices_admin_rule` VALUES (7, '/thinkersystem/menusEdit', 'Menus管理Edit界面', 1, '', 5, 1, NULL, NULL, NULL);
INSERT INTO `ices_admin_rule` VALUES (8, '/thinkersystem/roles', 'Roles管理', 1, '', 1, 1, NULL, NULL, NULL);
INSERT INTO `ices_admin_rule` VALUES (9, '/restful/adminrole', 'Roles管理Restful', 1, '', 8, 1, NULL, NULL, NULL);
INSERT INTO `ices_admin_rule` VALUES (10, '/thinkersystem/rolesEdit', 'Roles管理Edit界面', 1, '', 8, 1, NULL, NULL, NULL);
INSERT INTO `ices_admin_rule` VALUES (11, '/thinkersystem/members', 'Members管理', 1, '', 1, 1, NULL, NULL, NULL);
INSERT INTO `ices_admin_rule` VALUES (12, '/restful/adminmember', 'Members管理Restful', 1, '', 11, 1, NULL, NULL, NULL);
INSERT INTO `ices_admin_rule` VALUES (13, '/thinkersystem/membersEdit', 'Members管理Edit界面', 1, '', 11, 1, NULL, NULL, NULL);
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
