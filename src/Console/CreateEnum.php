<?php

namespace Kwaadpepper\Enum\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CreateEnum extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:enum';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new enum class';

    /**
     * Handle the command
     *
     * @return integer|void
     * @phpcs:disable Generic.Metrics.CyclomaticComplexity.TooHigh
     */
    public function handle()
    {
        // phpcs:enable
        $validate = false;
        $info     = [
            'name' => false,
            'definition' => [],
            'labels' => [],
            'values' => []
        ];

        $this->info('Enumeration Generator.');

        do {
            do {
                $name = ucfirst($this->ask('Which name should have the enum class ? (use PascalCase,  ex: UserRole)'));
                if (\strlen($name) <= 1 or \preg_match('/^[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*$/', $name) !== 1) {
                    $this->question('You shall provide a valid class name.');
                    if (!$this->confirm('Do you want to continue ?')) {
                        return -1;
                    }
                }
                if (File::exists(\app_path("Enums/$name.php"))) {
                    $this->question("$name.php already exists.");
                    if (!$this->confirm('Do you want to continue ?')) {
                        return -1;
                    }
                } else {
                    $info['name'] = $name;
                }
            } while ($info['name'] === false);

            do {
                // phpcs:ignore Generic.Files.LineLength.TooLong
                $values = \trim($this->ask('Write down definitions separated with pipes (|), ex: admin|editor|user|guest'));

                $info['definition'] = (\strlen($values) > 1 and \strpos($values, '|') !== 0) ?
                    explode('|', $values) : [];

                if (empty($info['definition'])) {
                    $this->question('You have to provide a definition for your enum.');
                    if (!$this->confirm('Do you want to continue ?')) {
                        return -1;
                    }
                }
            } while (empty($info['definition']));

            if ($this->confirm('Do you want to add custom values to your enum ?')) {
                // phpcs:ignore Generic.Files.LineLength.TooLong
                $values = $this->ask('Write down your values separated with pipes (|), ex: 0|1|2|3, or \'value1\'|\'value2\'|\'value3\'');

                $info['values'] = explode('|', $values);
            }
            if ($this->confirm('Do you want to add custom labels to your enum ?')) {
                // phpcs:ignore Generic.Files.LineLength.TooLong
                $values = $this->ask('Write down your labels separated with pipes (|), ex: When off|When on|When blinking');

                $info['labels'] = explode('|', str_replace('\'', '\\\'', $values));
            }

            $this->info('You have selected the followings :');
            $print = [];
            foreach ($info['definition'] as $k => $definition) {
                $print[] = [
                    $definition => [
                        'label' => $info['labels'][$k] ?? $definition,
                        'value' => $info['values'][$k] ?? $definition,
                    ]
                ];
            }
            $this->info(\json_encode($print, \JSON_PRETTY_PRINT | \JSON_NUMERIC_CHECK));

            if ($this->confirm('Do you want to use these parameters ?', true)) {
                $validate = true;
            } elseif (!$this->confirm('Do you want to continue ?')) {
                return -1;
            }
        } while (!$validate);

        if (!File::exists(\app_path('Enums'))) {
            $this->info('Creating Enums directory');
            File::makeDirectory(\app_path('Enums'));
        }

        $this->info('Generating enum');

        $templateEnum = File::get(__DIR__ . '/../../resources/templates/TemplateEnum.php');

        $this->replaceClassName($templateEnum, $info);
        $this->replaceDefinition($templateEnum, $info);
        $this->insertLabelsAndValues($templateEnum, $info);

        File::put(\app_path("Enums/$name.php"), $templateEnum);
        $this->info(\sprintf('File generated %s', \app_path("Enums/$name.php")));
    }

    /**
     * Replace the class name
     *
     * @param string $templateEnum
     * @param array  $info
     * @return void
     */
    private function replaceClassName(string &$templateEnum, array $info): void
    {
        $targetDefinition  = "class TemplateEnum";
        $replaceDefinition = "class {$info['name']}";

        $templateEnum = str_replace($targetDefinition, $replaceDefinition, $templateEnum);
    }

    /**
     * Replace definition
     *
     * @param string $templateEnum
     * @param array  $info
     * @return void
     */
    private function replaceDefinition(string &$templateEnum, array $info): void
    {
        $targetDefinition  = " * ##VALUES\n";
        $replaceDefinition = '';

        foreach ($info['definition'] as $definition) {
            $replaceDefinition .= " * @method static self $definition()\n";
        }
        $replaceDefinition .= " *\n";

        $templateEnum = str_replace($targetDefinition, $replaceDefinition, $templateEnum);
    }

    /**
     * Insert lables and values
     *
     * @param string $templateEnum
     * @param array  $info
     * @return void
     */
    private function insertLabelsAndValues(string &$templateEnum, array $info): void
    {
        $targetDefinition  = "    // ##OVERRIDE\n";
        $replaceDefinition = '';
        $templateValues    = 'public static function values()';
        $templateLabels    = 'public static function labels()';

        if (count($info['values'])) {
            $replaceDefinition = "    $templateValues\n    {\n        return [\n";
            foreach ($info['definition'] as $k => $definition) {
                $replaceDefinition .= sprintf(
                    "            '%s' => %s,\n",
                    $definition,
                    $info['values'][$k] ?? $definition
                );
            }
            $replaceDefinition  = rtrim($replaceDefinition, ',');
            $replaceDefinition .= "        ];\n    }\n";
        }
        if (count($info['labels'])) {
            $replaceDefinition .= "\n    $templateLabels\n    {\n        return [\n";
            foreach ($info['definition'] as $k => $definition) {
                $replaceDefinition .= sprintf(
                    "            '%s' => '%s',\n",
                    $definition,
                    $info['labels'][$k] ?? $definition
                );
            }
            $replaceDefinition  = rtrim($replaceDefinition, ',');
            $replaceDefinition .= "        ];\n    }\n";
        }
        $templateEnum = str_replace($targetDefinition, $replaceDefinition, $templateEnum);
    }
}
