<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Upgrade extends Command
{
    /**
     * @var bool
     */
    protected $routeCacheEnabled = true;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upgrade
                                {--dev : Executa o procedimento para ambiente de desenvolvimento}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Atualizar aplicação';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->showCommandHeader();

        $confirmMessage = 'Tem certeza que deseja atualizar a aplicação?';
        if (!$this->option('dev') && !$this->confirm($confirmMessage)) {
            $this->error('Operação cancelada');
            return;
        }

        $this->executeCommands();
        $this->info('Pronto!');
    }

    private function showCommandHeader()
    {
        $this->line("\nAtualização da aplicação.\n");
        $this->line('Este comando fará as seguintes ações:');
        $this->line(' - Executar as migrações');
        $this->line(' - Executar comando de otimização');
        $this->line(' - Limpar cache da aplicação (somente quando a opção "--dev" presente estiver presente)');
    }

    private function executeCommands()
    {
        $this->option('dev')
            ? $this->optimizeForDev()
            : $this->optimizeForProduction();

        $this->executeMigrate();
    }

    private function executeMigrate()
    {
        $migrationMessages = [
            'success' => 'Migrações concluídas.',
            'failed' => 'Erro ao rodar migrações'
        ];
        $this->executeWithMessages('migrate', ['--force' => true], $migrationMessages);
    }

    private function optimizeForProduction()
    {
        $this->cacheViews();
        $this->cacheEvents();

        ($this->routeCacheEnabled)
            ? $this->cacheRoutes()
            : $this->clearRoutesCache();

        $this->runOptimizeCommand();
    }

    private function optimizeForDev()
    {
        $this->clearApplicationCache();
        $this->clearRoutesCache();
        $this->clearConfigurationsCache();
        $this->clearViewsCache();
    }

    private function cacheRoutes()
    {
        $cacheRoutesMessages = ['success' => 'Criado cache das rotas.'];
        $this->executeWithMessages('route:cache', [], $cacheRoutesMessages);
    }

    private function runOptimizeCommand()
    {
        $cacheRoutesMessages = ['success' => 'Arquivos de cache otimizados para produção.'];
        $this->executeWithMessages('optimize', [], $cacheRoutesMessages);
    }

    private function cacheEvents()
    {
        $cacheEventsMessages = ['success' => 'Criado cache dos eventos.'];
        $this->executeWithMessages('event:cache', [], $cacheEventsMessages);
    }

    private function cacheViews()
    {
        $cacheViewsMessages = ['success' => 'Criado cache das views.'];
        $this->executeWithMessages('view:cache', [], $cacheViewsMessages);
    }

    private function clearRoutesCache()
    {
        $clearCacheRouteMessages = ['success' => 'Cache das rotas foi apagado.'];
        $this->executeWithMessages('route:clear', [], $clearCacheRouteMessages);
    }

    private function clearConfigurationsCache()
    {
        $clearCacheConfigurationMessages = ['success' => 'Cache das configurações foi apagado.'];
        $this->executeWithMessages('config:clear', [], $clearCacheConfigurationMessages);
    }

    private function clearViewsCache()
    {
        $clearCacheViewMessages = ['success' => 'Cache das views foi apagado.'];
        $this->executeWithMessages('view:clear', [], $clearCacheViewMessages);
    }

    private function clearApplicationCache()
    {
        $clearCacheMessages = ['success' => 'Cache da aplicação foi apagado.'];
        $this->executeWithMessages('cache:clear', [], $clearCacheMessages);
    }

    private function executeWithMessages($commandName, $commandOptions, $outputMessages = [])
    {
        try {
            $this->callSilent($commandName, $commandOptions);
            $message = data_get(
                $outputMessages,
                'success',
                "Comando [$commandName] executado com sucesso"
            );
            $this->info("-> $message");
        } catch (\Exception $exception) {
            $message = "Falha ao executar o comando [$commandName]";
            $this->error("{$message} - {$exception->getMessage()}");
        }
    }
}
