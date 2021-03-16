DROP TABLE IF EXISTS `FileList`;
CREATE TABLE `FileList`  (
  `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '对应的序号索引',
  `UpdateTime` datetime NOT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '最后一次更新的时间',
  `FileName` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '文件的名字（带有后缀）',
  `Status` int(4) NOT NULL DEFAULT 0 COMMENT '当前文件的状态（0: 未处理; 1: 已处理）',
  PRIMARY KEY (`ID`) USING BTREE,
  UNIQUE INDEX `FileName`(`FileName`) USING BTREE COMMENT '文件名是唯一的'
) ENGINE = InnoDB AUTO_INCREMENT = 10 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;
SET FOREIGN_KEY_CHECKS = 1;
