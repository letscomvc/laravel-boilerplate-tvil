<?php
/**
 * All files in this folder will be included in the application.
 */

if (!function_exists('implode_with_comma')) {
    /**
     * Separar os ítens de uma array por vírgula.
     *
     * @param  string[]  $array  .
     * @param  string  $lastGlue
     * @param  string  $outputWhenEmpty
     * @return string
     */
    function implode_with_comma(
        array $array,
        string $lastGlue = ', ',
        string $outputWhenEmpty = ''
    ) {
        if (sizeof($array) == 0) {
            return $outputWhenEmpty;
        }

        if (sizeof($array) != 1) {
            $last = array_pop($array);
            $glued = implode($array, ', ');
            return $glued.$lastGlue.$last;
        }

        return implode($array, ', ');
    }
}

if (!function_exists('mask')) {
    /**
     *  Aplica uma máscara à uma string.
     *
     * @param  string  $value  Valor a ser mascarado
     * @param  string  $mask  Máscara
     * @param  string  $mask_character  Caractere que representará os valores preenchíveis
     * @return string
     */
    function mask(string $value, string $mask, string $mask_character = '#'): string
    {
        $value = str_replace(" ", "", $value);
        for ($i = 0; $i < strlen($value); $i++) {
            $mask[strpos($mask, $mask_character)] = $value[$i];
        }

        return $mask;
    }
}

if (!function_exists('flash')) {
    /**
     * Retorna uma instância do helper Flash
     *
     * @return \App\Support\Flash
     */
    function flash()
    {
        return new \App\Support\Flash();
    }
}

if (!function_exists('current_user')) {
    /**
     * Retorna uma instância do usuário corrente.
     *
     * @return \App\Models\User
     */
    function current_user()
    {
        return auth()->user();
    }
}

if (!function_exists('apply_params')) {
    /**
     * Aplica argumentos em parâmetros de uma string
     *
     * @param  string  $string
     * @param  array  $params
     * @param  string  $before
     * @param  string  $after
     * @return string
     */
    function apply_params(string $string, array $params, $before = ':', $after = '')
    {
        $regex = '/'.$before.'[a-z_]+'.$after.'/';
        return preg_replace_array($regex, $params, $string);
    }
}

if (!function_exists('in_production')) {
    /**
     * Retorna se a aplicação está em produção.
     *
     * @return bool
     */
    function in_production()
    {
        $actualEnv = env('APP_ENV', 'local');
        return (starts_with($actualEnv, 'prod'));
    }
}

if (!function_exists('milliseconds')) {
    /**
     * Retorna o timestamp atual em milisegundos
     *
     * @return int
     */
    function milliseconds()
    {
        $microTime = explode(' ', microtime());
        return ((int) $microTime[1]) * 1000 + ((int) round($microTime[0] * 1000));
    }
}

if (!function_exists('stress')) {
    /**
     * Retorna o tempo, em milisegundos, que um método é executado.
     *
     * @param  callable  $function  Método a ser estressado
     * @param  int  $times  Quatidade de vezes que o método será executado
     * @param  bool  $dumpAndDie  Encerrar a aplicação com o resultado do teste
     * @return int tempo de execução
     */
    function stress(callable $function, int $times = 1000, bool $dumpAndDie = true)
    {
        return \App\Support\Debug::stress($function, $times, $dumpAndDie);
    }
}

if (!function_exists('cache_manager')) {
    /**
     *  Resolve o resultado de uma chave de cache e retorna seu valor. Caso chamado
     * sem nenhum argumento, retorna uma instancia de \App\Helpers\CacheManager.
     *
     * @param  string|callable  $key  Chave de cache
     * @param  string  $ttl  Tempo de duração do cache. Ver guia de formatos
     * relativos em http://php.net/manual/pt_BR/datetime.formats.relative.php
     * @param  callable  $value  Valor a ser criado cache
     * @param  array  $tags  Marcações para a chave de cache
     * @return mixed
     */
    function cache_manager()
    {
        $args = func_get_args();
        $cacheManager = app()->make(\App\Support\CacheManager::class);

        if (empty($args)) {
            return $cacheManager;
        }

        return $cacheManager->remember(...$args);
    }
}

if (!function_exists('request_cache')) {
    /**
     *  Faz cache de um resultado apenas durante a requisição atual.
     *
     * @param  string|callable  $key  Chave de cache
     * @param  callable  $value  Valor a ser criado cache
     * @return mixed
     */
    function request_cache($key, callable $value)
    {
        $cacheManager = app()->make(\App\Support\CacheManager::class);
        return $cacheManager->requestCache($key, $value);
    }
}

if (!function_exists('strbool')) {
    /**
     * Retorna a string de um valor booleano.
     *
     * @param $value
     * @return string
     */
    function strbool(bool $value): string
    {
        return $value ? 'true' : 'false';
    }
}

if (!function_exists('is_valid_url')) {
    /**
     * Validate if param is a valid URL;
     *
     * @param  string  $url
     * @return boolean
     */
    function is_valid_url(string $url)
    {
        return (bool) filter_var($url, FILTER_VALIDATE_URL);
    }
}
