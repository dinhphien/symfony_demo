<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=CommentRepository::class)
 */
#[ApiResource(
    attributes:[
        'order' => ['published' => 'DESC'],
        'pagination_enabled' => false,
        'pagination_client_enabled' => true
    ],
    itemOperations: [
        'GET' => [
            'normalization_context' => ['groups' => ['get']]
        ],
        'PUT' => [
            "security" => "object.getAuthor() === user"
        ]
    ],
    collectionOperations: [
        'GET' => [
            'normalization_context' => ['groups' => ['get-comment-with-author']]
        ],
        'api_blog_posts_comments_get_subresource' => [
            'normalization_context' => ['groups' => ['get-comment-with-author']]
        ],
        'POST' => [
            'denormalization_context' => ['groups' => ['post']]
        ]
    ]
)]
class Comment implements AuthoredEntityInterface, PublishedDateTimeInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"get-comment-with-author"})
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * @Groups({"get-comment-with-author", "post"})
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"get-comment-with-author"})
     */
    private $published;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"get-comment-with-author"})
     */
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity=BlogPost::class, inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"post"})
     */
    private $blogPost;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getPublished(): ?\DateTimeInterface
    {
        return $this->published;
    }

    public function setPublished(\DateTimeInterface $published): PublishedDateTimeInterface
    {
        $this->published = $published;

        return $this;
    }

    /**
     * @return User
     */ 
    public function getAuthor(): User
    {
        return $this->author;
    }

    /**
     * Set the value of author
     * @param UserInterface $author
     * @return  AuthoredEntityInterface
     */ 
    public function setAuthor(UserInterface $author): AuthoredEntityInterface
    {
        $this->author = $author;

        return $this;
    }

    public function getBlogPost(): ?BlogPost
    {
        return $this->blogPost;
    }

    public function setBlogPost(?BlogPost $blogPost): self
    {
        $this->blogPost = $blogPost;

        return $this;
    }
}
