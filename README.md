# Puzzle Learning Bundle

Project based on Symfony project for managing learning accounts and learning security.

## **Install bundle**

Open a command console, enter your project directory and execute the following command to download the latest stable version of this bundle:

```yaml
composer require webundle/puzzle-learning-bundle
```

## **Step 1: Enable bundle**
Enable admin bundle by adding it to the list of registered bundles in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Puzzle\LearningBundle\LearningBundle(),
        );

        // ...
    }

    // ...
}
```

## **Step 2: Configure bundle security**
Configure security by adding it in the `app/config/security.yml` file of your project:

```yaml
security:
   	...
    role_hierarchy:
        ...
        # User
        ROLE_LEARNING: ROLE_ADMIN
        ROLE_SUPER_ADMIN: [..,ROLE_LEARNING]
        
	...
    access_control:
        ...
        # User
        - {path: ^%admin_prefix%learning, host: "%admin_host%", roles: ROLE_LEARNING }

```

## **Step 3: Enable bundle routing**

Register default routes by adding it in the `app/config/routing.yml` file of your project:

```yaml
....
user:
    resource: "@LearningBundle/Resources/config/routing.yml"
    prefix:   /
```
See all learning routes by typing: `php bin/console debug:router | grep learning`

## **Step 4: Configure bundle**

Configure admin bundle by adding it in the `app/config/config.yml` file of your project:

```yaml
admin:
    ...
    modules_available: '..,learning'
    navigation:
        nodes:
            ...
            # Learning
            learning:
                label: 'learning.title'
                description: 'learning.description'
                translation_domain: 'learning'
                attr:
                    class: 'fa fa-microphone'
                parent: ~
                user_roles: ['ROLE_LEARNING']
            learning_course:
                label: 'learning.course.navigation'
                description: 'learning.course.description'
                translation_domain: 'learning'
                path: 'puzzle_admin_learning_course_list'
                sub_paths: ['puzzle_admin_learning_course_create', 'puzzle_admin_learning_course_update', 'puzzle_admin_learning_course_show', 'puzzle_admin_learning_comment_list']
                parent: learning
                user_roles: ['ROLE_LEARNING']
            learning_category:
                label: 'learning.category.sidebar'
                description: 'learning.category.description'
                translation_domain: 'learning'
                path: 'puzzle_admin_learning_category_list'
                sub_paths: ['puzzle_admin_learning_category_create', 'puzzle_admin_learning_category_update', 'puzzle_admin_learning_category_show']
                parent: learning
                user_roles: ['ROLE_LEARNING']
```
