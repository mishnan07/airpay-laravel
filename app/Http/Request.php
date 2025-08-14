<?php

namespace Illuminate\Http;

class Request
{
    protected $data;

    public function __construct($data = [])
    {
        $this->data = $data ?: $_POST;
    }

    public function all()
    {
        return $this->data;
    }

    public function validate($rules)
    {
        foreach ($rules as $field => $rule) {
            if (strpos($rule, 'required') !== false && empty($this->data[$field])) {
                throw new \Exception("Field $field is required");
            }
        }
    }
}