<?php

namespace Tests\Unit\controllers;

use App\Http\Controllers\AddressController;
use App\Models\Address;
use App\Models\Category;
use Illuminate\Http\Request;
use Mockery;
use Storage;
use Tests\TestCase;
use UploadedFile;

class AddressControllerTest extends TestCase
{
    public function testIndex()
    {
        $addressMock = Mockery::mock('overload:App\Models\Address');
        $addressMock->shouldReceive('search')
            ->andReturnSelf()
            ->shouldReceive('orderBy')
            ->andReturnSelf()
            ->shouldReceive('paginate')
            ->andReturn(collect([new Address()]));

        $addressMock->shouldReceive('where')
            ->with('active', true)
            ->andReturnSelf();

        $addressMock->shouldReceive('where')
            ->with('stock', '>', 0)
            ->andReturnSelf();

        $controller = new AddressController();
        $response = $controller->index(new Request());

        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
        $this->assertArrayHasKey('addresses', $response->getData());
        $this->assertEquals('addresses.index', $response->getName());
    }

    public function testShow()
    {
        $addressMock = Mockery::mock('overload:App\Models\Address');
        //crear address
        $address = new Address();
        $address->active = true;
        $addressMock->shouldReceive('findOrFail')
            ->andReturn($address);

        $controller = new AddressController();
        $response = $controller->show('test-id', new Request());

        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
        $this->assertEquals('addresses.show', $response->getName());
        $this->assertArrayHasKey('address', $response->getData());

    }

    public function testDestroy()
    {
        $addressMock = Mockery::mock('overload:App\Models\Address');
        $addressMock->shouldReceive('findOrFail')
            ->andReturnSelf();
        $addressMock->id = 1;
        $addressMock->name = 'test-address';
        $addressMock->shouldReceive('delete');

        $controller = Mockery::mock(AddressController::class)->makePartial();
        $controller->shouldReceive('removeAddressImage');

        $requestMock = Mockery::mock(Illuminate\Http\Request::class);
        $requestMock->shouldReceive('expectsJson')->andReturn(false);

        $response = $controller->destroy(1, new Request());

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
    }

    public function testCreate()
    {
        $categoryMock = Mockery::mock('overload:App\Models\Category');
        $categoryMock->shouldReceive('all')
            ->andReturn(collect([new Category()]));

        $controller = new AddressController();
        $response = $controller->create();

        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
    }

    public function testEdit()
    {
        $addressMock = Mockery::mock('overload:App\Models\Address');
        $addressMock->shouldReceive('find')
            ->andReturn(new Address());

        $categoryMock = Mockery::mock('overload:App\Models\Category');
        $categoryMock->shouldReceive('all')
            ->andReturn(collect([new Category()]));

        $controller = new AddressController();
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
