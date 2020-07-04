<?php

namespace App\Interfaces;

interface ToUserRelationsInterface
{
    public function createdBy();

    public function updatedBy();

    public function deletedBy();
}
