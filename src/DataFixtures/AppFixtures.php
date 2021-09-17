<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use App\Entity\Comment;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    /**
     * @var UserPasswordHasherInterface
     */
    private $passwordEncoder;

    /**
     * @var \Faker\Factory
     */

    public function __construct(UserPasswordHasherInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->faker = \Faker\Factory::create();
    }

    public function load(ObjectManager $manager)
    {
        $this->loadUsers($manager);
        $this->loadBlogPosts($manager);
        $this->loadComments($manager);
    }

    public function loadUsers(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('admin');
        $user->setPassword($this->passwordEncoder->hashPassword($user, 'secret123456'));
        $user->setActive(true);
        $user->setEmail('admin@gmail.com');
        $user->setName('Jake Marston');

        $this->addReference('admin_user', $user);

        $manager->persist($user);
        $manager->flush();

    }

    public function loadBlogPosts(ObjectManager $manager)
    {
        $user = $this->getReference('admin_user');

        for ($i = 0; $i < 100; $i++) {
            $blogPost = new BlogPost();
            $blogPost->setTitle($this->faker->realText(30));
            $blogPost->setPublished($this->faker->dateTimeThisYear);
            $blogPost->setAuthor($user);
            $blogPost->setContent($this->faker->realText());
            $blogPost->setSlug('blog-post-' . $i);
            $this->setReference("blog_post_$i", $blogPost);
            $manager->persist($blogPost);
        }
        $manager->flush();
    }

    public function loadComments(ObjectManager $manager)
    {
        $author = $this->getReference('admin_user');
        for ($i = 0; $i < 100; $i++) {
            for ($j = 0; $j < rand(0, 10); $j++) {
                $comment = new Comment();
                $comment->setContent($this->faker->realText(80));
                $comment->setPublished($this->faker->dateTimeThisYear);
                $comment->setAuthor($author);
                $comment->setBlogPost($this->getReference("blog_post_$i"));
                $manager->persist($comment);
            }
        }
        $manager->flush();
    }
}
