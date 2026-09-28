<?php

namespace app\common\model;
class MemberCharacterModel extends BaseModel
{
    protected $table = 'member_character';
    protected $pk = 'cuid';
    protected $json = ['current_coordinates', 'other_prompt', 'tone_tags'];
    protected $jsonAssoc = true;

    public const status = [
        1 => '正常',
        0 => '禁用',
    ];

    /** 五维坐标说明 */
    public const coordinates_desc = [
        'X' => '权力距离（臣服关系）',
        'Y' => '情感效价（好感度）',
        'Z' => '纽带连接（亲密度）',
        'T' => '信任透明度（信任值）',
        'R' => '共振理解度（是否理解）',
    ];

    /** 文件类型 */
    public const file_type = [
        'emoji'   => '表情',
        'live2d'  => 'Live2D',
        'preview' => '预览图',
        'icon'    => '头像',
        'audio'   => '音频',
    ];

    public function getIconAttr($value)
    {
        return formatUrl($value);
    }

    public function files()
    {
        return $this->hasMany(MemberCharacterFileModel::class, 'cuid', 'cuid')
            ->field('id,file_type,file_url,file_name,file_size,file_hash,create_time,update_time');
    }
}