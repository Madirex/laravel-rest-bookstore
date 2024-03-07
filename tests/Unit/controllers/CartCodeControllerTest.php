<?php

namespace Tests\Unit\controllers;

use App\Http\Controllers\CartCodeController;
use App\Models\CartCode;
use App\Models\Category;
use Illuminate\Http\Request;
use Mockery;
use Storage;
use Tests\TestCase;
use UploadedFile;

class CartCodeControllerTest extends TestCase
{
    public function testIndex()
    {
        $cartcodeModel = Mockery::mock('overload:App\Models\Book');
        $cartcodeModel->percent_discount = 2;
        $cartcodeModel->fixed_discount = 2;
        $cartcodeMock = Mockery::mock('overload:App\Models\CartCode');
        $cartcodeMock->shouldReceive('search')
            ->andReturnSelf()
            ->shouldReceive('orderBy')
            ->andReturnSelf()
            ->shouldReceive('paginate')
            ->andReturn(collect([$cartcodeModel]));

        $cartcodeMock->shouldReceive('where')
            ->with('percent_discount', 2)
            ->andReturnSelf();

        $cartcodeMock->shouldReceive('where')
            ->with('fixed_discount', 2)
            ->andReturnSelf();

        $cartcodeMock->shouldReceive('where')
            ->with('is_deleted', false)
            ->andReturnSelf();

        $controller = new CartCodeController();
        $response = $controller->index(new Request());

        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
        $this->assertArrayHasKey('cartcodes', $response->getData());
        $this->assertEquals('cartcodes.index', $response->getName());
    }

    public function testShow()
    {
        $cartcodeMock = Mockery::mock('overload:App\Models\CartCode');
        //crear cartcode
        $cartcode = new CartCode();
        $cartcode->active = true;
        $cartcode->percent_discount = 2;
        $cartcode->fixed_discount = 2;
        $cartcodeMock->shouldReceive('findOrFail')
            ->andReturn($cartcode);

        $controller = new CartCodeController();
        $response = $controller->show('test-id');

        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
        $this->assertEquals('cartcodes.show', $response->getName());
        $this->assertArrayHasKey('cartcode', $response->getData());

    }

    public function testStoreJsonResponse()
    {
        $cartcodeMock = Mockery::mock('overload:App\Models\CartCode');
        $cartcodeMock->shouldReceive('__get')
            ->with('name')
            ->andReturn('mocked-name');
        $cartcodeMock->shouldReceive('save');

        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('expectsJson')->andReturn(true);
        $requestMock->shouldReceive('input')->andReturn('new-value');
        $requestMock->shouldReceive('all')->andReturn(['name' => 'mocked-name']);

        $controller = Mockery::mock(CartCodeController::class)->makePartial();
        $controller->shouldReceive('validateCartCode')->andReturn(null);
        $controller->shouldReceive('getCartCodeStore')->andReturn($cartcodeMock);

        $response = $controller->store($requestMock);

        $this->assertInstanceOf(\Illuminate\Http\JsonResponse::class, $response);
    }

    public function testUpdate()
    {
        $cartcodeMock = Mockery::mock('overload:App\Models\CartCode');
        $cartcodeMock->shouldReceive('findOrFail')
            ->andReturnSelf();
        $cartcodeMock->id = 1;
        $cartcodeMock->code = 'test-cartcode';
        $cartcodeMock->shouldReceive('save');

        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('expectsJson')->andReturn(false);
        $requestMock->shouldReceive('input')->andReturn('new-value');

        $controller = Mockery::mock(CartCodeController::class)->makePartial();
        $controller->shouldReceive('validateCartCode')->andReturn(null);

        $response = $controller->update($requestMock, 1);

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
    }

    public function testDestroy()
    {
        $cartcodeMock = Mockery::mock('overload:App\Models\CartCode');
        $cartcodeMock->shouldReceive('findOrFail')
            ->andReturnSelf();
        $cartcodeMock->id = 1;
        $cartcodeMock->name = 'test-cartcode';
        $cartcodeMock->shouldReceive('delete');
        $cartcodeMock->shouldReceive('save');

        $controller = Mockery::mock(CartCodeController::class)->makePartial();
        $controller->shouldReceive('removeCartCodeImage');

        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('expectsJson')->andReturn(false);

        $response = $controller->destroy($requestMock, 1);

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
    }

    public function testValidateCartCode()
    {
        $cartcodeMock = Mockery::mock('overload:App\Models\CartCode');
        $cartcodeMock->shouldReceive('validateCartCode')
            ->andReturn(collect([new CartCode()]));

        $controller = new CartCodeController();
        $response = $controller->validateCartCode(new Request());

        $this->assertEquals('El campo code es obligatorio. El campo percent discount es obligatorio. El campo fixed discount es obligatorio. El campo available uses es obligatorio. El campo expiration date es obligatorio.', $response);

    }

    public function testCreate()
    {
        $categoryMock = Mockery::mock('overload:App\Models\Category');
        $categoryMock->shouldReceive('all')
            ->andReturn(collect([new Category()]));

        $controller = new CartCodeController();
        $response = $controller->create();

        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
    }

    public function testEdit()
    {
        $cartcodeMock = Mockery::mock('overload:App\Models\CartCode');
        $cartcodeMock->shouldReceive('find')
            ->andReturn(new CartCode());

        $categoryMock = Mockery::mock('overload:App\Models\Category');
        $categoryMock->shouldReceive('all')
            ->andReturn(collect([new Category()]));

        $controller = new CartCodeController();
        $response = $controller->edit('test-id');

        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
    }

    protected function tearDown(): void
    {
        if (Mockery::getContainer() !== null) {
            Mockery::close();
        }
    }
}
