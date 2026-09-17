<?php

namespace app\common\model;
class InitMbtiTestRecordModel extends BaseModel
{
    protected $table = 'init_mbit_test_record';
    protected $json = ['scores'];
    protected $jsonAssoc = true;

}