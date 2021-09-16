<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $blogPost = new BlogPost();
        $blogPost->setTitle('A first blog post!');
        $blogPost->setPublished(new DateTime('2021-09-16 16:01:00'));
        $blogPost->setAuthor('Jake');
        $blogPost->setContent('this is the first blog post text and I am really happy with that');
        $blogPost->setSlug('a-first-blog-post');
        $manager->persist($blogPost);

        $blogPost = new BlogPost();
        $blogPost->setTitle('A second blog post!');
        $blogPost->setPublished(new DateTime('2021-09-16 16:10:00'));
        $blogPost->setAuthor('Jake');
        $blogPost->setContent('this is the second blog post text and I am still really happy with that');
        $blogPost->setSlug('a-second-blog-post');
        $manager->persist($blogPost);

        $manager->flush();
    }
}
