<?php
namespace Interfaces;

interface Model {
    public function db($var);
    public function or(string $var);
    public function and(string $var);
    public function limit(string $var);
}