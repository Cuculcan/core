<?php
namespace Cuculcan\Core\Interfaces;

interface StorageInterface
{
    public function add(EntityInterface $item, $insertIgnore = false);

    public function addBatch(array $items, $updateDub = false, $insertIgnore = false);

    public function update(EntityInterface $item, array $updateFields);

    public function updateAllByOne(array $items, EntityInterface $item, array $updateFields);

    public function delete($id);

    public function getById($id);

    public function getByIds(array $ids);
}
