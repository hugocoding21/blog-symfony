<?php

namespace App\Tests\Entity;

use App\Entity\Category;
use PHPUnit\Framework\TestCase;

class CategoryTest extends TestCase
{
    public function testCategoryName()
    {
        $category = new Category();
        $category->setName('Test Category');

        $this->assertSame('Test Category', $category->getName());
    }
}
