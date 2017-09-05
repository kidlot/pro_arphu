<?php
namespace Backstage\Model;
use Backstage\Model\BaseModel;
/**
 * ModelName
 */
class AdminUsersModel extends BaseModel{
    // 自动验证
    protected $_validate=array(
        array('username','require','用户名必须',0,'',3), // 验证字段必填
    );

    // 自动完成
    protected $_auto=array(
        array('register_time','time',1,'function'), // 对date字段在新增的时候写入当前时间戳
    );

    /**
     * 添加用户
     */
    public function addData($data){
        // 对data数据进行验证
        if(!$data=$this->create($data)){
            // 验证不通过返回错误
            return false;
        }else{
            // 验证通过
            $result=$this->add($data);
            return $result;
        }
    }

    /**
     * 更新
     * @return boolean fasle 失败 ， int  成功 返回完整的数据
     */
    public function updateData($map, $data){
        if(count($data) == 0) {
            return false;
        }
        $res = $this->where($map)->data($data)->save();
        //内容添加完成
        return $res;
    }

    /**
     * 删除数据
     * @param   array   $map    where语句数组形式
     * @return  boolean         操作是否成功
     */
    public function deleteData($map){
        die('禁止删除用户');
    }

    /**
     * 查询一条数据
     * @return boolean fasle 失败 ， int  成功 返回完整的数据
     */
    public function getData($map){
        $res = $this->where($map)->find();
        //内容添加完成
        return $res;
    }
}
