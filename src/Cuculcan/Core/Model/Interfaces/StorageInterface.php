<?php
namespace Cuculcan\Core\Model\Interfaces;

interface StorageInterface
{
    public function add(EntityInterface $item);

    public function update(EntityInterface $item);

    public function delete(EntityInterface $item);

    public function getById($id);

    public function getByIds(array $ids);
}
