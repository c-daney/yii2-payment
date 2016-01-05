
drop table if exists `pay_channel`;
create table `pay_channel` (
    `id` smallint unsigned primary key not null auto_increment comment '自增id',
    `name` varchar(30) not null default '' comment '支付平台名称',
    `alias` varchar(15) not null comment '支付平台别名',
    `company` varchar(64) not null default '' comment '第三方支付平台公司',
    `url` varchar(64) not null default '' comment '公司网站地址',
    `icon` varchar(256) not null default '' comment '支付logo',
    `gateway` varchar(256) not null default '' comment '支付网关',
    `verify_server` varchar(256) not null default '' comment '验证服务器',
    `partner_id` varchar(64) not null default '' comment '支付网关商户号',
    `partner_secret` varchar(64) not null default '' comment '支付网关密码',
    `enabled` tinyint(1) unsigned not null default 0 comment '账号状态: 1 有效 2 异常封禁 0为非法值',
    `created_at` int(10) not null default 0 comment '记录创建时间',
    `updated_at` int(10) not null default 0 comment '记录更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='充值渠道列表';


drop table if exists `user_bank_card`;
create table `user_bank_card` (
    `id` smallint unsigned primary key not null auto_increment comment '自增id',
    `uid` bigint(20) unsigned not null default 0 comment '用户id',
    `bank_id` smallint unsigned not null default 0 comment '银行id',
    `bank_name` varchar(30) not null default '' comment '银行名称',
    `card_type` tinyint unsigned not null comment '账号类型: 1 借记卡 2信用卡',
    `account_no` varchar(64) not null default '' comment '银行账号',
    `account_name` varchar(64) not null default '' comment '用户真是姓名',
    `province` varchar(16) not null default '' comment '省份名称',
    `city` varchar(16) not null default '' comment '城市名称',
    `verified` tinyint(1) unsigned not null default '0' comment '是否验证过', 
    `created_at` int(10) not null default 0 comment '记录创建时间',
    `updated_at` int(10) not null default 0 comment '记录更新时间',
    key uidx_uid_verified (`uid`, `verified`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户银行卡绑定表';

drop table if exists `bank`;
create table `bank` (
    `id` smallint unsigned primary key not null auto_increment comment '自增id',
    `name` varchar(30) not null default '' comment '银行名称',
    `pay_template` text not null default '' comment '银行批量付款单模板',
    `pay_back_template` text not null default '' comment '批量付款单银行回款单模板',
    `description` varchar(30) not null default '' comment '银行描述', 
    `created_at` int(10) not null default 0 comment '记录创建时间',
    `updated_at` int(10) not null default 0 comment '记录更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='银行列表';

drop table if exists `receivable`;
create table `receivable` (
    `id` bigint unsigned primary key not null auto_increment comment '自增id',
    `type` tinyint unsigned not null comment '付款类型: 1 用户充值 目前仅有充值会存在从第三方收款',
    `trans_id` varchar(32) not null default '' comment '和变动关联的交易单号',
    `uid` bigint(20) unsigned not null default 0 comment '收款用户id,该值为公司账户uid',
    `currency` tinyint unsigned not null default 1 comment '币种: 1 人民币',
    `from_uid` bigint unsigned not null comment '付款用户uid, 该场景下是指用户uid',
    `from_channel_id` smallint unsigned not null comment '付款渠道id, 渠道参加渠道表,对应于pay_channel中的id',
    `from_channel_name` varchar(12)  not null comment '付款渠道名称',
    `user_channel_account` varchar(32) not null comment '用户渠道账号,可为空', 
    `money` decimal(12,2) not null default '0.0' comment '收款金额',
    `description` varchar(32) not null default '' comment '交付给第三方平台的订单描述',
    `status` tinyint unsigned not null comment '收款状态: 1 未完成 2完成支付',
    `memo` varchar(16) not null default '' comment '备忘',
    `created_at` int(10) not null default 0 comment '记录创建时间',
    `updated_at` int(10) not null default 0 comment '记录更新时间',
    key idx_trans (`trans_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='应收账款';

drop table if exists `payable`;
create table `payable` (
    `id` bigint unsigned primary key not null auto_increment comment '自增id,付款给对方的id',
    `trans_id` varchar(32) not null default '' comment '和变动关联的交易单号',
    `pay_method` tinyint unsigned not null comment '支付形式: 1 银行付款  2 原路返回,原路返回的支持不太好',
    `currency` tinyint unsigned not null default 1 comment '币种: 1 人民币',
    `status` tinyint unsigned not null comment '付款状态: 1 新建成功,待下载汇总, 2 已下载，待银行付款  3 付款成功 4 付款失败',
    `pay_uid` bigint unsigned not null comment '付款用户uid,公司账户uid',
    `receive_uid` bigint unsigned not null comment '收款用户uid',
    `pay_user_bank_card_id` bigint unsigned not null default 0 comment '实际付款银行账号id',
    `receive_user_bank_card_id` varchar(32)  not null default '' comment '收款人银行账号id',
    `money` decimal(12,2) not null default '0.0' comment '付款金额',
    `failded_reason` varchar(32) not null default '' comment '付款失败原因',
    `memo` varchar(16) not null default '' comment '备忘',
    `process_batch_no` int unsigned not null default 0 comment '支付批处理批次号',
    `created_at` int(10) not null default 0 comment '记录创建时间',
    `updated_at` int(10) not null default 0 comment '记录更新时间',
    key uidx_ruid_status (`receive_uid`, `status`, `updated_at`),
    key uidx_puid_status (`pay_uid`, `status`, `updated_at`),
    key idx_trans (`trans_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='应付账款';

drop table if exists `pay_log`;
create table `pay_log` (
    `id` bigint unsigned primary key not null auto_increment comment '自增id',
    `pay_id` bigint unsigned not null comment '支付id',
    `trans_id` varchar(32) not null default '' comment '和变动关联的交易单号',
    `status` tinyint unsigned not null comment '付款状态: 1 新建成功,待下载汇总, 2 已下载，待银行付款  3 付款成功 4 付款失败',
    `money` decimal(12,2) not null default '0.0' comment '付款金额',
    `action` tinyint not null comment '变化行为: 1 下载 2 付款成功  3 付款失败',
    `action_name` varchar(8) not null comment '变化行为: 1 下载 2 付款成功  3 付款失败',
    `created_at` int(10) not null default 0 comment '记录创建时间',
    `updated_at` int(10) not null default 0 comment '记录更新时间',
    key uidx_trans (`trans_id`, `created_at`) 
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='应付账款日志';

drop table if exists `payable_process_batch`;
create table `payable_process_batch` (
    `id` bigint unsigned primary key not null auto_increment comment '自增id',
    `count` int unsigned not null default 0 comment '支付记录数',
    `total_money` decimal(16,2) not null default 0.00 comment '支付总金额',
    `filename` varchar(32) not null default '' comment '下载的文件名称',
    `download_time` int(10) not null default 0 comment '批次记录下载时间',
    `pay_time` int(10) not null default 0 comment '支付完成时间',
    `admin_uid` bigint(20) unsigned not null default 0 comment '处理管理员uid',
    `admin_username` varchar(20) not null default '' comment '处理管理员名称',
    `created_at` int(10) not null default 0 comment '记录创建时间',
    `updated_at` int(10) not null default 0 comment '记录更新时间',
    key uidx_uidc (`admin_uid`, `created_at`), 
    key uidx_uidu (`admin_uid`, `updated_at`) 
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='支付处理批次';

