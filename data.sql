/*
 Navicat Premium Dump SQL

 Source Server         : 124.222.131.85
 Source Server Type    : MySQL
 Source Server Version : 80024 (8.0.24)
 Source Host           : 124.222.131.85:3306
 Source Schema         : echo

 Target Server Type    : MySQL
 Target Server Version : 80024 (8.0.24)
 File Encoding         : 65001

 Date: 12/09/2026 17:09:03
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for common_article
-- ----------------------------
DROP TABLE IF EXISTS `common_article`;
CREATE TABLE `common_article`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `position` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '位置',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '标题',
  `keyword` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '关键字',
  `detaill` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '内容',
  `status` smallint NOT NULL DEFAULT 1 COMMENT '状态：1正常，0隐藏',
  `create_time` datetime NULL DEFAULT NULL,
  `update_time` datetime NULL DEFAULT NULL,
  `delete_time` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '通用功能-文章' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of common_article
-- ----------------------------

-- ----------------------------
-- Table structure for common_mbti_type
-- ----------------------------
DROP TABLE IF EXISTS `common_mbti_type`;
CREATE TABLE `common_mbti_type`  (
  `code` char(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT '人格代码',
  `name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT '中文名',
  `group` char(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'NT/NF/SJ/SP',
  `description` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT '详细描述',
  `traits` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT '标签，逗号分隔',
  `theme_color` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL COMMENT '主题色',
  `status` smallint NOT NULL DEFAULT 1 COMMENT '状态：1正常，0隐藏',
  `create_time` datetime NULL DEFAULT NULL,
  `update_time` datetime NULL DEFAULT NULL,
  `delete_time` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`code`) USING BTREE,
  INDEX `idx_group`(`group` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '人格字典表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of common_mbti_type
-- ----------------------------
INSERT INTO `common_mbti_type` VALUES ('ENFJ', '主人公', 'NF', '你是天生的鼓舞者。热情、有感染力，能敏锐感知他人情绪，并激励身边的人发挥潜力。你享受与人连接，喜欢帮助别人成长，看到他人变好会由衷开心。你有很强的组织能力和责任感，常常主动承担起团队凝聚者的角色。你相信人性本善，也愿意为关系投入时间和精力。缺点是容易忽略自己的需求。适合你的角色：领导者、导师、团队凝聚者。', '热情,有感染力,善解人意,领导力,有责任感', '#D35400', 1, '2026-09-12 15:26:14', '2026-09-12 15:26:14', NULL);
INSERT INTO `common_mbti_type` VALUES ('ENFP', '竞选者', 'NF', '你是热情自由的创意家。对世界充满好奇，喜欢尝试新体验，总能找到理由微笑。你擅长与人连接，能快速和陌生人打成一片，也能敏锐感知他人的情绪。你脑子里总有源源不断的点子，喜欢把生活过得有色彩。你讨厌被束缚和重复，渴望自由和意义并存。缺点是容易分心、难以坚持。适合你的角色：创意人、社交达人、灵感制造者。', '热情,有创意,爱自由,感染力强,好奇', '#F39C12', 1, '2026-09-12 15:26:14', '2026-09-12 15:26:14', NULL);
INSERT INTO `common_mbti_type` VALUES ('ENTJ', '指挥官', 'NT', '你天生就是领导者。目标清晰、行动果断，擅长把混乱的局面快速理清并推动向前。你不怕做决定，也不怕承担责任，越是困难的挑战越能激发你的斗志。你看重效率和结果，讨厌拖泥带水。你有强大的组织能力，能把一群人的力量拧成一股绳。有时显得强势，但你的出发点始终是把事情做成。适合你的角色：管理者、创业者、团队核心。', '果断,有魄力,目标导向,领导力,高效', '#C0392B', 1, '2026-09-12 15:26:14', '2026-09-12 15:26:14', NULL);
INSERT INTO `common_mbti_type` VALUES ('ENTP', '辩论家', 'NT', '你是天生的点子王和挑战者。脑子转得快，喜欢用新思路打破常规，享受思辨和辩论的乐趣。你不怕冲突，反而觉得思想的碰撞能激发更好的方案。你总能看到别人看不到的可能性，也乐于尝试各种新鲜事物。缺点是容易三分钟热度，喜欢开头不喜欢收尾。适合你的角色：创新者、辩论者、破局者。', '机智,善辩,创新,灵活,思维活跃', '#E67E22', 1, '2026-09-12 15:26:14', '2026-09-12 15:26:14', NULL);
INSERT INTO `common_mbti_type` VALUES ('ESFJ', '执政官', 'SJ', '你是热心受欢迎的组织者。重视人际关系，乐于助人，总能让周围充满温暖。你善于察觉他人的需求，主动提供帮助，是团队里的\"粘合剂\"。你喜欢和谐的氛围，不喜欢冲突，会努力维护关系。你有很强的责任心，答应的事一定会做到。你享受和人在一起的时光，也愿意为集体付出。缺点是太在意别人看法。适合你的角色：组织者、协调者、关系维护者。', '热心,善社交,有爱心,负责任,重视关系', '#3498DB', 1, '2026-09-12 15:26:14', '2026-09-12 15:26:14', NULL);
INSERT INTO `common_mbti_type` VALUES ('ESFP', '表演者', 'SP', '你是天生的表演者。热情、爱玩、有活力，走到哪里都能让气氛活跃起来。你喜欢新鲜体验，讨厌无聊和重复，总在寻找下一个有趣的瞬间。你善于感知他人情绪，能快速让陌生人放松。你真诚、大方，愿意分享快乐。你活在当下，享受生活的每一刻。缺点是容易冲动、缺乏耐心。适合你的角色：表演者、气氛担当、快乐制造者。', '热情,爱玩,有活力,感染力强,乐观', '#F1C40F', 1, '2026-09-12 15:26:14', '2026-09-12 15:26:14', NULL);
INSERT INTO `common_mbti_type` VALUES ('ESTJ', '总经理', 'SJ', '你是天生的管理者。条理清晰、执行力强，擅长把混乱的事情梳理得井井有条。你重视规则和效率，做事有始有终，说到做到。你有很强的组织能力，能带领团队高效完成任务。你直接、坦率，不喜欢拐弯抹角。你相信秩序和纪律是成事的基础。缺点是有点强势，容易忽略他人感受。适合你的角色：管理者、组织者、规则的执行者。', '高效,有条理,果断,负责任,执行力强', '#2980B9', 1, '2026-09-12 15:26:14', '2026-09-12 15:26:14', NULL);
INSERT INTO `common_mbti_type` VALUES ('ESTP', '企业家', 'SP', '你是精力充沛的行动派。反应敏捷、胆子大，喜欢冒险和刺激，享受在真实世界里快速解决问题。你善于观察环境和人，能迅速抓住机会。你不喜欢纸上谈兵，更愿意直接上手试。你有很强的社交能力，能在任何场合快速融入。你活在当下，不太纠结过去和未来。缺点是容易冲动，缺乏长期规划。适合你的角色：行动者、谈判者、机会捕捉者。', '大胆,行动力强,灵活,善交际,反应快', '#E74C3C', 1, '2026-09-12 15:26:14', '2026-09-12 15:26:14', NULL);
INSERT INTO `common_mbti_type` VALUES ('INFJ', '提倡者', 'NF', '你是安静而有力量的理想主义者。表面温和，内心却有一套坚定的价值观和信念。你洞察人心，能敏锐感知他人的情绪和需求，常常成为别人倾诉的对象。你追求有意义的事，愿意为理想默默付出，哪怕不被理解也坚持。你既理想又务实，既温柔又坚定。缺点是容易过度消耗自己。适合你的角色：倡导者、倾听者、理想主义实践者。', '洞察力强,有信念,温柔坚定,利他,有深度', '#8E44AD', 1, '2026-09-12 15:26:14', '2026-09-12 15:26:14', NULL);
INSERT INTO `common_mbti_type` VALUES ('INFP', '调停者', 'NF', '你是一个内心世界极其丰富的人。敏感、真诚、有诗意，总在寻找生活的意义和美好。你重视价值观胜过一切，不愿做违背内心的事。你对他人抱有善意，能共情别人的痛苦，也愿意为在乎的人付出。你喜欢用文字、艺术或独处来表达自己，不喜欢被世俗的标准绑架。缺点是容易陷入内耗，做决定时犹豫。适合你的角色：创作者、理想主义者、治愈者。', '温柔,有理想,共情强,真诚,内心丰富', '#16A085', 1, '2026-09-12 15:26:14', '2026-09-12 15:26:14', NULL);
INSERT INTO `common_mbti_type` VALUES ('INTJ', '建筑师', 'NT', '你是天生的战略家，脑子里总有一张未来的蓝图。你习惯用逻辑和系统思维拆解复杂问题，不喜欢被情绪和无意义的社交消耗。独立、自驱、目标明确，一旦认定方向就会默默推进直到实现。你对自己和他人要求都很高，追求效率和长期价值。别人可能觉得你冷静甚至疏离，但其实你只是把精力留给了真正重要的事。适合你的角色：策划者、研究者、长期主义者。', '理性,独立,有远见,追求效率,战略思维', '#2C3E50', 1, '2026-09-12 15:26:14', '2026-09-12 15:26:14', NULL);
INSERT INTO `common_mbti_type` VALUES ('INTP', '逻辑学家', 'NT', '你是一个永远在思考的人。对世界充满好奇，喜欢追问\"为什么\"，享受在概念和理论里自由探索。你不盲从权威，任何观点都要经过自己的逻辑检验。你擅长发现别人忽略的矛盾和漏洞，常常提出让人眼前一亮的新角度。你不喜欢被规则束缚，更愿意按自己的节奏深入感兴趣的事。缺点是有时想得太多、行动太少。适合你的角色：研究者、发明家、问题拆解者。', '好奇,爱思考,独立,创新,逻辑严谨', '#34495E', 1, '2026-09-12 15:26:14', '2026-09-12 15:26:14', NULL);
INSERT INTO `common_mbti_type` VALUES ('ISFJ', '守卫者', 'SJ', '你是温暖而专注的守护者。默默照顾身边的人，记得每个人的喜好和细节，用行动表达关心。你忠诚、体贴、有责任感，愿意为在乎的人付出。你不喜欢成为焦点，更愿意在背后支持别人。你有很强的执行力和耐心，能把琐碎的事处理得井井有条。别人可能觉得你太低调，但其实你是很多人心里最安心的存在。适合你的角色：照顾者、支持者、幕后英雄。', '体贴,细心,忠诚,有责任感,温暖', '#27AE60', 1, '2026-09-12 15:26:14', '2026-09-12 15:26:14', NULL);
INSERT INTO `common_mbti_type` VALUES ('ISFP', '探险家', 'SP', '你是敏感而有魅力的艺术家。活在当下，享受感官体验，用行动而不是语言表达自己。你温和、真诚，不喜欢冲突，也不喜欢被强迫。你对美有天然的感知力，喜欢用色彩、音乐、食物等方式感受生活。你重视自由和真实，不愿做违背内心的事。你看起来随性，其实内心有自己的坚持。适合你的角色：艺术家、体验者、美的发现者。', '温和,有艺术感,灵活,真诚,活在当下', '#1ABC9C', 1, '2026-09-12 15:26:14', '2026-09-12 15:26:14', NULL);
INSERT INTO `common_mbti_type` VALUES ('ISTJ', '物流师', 'SJ', '你是踏实可靠的执行者。注重事实、遵守承诺，做事有条理、有始有终。你相信规则和秩序，认为把事情做对、做扎实比什么都重要。你不喜欢浮夸和空谈，更愿意用行动证明自己。你对细节敏感，能记住别人忽略的信息。你忠诚、负责，是团队里最让人放心的那个。缺点是有点固执，不太容易接受变化。适合你的角色：执行者、管理者、秩序的守护者。', '可靠,务实,有责任心,注重细节,守承诺', '#7F8C8D', 1, '2026-09-12 15:26:14', '2026-09-12 15:26:14', NULL);
INSERT INTO `common_mbti_type` VALUES ('ISTP', '鉴赏家', 'SP', '你是冷静灵巧的实践家。话不多，但动手能力极强，喜欢用最直接的方式解决眼前的问题。你对机械、工具、技术类的东西有天然的兴趣，享受拆解和组装的过程。你临场反应快，越是紧急情况越能保持冷静。你不喜欢被规则和计划束缚，更愿意按自己的节奏来。你独立、务实，不轻易表露情绪。适合你的角色：技术专家、问题解决者、动手派。', '冷静,动手能力强,灵活,务实,独立', '#95A5A6', 1, '2026-09-12 15:26:14', '2026-09-12 15:26:14', NULL);

-- ----------------------------
-- Table structure for init_mbit_test_record
-- ----------------------------
DROP TABLE IF EXISTS `init_mbit_test_record`;
CREATE TABLE `init_mbit_test_record`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '用户ID',
  `result_type` char(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '结果人格，如INTJ',
  `scores` json NULL COMMENT '各维度得分',
  `status` smallint NOT NULL DEFAULT 1 COMMENT '状态：1正常，0隐藏',
  `create_time` datetime NULL DEFAULT NULL,
  `update_time` datetime NULL DEFAULT NULL,
  `delete_time` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_user`(`uuid` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '测试记录表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of init_mbit_test_record
-- ----------------------------

-- ----------------------------
-- Table structure for init_mbti_question
-- ----------------------------
DROP TABLE IF EXISTS `init_mbti_question`;
CREATE TABLE `init_mbti_question`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `content` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '题目内容',
  `dimension` enum('EI','SN','TF','JP') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT '所属维度',
  `sort` int NOT NULL DEFAULT 0 COMMENT '排序',
  `status` tinyint NOT NULL DEFAULT 1 COMMENT '1启用 0禁用',
  `create_time` datetime NULL DEFAULT NULL,
  `update_time` datetime NULL DEFAULT NULL,
  `delete_time` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 17 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '题目表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of init_mbti_question
-- ----------------------------
INSERT INTO `init_mbti_question` VALUES (1, '周末你更想', 'EI', 1, 1, '2026-09-12 15:26:14', '2026-09-12 15:26:14', NULL);
INSERT INTO `init_mbti_question` VALUES (2, '陌生场合你', 'EI', 2, 1, '2026-09-12 15:26:14', '2026-09-12 15:26:14', NULL);
INSERT INTO `init_mbti_question` VALUES (3, '社交后你', 'EI', 3, 1, '2026-09-12 15:26:14', '2026-09-12 15:26:14', NULL);
INSERT INTO `init_mbti_question` VALUES (4, '你的想法', 'EI', 4, 1, '2026-09-12 15:26:14', '2026-09-12 15:26:14', NULL);
INSERT INTO `init_mbti_question` VALUES (5, '你更关注', 'SN', 5, 1, '2026-09-12 15:26:14', '2026-09-12 15:26:14', NULL);
INSERT INTO `init_mbti_question` VALUES (6, '你更相信', 'SN', 6, 1, '2026-09-12 15:26:14', '2026-09-12 15:26:14', NULL);
INSERT INTO `init_mbti_question` VALUES (7, '你更喜欢', 'SN', 7, 1, '2026-09-12 15:26:14', '2026-09-12 15:26:14', NULL);
INSERT INTO `init_mbti_question` VALUES (8, '做事你倾向', 'SN', 8, 1, '2026-09-12 15:26:14', '2026-09-12 15:26:14', NULL);
INSERT INTO `init_mbti_question` VALUES (9, '做决定看', 'TF', 9, 1, '2026-09-12 15:26:14', '2026-09-12 15:26:14', NULL);
INSERT INTO `init_mbti_question` VALUES (10, '朋友诉苦你', 'TF', 10, 1, '2026-09-12 15:26:14', '2026-09-12 15:26:14', NULL);
INSERT INTO `init_mbti_question` VALUES (11, '你更看重', 'TF', 11, 1, '2026-09-12 15:26:14', '2026-09-12 15:26:14', NULL);
INSERT INTO `init_mbti_question` VALUES (12, '被批评时', 'TF', 12, 1, '2026-09-12 15:26:14', '2026-09-12 15:26:14', NULL);
INSERT INTO `init_mbti_question` VALUES (13, '你的生活', 'JP', 13, 1, '2026-09-12 15:26:14', '2026-09-12 15:26:14', NULL);
INSERT INTO `init_mbti_question` VALUES (14, '面对deadline', 'JP', 14, 1, '2026-09-12 15:26:14', '2026-09-12 15:26:14', NULL);
INSERT INTO `init_mbti_question` VALUES (15, '你更喜欢', 'JP', 15, 1, '2026-09-12 15:26:14', '2026-09-12 15:26:14', NULL);
INSERT INTO `init_mbti_question` VALUES (16, '你的桌面', 'JP', 16, 1, '2026-09-12 15:26:14', '2026-09-12 15:26:14', NULL);

-- ----------------------------
-- Table structure for init_mbti_question_option
-- ----------------------------
DROP TABLE IF EXISTS `init_mbti_question_option`;
CREATE TABLE `init_mbti_question_option`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `question_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '题目ID',
  `content` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '选项内容',
  `score_value` tinyint NOT NULL DEFAULT 0 COMMENT 'A=+1 B=-1',
  `status` tinyint NULL DEFAULT 1 COMMENT '1启用 0禁用',
  `create_time` datetime NULL DEFAULT NULL,
  `update_time` datetime NULL DEFAULT NULL,
  `delete_time` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_qid`(`question_id` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 33 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '选项表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of init_mbti_question_option
-- ----------------------------
INSERT INTO `init_mbti_question_option` VALUES (1, 1, '和朋友聚会', 1, 1, '2026-09-12 15:28:54', '2026-09-12 15:28:54', NULL);
INSERT INTO `init_mbti_question_option` VALUES (2, 1, '一个人待着', -1, 1, '2026-09-12 15:28:54', '2026-09-12 15:28:54', NULL);
INSERT INTO `init_mbti_question_option` VALUES (3, 2, '主动搭话', 1, 1, '2026-09-12 15:28:54', '2026-09-12 15:28:54', NULL);
INSERT INTO `init_mbti_question_option` VALUES (4, 2, '等别人开口', -1, 1, '2026-09-12 15:28:54', '2026-09-12 15:28:54', NULL);
INSERT INTO `init_mbti_question_option` VALUES (5, 3, '越聊越有劲', 1, 1, '2026-09-12 15:28:54', '2026-09-12 15:28:54', NULL);
INSERT INTO `init_mbti_question_option` VALUES (6, 3, '想赶紧回家', -1, 1, '2026-09-12 15:28:54', '2026-09-12 15:28:54', NULL);
INSERT INTO `init_mbti_question_option` VALUES (7, 4, '边说边清晰', 1, 1, '2026-09-12 15:28:54', '2026-09-12 15:28:54', NULL);
INSERT INTO `init_mbti_question_option` VALUES (8, 4, '想清楚再说', -1, 1, '2026-09-12 15:28:54', '2026-09-12 15:28:54', NULL);
INSERT INTO `init_mbti_question_option` VALUES (9, 5, '事实细节', 1, 1, '2026-09-12 15:28:54', '2026-09-12 15:28:54', NULL);
INSERT INTO `init_mbti_question_option` VALUES (10, 5, '可能趋势', -1, 1, '2026-09-12 15:28:54', '2026-09-12 15:28:54', NULL);
INSERT INTO `init_mbti_question_option` VALUES (11, 6, '亲身经验', 1, 1, '2026-09-12 15:28:54', '2026-09-12 15:28:54', NULL);
INSERT INTO `init_mbti_question_option` VALUES (12, 6, '直觉灵感', -1, 1, '2026-09-12 15:28:54', '2026-09-12 15:28:54', NULL);
INSERT INTO `init_mbti_question_option` VALUES (13, 7, '具体实用', 1, 1, '2026-09-12 15:28:54', '2026-09-12 15:28:54', NULL);
INSERT INTO `init_mbti_question_option` VALUES (14, 7, '抽象理念', -1, 1, '2026-09-12 15:28:54', '2026-09-12 15:28:54', NULL);
INSERT INTO `init_mbti_question_option` VALUES (15, 8, '用老方法', 1, 1, '2026-09-12 15:28:54', '2026-09-12 15:28:54', NULL);
INSERT INTO `init_mbti_question_option` VALUES (16, 8, '试新思路', -1, 1, '2026-09-12 15:28:54', '2026-09-12 15:28:54', NULL);
INSERT INTO `init_mbti_question_option` VALUES (17, 9, '逻辑', 1, 1, '2026-09-12 15:28:54', '2026-09-12 15:28:54', NULL);
INSERT INTO `init_mbti_question_option` VALUES (18, 9, '感受', -1, 1, '2026-09-12 15:28:54', '2026-09-12 15:28:54', NULL);
INSERT INTO `init_mbti_question_option` VALUES (19, 10, '帮分析', 1, 1, '2026-09-12 15:28:54', '2026-09-12 15:28:54', NULL);
INSERT INTO `init_mbti_question_option` VALUES (20, 10, '先安慰', -1, 1, '2026-09-12 15:28:54', '2026-09-12 15:28:54', NULL);
INSERT INTO `init_mbti_question_option` VALUES (21, 11, '公平', 1, 1, '2026-09-12 15:28:54', '2026-09-12 15:28:54', NULL);
INSERT INTO `init_mbti_question_option` VALUES (22, 11, '和谐', -1, 1, '2026-09-12 15:28:54', '2026-09-12 15:28:54', NULL);
INSERT INTO `init_mbti_question_option` VALUES (23, 12, '看有没有道理', 1, 1, '2026-09-12 15:28:54', '2026-09-12 15:28:54', NULL);
INSERT INTO `init_mbti_question_option` VALUES (24, 12, '容易受伤', -1, 1, '2026-09-12 15:28:54', '2026-09-12 15:28:54', NULL);
INSERT INTO `init_mbti_question_option` VALUES (25, 13, '有计划', 1, 1, '2026-09-12 15:28:54', '2026-09-12 15:28:54', NULL);
INSERT INTO `init_mbti_question_option` VALUES (26, 13, '随性', -1, 1, '2026-09-12 15:28:54', '2026-09-12 15:28:54', NULL);
INSERT INTO `init_mbti_question_option` VALUES (27, 14, '提前完成', 1, 1, '2026-09-12 15:28:54', '2026-09-12 15:28:54', NULL);
INSERT INTO `init_mbti_question_option` VALUES (28, 14, '临近冲刺', -1, 1, '2026-09-12 15:28:54', '2026-09-12 15:28:54', NULL);
INSERT INTO `init_mbti_question_option` VALUES (29, 15, '尽快定下来', 1, 1, '2026-09-12 15:28:54', '2026-09-12 15:28:54', NULL);
INSERT INTO `init_mbti_question_option` VALUES (30, 15, '保留选择', -1, 1, '2026-09-12 15:28:54', '2026-09-12 15:28:54', NULL);
INSERT INTO `init_mbti_question_option` VALUES (31, 16, '整齐', 1, 1, '2026-09-12 15:28:54', '2026-09-12 15:28:54', NULL);
INSERT INTO `init_mbti_question_option` VALUES (32, 16, '有点乱', -1, 1, '2026-09-12 15:28:54', '2026-09-12 15:28:54', NULL);

-- ----------------------------
-- Table structure for member_account
-- ----------------------------
DROP TABLE IF EXISTS `member_account`;
CREATE TABLE `member_account`  (
  `uuid` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户UUID',
  `account` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '登录账号',
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '登录密码',
  `salt` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '密码盐',
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '电子邮箱',
  `nickname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '昵称',
  `qq_number` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT 'QQ号',
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '头像',
  `status` smallint NOT NULL DEFAULT 1 COMMENT '状态：1正常，0禁用',
  `hope_amount` bigint NOT NULL DEFAULT 0 COMMENT '琥珀余额',
  `mbti_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT 'mbti人格',
  `create_time` datetime NULL DEFAULT NULL,
  `update_time` datetime NULL DEFAULT NULL,
  `delete_time` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`uuid`) USING BTREE,
  UNIQUE INDEX `uuid`(`uuid` DESC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户账户表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of member_account
-- ----------------------------

-- ----------------------------
-- Table structure for member_character
-- ----------------------------
DROP TABLE IF EXISTS `member_character`;
CREATE TABLE `member_character`  (
  `cuid` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '角色卡UID',
  `uuid` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '用户ID',
  `name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '角色显示名称，用户可自定义修改',
  `birth` date NOT NULL COMMENT '角色生日',
  `icon` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '角色头像图标相对路径（小尺寸方形）',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '角色描述',
  `personality` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '角色性格',
  `scenario` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '当前场景',
  `system_prompt` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '开发者提示',
  `tone_tags` json NOT NULL COMMENT '角色标签',
  `soul_card` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '灵魂卡片',
  `rigidity` int NOT NULL DEFAULT 70 COMMENT '刚性系数：0-100',
  `other_prompt` json NOT NULL COMMENT '其他提示词',
  `loader_call` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '主人称呼',
  `current_coordinates` json NOT NULL COMMENT '五维坐标：{\"X\":0,\"Y\":0,\"Z\":0,\"T\":0,\"R\":0}\r\nX:权力距离,-100 ~ 100,臣服关系\r\nY:情感效价,-100 ~ 100,好感度\r\nZ:纽带连接,-100 ~ 100,亲密度\r\nT:信任透明度,-100 ~ 100,信任值\r\nR:共振理解度,-100 ~ 100,是否理解',
  `status` smallint NOT NULL DEFAULT 1 COMMENT '角色状态: 1=正常，0禁用',
  `created_time` datetime NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_time` datetime NULL DEFAULT CURRENT_TIMESTAMP,
  `delete_time` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`cuid`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户角色卡' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of member_character
-- ----------------------------

-- ----------------------------
-- Table structure for member_character_file
-- ----------------------------
DROP TABLE IF EXISTS `member_character_file`;
CREATE TABLE `member_character_file`  (
  `id` int NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `cuid` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '关联角色UUID',
  `file_type` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '文件类型: emoji/live2d/preview/icon/audio',
  `file_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'CDN存储完整地址',
  `file_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '原始文件名',
  `file_size` int NOT NULL COMMENT '文件大小（KB）',
  `file_hash` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'SHA-256哈希值',
  `created_time` datetime NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` datetime NULL DEFAULT NULL,
  `delete_time` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '角色文件表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of member_character_file
-- ----------------------------

-- ----------------------------
-- Table structure for member_device
-- ----------------------------
DROP TABLE IF EXISTS `member_device`;
CREATE TABLE `member_device`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户UUID',
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '设备编号(指纹)',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '设备名称',
  `status` smallint NOT NULL DEFAULT 1 COMMENT '状态：1正常，0禁用',
  `online_status` smallint NOT NULL DEFAULT 1 COMMENT '在线状态：1在线；0离线',
  `create_time` datetime NULL DEFAULT NULL,
  `update_time` datetime NULL DEFAULT NULL,
  `delete_time` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户设备表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of member_device
-- ----------------------------

-- ----------------------------
-- Table structure for member_hope_log
-- ----------------------------
DROP TABLE IF EXISTS `member_hope_log`;
CREATE TABLE `member_hope_log`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户UUID',
  `change` int NOT NULL DEFAULT 0 COMMENT '变更值',
  `before` bigint NOT NULL DEFAULT 0 COMMENT '变更前',
  `after` bigint NOT NULL DEFAULT 0 COMMENT '变更后',
  `desc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '详情',
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '类型',
  `from` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'system' COMMENT '来源：admin管理员，member会员，system系统',
  `create_time` datetime NULL DEFAULT NULL,
  `update_time` datetime NULL DEFAULT NULL,
  `delete_time` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户琥珀变更日志' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of member_hope_log
-- ----------------------------

-- ----------------------------
-- Table structure for member_notice
-- ----------------------------
DROP TABLE IF EXISTS `member_notice`;
CREATE TABLE `member_notice`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户UUID',
  `cuid` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '角色CUID，空代表全部',
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '消息类型，系统定义',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '标题',
  `msg` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '信息',
  `from` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '来源',
  `status` smallint NOT NULL COMMENT '状态：0未读，1已读',
  `create_time` datetime NULL DEFAULT NULL,
  `update_time` datetime NULL DEFAULT NULL,
  `delete_time` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统公告表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of member_notice
-- ----------------------------

-- ----------------------------
-- Table structure for open_character
-- ----------------------------
DROP TABLE IF EXISTS `open_character`;
CREATE TABLE `open_character`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户UUID',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '角色卡名称',
  `version` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '角色卡版本',
  `desc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '角色卡描述',
  `status` smallint NOT NULL DEFAULT 0 COMMENT '状态：-1关闭，0审核中，1正常',
  `card` json NOT NULL COMMENT '角色卡信息',
  `create_time` datetime NULL DEFAULT NULL,
  `update_time` datetime NULL DEFAULT NULL,
  `delete_time` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '开放平台-角色卡' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of open_character
-- ----------------------------

-- ----------------------------
-- Table structure for open_plugin
-- ----------------------------
DROP TABLE IF EXISTS `open_plugin`;
CREATE TABLE `open_plugin`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户UUID',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '插件名称',
  `version` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '插件版本',
  `secret` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '插件秘钥',
  `desc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '插件描述',
  `status` smallint NOT NULL DEFAULT 0 COMMENT '插件状态：-1关闭，0审核中，1正常',
  `files` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '插件下载目录',
  `site` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '可用平台多选：ad安卓，win微软，ios苹果手机，mac苹果电脑，hm鸿蒙',
  `create_time` datetime NULL DEFAULT NULL,
  `update_time` datetime NULL DEFAULT NULL,
  `delete_time` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '开放平台-插件' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of open_plugin
-- ----------------------------

-- ----------------------------
-- Table structure for service_email_log
-- ----------------------------
DROP TABLE IF EXISTS `service_email_log`;
CREATE TABLE `service_email_log`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '收件邮箱',
  `code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '验证码',
  `type` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '发送类型 bind|password|order',
  `subject` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '邮件主题',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '邮件内容',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '状态 -1已失效 0未使用 1已使用',
  `error_count` int NOT NULL DEFAULT 3 COMMENT '剩余验证次数',
  `use_time` datetime NULL DEFAULT NULL COMMENT '使用时间',
  `create_time` datetime NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` datetime NULL DEFAULT NULL COMMENT '更新时间',
  `delete_time` datetime NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `type`(`type` ASC) USING BTREE,
  INDEX `email`(`email` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '邮箱发送记录表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of service_email_log
-- ----------------------------

-- ----------------------------
-- Table structure for service_order
-- ----------------------------
DROP TABLE IF EXISTS `service_order`;
CREATE TABLE `service_order`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '订单编码，长加密字符串，用来核验',
  `order_sn` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '订单编号',
  `uuid` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户UUID',
  `order_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '订单类型',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '标题',
  `desc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '描述',
  `price` decimal(10, 2) NOT NULL COMMENT '价格',
  `order_status` smallint NOT NULL DEFAULT 0 COMMENT '订单状态：-1已失效，0待支付，1已支付',
  `service_status` smallint NOT NULL DEFAULT 0 COMMENT '服务状态：0未使用，1使用中，2已完成',
  `create_time` datetime NULL DEFAULT NULL,
  `update_time` datetime NULL DEFAULT NULL,
  `delete_time` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '平台-订单表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of service_order
-- ----------------------------

-- ----------------------------
-- Table structure for service_sms_log
-- ----------------------------
DROP TABLE IF EXISTS `service_sms_log`;
CREATE TABLE `service_sms_log`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `mobile` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '手机号',
  `code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '验证码',
  `type` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '发送类型 register|login|reset',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '状态 -1已失效 0未使用 1已使用',
  `error_count` int NOT NULL DEFAULT 3 COMMENT '验证次数',
  `create_time` datetime NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` datetime NULL DEFAULT NULL COMMENT '更新时间',
  `delete_time` datetime NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `mobile`(`mobile` ASC) USING BTREE,
  INDEX `type`(`type` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci COMMENT = '短信发送记录表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of service_sms_log
-- ----------------------------

-- ----------------------------
-- Table structure for system_ai_providers
-- ----------------------------
DROP TABLE IF EXISTS `system_ai_providers`;
CREATE TABLE `system_ai_providers`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '标题',
  `desc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '备注',
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '类型：openai,huoshan,deepseek',
  `config` json NOT NULL COMMENT '配置参数',
  `models` json NOT NULL COMMENT '模型列表',
  `default_model` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '默认模型',
  `status` smallint NOT NULL DEFAULT 1 COMMENT '状态：0禁用，1正常',
  `total_token_used` int NOT NULL DEFAULT 0 COMMENT 'token使用量',
  `create_time` datetime NULL DEFAULT NULL,
  `udpate_time` datetime NULL DEFAULT NULL,
  `delete_time` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统-AI提供商' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of system_ai_providers
-- ----------------------------

-- ----------------------------
-- Table structure for system_config
-- ----------------------------
DROP TABLE IF EXISTS `system_config`;
CREATE TABLE `system_config`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '配置键（英文唯一标识）',
  `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '配置标题',
  `group` tinyint NOT NULL DEFAULT 1 COMMENT '配置分组，系统配置项',
  `type` tinyint NOT NULL DEFAULT 1 COMMENT '输入类型：1文本 2数字 3文本域 4开关 5下拉 6图片 7编辑器',
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL COMMENT '配置值',
  `extra` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '附加数据（如下拉选项）',
  `remark` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '' COMMENT '备注',
  `sort` int NOT NULL DEFAULT 100 COMMENT '排序',
  `status` tinyint NOT NULL DEFAULT 1 COMMENT '状态：0禁用 1启用',
  `create_time` datetime NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` datetime NULL DEFAULT NULL COMMENT '更新时间',
  `delete_time` datetime NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统配置表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of system_config
-- ----------------------------

-- ----------------------------
-- Table structure for word_message
-- ----------------------------
DROP TABLE IF EXISTS `word_message`;
CREATE TABLE `word_message`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `channel_id` int NOT NULL DEFAULT 0 COMMENT '频道ID,0世界频道',
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '用户UUID',
  `uname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户名称',
  `ucall` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户称号',
  `msg` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '消息内容',
  `create_time` datetime NULL DEFAULT NULL,
  `update_time` datetime NULL DEFAULT NULL,
  `delete_time` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '世界聊天消息' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of word_message
-- ----------------------------

-- ----------------------------
-- Table structure for world_channel
-- ----------------------------
DROP TABLE IF EXISTS `world_channel`;
CREATE TABLE `world_channel`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '名称',
  `desc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '描述',
  `status` smallint NOT NULL DEFAULT 1 COMMENT '状态：0禁用，1正常',
  `sort` int NOT NULL DEFAULT 50 COMMENT '排序，数字越小越靠前',
  `create_time` datetime NULL DEFAULT NULL,
  `update_time` datetime NULL DEFAULT NULL,
  `delete_time` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '世界频道表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of world_channel
-- ----------------------------

SET FOREIGN_KEY_CHECKS = 1;
