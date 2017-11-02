<?php

/**
 * Created by PhpStorm.
 * User: Ara Arakelyan
 * Date: 7/18/2017
 * Time: 5:48 PM
 */

namespace Btybug\btybug\Repositories;

interface RepositoryInterface
{
    /**
     * @return mixed
     */
    public function getAll();

    /**
     * @param string $attribute
     * @param string $value
     * @param array $columns
     * @param array $with
     * @return mixed
     */
    public function getBy(string $attribute, string $value, array $columns, array $with = []);

    /**
     * @return mixed
     */
    public function countAll();

    /**
     * @param string $attribute
     * @param string $value
     * @return mixed
     */
    public function countBy(string $attribute, string $value);

    /**
     * @param int $id
     * @param array $columns
     * @param array $with
     * @return mixed
     */
    public function find(int $id, array $columns, array $with = []);

    /**
     * @param int $id
     * @param array $columns
     * @param array $with
     * @return mixed
     */
    public function findOrFail(int $id, array $columns, array $with = []);


    /**
     * @param string $attribute
     * @param string $value
     * @param string $operator
     * @param array $columns
     * @param array $with
     * @return mixed
     */
    public function findBy(string $attribute, string $value, string $operator, array $columns, array $with = []);


    /**
     * @param array $conditions
     * @param array $columns
     * @param array $with
     * @return mixed
     */
    public function findAllByMultiple(array $conditions, array $columns, array $with = []);

    /**
     * @param array $conditions
     * @param array $columns
     * @param array $with
     * @return mixed
     */
    public function findOneByMultiple(array $conditions, array $columns, array $with = []);

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data);

    /**
     * @param int $id
     * @param array $data
     * @return mixed
     */
    public function update(int $id, array $data);

    /**
     * @param array $params
     * @param array $data
     * @return mixed
     */
    public function updateOrCreate(array $params, array $data);

    /**
     * @param int $id
     * @return mixed
     */
    public function delete(int $id);

    /**
     * @param array $ids
     * @return mixed
     */
    public function destroy(array $ids);

    /**
     * @param string $attribute
     * @param string $value
     * @return mixed
     */
    public function deleteByCondition(string $attribute, string $value);

    /**
     * @param string $key
     * @param string $value
     * @return mixed
     */
    public function pluck(string $key, string $value);


}