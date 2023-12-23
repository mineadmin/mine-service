<?php

namespace Mine\Traits;

use Hyperf\DbConnection\Annotation\Transactional;
use Hyperf\DbConnection\Model\Model;
use Mine\ServiceException;

trait DeleteServiceTrait
{
    use GetModelTrait;

    /**
     * @throws ServiceException
     */
    #[Transactional]
    public function remove(mixed $idOrWhere, bool $force = false): bool
    {
        $modelClass = $this->getModel();
        $query = $modelClass::query();
        /**
         * @var null|bool|Model $instance
         */
        $instance = false;
        if (is_array($idOrWhere)){
            $instance = $query->where($idOrWhere)->first();
        }
        if (is_callable($idOrWhere)){
            $instance = $query->where($idOrWhere)->first();
        }
        if ($instance === false){
            $instance = $query->find($idOrWhere);
        }
        if (empty($instance)){
            return false;
        }
        if ($force){
            return $instance->forceDelete();
        }
        return false;
    }

    /**
     * 删除单条记录 不会触发 model 事件
     * @param array|integer|string|Closure $id 主键或自定义条件
     * @return bool
     * @throws ServiceException
     */
    public function delete(mixed $id): bool
    {
        $model = $this->getModel();
        $query = $model::query()->getModel();
        $keyName = $query->getModel()->getKeyName();
        /**
         * @var null|Model $instance
         */
        $instance = false;
        if (is_array($id)){
            $instance = $query->where($id)->first();
        }
        if (is_callable($id)){
            $instance = $query->where($id)->first();
        }
        if ($instance === null){
            $instance = $query->find($id);
        }
        if (empty($instance)){
            return false;
        }
        return $model::query()
            ->where(
                $keyName,
                $instance->getKey()
            )->delete();
    }


    /**
     * 根据主键批量删除
     * @param array $ids
     * @return bool
     * @throws ServiceException
     */
    #[Transactional]
    public function removeByIds(array $ids): bool
    {
        $query = $this->getModelQuery();
        $keyName = $query->getModel()->getKeyName();
        return $query->whereIn($keyName,$ids)->delete();
    }
}