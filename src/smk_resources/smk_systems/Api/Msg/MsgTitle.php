<?php
/**
 * Created by IntelliJ IDEA.
 * User: yu
 * Date: 18-1-29
 * Time: 下午2:26
 */

namespace App\Http\Controllers\smk_systems\Api\Msg;


class MsgTitle
{
    private $touser;
    private $toparty;
    private $msgtype;
    private $smk_id;
    private $corp_id;

    /**
     * @return mixed
     */
    public function getCorpId()
    {
        return $this->corp_id;
    }

    /**
     * @param mixed $corp_id
     */
    public function setCorpId($corp_id)
    {
        $this->corp_id = $corp_id;
    }


    /**
     * @return mixed
     */
    public function getTouser()
    {
        $user = "";
        if ($this->touser!="all"&&count($this->touser) > 0) {
            foreach ($this->touser as $d) {
                $user .= $d . '|';
            }
            $user = substr($user, 0, strlen($user) - 1);
        }
        return $user;
    }

    /**
     * @param mixed $touser
     */
    public function setTouser($touser)
    {
        if (count($touser) > 0) {
            $this->touser = $touser;
        } else {
            $this->touser = [];
        }

    }

    /**
     * @return mixed
     */
    public function getToparty()
    {
        $dep = "";
        if (count($this->toparty) > 0) {
            foreach ($this->toparty as $d) {
                $dep .= $d . '|';
            }
            $dep = substr($dep, 0, strlen($dep) - 1);
        }
        return $dep;
    }

    /**
     * @param mixed $toparty
     */
    public function setToparty($toparty)
    {
        if (count($toparty) > 0) {
            $this->toparty = $toparty;
        } else {
            $this->toparty = [];
        }
    }

    /**
     * @return string
     */
    public function getMsgtype()
    {
        return $this->msgtype;
    }

    /**
     * @param string $msgtype
     */
    public function setMsgtype($msgtype)
    {
        $this->msgtype = $msgtype;
    }

    /**
     * @return mixed
     */
    public function getSmkId()
    {
        return $this->smk_id;
    }

    /**
     * @param mixed $smk_id
     */
    public function setSmkId($smk_id)
    {
        $this->smk_id = $smk_id;
    }

    public function msgTitle($d)
    {
        $x = array(
            'msgtype' => $this->getMsgtype(),
            $this->getMsgtype() => $d
        );

        if($this->touser=='all'){
            $x['touser'] = '@all';
        }else if (count($this->touser) > 0) {
            $x['touser'] = $this->getTouser();
        }

        if (count($this->toparty) > 0) {
            $x['toparty'] = $this->getToparty();
        }
        $pam = array(
            'smk_id' => $this->getSmkId(),
            'corp_id' => $this->getCorpId(),
            'd' => $x
        );
        return $pam;
    }

}