<?php

namespace Tests\Unit\controllers;

use App\Http\Controllers\ShopController;
use App\Models\Category;
use App\Models\Shop;
use Illuminate\Http\Request;
use Mockery;
use Storage;
use Tests\TestCase;
use UploadedFile;

class ShopControllerTest extends TestCase
{
    public function testIndex()
    {
        $shopModel = Mockery::mock('overload:App\Models\Book');
        $shopModel->percent_discount = 2;
        $shopModel->fixed_discount = 2;

        $shopMock = Mockery::mock('overload:App\Models\Shop');
        $shopMock->shouldReceive('search')
            ->andReturnSelf()
            ->shouldReceive('orderBy')
            ->andReturnSelf()
            ->shouldReceive('paginate')
            ->andReturn(collect([$shopModel]));

        //mockear lo de arriba:
        $shopMock->shouldReceive('where')
            ->with('active', true)
            ->andReturnUsing(function () use ($shopMock) {
                return $shopMock;
            });

        $shopMock->shouldReceive('where')
            ->withArgs(function ($column) {
                return $column instanceof \Closure;
            })
            ->andReturnUsing(function () use ($shopMock) {
                return $shopMock;
            });

        $shopMock->shouldReceive('orderBy')
            ->with('id', 'asc')
            ->andReturnSelf();

        $shopMock->shouldReceive('paginate')
            ->with(8)
            ->andReturnSelf();

        $shopMock->shouldReceive('where')
            ->withArgs([Mockery::on(function ($closure) {
                $query = Mockery::mock('Illuminate\Database\Query\Builder');
                $query->shouldReceive('where')->with('name', 'LIKE', '%test%')->andReturnSelf();
                $query->shouldReceive('orderBy')->with('id', 'asc')->andReturnSelf();
                $query->shouldReceive('paginate')->with(8)->andReturn(collect([$shopModel]));
                $closure($query);
                return true;
            })])
            ->andReturnSelf();

        $controller = new ShopController();
        $response = $controller->index(new Request([]));

        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
        $this->assertArrayHasKey('shops', $response->getData());
        $this->assertEquals('shops.index', $response->getName());
    }


    public function testShow()
    {
        $shopMock = Mockery::mock('overload:App\Models\Shop');
        $shop = new Shop();
        $shop->active = false;
        $shop->percent_discount = 2;
        $shop->fixed_discount = 2;
        $shopMock->shouldReceive('findOrFail')
            ->andReturn($shop);

        $controller = new ShopController();
        $response = $controller->show(new Request(), 'test-id');

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
    }

    public function testStoreJsonResponse()
    {
        $shopMock = Mockery::mock('overload:App\Models\Shop');
        $shopMock->shouldReceive('__get')
            ->with('name')
            ->andReturn('mocked-name');
        $shopMock->shouldReceive('findOrFail')
            ->andReturn($shopMock);
        $shopMock->shouldReceive('save');

        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('expectsJson')->andReturn(true);
        $requestMock->shouldReceive('input')->andReturn('new-value');
        $requestMock->shouldReceive('all')->andReturn(['name' => 'mocked-name']);

        $controller = Mockery::mock(ShopController::class)->makePartial();
        $controller->shouldReceive('validateShop')->andReturn(null);
        $controller->shouldReceive('getShopStore')->andReturn($shopMock);

        $response = $controller->store($requestMock);

        $this->assertInstanceOf(\Illuminate\Http\JsonResponse::class, $response);
    }

    public function testUpdate()
    {
        $shopMock = Mockery::mock('overload:App\Models\Shop');
        $shopMock->shouldReceive('findOrFail')
            ->andReturnSelf();
        $shopMock->id = 1;
        $shopMock->code = 'test-shop';
        $shopMock->shouldReceive('save');

        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('expectsJson')->andReturn(false);
        $requestMock->shouldReceive('input')->andReturn('new-value');

        $controller = Mockery::mock(ShopController::class)->makePartial();
        $controller->shouldReceive('validateShop')->andReturn(null);

        $response = $controller->update($requestMock, 1);

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
    }

    public function testDestroy()
    {
        // Mockear el modelo Shop
        $shopMock = Mockery::mock('overload:App\Models\Shop');
        $shopMock->shouldReceive('findOrFail')->with(1)->andReturnSelf();
        $shopMock->shouldReceive('delete');
        $shopMock->name = 'test-shop';

        // Mockear el controlador ShopController
        $controller = Mockery::mock(ShopController::class)->makePartial();
        $controller->shouldReceive('removeShopImage');

        // Mockear la solicitud (Request)
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('expectsJson')->andReturn(false);

        // Mockear el método books() del modelo Shop
        $bookMock = Mockery::mock('overload:App\Models\Book');
        $bookMock->shop = $shopMock;
        $bookMock->shop->books = collect([$bookMock]);
        $shopMock->shouldReceive('books')
            ->andReturn(collect([$bookMock]));

        //mockear el método save()
        $bookMock->shouldReceive('save');
        $shopMock->shouldReceive('save');

        // Llamar al método destroy() del controlador y obtener la respuesta
        $response = $controller->destroy(1);

        // Verificar que la respuesta es una instancia de RedirectResponse
        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
    }


    public function testValidateShop()
    {
        $shopMock = Mockery::mock('overload:App\Models\Shop');
        $shopMock->shouldReceive('validateShop')
            ->andReturn(collect([new Shop()]));

        $controller = new ShopController();
        $response = $controller->validateShop(new Request());

        $this->assertEquals('El campo name es obligatorio.', $response);

    }

    public function testCreate()
    {
        $categoryMock = Mockery::mock('overload:App\Models\Category');
        $categoryMock->shouldReceive('all')
            ->andReturn(collect([new Category()]));

        $controller = new ShopController();
        $response = $controller->create();

        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
    }

    public function testEdit()
    {
        $shopMock = Mockery::mock('overload:App\Models\Shop');
        $shopMock->shouldReceive('find')
            ->andReturn(new Shop());

        $categoryMock = Mockery::mock('overload:App\Models\Category');
        $categoryMock->shouldReceive('all')
            ->andReturn(collect([new Category()]));

        $controller = new ShopController();
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
