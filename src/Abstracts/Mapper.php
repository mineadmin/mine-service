<?php

declare(strict_types=1);
/**
 * This file is part of MineAdmin.
 *
 * @link     https://www.mineadmin.com
 * @document https://doc.mineadmin.com
 * @contact  root@imoi.cn
 * @license  https://github.com/mineadmin/MineAdmin/blob/master/LICENSE
 */

namespace Mine\Abstracts;

use Hyperf\Database\Model\Builder;
use Hyperf\DbConnection\Model\Model;
use Mine\Annotation\CrudModelCollector;
use Mine\ServiceException;

/**
 * @template ModelClass
 */
abstract class Mapper
{
    /**
     * @var class-string<ModelClass>|string
     */
    protected string $model;

    /**
     * 获取模型类名.
     * @return ModelClass
     * @throws ServiceException
     */
    public function getModel(): string
    {
        $modelClass = null;
        if (property_exists($this, 'model')) {
            $modelClass = $this->model;
        }
        if (property_exists($this, 'mapper')) {
            $modelClass = $this->mapper;
        }
        if (! empty(CrudModelCollector::list()[static::class])) {
            $modelClass = CrudModelCollector::list()[static::class];
        }
        if (! class_exists($modelClass) || ! ($modelClass instanceof Model)) {
            throw new ServiceException('The class to which the ' . static::class . ' class belongs was not found');
        }
        /** @var class-string<Model> $modelClass */
        return $modelClass;
    }

    /**
     * @throws ServiceException
     */
    public function getModelQuery(): Builder
    {
        return $this->getModel()::query();
    }

    /**
     * @throws ServiceException
     */
    public function getModelInstance(): Builder|\Hyperf\Database\Model\Model
    {
        return $this->getModelQuery()->getModel();
    }
}
