<?php

namespace Tests\Unit\controllers;

use App\Http\Controllers\UserController;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use Mockery;
use Storage;
use Tests\TestCase;
use UploadedFile;

class UserControllerTest extends TestCase
{
    public function testIndex()
    {
        $userMock = Mockery::mock('overload:App\Models\User');
        $userMock->shouldReceive('search')
            ->andReturnSelf()
            ->shouldReceive('orderBy')
            ->andReturnSelf()
            ->shouldReceive('paginate')
            ->andReturn(collect([new User()]));

        $userMock->shouldReceive('where')
            ->with('active', true)
            ->andReturnSelf();

        $userMock->shouldReceive('where')
            ->with('stock', '>', 0)
            ->andReturnSelf();

        $controller = new UserController();
        $response = $controller->index(new Request());

        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
        $this->assertArrayHasKey('users', $response->getData());
        $this->assertEquals('users.admin.index', $response->getName());
    }

    public function testShow()
    {
        $userMock = Mockery::mock('overload:App\Models\User');
        //crear user
        $user = new User();
        $user->active = true;
        $userMock->shouldReceive('findOrFail')
            ->andReturn($user);

        $controller = new UserController();
        $response = $controller->show('test-id');

        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
        $this->assertEquals('users.user', $response->getName());
        $this->assertArrayHasKey('user', $response->getData());

    }

    public function testDestroy()
    {
        $userMock = Mockery::mock('overload:App\Models\User');
        $userMock->shouldReceive('findOrFail')
            ->andReturnSelf();
        $userMock->id = 1;
        $userMock->name = 'test-user';
        $userMock->shouldReceive('delete');

        $controller = Mockery::mock(UserController::class)->makePartial();
        $controller->shouldReceive('removeUserImage');

        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('expectsJson')->andReturn(false);

        $response = $controller->destroy($requestMock, 1);

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
    }

    public function testCreate()
    {
        $categoryMock = Mockery::mock('overload:App\Models\Category');
        $categoryMock->shouldReceive('all')
            ->andReturn(collect([new Category()]));

        $controller = new UserController();
        $response = $controller->create();

        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
    }

    public function testEdit()
    {
        $userMock = Mockery::mock('overload:App\Models\User');
        $userMock->shouldReceive('find')
            ->andReturn(new User());

        $categoryMock = Mockery::mock('overload:App\Models\Category');
        $categoryMock->shouldReceive('all')
            ->andReturn(collect([new Category()]));

        $controller = new UserController();
        $response = $controller->edit('test-id');

        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
    }

    public function testEditImage()
    {
        $userMock = Mockery::mock('overload:App\Models\User');
        $userMock->shouldReceive('find')
            ->andReturn(new User());

        $controller = new UserController();
        $response = $controller->editImage('test-id');

        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
    }

    protected function tearDown(): void
    {
        if (Mockery::getContainer() !== null) {
            Mockery::close();
        }
    }
}
