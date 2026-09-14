<?php

namespace app\common\model;
class MemberCharacterModel extends BaseModel
{
    protected $table = 'member_character';
    protected $pk = 'cuid';
    protected $json = ['current_coordinates', 'other_prompt'];
    protected $jsonAssoc = true;

    public function getIconAttr($value)
    {
        return formatUrl($value);
    }

    public function files()
    {
        return $this->hasMany(MemberCharacterFileModel::class, 'cuid', 'cuid')
            ->field('id,file_type,file_url,file_name,file_size,file_hash,created_time,update_time');
    }
}