<?php

namespace Fico7489\Laravel\UpdatedRelated\Events;

class ModelChanged
{
    private $id;
    private $model;
    private $name;

    public function __construct($id, $model, $name)
    {
        $this->id    = $id;
        $this->model = $model;
        $this->name  = $name;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getModel()
    {
        return $this->model;
    }

    public function getName()
    {
        return $this->name;
    }
}
