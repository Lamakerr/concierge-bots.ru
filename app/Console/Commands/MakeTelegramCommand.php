<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeTelegramCommand extends Command
{
    protected $signature = 'make:telegram-command {name}';
    protected $description = 'Создает новую Telegram команду';

    public function handle()
    {
        $name = $this->argument('name');
        $className = ucfirst($name) . 'Command';
        $filePath = app_path("Telegram/Commands/{$className}.php");

        if (File::exists($filePath)) {
            $this->error("Команда с именем {$className} уже существует!");
            return;
        }

        $template = $this->getTemplate($className);

        File::ensureDirectoryExists(app_path('Telegram/Commands'));
        File::put($filePath, $template);

        $this->info("Команда {$className} была успешно создана!");
    }

    protected function getTemplate($className)
    {
        return "<?php\n\n" .
            "namespace App\\Telegram\\Commands;\n\n" .
            "use Telegram\\Bot\\Commands\\Command;\n\n" .
            "class {$className} extends Command\n" .
            "{\n" .
            "    protected \$name = '" . strtolower($className) . "';\n\n" .
            "    protected \$description = 'Описание вашей команды';\n\n" .
            "    public function handle()\n" .
            "    {\n" .
            "        // Логика вашей команды\n" .
            "        \$this->replyWithMessage(['text' => 'Hello from {$className}!']);\n" .
            "    }\n" .
            "}\n";
    }
}