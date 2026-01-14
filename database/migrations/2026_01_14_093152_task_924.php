<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('news', function(Blueprint $table) {
            $table->id();

            //Не знаю ограничение по длине, поэтоу взяла varchar(255)
            $table->string('title')
                ->comment('Наименование');

            // Не знаю какой размер текста подразумевается, исхожу, что это поле и есть содержимое новости
            $table->longText('body')
                ->comment('Описание');

            $table->timestamps();
        });

        Schema::create('posts', function(Blueprint $table) {
            $table->id();

            //Не знаю ограничение по длине, поэтоу взяла varchar(255)
            $table->string('title')
                ->comment('Наименование');

            /**
             * Не знаю, будет ли кроме кода на видео еще какой то текст в контенте, поэтому
             * предусматриваю большое поле. Если подразумевается только ссылка на видео
             * из какого либо сервиса, то нужно делать поле varchar(255)
             */
            $table->longText('body')
                ->comment('Описание');

            $table->timestamps();
        });

        Schema::create('comments', function(Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                /**
                 * не знаю, нужно ли удалять комменты при удалении пользователя,
                 * поэтому ставлю null в ключе
                 *
                 * Если убрать контроль за целостностью данных из БД и перенести в код,
                 * то можно использовать softDeletes
                 */
                ->nullOnDelete();

            $table->longText('body')
                ->comment('Содержимое комментария');

            $table->string('entity')
                ->index()
                ->comment('К какой сущности оставлен комментарий, например: N:1, P:5, C:34');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('news');
    }
};
