<?php

namespace App\Tests\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\AuthoredEntityInterface;
use App\Entity\BlogPost;
use App\Entity\Comment;
use App\Entity\User;
use App\EventSubscriber\AuthoredEntitySubscriber;
use DG\BypassFinals;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Validator\Constraints\Blank;

class AuthoredEntitySubscriberTest extends TestCase
{
    public function testConfiguration()
    {
        $result = AuthoredEntitySubscriber::getSubscribedEvents();
        $this->assertArrayHasKey(KernelEvents::VIEW, $result);
        $this->assertEquals(
            ['getAuthenticatedUser', EventPriorities::PRE_WRITE],
            $result[KernelEvents::VIEW]
        );
    }

    /** 
     * @dataProvider providerSetAuthorCall
     * 
     */
    public function testSetAuthorCall(string $className, string $method, bool $shoulCallSetAuthor)
    {

        $entityMock = $this->getEntityMock($className, $shoulCallSetAuthor);
        $tokenStorageMock = $this->getTokenStorageMock();
        $eventMock = $this->getEventMock($method, $entityMock);
        (new AuthoredEntitySubscriber($tokenStorageMock))->getAuthenticatedUser(
            $eventMock
        );
    }

    private function getTokenStorageMock(bool $hasToken = true): MockObject
    {
        $tokenMock = $this->createMock(TokenInterface::class);
        $tokenMock->expects($hasToken ? $this->once() : $this->never())
            ->method('getUser')
            ->willReturn(new User());

        $tokenStorageMock = $this->createMock(TokenStorageInterface::class);
        $tokenStorageMock->expects($this->once())
            ->method('getToken')
            ->willReturn($hasToken ? $tokenMock : null);
        return $tokenStorageMock;
    }

    private function getEventMock(string $method, AuthoredEntityInterface $controllerResult): MockObject
    {

        $requestMock = $this->createMock(Request::class);
        $requestMock->expects($this->once())
            ->method('getMethod')
            ->willReturn($method);

        BypassFinals::enable();
        $eventMock = $this->createMock(ViewEvent::class);

        $eventMock->expects($this->once())
            ->method('getControllerResult')
            ->willReturn($controllerResult);
        $eventMock->expects($this->once())
            ->method('getRequest')
            ->willReturn($requestMock);

        return $eventMock;
    }

    private function getEntityMock(string $className, bool $shoulCallSetAuthor): MockObject
    {
        $entityMock = $this->createMock($className);
        $entityMock->expects($shoulCallSetAuthor ? $this->once() : $this->never())
            ->method('setAuthor');

        return $entityMock;
    }

    public function providerSetAuthorCall(): array
    {
        return [
            [BlogPost::class, 'POST', true],
            [BlogPost::class, 'GET', false],
            [Comment::class, 'POST', true]
        ];
    }


    public function testNoTokenPresent()
    {
        $tokenStorageMock = $this->getTokenStorageMock(false);
        $eventMock = $this->getEventMock(Request::METHOD_POST, new BlogPost());
        (new AuthoredEntitySubscriber($tokenStorageMock))->getAuthenticatedUser(
            $eventMock
        );
    }
}
