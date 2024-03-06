<?php

use App\Http\Controllers\BookController;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Tests\TestCase;

class BookControllerTest extends TestCase
{
    public function testIndex()
    {
        $bookMock = Mockery::mock('overload:App\Models\Book');
        $bookMock->shouldReceive('search')
            ->andReturnSelf()
            ->shouldReceive('orderBy')
            ->andReturnSelf()
            ->shouldReceive('paginate')
            ->andReturn(collect([new Book()]));

        $bookMock->shouldReceive('where')
            ->with('active', true)
            ->andReturnSelf();

        $bookMock->shouldReceive('where')
            ->with('stock', '>', 0)
            ->andReturnSelf();

        $controller = new BookController();
        $response = $controller->index(new Request());

        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
        $this->assertArrayHasKey('books', $response->getData());
        $this->assertEquals('books.index', $response->getName());
    }

    public function testShow()
    {
        $bookMock = Mockery::mock('overload:App\Models\Book');
        //crear book
        $book = new Book();
        $book->active = true;
        $bookMock->shouldReceive('findOrFail')
            ->andReturn($book);

        $controller = new BookController();
        $response = $controller->show('test-id');

        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
        $this->assertEquals('books.show', $response->getName());
        $this->assertArrayHasKey('book', $response->getData());

    }

    public function testStoreJsonResponse()
    {
        $bookMock = Mockery::mock('overload:App\Models\Book');
        $bookMock->shouldReceive('__get')
            ->with('name')
            ->andReturn('mocked-name');
        $bookMock->shouldReceive('save');

        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('expectsJson')->andReturn(true);
        $requestMock->shouldReceive('input')->andReturn('new-value');
        $requestMock->shouldReceive('all')->andReturn(['name' => 'mocked-name']);

        $controller = Mockery::mock(BookController::class)->makePartial();
        $controller->shouldReceive('validateBook')->andReturn(null);
        $controller->shouldReceive('getBookStore')->andReturn($bookMock);

        $response = $controller->store($requestMock);

        $this->assertInstanceOf(\Illuminate\Http\JsonResponse::class, $response);
    }

    public function testUpdate()
    {
        $bookMock = Mockery::mock('overload:App\Models\Book');
        $bookMock->shouldReceive('findOrFail')
            ->andReturnSelf();
        $bookMock->id = 1;
        $bookMock->name = 'test-book';
        $bookMock->isbn = 'test-isbn';
        $bookMock->shouldReceive('save');

        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('expectsJson')->andReturn(false);
        $requestMock->shouldReceive('input')->andReturn('new-value');

        $controller = Mockery::mock(BookController::class)->makePartial();
        $controller->shouldReceive('validateBook')->andReturn(null);

        $response = $controller->update($requestMock, 1);

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
    }

    public function testDestroy()
    {
        $bookMock = Mockery::mock('overload:App\Models\Book');
        $bookMock->shouldReceive('findOrFail')
            ->andReturnSelf();
        $bookMock->id = 1;
        $bookMock->name = 'test-book';
        $bookMock->shouldReceive('delete');

        $controller = Mockery::mock(BookController::class)->makePartial();
        $controller->shouldReceive('removeBookImage');

        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('expectsJson')->andReturn(false);

        $response = $controller->destroy($requestMock, 1);

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
    }

    public function testUpdateImage()
    {
        $fileMock = Mockery::mock(UploadedFile::class);
        $fileMock->shouldReceive('getClientOriginalExtension')
            ->andReturn('jpg');
        $fileMock->shouldReceive('storeAs')
            ->andReturn('books/test-book.jpg');

        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('validate');
        $requestMock->shouldReceive('file')
            ->andReturn($fileMock);
        $requestMock->shouldReceive('expectsJson')->andReturn(false);

        $bookMock = Mockery::mock('overload:App\Models\Book');
        $bookMock->shouldReceive('find')
            ->andReturnSelf();
        $bookMock->id = 1;
        $bookMock->name = 'test-book';
        $bookMock->shouldReceive('save');

        Storage::shouldReceive('exists')
            ->andReturnTrue();
        Storage::shouldReceive('delete');

        $controller = Mockery::mock(BookController::class)->makePartial();
        $controller->shouldReceive('removeBookImage');

        $response = $controller->updateImage($requestMock, 1);

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
    }

    public function testValidateBook()
    {
        $bookMock = Mockery::mock('overload:App\Models\Book');
        $bookMock->shouldReceive('validateBook')
            ->andReturn(collect([new Book()]));

        $controller = new BookController();
        $response = $controller->validateBook(new Request());

        $this->assertEquals('El campo isbn es obligatorio. El campo name es obligatorio. El campo author es obligatorio. El campo publisher es obligatorio. El campo description es obligatorio. El campo price es obligatorio. El campo stock es obligatorio. El campo category name es obligatorio. El campo shop id es obligatorio.', $response);

    }

    public function testCreate()
    {
        $categoryMock = Mockery::mock('overload:App\Models\Category');
        $categoryMock->shouldReceive('all')
            ->andReturn(collect([new Category()]));

        $controller = new BookController();
        $response = $controller->create();

        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
    }

    public function testEdit()
    {
        $bookMock = Mockery::mock('overload:App\Models\Book');
        $bookMock->shouldReceive('find')
            ->andReturn(new Book());

        $categoryMock = Mockery::mock('overload:App\Models\Category');
        $categoryMock->shouldReceive('all')
            ->andReturn(collect([new Category()]));

        $controller = new BookController();
        $response = $controller->edit('test-id');

        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
    }

    public function testEditImage()
    {
        $bookMock = Mockery::mock('overload:App\Models\Book');
        $bookMock->shouldReceive('find')
            ->andReturn(new Book());

        $controller = new BookController();
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
