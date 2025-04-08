<?php 

namespace system\core\config;

interface iConfig 
{
    public function set():array;
    public function get(string $param): ?string;
    public function all(): ?array;
    public function update(): void;
}