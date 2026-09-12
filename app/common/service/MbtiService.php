<?php

namespace app\common\service;

use app\common\model\InitMbtiQuestionModel;
use app\common\model\InitMbtiQuestionOptionModel;

/**
 * MBTI 计算器
 * 输入：用户作答 [['question'=>1,'option'=>2], ...]
 * 输出：人格类型 + 各维度得分
 */
class MbtiService
{
    /**
     * 四个维度的正负极定义
     * 正极：E/S/T/J  （score_value 为 +1）
     * 负极：I/N/F/P  （score_value 为 -1）
     */
    private const DIMENSIONS = [
        'EI' => ['positive' => 'E', 'negative' => 'I'],
        'SN' => ['positive' => 'S', 'negative' => 'N'],
        'TF' => ['positive' => 'T', 'negative' => 'F'],
        'JP' => ['positive' => 'J', 'negative' => 'P'],
    ];

    /**
     * 计算人格类型
     *
     * @param array $answers 作答列表，每项含 question 和 option
     * @return array ['type' => 'INTJ', 'scores' => [...]]
     */
    public static function calculate(array $answers): array
    {
        // 0. 空作答兜底
        if (empty($answers)) {
            return [
                'type'   => '',
                'scores' => ['EI' => 0, 'SN' => 0, 'TF' => 0, 'JP' => 0],
            ];
        }

        // 1. 收集 ID（注意字段名是 question / option）
        $questionIds = array_column($answers, 'question');
        $optionIds   = array_column($answers, 'option');

        // 2. 批量查询
        $questions = self::fetchQuestions($questionIds);
        $options   = self::fetchOptions($optionIds);

        // 3. 初始化得分
        $scores = ['EI' => 0, 'SN' => 0, 'TF' => 0, 'JP' => 0];

        // 4. 逐题累加
        foreach ($answers as $ans) {
            $qid = (int) ($ans['question'] ?? 0);
            $oid = (int) ($ans['option'] ?? 0);

            if (!isset($questions[$qid]) || !isset($options[$oid])) {
                continue; // 无效作答跳过
            }

            $question = $questions[$qid];
            $option   = $options[$oid];

            // 校验选项是否属于该题
            if ((int) $option['question_id'] !== $qid) {
                continue;
            }

            $dimension = $question['dimension'];       // EI/SN/TF/JP
            $value     = (int) $option['score_value']; // +1 或 -1

            if (isset($scores[$dimension])) {
                $scores[$dimension] += $value;
            }
        }

        // 5. 判定人格
        $type = self::buildType($scores);

        return [
            'type'   => $type,
            'scores' => $scores,
        ];
    }

    /**
     * 根据各维度得分构建人格类型
     */
    private static function buildType(array $scores): string
    {
        $type = '';
        foreach (self::DIMENSIONS as $dim => $map) {
            // 得分 > 0 取正极，<= 0 取负极
            $type .= $scores[$dim] > 0 ? $map['positive'] : $map['negative'];
        }
        return $type;
    }

    /**
     * 批量查询题目
     * 注意：计算时不过滤 status，避免历史作答因题目下架而失效
     */
    private static function fetchQuestions(array $ids): array
    {
        $ids = array_values(array_unique(array_map('intval', $ids)));
        if (empty($ids)) {
            return [];
        }

        $list = InitMbtiQuestionModel::whereIn('id', $ids)
            ->field('id,content,dimension')
            ->select();

        $result = [];
        foreach ($list as $row) {
            $result[(int) $row['id']] = [
                'id'        => (int) $row['id'],
                'content'   => $row['content'],
                'dimension' => $row['dimension'],
            ];
        }
        return $result;
    }

    /**
     * 批量查询选项
     * 注意：计算时不过滤 status
     */
    private static function fetchOptions(array $ids): array
    {
        $ids = array_values(array_unique(array_map('intval', $ids)));
        if (empty($ids)) {
            return [];
        }

        $list = InitMbtiQuestionOptionModel::whereIn('id', $ids)
            ->field('id,question_id,content,score_value')
            ->select();

        $result = [];
        foreach ($list as $row) {
            $result[(int) $row['id']] = [
                'id'          => (int) $row['id'],
                'question_id' => (int) $row['question_id'],
                'content'     => $row['content'],
                'score_value' => (int) $row['score_value'],
            ];
        }
        return $result;
    }
}