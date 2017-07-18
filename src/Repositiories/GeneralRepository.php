<?php

/**
 * Created by PhpStorm.
 * User: Ara Arakelyan
 * Date: 7/18/2017
 * Time: 5:45 PM
 */

namespace Sahakavatar\Cms\Repositories;

abstract class GeneralRepository implements RepositoryInterface
{
    /**
     * @var
     */
    protected $model;

    /**
     * @return mixed
     */
    public function getAll()
    {
        return $this->model->all();
    }

    /**
     * @param string $attribute
     * @param string $value
     * @param array $columns
     * @param array $with
     * @return mixed
     */
    public function getBy(string $attribute, string $value, array $columns = ['*'], array $with = [])
    {
        return $this->model->with($with)->where($attribute, $value)->get($columns);
    }

    /**
     * @return mixed
     */
    public function countAll()
    {
        return $this->model->count();
    }

    /**
     * @param string $attribute
     * @param string $value
     * @return mixed
     */
    public function countBy(string $attribute, string $value)
    {
        return $this->model->where($attribute, $value)->count();
    }

    /**
     * @param int $id
     * @param array $columns
     * @param array $with
     * @return mixed
     */
    public function find(int $id, array $columns = ['*'], array $with = [])
    {
        return $this->model->with($with)->find($id, $columns);
    }

    /**
     * @param int $id
     * @param array $columns
     * @param array $with
     * @return mixed
     */
    public function findOrFail(int $id, array $columns = ['*'], array $with = [])
    {
        return $this->model->with($with)->findOrFail($id, $columns);
    }

    /**
     * @param string $attribute
     * @param string $value
     * @param array $columns
     * @param array $with
     * @return mixed
     */
    public function findBy(string $attribute, string $value, array $columns = ['*'], array $with = [])
    {
        return $this->model
            ->with($with)
            ->where($attribute, $value)
            ->first($columns);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * @param int $id
     * @param array $data
     * @return null
     */
    public function update(int $id, array $data)
    {
        $model = $this->model->find($id);
        if (empty($model)) {
            return null;
        }
        $model->update($data);
        return $model;
    }

    /**
     * @param array $params
     * @param array $data
     * @return mixed
     */
    public function updateOrCreate(array $params, array $data)
    {
        return $this->model->updateOrCreate($params, $data);
    }

    /**
     * @param int $id
     * @return bool
     */
    public function delete(int $id)
    {
        $model = $this->model->find($id);
        return $model->delete() ? true : false;
    }



}