Feature: Manage blog posts
    Scenario: Create a blog post 
    When I add "Content-Type" header equal to "application/ld+json"
    And I add "Accept" header equal to "application/ld+json"
    And I send a "POST" request to "/api/blog_posts" with body:
    """
    {
        "title": "A new blog post!",
        "content": "Hello there!",
        "author": "Jake Marston",
        "slug": "a-new-blog-post"
    }
    """
    Then the response status code should be 201