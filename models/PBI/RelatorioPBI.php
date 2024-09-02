<?php
require_once __DIR__ . '\\..\\..\\config\\config.php';
require_once CAMINHO_BASE . '\\models\\Azure\\AzureAPI.php';
require_once CAMINHO_BASE . '\\models\\Azure\\Capacidade.php';

require_once CAMINHO_BASE . '\\config\\database.php';
require_once CAMINHO_BASE . '\\models\\PBI\\PowerBISession.php';

require_once CAMINHO_BASE . '\\models\\SessionManager.php';

class RelatorioPBI {
    private $pdo;
    private const LOG_FILE = 'PowerBI';

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function pegarRelatoriosAtivos(){
        $log = AppLogger::getInstance(self::LOG_FILE);

        SessionManager::checarSessao();
        try {
            $sql = "SELECT * FROM RelatorioPBI WHERE ativo = 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
    
            $reportsArray = array_reduce($reports, function($carry, $report) {
            $carry[$report['relatorio_clean']] = [
                "relatorio" => $report['relatorio'],
                "id_relatorio" => $report['id_relatorio'],
                "id_dataset" => $report['id_dataset'],
                "rls" => $report['rls']
            ];
                return $carry;
            }, []);
            
            $log->info('Relatórios ativos listados', ['user' => $_SESSION['id_usuario'], 'page' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $_SERVER['REQUEST_URI']]);

            return $reportsArray;
        } catch (Exception $e) {
            $log->error('Erro ao listar relatórios ativos', ['user' => $_SESSION['id_usuario'], 'page' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $_SERVER['REQUEST_URI'], 'erro' => $e->getMessage()]);
            return json_encode(['sucesso' => false, 'erro' => `Erro:` . $e->getMessage()]);
        }
    }

    function gerarRelatorioPBI($actualLink){
        $capacidade = new Capacidade();
    
        $log = AppLogger::getInstance(self::LOG_FILE);

        SessionManager::checarSessao();
    
        $log->info('Gerando relatório PowerBI', ['user' => $_SESSION['id_usuario'], 'page' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $_SERVER['REQUEST_URI']]);
        try {
            $conn = (new Database())->getConnection();
            
            $reports = self::pegarRelatoriosAtivos();
            
            if (!isset($reports[$actualLink])) {
                $log->error('Relatório não encontrado', ['user' => $_SESSION['id_usuario'], 'page' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $_SERVER['REQUEST_URI'], 'relatorio' => $actualLink]);            
                header('Location: /views/index.php');
                exit;
            } 
    
            $currentReport = $reports[$actualLink];
            
            $powerBISession = new PowerBISession($conn, $_SESSION['id_usuario']);
            $powerBISession->criarSessaoPBI();
            
            $azureAPI = new AzureAPI();
            
            $log->info('Ligando Capacity', ['user' => $_SESSION['id_usuario'], 'page' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $_SERVER['REQUEST_URI']]);
            $capacidadeAtiva = $capacidade->ligarCapacity($powerBISession->sessoesAtivasPBI(), $azureAPI);
    
            if (!json_decode($capacidadeAtiva)->sucesso){
                $log->error('Não foi possível gerar relatório, capacidade não iniciada', ['user' => $_SESSION['id_usuario'], 'page' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $_SERVER['REQUEST_URI'], 'relatorio' => $actualLink]);
                return $capacidadeAtiva;
            }
            
            $log->info('Buscando parâmetros de Embed', ['user' => $_SESSION['id_usuario']]);
            $reportEmbedConfig = $azureAPI->pegarEmbedParams($currentReport['id_relatorio'], $currentReport['id_dataset'], null);
            $conn = null;
    
            $log->info('Relatório gerado com sucesso', ['user' => $_SESSION['id_usuario'], 'page' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $_SERVER['REQUEST_URI'], 'relatorio' => $actualLink]);
            return json_encode(['sucesso' => true, 'dados' => json_encode($reportEmbedConfig)]);
    
        } catch (Exception $e) {
            $log->error('Erro ao gerar relatório PowerBI: ' . $e->getMessage(), ['user' => $_SESSION['id_usuario'], 'page' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $_SERVER['REQUEST_URI'], 'relatorio' => $actualLink]);
        }
    }
    public function buscarInformacoesRelatorio($relatorioClean){
        $log = AppLogger::getInstance(self::LOG_FILE);

        SessionManager::checarSessao();
        try {
            $sql = 'SELECT relatorio, relatorio_clean FROM RelatorioPBI WHERE ativo = 1 AND relatorio_clean = :relatorio_clean';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':relatorio_clean', $relatorioClean);
            $stmt->execute();
            $report = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            if (!$report) {
                $log->error('Relatório não encontrado', ['user' => $_SESSION['id_usuario'], 'page' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $_SERVER['REQUEST_URI'], 'relatorio' => $relatorioClean]);
                return json_encode(['sucesso' => false, 'erro' => 'Relatório não encontrado']);
            }

            $log->info('Informações do relatório buscadas com sucesso', ['user' => $_SESSION['id_usuario'], 'report' => $report['relatorio'],'page' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $_SERVER['REQUEST_URI']]);
            return $report;
        } catch (Exception $e) {
            $log->error('Exceção ao listar informações relatórios ativos', ['user' => $_SESSION['id_usuario'], 'page' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $_SERVER['REQUEST_URI'], 'erro' => $e->getMessage()]);
            return json_encode(['sucesso' => false, 'erro' => `Erro:` . $e->getMessage()]);
        }
    }
}
