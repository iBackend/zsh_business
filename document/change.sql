update `zsh`.zsh_Menu set title='商品类型' where `tip` = 'business' and title='商品类目' ;
update `zsh`.zsh_Menu set title='账目核对' where `tip` = 'business' and title='账目核销' ;
update `zsh`.zsh_Menu set title='诚信保证金充值' where `tip` = 'business' and title='添加保证金' ;

insert into `zsh`.zsh_Menu (title, pid, sort, url, hide, tip) values ('商品详情', '182', 0, '/index.php?m=Goodmanage&a=detailGood', 1, 'business' );
insert into `zsh`.zsh_Menu (title, pid, sort, url, hide, tip) values ('账号信息', '181', 0, '/index.php?m=Sysconf&a=accountInfo', 1, 'business' );

insert into `zsh`.zsh_Menu (title, pid, sort, url, hide, tip) values ('活动&粉丝', '0', 0, '/index.php?m=Promotion&a=redpaper', 1, 'business' );
insert into `zsh`.zsh_Menu (title, pid, sort, url, hide, tip) values ('所有红包', '211', 0, '/index.php?m=Promotion&a=redpaper', 1, 'business' );
insert into `zsh`.zsh_Menu (title, pid, sort, url, hide, tip) values ('用户统计', '211', 0, '/index.php?m=Promotion&a=userstatstics', 1, 'business' );
insert into `zsh`.zsh_Menu (title, pid, sort, url, hide, tip) values ('创建红包', '211', 0, '/index.php?m=Promotion&a=createpaper', 1, 'business' );
insert into `zsh`.zsh_Menu (title, pid, sort, url, hide, tip) values ('粉丝列表', '211', 0, '/index.php?m=Promotion&a=fensilist', 1, 'business' );

update `zsh`.zsh_message set pid=14;

ALTER TABLE `zsh`.`zsh_admin` ADD COLUMN `name` VARCHAR(45) DEFAULT '' AFTER `login_ip`,
 ADD COLUMN `img` VARCHAR(128) DEFAULT '' AFTER `name`,
 ADD COLUMN `phone` VARCHAR(14) DEFAULT '' AFTER `img`,
 ADD COLUMN `qq` VARCHAR(14) DEFAULT '' AFTER `phone`,
 ADD COLUMN `email` VARCHAR(128) DEFAULT '' AFTER `qq`,
 ADD COLUMN `wx` VARCHAR(14) DEFAULT '' AFTER `email`;

alter table zsh_redpaper add column name varchar(128) not null after id;
alter table zsh_redpaper add column genus smallint(1) not null default 1 after name;
ALTER TABLE `zsh`.`zsh_redpaper` MODIFY COLUMN `point` DECIMAL(20,2) NOT NULL DEFAULT 0 COMMENT '点数';
ALTER TABLE `zsh`.`zsh_redpaper` MODIFY COLUMN `begin_time` INT(10) UNSIGNED NOT NULL COMMENT '开始时间',
 ADD COLUMN `create_time` INT(10) UNSIGNED NOT NULL AFTER `remark`;
alter table zsh_redpaper add column img varchar(225)  after point;

DROP TABLE IF EXISTS `zsh`.`zsh_supplier_location_auth`;
CREATE TABLE  `zsh`.`zsh_supplier_location_auth` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `location_id` int(10) unsigned NOT NULL,
  `supplier_id` int(10) unsigned NOT NULL,
  `id_card` varchar(20) default NULL,
  `card_img_head` varchar(255) default NULL COMMENT '身份证正反面',
  `card_img_back` varchar(255) default NULL COMMENT '身份证正反面',
  `business_license` varchar(128) default NULL COMMENT '营业执照',
  `tax_certificate` varchar(128) default NULL COMMENT '税务登记证',
  `organizing_code` varchar(128) default NULL COMMENT '组织机构代码证',
  `apply_route` smallint(1) unsigned default '0' COMMENT '申请入驻任务到家状态',
  `verify_status` smallint(1) unsigned default '0' COMMENT '审核状态',
  `create_time` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `zsh`.`zsh_deal` MODIFY COLUMN `origin_price` DECIMAL(20,2) NOT NULL,
 MODIFY COLUMN `current_price` DECIMAL(20,2) NOT NULL;
ALTER TABLE `zsh`.`zsh_deal` MODIFY COLUMN `return_money` DECIMAL(20,2) NOT NULL,
 MODIFY COLUMN `weight` DECIMAL(20,2) NOT NULL,
 MODIFY COLUMN `discount` DECIMAL(20,2) NOT NULL,
 MODIFY COLUMN `balance_price` DECIMAL(20,2) NOT NULL;


 
 