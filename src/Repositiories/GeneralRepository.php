<?php

/**
 * Created by PhpStorm.
 * User: Ara Arakelyan
 * Date: 7/18/2017
 * Time: 5:45 PM
 */

namespace Btybug\btybug\Repositories;

abstract class GeneralRepository implements RepositoryInterface
{
    /**
     * @var
     */
    protected $model;

    /**
     * GeneralRepository constructor.
     */
    public function __construct()
    {
        $this->model = $this->model();
    }

    /**
     * @return mixed
     */
    protected abstract function model();

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
     * @return mixed
     */
    public function first()
    {
        return $this->model->first();
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
     * @param string $operator
     * @param array $columns
     * @param array $with
     * @return mixed
     */
    public function findBy(string $attribute, string $value, string $operator = '=', array $columns = ['*'], array $with = [])
    {
        return $this->model
            ->with($with)
            ->where($attribute, $value)
            ->first($columns);
    }

    /**
     * @param array $conditions
     * @param array $columns
     * @param array $with
     * @return mixed
     */
    public function findAllByMultiple(array $conditions, array $columns = ['*'], array $with = [])
    {
        $model = $this->model->with($with);
        foreach ($conditions as $column => $condition) {
            $model = $model->where($column, $condition);
        }
        return $model->get($columns);
    }

    /**
     * @param array $conditions
     * @param array $columns
     * @param array $with
     * @return mixed
     */
    public function findOneByMultiple(array $conditions, array $columns = ['*'], array $with = [])
    {
        $model = $this->model->with($with);
        foreach ($conditions as $column => $condition) {
            $model = $model->where($column, $condition);
        }
        return $model->first($columns);
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

    /**
     * @param array $ids
     * @return bool
     */
    public function destroy(array $ids)
    {
        return $this->model->destroy($ids) ? true : false;
    }

    /**
     * @param string $attribute
     * @param string $value
     * @return bool
     */
    public function deleteByCondition(string $attribute, string $value)
    {
        return $this->model->where($attribute, $value)->delete() ? true : false;

    }

    /**
     * @param string $key
     * @param string $value
     * @return mixed
     */
    public function pluck(string $key, string $value)
    {
        return $this->model->pluck($key, $value);
    }

}