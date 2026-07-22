<?php

namespace App\Contracts;

interface NewsRepositoryInterface
{
    public function save(array $data);
    public function insert(array $data): bool;
    public function update($id, array $data);
    public function delete($id);
    public function find($id);
    public function findByUrl(string $url);
    public function findByTitle(string $title);
    public function latest(int $limit = 10);
    public function search(string $keyword);
    public function paginate(int $perPage = 15);
    public function exists(string $field, $value): bool;
}
