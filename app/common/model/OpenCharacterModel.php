<?php

namespace app\common\model;
class OpenCharacterModel extends BaseModel
{
    protected $table = 'open_character';
    protected $json = ['card'];
    protected $jsonAssoc = true;
}