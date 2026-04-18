```shell
    composer require laravel/breeze --dev
```


```shell

    php artisan breeze:install

    php artisan migrate
    npm install
    npm run dev

    php artisan make:model Category -m
    php artisan make:model Notice -m 

    Schema::create('categories', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
        $table->string('name')->unique();
        $table->string('slug')->nullable();
        $table->timestamps();
    });

    Schema::create('notices', function (Blueprint $table) {
        $table->id();
        $table->foreignId('category_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
        $table->string('title');
        $table->string('description');
        $table->text('notice');
         $table->string('path_image')->nullable();
        $table->string('slug')->nullable();
        $table->timestamps();
    });

    php artisan make:controller Api/Admin/CategoryController -r
    php artisan make:controller Api/Admin/NoticeController -r
    php artisan make:controller Api/Public/CategoryController -r
    php artisan make:controller Api/Public/NoticeController -r
    php artisan make:factory NoticeFactory
    php artisan make:factory CategoryFactory

    php artisan storage:link 

```
