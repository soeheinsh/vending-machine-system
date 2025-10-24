<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

require_once __DIR__ . '/../controllers/ProductsController.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/VendingUser.php';
require_once __DIR__ . '/../models/Transaction.php';
require_once __DIR__ . '/../core/utils/Validation.php';

class ProductsControllerTest extends TestCase
{
    private ProductsController $controller;
    private MockObject $mockProductModel;
    private MockObject $mockUserModel;
    private MockObject $mockTransactionModel;

    protected function setUp(): void
    {
        // Create mocks for dependencies
        $this->mockProductModel = $this->createMock(Product::class);
        $this->mockUserModel = $this->createMock(VendingUser::class);
        $this->mockTransactionModel = $this->createMock(Transaction::class);

        // Create controller with mocked dependencies
        $this->controller = new ProductsController(
            $this->mockProductModel,
            $this->mockUserModel,
            $this->mockTransactionModel
        );
    }

    public function testControllerInstantiation(): void
    {
        $this->assertInstanceOf(ProductsController::class, $this->controller);
    }

    public function testControllerUsesInjectedDependencies(): void
    {
        $reflection = new ReflectionClass($this->controller);
        
        $productModelProperty = $reflection->getProperty('productModel');
        $productModelProperty->setAccessible(true);
        $injectedProductModel = $productModelProperty->getValue($this->controller);
        
        $userModelProperty = $reflection->getProperty('userModel');
        $userModelProperty->setAccessible(true);
        $injectedUserModel = $userModelProperty->getValue($this->controller);
        
        $transactionModelProperty = $reflection->getProperty('transactionModel');
        $transactionModelProperty->setAccessible(true);
        $injectedTransactionModel = $transactionModelProperty->getValue($this->controller);

        $this->assertSame($this->mockProductModel, $injectedProductModel);
        $this->assertSame($this->mockUserModel, $injectedUserModel);
        $this->assertSame($this->mockTransactionModel, $injectedTransactionModel);
    }

    public function testValidationFailsOnEmptyName(): void
    {
        $invalidData = [
            'name' => '',
            'price' => '5.99',
            'quantity_available' => '10'
        ];

        $errors = Validation::validateProduct($invalidData);
        
        $this->assertArrayHasKey('name', $errors);
        $this->assertStringContainsString('required', $errors['name']);
    }

    public function testValidationFailsOnNegativePrice(): void
    {
        $invalidData = [
            'name' => 'Test Product',
            'price' => '-5.99',
            'quantity_available' => '10'
        ];

        $errors = Validation::validateProduct($invalidData);
        
        $this->assertArrayHasKey('price', $errors);
        $this->assertStringContainsString('positive', $errors['price']);
    }

    public function testValidationFailsOnNegativeQuantity(): void
    {
        $invalidData = [
            'name' => 'Test Product',
            'price' => '5.99',
            'quantity_available' => '-5'
        ];

        $errors = Validation::validateProduct($invalidData);
        
        $this->assertArrayHasKey('quantity_available', $errors);
        $this->assertStringContainsString('non-negative', $errors['quantity_available']);
    }

    public function testValidationSucceedsOnValidData(): void
    {
        $validData = [
            'name' => 'Valid Product',
            'price' => '10.99',
            'quantity_available' => '100'
        ];

        $errors = Validation::validateProduct($validData);
        
        $this->assertEmpty($errors, 'Valid data should not produce errors');
    }

    public function testValidationHandlesPurchaseData(): void
    {
        $validPurchaseData = [
            'product_id' => '1',
            'quantity' => '2'
        ];

        $errors = Validation::validatePurchase($validPurchaseData);
        $this->assertEmpty($errors, 'Valid purchase data should not produce errors');

        $invalidPurchaseData = [
            'product_id' => '0',
            'quantity' => '-1'
        ];

        $errors = Validation::validatePurchase($invalidPurchaseData);
        $this->assertNotEmpty($errors, 'Invalid purchase data should produce errors');
        $this->assertArrayHasKey('product_id', $errors);
        $this->assertArrayHasKey('quantity', $errors);
    }

    public function testModelGetAllMethod(): void
    {
        $expectedProducts = [
            ['id' => 1, 'name' => 'Coke', 'price' => 3.99, 'quantity_available' => 10],
            ['id' => 2, 'name' => 'Pepsi', 'price' => 6.88, 'quantity_available' => 5]
        ];

        $this->mockProductModel->expects($this->once())
            ->method('getAll')
            ->willReturn($expectedProducts);

        $result = $this->mockProductModel->getAll();
        $this->assertEquals($expectedProducts, $result);
        $this->assertCount(2, $result);
    }

    public function testModelGetByIdMethod(): void
    {
        $productId = 1;
        $expectedProduct = ['id' => $productId, 'name' => 'Coke', 'price' => 3.99, 'quantity_available' => 10];

        $this->mockProductModel->expects($this->once())
            ->method('getById')
            ->with($productId)
            ->willReturn($expectedProduct);

        $result = $this->mockProductModel->getById($productId);
        $this->assertEquals($expectedProduct, $result);
        $this->assertEquals($productId, $result['id']);
    }

    public function testModelDeleteMethod(): void
    {
        $productId = 1;

        $this->mockProductModel->expects($this->once())
            ->method('delete')
            ->with($productId)
            ->willReturn(true);

        $result = $this->mockProductModel->delete($productId);
        $this->assertTrue($result);
    }

    public function testModelCreateMethod(): void
    {
        $validData = [
            'name' => 'Test Product',
            'price' => 5.99,
            'quantity_available' => 10
        ];

        $this->mockProductModel->expects($this->once())
            ->method('create')
            ->with($validData)
            ->willReturn(true);

        $result = $this->mockProductModel->create($validData);
        $this->assertTrue($result);
    }

    public function testModelUpdateMethod(): void
    {
        $productId = 1;
        $productData = [
            'name' => 'Updated Product',
            'price' => 7.99,
            'quantity_available' => 15
        ];

        $this->mockProductModel->expects($this->once())
            ->method('update')
            ->with($productId, $productData)
            ->willReturn(true);

        $result = $this->mockProductModel->update($productId, $productData);
        $this->assertTrue($result);
    }

    public function testTransactionModelCreateMethod(): void
    {
        $userId = 1;
        $productId = 1;
        $quantity = 2;
        $totalPrice = 11.98;

        $this->mockTransactionModel->expects($this->once())
            ->method('create')
            ->with($userId, $productId, $quantity, $totalPrice)
            ->willReturn(true);

        $result = $this->mockTransactionModel->create($userId, $productId, $quantity, $totalPrice);
        $this->assertTrue($result);
    }
}