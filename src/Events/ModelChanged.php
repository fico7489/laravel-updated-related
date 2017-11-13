<?php

namespace Fico7489\Laravel\UpdatedRelated\Events;

class ModelChanged
{
    private $id;
    private $model;
    private $enviroment;

    public function __construct($id, $model, $enviroment)
    {
        $this->id = $id;
        $this->model  = $model;
        $this->enviroment  = $enviroment;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getModel()
    {
        return $this->model;
    }

    public function getEnviroment()
    {
        return $this->enviroment;
    }
}
