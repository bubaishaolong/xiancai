<?php
// +----------------------------------------------------------------------
// | PHP框架 [ ThinkPHP ]
// +----------------------------------------------------------------------
// | 版权所有 为开源做努力
// +----------------------------------------------------------------------
// | 时间: 2018-07-06 09:42:56
// +----------------------------------------------------------------------
// | 开源协议 ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------

/**
 * 模块信息
 */
return [
  'name' => 'enterprise',
  'title' => '企业站点',
  'identifier' => 'enterprise.caijiong.module',
  'icon' => 'fa fa-fw fa-behance',
  'description' => '',
  'author' => '楚留香',
  'author_url' => '楚留香',
  'version' => '1.0.0.1',
  'need_module' => [
    [
      'admin',
      'admin.dolphinphp.module',
      '1.0.0',
    ],
  ],
  'tables' => [
    'cj_enterprise_setting',
    'cj_enterprise_adv',
  ],
];
