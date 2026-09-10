<?php

namespace app\common\model;
class MemberCharacterModel extends BaseModel
{
    protected $table = 'member_character';
    protected $pk = 'cuid';
    protected $json = ['current_coordinates', 'other_prompt'];
    protected $jsonAssoc = true;

}