<?php

namespace app\models;

use Yii;
use yii\db\Query;
use yii\db\Expression;
use yii\helpers\FileHelper;
use yii\caching\TagDependency;
                ini_set("display_errors","On");
		    error_reporting(E_ALL);
/**
 * This is the model class for table "{{%contest}}".
 *
 * @property int $id
 * @property string $title
 * @property string $start_time
 * @property string $end_time
 * @property string $lock_board_time
 * @property string $status
 * @property string $description
 * @property string $editorial
 * @property int $group_id
 * @property int $type
 * @property int $scenario
 * @property int $created_by
 */
class Contest extends \yii\db\ActiveRecord
{
    /**
     * 单人赛榜单中一道题基础分数
     */
    const BASIC_SCORE = 500;

    /**
     * 第一次参加排位赛的初始分数
     */
    const RATING_INIT_SCORE = 1500;

    /**
     * 比赛的状态信息
     */
    const STATUS_NOT_START = 0;
    const STATUS_RUNNING = 1;
    const STATUS_ENDED = 2;

    /**
     * 比赛的类型
     */
    const TYPE_EDUCATIONAL = 0;
    const TYPE_RANK_SINGLE = 1;
    const TYPE_RANK_GROUP  = 2;
    const TYPE_HOMEWORK    = 3;
    const TYPE_OI          = 4;
    const TYPE_IOI         = 5;

    /**
     * 是否可见
     */
    const STATUS_HIDDEN = 0; // 隐藏
    const STATUS_VISIBLE = 1; // 公开
    const STATUS_PRIVATE = 2; // 私有

    /**
     * 线上线下场景
     */
    const SCENARIO_ONLINE = 0;
    const SCENARIO_OFFLINE = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%contest}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'start_time', 'end_time'], 'required'],
            [['start_time', 'end_time', 'lock_board_time'], 'safe'],
            [['description', 'editorial'], 'string'],
            [['id', 'status', 'type', 'scenario', 'created_by', 'group_id'], 'integer'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'Contest ID'),
            'title' => Yii::t('app', 'Title'),
            'start_time' => Yii::t('app', 'Start Time'),
            'end_time' => Yii::t('app', 'End Time'),
            'lock_board_time' => Yii::t('app', 'Lock Board Time'),
            'editorial' => Yii::t('app', 'Editorial'),
            'description' => Yii::t('app', 'Description'),
            'status' => Yii::t('app', 'Status'),
            'type' => Yii::t('app', 'Type'),
            'scenario' => Yii::t('app', 'Scenario')
        ];
    }

    /**
     * This is invoked before the record is saved.
     * @return boolean whether the record should be saved.
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->created_by = Yii::$app->user->id;
            }
            return true;
        } else {
            return false;
        }
    }

    public function beforeDelete()
    {
        ContestUser::deleteAll(['contest_id' => $this->id]);
        ContestProblem::deleteAll(['contest_id' => $this->id]);
        Solution::deleteAll(['contest_id' => $this->id]);
        Discuss::deleteAll(['entity' => Discuss::ENTITY_CONTEST, 'entity_id' => $this->id]);
        ContestPrint::deleteAll(['contest_id' => $this->id]);
        ContestAnnouncement::deleteAll(['contest_id' => $this->id]);
        return parent::beforeDelete();
    }

    public function getSolutions()
    {
        return $this->hasMany(Solution::className(), ['problem_id' => 'problem_id'])
            ->viaTable(ContestProblem::tableName(), ['contest_id' => 'id']);
    }

    public function getType()
    {
        switch ($this->type) {
            case Contest::TYPE_EDUCATIONAL:
                $res = Yii::t('app', 'Educational');
                break;
            case Contest::TYPE_RANK_SINGLE:
                $res = Yii::t('app', 'Single Ranked');
                break;
            case Contest::TYPE_RANK_GROUP:
                $res = Yii::t('app', 'ICPC');
                break;
            case Contest::TYPE_HOMEWORK:
                $res = Yii::t('app', 'Homework');
                break;
            case Contest::TYPE_OI:
                $res = Yii::t('app', 'OI');
                break;
            case Contest::TYPE_IOI:
                $res = Yii::t('app', 'IOI');
                break;
            default:
                $res = "null";
        }
        return $res;
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    public function getGroup()
    {
        return $this->hasOne(Group::className(), ['id' => 'group_id']);
    }

    /**
     * 返回比赛的状态，还没开始，正在进行，已经结束
     * @param $description boolean 是否显示文字描述
     * @return mixed
     */
    public function getRunStatus($description = false)
    {
        $start_time = strtotime($this->start_time);
        $end_time = strtotime($this->end_time);
        $current_time = time();
        if ($description) {
            if ($start_time > $current_time) {
                return Yii::t('app', 'Not started yet');
            } else if ($start_time <= $current_time && $current_time <= $end_time) {
                return Yii::t('app', 'Running');
            } else {
                return Yii::t('app', 'Ended');
            }
        } else {
            if ($start_time > $current_time) {
                return Contest::STATUS_NOT_START;
            } else if ($start_time <= $current_time && $current_time <= $end_time) {
                return Contest::STATUS_RUNNING;
            } else {
                return Contest::STATUS_ENDED;
            }
        }
    }

    /**
     * 比赛是否结束
     * @return bool
     */
    public function isContestEnd()
    {
        return time() > strtotime($this->end_time);
    }

    public static function getContestList()
    {
        $res = (new Query())->select('id, title')
            ->from('{{%contest}}')
            ->orderBy('id DESC')
            ->all();
        $list = ['' => 'None'];
        foreach ($res as $key => $value) {
            $list[$value['id']] = $value['id'] . ' [' . $value['title'] . ']';
        }
        return $list;
    }

    public function getAnnouncements()
    {
        return $this->hasMany(ContestAnnouncement::className(), ['contest_id' => 'id']);
    }

    /**
     * 获取比赛问题
     */
    public function getProblems()
    {
        $dependency = new \yii\caching\DbDependency([
            'sql'=>'SELECT COUNT(*) FROM {{%contest_problem}} WHERE contest_id=:cid',
            'params' => [':cid' => $this->id]
        ]);
        return Yii::$app->db->cache(function ($db) {
            return $db->createCommand('
                SELECT `p`.`title`, `p`.`id` AS `problem_id`, `c`.`num`
                FROM `problem` `p`
                LEFT JOIN `contest_problem` `c` ON `c`.`contest_id`=:cid
                WHERE p.id=c.problem_id
                ORDER BY `c`.`num`
            ', [':cid' => $this->id])->queryAll();
        }, 60, $dependency);
    }
    
    /*
    	get problem points
    */
    public function getPoints($pid,$cid)
    {
    	$command = Yii::$app->db->createCommand('SELECT points FROM contest_problem WHERE problem_id=:pid and contest_id=:cid');
    	$command->bindValue(':pid', $pid);
    	$command->bindValue(':cid', $cid);
    	return $command->queryScalar();
    }
    
    /*
    	get problem points dcrease per second
    */
    
    public function getPointsDecrease($pid,$cid)
    {
    	$command = Yii::$app->db->createCommand('SELECT decrease FROM contest_problem WHERE problem_id=:pid and contest_id=:cid');
    	$command->bindValue(':pid', $pid);
    	$command->bindValue(':cid', $cid);
    	return $command->queryScalar();
    }
    /**
     * 获取用户提交
     * @param boolean $betweenContest
     * @return array
     * @throws \yii\db\Exception
     */
    public function getUsersSolution()
    {
        return Yii::$app->db->createCommand('
            SELECT u.id as user_id, username, nickname, result, s.problem_id, s.created_at, s.id, s.score
            FROM `solution` `s`
            LEFT JOIN `user` `u` ON u.id=s.created_by
            WHERE `contest_id`=:id ORDER BY `s`.`id`
        ', [':id' => $this->id])->queryAll();
    }

    /**
     * 将比赛期间成功解答的提交保存到文件。
     * 用于下载进行查重。成功返回用于下载的路径。
     * 
     * @return bool|string
     */
    public function saveContestSolutionToFile()
    {
        $solutions = Yii::$app->db->createCommand('
            SELECT u.id as user_id, username, result, s.problem_id, s.created_at, s.id, s.score, s.language, s.source
            FROM `solution` `s`
            LEFT JOIN `user` `u` ON u.id=s.created_by
            WHERE `contest_id`=:id AND `result`=:result AND s.created_at <= :end
        ', [':id' => $this->id, ':result' => Solution::OJ_AC, ':end' => $this->end_time])->queryAll();

        $problems = $this->getProblems();
        foreach ($problems as $p) {
            $problems[$p['problem_id']] = $p;
        }
        $workDir = Yii::$app->getRuntimePath() . '/contest/' . $this->id . '/';
        foreach ($solutions as $solution) {
            // 问题号
            $problemIndex = chr(65 + $problems[$solution['problem_id']]['num']);
            $path = $workDir . $problemIndex . '/';
            if (!is_dir($path)) {
                FileHelper::createDirectory($path);
            }
            // 问题号_运行ID[id]_用户名[username].语言
            $fileName = $problemIndex
                . '_RunID[' . $solution['id'] . ']'
                . '_Username[' . $solution['username'] . ']'
                . '.' . Solution::getLangFileExtension($solution['language']);
            
            $fp = fopen($path . $fileName, 'w');
            fputs($fp, $solution['source']);
            fclose($fp);
        }

        // 压缩。调用系统命令来压缩。
        $zipName = Yii::$app->getRuntimePath() . '/oj_contest_' . $this->id . '.zip';
        exec("zip -jqr $zipName $workDir");
        FileHelper::removeDirectory($workDir);
        if (!file_exists($zipName)) {
            return false;
        }
        return $zipName;
    }

    /**
     * 获取比赛问题数目
     * @return int
     */
    public function getProblemCount()
    {
        return Yii::$app->db->createCommand('SELECT COUNT(*) FROM {{%contest_problem}} WHERE contest_id=' . $this->id)->queryScalar();
    }

    /**
     * 获取比赛用户
     * @return array
     */
    public function getContestUser()
    {
        $dependency = new \yii\caching\DbDependency([
            'sql'=>'SELECT COUNT(*) FROM {{%contest_user}} WHERE contest_id=:cid',
            'params' => [':cid' => $this->id]
        ]);
        return Yii::$app->db->cache(function ($db) {
            return $db->createCommand('
                SELECT `u`.`username`, `u`.`nickname`, `p`.`student_number`, `u`.`id` as `user_id`, `u`.`role`, `u`.`rating`
                FROM `user` `u`
                LEFT JOIN `contest_user` `c` ON `c`.`contest_id`=:cid
                LEFT JOIN `user_profile` `p` ON `p`.`user_id`=`c`.`user_id`
                WHERE u.id=c.user_id ORDER BY `c`.`id`
            ', [':cid' => $this->id])->queryAll();
        }, 3600, $dependency);
    }

    public function getContestUserCount()
    {
        return Yii::$app->db->createCommand('
            SELECT COUNT(*) FROM {{%contest_user}} WHERE contest_id=:cid
        ', [':cid' => $this->id])->queryScalar();
    }

    /**
     * 获取每道题提交过题情况
     */
    public function getSubmissionStatistics()
    {
        $userSolutions = $this->getUsersSolution();
        $problems = $this->getProblems();
        $isScoreboardFrozen = $this->isScoreboardFrozen();
        $contestEndTime = strtotime($this->end_time);
        if ($isScoreboardFrozen) {
            $lockBoardTime = strtotime($this->lock_board_time);
        }
        $res = [];
        foreach ($problems as $problem) {
            $res[$problem['problem_id']]['solved'] = 0;
            $res[$problem['problem_id']]['submit'] = 0;
        }
        foreach ($userSolutions as $solution) {
            $createdAt = strtotime($solution['created_at']);
            $pid = $solution['problem_id'];
            // 初始化数据信息
            if (!isset($res[$pid]['solved'])) {
                $res[$pid]['solved'] = 0;
            }
            if (!isset($res[$pid]['submit'])) {
                $res[$pid]['submit'] = 0;
            }
            $res[$pid]['submit']++;
            // 不记录封榜后提交情况
            if ($isScoreboardFrozen && $createdAt > $lockBoardTime &&
                $createdAt < $contestEndTime) {
                continue;
            }
            if ($solution['result'] == Solution::OJ_AC) {
                $res[$pid]['solved']++;
            }
        }
        return $res;
    }

    /**
     * 获取比赛排名数据
     * @param bool $lock 是否获取封榜的数据
     * @param null $endtime Unix 时间戳格式，在此时间之前的榜单
     * @return array
     * @throws \yii\db\Exception
     */
    public function getRankData($lock = true, $endtime = null)
    {
        if ($this->type == Contest::TYPE_OI || $this->type == Contest::TYPE_IOI) {
            return $this->getOIRankData($lock, $endtime);
        }
        return $this->getICPCRankData($lock, $endtime);
    }

    /**
     * 获取ICPC比赛排名数据
     * @param bool $lock 是否获取封榜的数据
     * @param null $endtime 在此时间之前的榜单
     * @return array
     * @throws \yii\db\Exception
     */
    public function getICPCRankData($lock = true, $endtime = null)
    {
        $users_solution_data = $this->getUsersSolution();
        $users = $this->getContestUser();
        $problems = $this->getProblems();
        $result = [];
        $first_blood = [];
        $submit_count = [];
        $problem_ids = [];
        $count = count($users_solution_data);
        $start_time = strtotime($this->start_time);
        $lock_time = 0x7fffffff;
        $contest_end_time = strtotime($this->end_time);
        if ($endtime == null) {
            $endtime = $contest_end_time;
        }

        foreach ($problems as $problem) {
            $problem_ids[$problem['problem_id']] = 1;
        }
        foreach ($users as $user) {
            $result[$user['user_id']]['username'] = $user['username'];
            $result[$user['user_id']]['role'] = $user['role'];
            $result[$user['user_id']]['rating'] = $user['rating'];
            $result[$user['user_id']]['time'] = 0;
            $result[$user['user_id']]['solved'] = 0;
            $result[$user['user_id']]['submit'] = 0;
            $result[$user['user_id']]['nickname'] = $user['nickname'];
            $result[$user['user_id']]['student_number'] = $user['student_number'];
            $result[$user['user_id']]['user_id'] = $user['user_id'];
        }

        if (!empty($this->lock_board_time)) {
            $lock_time = strtotime($this->lock_board_time);
        }

        for ($i = 0; $i < $count; $i++) {
            $row = $users_solution_data[$i];
            $user = $row['user_id'];
            $pid = $row['problem_id'];
            $created_at = strtotime($row['created_at']);
            if ($created_at > $endtime) {
                break;
            }

            if (!isset($problem_ids[$pid])) {
                continue;
            }

            // 初始化数据信息
            if (!isset($submit_count[$pid]['solved']))
                $submit_count[$pid]['solved'] = 0;
            if (!isset($submit_count[$pid]['submit']))
                $submit_count[$pid]['submit'] = 0;

            // AC 时间
            if (!isset($result[$user]['ac_time'][$pid]))
                $result[$user]['ac_time'][$pid] = -1;
            // 没 AC 的次数（不含 CE 编译出错 次数）
            if (!isset($result[$user]['wa_count'][$pid]))
                $result[$user]['wa_count'][$pid] = 0;
            // CE（编译出错） 次数
            if (!isset($result[$user]['ce_count'][$pid]))
                $result[$user]['ce_count'][$pid] = 0;
            // 正在测评
            if (!isset($result[$user]['pending'][$pid]))
                $result[$user]['pending'][$pid] = 0;
            // 最快解题
            if (!isset($first_blood[$pid]))
                $first_blood[$pid] = '';

            // 已经 Accepted
            if ($result[$user]['ac_time'][$pid] >= 0) {
                continue;
            }

            $submit_count[$pid]['submit']++;

            // 封榜，比赛结束后的一定时间解榜，解榜时间 scoreboardFrozenTime 变量的设置详见后台设置页面
            if ($lock && $lock_time <= $created_at &&
                time() <= $contest_end_time + Yii::$app->setting->get('scoreboardFrozenTime')) {
                ++$result[$user]['pending'][$pid];
                continue;
            }

            if ($row['result'] == Solution::OJ_AC) {
                // AC
                $submit_count[$pid]['solved']++;
                $result[$user]['pending'][$pid] = 0;

                if (empty($first_blood[$pid])) {
                    if ($this->type == self::TYPE_RANK_SINGLE) {
                        //$result[$user]['time'] += 0.1 * self::BASIC_SCORE;
                    }
                    $first_blood[$pid] = $user;
                }
                $sec = $created_at - $start_time;
                ++$result[$user]['solved'];
                // 单人赛计分，详见 view/wiki/contest.php。
                if ($this->type == self::TYPE_RANK_SINGLE) {
                    $points = $this->getPoints($pid,$this->id);
                    $decr = $this->getPointsDecrease($pid,$this->id);
                    //$points = (int)$points;
                    $score = max(0.3*$points, $points-$decr * $sec / 60 - $result[$user]['wa_count'][$pid] * 50);
                    //$score=500;
                    $result[$user]['ac_time'][$pid] = intval($score);
                    $result[$user]['time'] += intval($score);
                    
                } else {
                    // 记录解答时间
                    if ($created_at < $contest_end_time) {
                        $result[$user]['ac_time'][$pid] = $sec / 60;
                    } else {
                        $result[$user]['ac_time'][$pid] = 0;
                    }
                    $result[$user]['time'] += $sec + $result[$user]['wa_count'][$pid] * 60 * 20;
                }
            } else if ($row['result'] <= 3) {
                // 还未测评
                ++$result[$user]['pending'][$pid];
            } else if ($row['result'] == Solution::OJ_CE) {
                // 编译出错
                ++$result[$user]['ce_count'][$pid];
            } else {
                // 其它情况
                ++$result[$user]['wa_count'][$pid];
            }
            ++$result[$user]['submit'];
        }

        usort($result, function($a, $b) {
            if ($a['solved'] != $b['solved'] &&$this->type != self::TYPE_RANK_SINGLE) { //优先解题数
                return $a['solved'] < $b['solved'];
            } else if ($a['time'] != $b['time']) { //按时间（分数）
                if ($this->type == self::TYPE_RANK_SINGLE) {
                    return $a['time'] < $b['time'];
                } else {
                    return $a['time'] > $b['time'];
                }
            } else {
                return $a['submit'] < $b['submit'];
            }
        });

        return [
            'rank_result' => $result,
            'submit_count' => $submit_count,
            'first_blood' => $first_blood
        ];
    }

    /**
     * 获取 OI 比赛排名数据
     * @param bool $lock 是否获取封榜的数据
     * @param null $endtime 在此时间之前的榜单
     * @return array
     * @throws \yii\db\Exception
     */
    public function getOIRankData($lock = true, $endtime = null)
    {
        $users_solution_data = $this->getUsersSolution();
        $users = $this->getContestUser();
        $problems = $this->getProblems();
        $result = [];
        $first_blood = [];
        $submit_count = [];
        $count = count($users_solution_data);
        $start_time = strtotime($this->start_time);
        $lock_time = 0x7fffffff;
        $contest_end_time = strtotime($this->end_time);
        if ($endtime == null) {
            $endtime = $contest_end_time;
        }

        foreach ($users as $user) {
            $result[$user['user_id']]['username'] = $user['username'];
            $result[$user['user_id']]['user_id'] = $user['user_id'];
            $result[$user['user_id']]['nickname'] = $user['nickname'];
            $result[$user['user_id']]['role'] = $user['role'];
            $result[$user['user_id']]['rating'] = $user['rating'];
            $result[$user['user_id']]['solved'] = 0;
            $result[$user['user_id']]['total_score'] = 0; // 测评总分
            $result[$user['user_id']]['score'] = []; // 记录每道题最后一次得分（OI专属）
            $result[$user['user_id']]['max_score'] = []; // 记录每道题最大得分
            $result[$user['user_id']]['correction_score'] = 0; //订正总分
            $result[$user['user_id']]['student_number'] = $user['student_number'];
            $result[$user['user_id']]['total_time'] = 0; // 记录 AC 的题目的总时间
        }

        foreach ($problems as $problem) {
            $problem_ids[$problem['problem_id']] = 1;
        }

        if (!empty($this->lock_board_time)) {
            $lock_time = strtotime($this->lock_board_time);
        }

        for ($i = 0; $i < $count; $i++) {
            $row = $users_solution_data[$i];
            $user = $row['user_id'];
            $pid = $row['problem_id'];
            $created_at = strtotime($row['created_at']);
            $score = $row['score'];
            if ($created_at > $endtime) {
                break;
            }
            if (!isset($problem_ids[$pid])) {
                continue;
            }

            // 初始化数据信息
            if (!isset($submit_count[$pid]['solved']))
                $submit_count[$pid]['solved'] = 0;
            if (!isset($submit_count[$pid]['submit']))
                $submit_count[$pid]['submit'] = 0;
            if (!isset($result[$user]['score'][$pid]))
                $result[$user]['score'][$pid] = 0;
            if (!isset($result[$user]['max_score'][$pid]))
                $result[$user]['max_score'][$pid] = 0;

            // 针对 OI 榜单，需要记录最后一次提交的分数
            if ($created_at <= $contest_end_time) {
                $result[$user]['score'][$pid] = $score;
            }
            // 已经 AC
            if (isset($result[$user]['solved_flag'][$pid])) {
                continue;
            }
            // 记录提交时间。仅记录比赛期间的提交时间。
            if (!isset($result[$user]['submit_time'][$pid]) && $created_at < $contest_end_time) {
                $result[$user]['submit_time'][$pid] = ($created_at - $start_time) / 60;
            }
            // 记录最大分数
            if ($result[$user]['max_score'][$pid] < $score) {
                $result[$user]['max_score'][$pid] = $score;
                if ($created_at < $contest_end_time) {
                    $result[$user]['submit_time'][$pid] = ($created_at - $start_time) / 60;
                }
            }

            // 正在测评
            if (!isset($result[$user]['pending'][$pid]))
                $result[$user]['pending'][$pid] = 0;
            // 最快解题
            if (!isset($first_blood[$pid]))
                $first_blood[$pid] = '';

            // 封榜，比赛结束后的一定时间解榜，解榜时间 scoreboardFrozenTime 变量的设置详见后台设置页面
            if ($lock && $lock_time <= $created_at &&
                time() <= $contest_end_time + Yii::$app->setting->get('scoreboardFrozenTime')) {
                ++$result[$user]['pending'][$pid];
                continue;
            }
            $submit_count[$pid]['submit']++;
            if ($row['result'] == Solution::OJ_AC) {
                // AC
                $submit_count[$pid]['solved']++;
                $result[$user]['pending'][$pid] = 0;
                $result[$user]['solved_flag'][$pid] = 1; // 标记该题已解答
                $result[$user]['solved']++; // 解题数目
                if ($created_at < $contest_end_time) {
                    $result[$user]['total_time'] += ($created_at - $start_time) / 60;
                }
                if (empty($first_blood[$pid])) {
                    $first_blood[$pid] = $user;
                }
            } else if ($row['result'] <= 3) {
                // 还未测评
                ++$result[$user]['pending'][$pid];
            }
        }

        foreach ($result as &$v) {
            foreach ($v['score'] as $s) {
                $v['total_score'] += $s;
            }
            foreach ($v['max_score'] as $s) {
                $v['correction_score'] += $s;
            }
        }

        $type = $this->type;
        usort($result, function($a, $b) use ($type) {
            if ($type == self::TYPE_OI) {
                if ($a['total_score'] != $b['total_score']) { // 优先测评总分
                    return $a['total_score'] < $b['total_score'];
                } else if ($a['correction_score'] != $b['correction_score']) { //订正总分
                    return $a['correction_score'] < $b['correction_score'];
                } else {
                    return $a['total_time'] > $b['total_time'];
                }
            } else { // IOI 只需要最大值的总分排序。
                if ($a['solved'] != $b['solved']) { //优先解题数
                    return $a['solved'] < $b['solved'];
                } else if ($a['correction_score'] != $b['correction_score']) {
                    return $a['correction_score'] < $b['correction_score'];
                } else {
                    return $a['total_time'] > $b['total_time'];
                }
            }
        });

        return [
            'rank_result' => $result,
            'submit_count' => $submit_count,
            'first_blood' => $first_blood
        ];
    }

    /**
     * 判断用户是否参加比赛
     * @return boolean
     */
    public function isUserInContest()
    {
        return Yii::$app->db->createCommand('SELECT count(*) FROM {{%contest_user}} WHERE user_id=:uid AND contest_id=:cid', [
            ':uid' => Yii::$app->user->id,
            ':cid' => $this->id
        ])->queryScalar();
    }

    /**
     * 通过题目在比赛中的序号来获取题目信息
     * @param $id
     * @return array|bool
     */
    public function getProblemById($id)
    {
        $contestID = $this->id;
        $dependency = new TagDependency(['tags' => ['id' => $id, 'contestID' => $contestID]]);
        return Yii::$app->db->cache(function ($db) use ($id, $contestID) {
            return $db->createCommand(
                "SELECT `cp`.`num`, `p`.`title`, `p`.`id`, `p`.`description`, 
                `p`.`input`, `p`.`output`, `p`.`sample_input`, `p`.`sample_output`, `p`.`hint`, `p`.`time_limit`, 
                `p`.`memory_limit` 
                FROM `problem` `p` 
                LEFT JOIN `contest_problem` `cp` ON cp.problem_id=p.id 
                WHERE (`cp`.`num`={$id}) AND (`cp`.`contest_id`={$contestID})"
            )->queryOne();
        }, 60, $dependency);
    }

    public function getClarifies()
    {
        return $this->hasMany(Discuss::className(), ['contest_id' => 'id']);
    }

    /**
     * 计算某个比赛的Rating
     *
     * @see https://en.wikipedia.org/wiki/Elo_rating_system
     */
    public function calRating()
    {
        $users = Yii::$app->db->createCommand('
            SELECT `u`.`id` as `user_id`, `rating`, `rating_change`
            FROM `user` `u`
            LEFT JOIN `contest_user` `c` ON `c`.`contest_id`=:cid
            WHERE u.id=c.user_id ORDER BY `c`.`id`
        ', [':cid' => $this->id])->queryAll();

        if ($this->type == self::TYPE_OI) {
            $rankResult = $this->getOIRankData(false)['rank_result'];
        } else {
            $rankResult = $this->getICPCRankData(false)['rank_result'];
        }
        $tmp = [];
        foreach ($rankResult as $k => $user) {
            //$tmp[$user['user_id']] = ['solved' => $user['solved'], 'rank' => $k];
            $tmp[$user['user_id']] = ['submit' => $user['submit'], 'rank' => $k];
        }
        $rankResult = $tmp;

        $userCount = 0;
        foreach ($users as $user) {
            if ($rankResult[$user['user_id']]['submit']> 0) {
                //如果该场比赛已经计算过了，就不再计算
                if ($user['rating_change'] != NULL) {
                    return;
                }
                $userCount++;
            }
        }
        $seed = [];
        $m = [];
        $R = [];
        $d = [];
        $i = 0;
        foreach ($users as $user) {
            
            $i++;
            $seed[$i] = 0;
            $old = $user['rating'] == NULL ? self::RATING_INIT_SCORE : $user['rating'];

            // 没有解决题目的不计算
            
            if ($user['rating']) {
                foreach ($users as $u) {
                    if ($user['user_id'] != $u['user_id'] &&$rankResult[$u['user_id']]['submit']> 0) {
                        $seed[$i] += 1.0 / (1.0 + pow(10, ($old-($u['rating'] ? $u['rating'] : self::RATING_INIT_SCORE) )/400 ));
                    }
                }
            }
            $seed[$i] += 1.0;
            if($user['rating'] == NULL ) $seed[$i]=1+($userCount/2);
            $m[$i] = sqrt($seed[$i] * ($rankResult[$user['user_id']]['rank']+1));
            $l=-1000;
            $r=4000;
            for($j=1;$j<=100;$j++)
            {
            	$mid=($l+$r)/2.0;
            	$CK=0;
            	foreach ($users as $u) {
                    if ($user['user_id'] != $u['user_id'] &&$rankResult[$u['user_id']]['submit']> 0) {
                        $CK += 1.0 / (1.0 + pow(10, ($mid-($u['rating'] ? $u['rating'] : self::RATING_INIT_SCORE) )/400 ));
                    }
                }
            	if($CK <= $m[$i]) $r=$mid;
            	else $l=$mid;
            }
            $d[$i] = intval(($r - $old) / 2);
        }
        $i=0;
        $sum_d = 0;
        // 第一次微调
       foreach ($users as $user){
	$i++;
             if($rankResult[$user['user_id']]['submit']== 0) continue;
	$sum_d+=$d[$i];	
        }
        $inc=(-1-$sum_d)/$userCount;
       $i=0;
         foreach ($users as $user){
             $i++;
	if($rankResult[$user['user_id']]['submit']== 0) continue;
	$d[$i]+=$inc;
	
        }
       $i=0;
      $sum_d=0;
       $tmp = min($userCount, intval(4 * sqrt($userCount)));
        foreach ($users as $user){
             $i++;
	if($i>$tmp) break;
	if($rankResult[$user['user_id']]['submit']== 0) continue;
	$sum_d += $d[$i];
	
        }
        $inc = min(max(-$sum_d/$tmp,-10),0);
      $i=0;
        foreach ($users as $user){
             $i++;
	if($i>$tmp) break;
	if($rankResult[$user['user_id']]['submit']== 0) continue;
	$d[$i]+=$inc;
	
        }
        // 两次微调结束
        $i = 0;
        foreach ($users as $user) {
            $i++;
            $old = $user['rating'] == NULL ? self::RATING_INIT_SCORE : $user['rating'];
           $d[$i]=intval($d[$i]);
            $newRating = $old+$d[$i];
	    if($rankResult[$user['user_id']]['submit']== 0) continue;
            // echo $old . " " . $newRating . " " . ($newRating - $old) . "<br>";
            Yii::$app->db->createCommand()->update('{{%user}}', [
                'rating' => $newRating
            ], ['id' => $user['user_id']])->execute();
            Yii::$app->db->createCommand()->update('{{contest_user}}', [
                'rating_change' => $newRating - $old,
                'rank' => $rankResult[$user['user_id']]['rank'] + 1
            ], ['user_id' => $user['user_id'], 'contest_id' => $this->id])->execute();
        }
    }


    /**
     * 是否有权限访问。用于限制比赛信息、问题、提交队列、榜单、答疑内容的访问，仅供管理员、参赛用户或比赛结束才能访问
     */
    public function canView()
    {
        // 比赛结束
        if ($this->status == Contest::STATUS_VISIBLE && $this->getRunStatus() == Contest::STATUS_ENDED) {
            return true;
        }
        $isAdmin = !Yii::$app->user->isGuest && Yii::$app->user->identity->role == User::ROLE_ADMIN;
        $isAuthor = !Yii::$app->user->isGuest && $this->created_by == Yii::$app->user->id;
        // 管理员或者创建人
        if ($isAdmin || $isAuthor) {
            return true;
        }
        // 该比赛/作业不可见
        if ($this->status == Contest::STATUS_HIDDEN) {
            return false;
        }
        // 参赛用户
        if ($this->isUserInContest()) {
            return true;
        }
        // 小组成员
        if ($this->group_id != 0) {
            $role = Yii::$app->db->createCommand('SELECT role FROM {{%group_user}} WHERE user_id=:uid AND group_id=:gid', [
                ':uid' => Yii::$app->user->id,
                ':gid' => $this->group_id
            ])->queryScalar();
            if ($role == GroupUser::ROLE_MEMBER || $role == GroupUser::ROLE_MANAGER || $role == GroupUser::ROLE_LEADER) {
                return true;
            }
        }
        return false;
    }

    public function getLoginUserProblemSolvingStatus()
    {
        if (Yii::$app->user->isGuest) {
            return null;
        }
        $statuses = Yii::$app->db->createCommand('
            SELECT `s`.`result`, `s`.`problem_id`
            FROM `solution` `s` LEFT JOIN `user` `u` ON u.id=s.created_by
            WHERE `contest_id`=:id AND `s`.`created_at`<=:endtime AND `s`.`created_by`=:uid
        ', [':id' => $this->id, ':endtime' => $this->end_time, ':uid' => Yii::$app->user->id])->queryAll();
        $res = [];
        foreach ($statuses as $status) {
            if (isset($res[$status['problem_id']]) && $res[$status['problem_id']] == Solution::OJ_AC) {
                continue;
            }
            $res[$status['problem_id']] = $status['result'];
        }
        return $res;
    }

    /**
     * 是否处于封榜状态
     */
    public function isScoreboardFrozen()
    {
       return !empty($this->lock_board_time) && strtotime($this->lock_board_time) <= time() &&
           time() <= strtotime($this->end_time) + Yii::$app->setting->get('scoreboardFrozenTime');
    }

    /**
     * 是否可以编辑比赛信息
     */
    public function isContestAdmin() {
        if (Yii::$app->user->isGuest) {
            return false;
        }
        // 管理员
        if (Yii::$app->user->identity->isAdmin()) {
            return true;
        }
        // 创建人
        if ($this->id == Yii::$app->user->id) {
            return true;
        }
        // 小组管理员
        if (!empty($this->group_id)) {
            if ($this->group->hasPermission()) {
                return true;
            }
        }
        return false;
    }

    /**
     * 删除比赛中的某道题目
     * @param $pid integer Problem ID
     * @throws \Throwable
     */
    public function deleteProblem($pid) {
        $db = Yii::$app->db;
        $cid = $this->id;
        $db->transaction(function () use ($pid, $cid) {
            Yii::$app->db->createCommand()
                ->delete('{{%contest_problem}}', ['contest_id' => $cid, 'problem_id' => $pid])
                ->execute();
            Solution::deleteAll(['contest_id' => $cid, 'problem_id' => $pid]);

            $problems = Yii::$app->db->createCommand('
                SELECT `p`.`id` AS `problem_id`
                FROM `problem` `p`
                LEFT JOIN `contest_problem` `c` ON `c`.`contest_id`=:cid
                WHERE p.id=c.problem_id
                ORDER BY `c`.`num`
            ', [':cid' => $cid])->queryAll();

            $i = 0;
            foreach ($problems as $problem) {
                Yii::$app->db->createCommand()->update('{{%contest_problem}}', [
                    'num' => $i
                ], ['contest_id' => $cid, 'problem_id' => $problem['problem_id']])->execute();
                TagDependency::invalidate(Yii::$app->cache, ['id' => $i, 'contestID' => $cid]);
                $i++;
            }
        });
    }
}

